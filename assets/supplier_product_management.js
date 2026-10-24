document.addEventListener('DOMContentLoaded', () => {
    const section = document.getElementById('pricing-markup');

    if (!section) return;

    const tabs = section.querySelectorAll('[data-pricing-tab]');
    const rulesPanel = document.getElementById('pricing-rules-panel');
    const suppliersPanel = document.getElementById('supplier-products-panel');

    const form = document.getElementById('supplier-product-form');
    const productSelect = document.getElementById('sp-product');
    const supplierSelect = document.getElementById('sp-supplier');
    const priceInput = document.getElementById('sp-price');
    const tableBody = document.getElementById('supplier-product-table');
    const message = document.getElementById('supplier-product-message');

    if (
        !rulesPanel || !suppliersPanel || !form ||
        !productSelect || !supplierSelect ||
        !priceInput || !tableBody || !message
    ) {
        console.error('Supplier Products panel is missing required HTML elements.');
        return;
    }

    let managementData = {
        products: [],
        suppliers: [],
        offers: []
    };

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[char]);
    }

    function money(value) {
        return '₱' + Number(value).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function setMessage(text, isError = false) {
        message.textContent = text;
        message.style.color = isError ? '#b00020' : '#236b35';
    }



    function openConfirmation(summaryHtml, onConfirm) {
        if (typeof window.openPricingConfirmation !== 'function') {
            setMessage(
                'The confirmation modal is unavailable. Check that order_confirmation.js is loaded.',
                true
            );
            return;
        }

        window.openPricingConfirmation(summaryHtml, onConfirm);
    }



    function showTab(tabName) {
        const showSuppliers = tabName === 'suppliers';

        rulesPanel.style.display = showSuppliers ? 'none' : '';
        suppliersPanel.style.display = showSuppliers ? '' : 'none';

        tabs.forEach(tab => {
            const active = tab.dataset.pricingTab === tabName;

            tab.classList.toggle('active', active);
            tab.setAttribute('aria-pressed', active ? 'true' : 'false');
        });

        if (showSuppliers) {
            loadManagementData();
        }
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            showTab(tab.dataset.pricingTab);
        });
    });

    async function requestJSON(url, options = {}) {
        const response = await fetch(url, {
            cache: 'no-store',
            ...options,
            headers: {
                ...(options.headers || {})
            }
        });

        const result = await response.json();

        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'The request could not be completed.');
        }

        return result;
    }

    async function loadManagementData() {
        tableBody.innerHTML =
            '<tr><td colspan="5">Loading supplier offers...</td></tr>';

        try {
            const data = await requestJSON(
                'get_supplier_product_management.php?t=' + Date.now()
            );

            managementData = {
                products: data.products || [],
                suppliers: data.suppliers || [],
                offers: data.offers || []
            };

            renderDropdowns();
            renderOffers();
            setMessage('Supplier offers loaded.');
        } catch (error) {
            tableBody.innerHTML =
                '<tr><td colspan="5">Unable to load supplier offers.</td></tr>';

            setMessage(error.message, true);
        }
    }

    function renderDropdowns() {
        const previousProduct = productSelect.value;
        const previousSupplier = supplierSelect.value;

        productSelect.innerHTML =
            '<option value="">Select product</option>';

        managementData.products.forEach(product => {
            productSelect.add(new Option(
                product.ProductName,
                product.Product_ID
            ));
        });

        supplierSelect.innerHTML =
            '<option value="">Select supplier</option>';

        managementData.suppliers.forEach(supplier => {
            const label = supplier.ContactPerson
                ? `${supplier.CompanyName} — ${supplier.ContactPerson}`
                : supplier.CompanyName;

            supplierSelect.add(new Option(
                label,
                supplier.Supplier_ID
            ));
        });

        if ([...productSelect.options].some(
            option => option.value === previousProduct
        )) {
            productSelect.value = previousProduct;
        }

        if ([...supplierSelect.options].some(
            option => option.value === previousSupplier
        )) {
            supplierSelect.value = previousSupplier;
        }
    }

    function renderOffers() {
        if (!managementData.offers.length) {
            tableBody.innerHTML =
                '<tr><td colspan="5">No supplier offers found.</td></tr>';
            return;
        }

        tableBody.innerHTML = managementData.offers.map(offer => {
            const active = Number(offer.IsActive) === 1;

            return `
                <tr>
                    <td>${escapeHtml(offer.ProductName)}</td>
                    <td>
                        <strong>${escapeHtml(offer.CompanyName)}</strong>
                        ${
                            offer.ContactPerson
                                ? `<br><small>${escapeHtml(offer.ContactPerson)}</small>`
                                : ''
                        }
                    </td>
                    <td>
                        <input
                            type="number"
                            class="form-control"
                            min="0.01"
                            step="0.01"
                            value="${Number(offer.SupplierPrice).toFixed(2)}"
                            data-price-input="${Number(offer.ProductSupplier_ID)}"
                            aria-label="Purchase price for ${escapeHtml(offer.ProductName)} from ${escapeHtml(offer.CompanyName)}"
                        >
                    </td>
                    <td>
                        <span>${active ? 'Active' : 'Inactive'}</span>
                    </td>
                    <td>
                        <div class="supplier-offer-actions">
                            <button
                                type="button"
                                class="na-btn na-btn-update"
                                data-save-price="${Number(offer.ProductSupplier_ID)}">
                                Save Price
                            </button>

                            <button
                                type="button"
                                class="na-btn ${active ? 'btn-deactivate' : 'btn-activate'}"
                                data-toggle-offer="${Number(offer.ProductSupplier_ID)}"
                                data-active="${active ? 1 : 0}">
                                ${active ? 'Deactivate' : 'Activate'}
                            </button>

                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function saveAction(payload) {
        return requestJSON('save_supplier_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
    }

    async function refreshAfterSave(successMessage) {
        await loadManagementData();

        // Refresh the existing markup preview, if it exposes its reload function.
        if (typeof window.refreshPricingMarkup === 'function') {
            await window.refreshPricingMarkup();
        }

        setMessage(successMessage);
    }

    // form.addEventListener('submit', async event => {
    //     event.preventDefault();

    //     const productId = Number(productSelect.value);
    //     const supplierId = Number(supplierSelect.value);
    //     const supplierPrice = Number(priceInput.value);

    //     if (!productId || !supplierId) {
    //         setMessage('Select both a product and a supplier.', true);
    //         return;
    //     }

    //     if (!Number.isFinite(supplierPrice) || supplierPrice <= 0) {
    //         setMessage('Enter a purchase price greater than zero.', true);
    //         return;
    //     }

    //     const product = managementData.products.find(
    //         item => Number(item.Product_ID) === productId
    //     );

    //     const supplier = managementData.suppliers.find(
    //         item => Number(item.Supplier_ID) === supplierId
    //     );

    //     if (!confirm(
    //         'Add this supplier offer?\n\n' +
    //         `Product: ${product?.ProductName || ''}\n` +
    //         `Supplier: ${supplier?.CompanyName || ''}\n` +
    //         `Purchase price: ${money(supplierPrice)}\n\n` +
    //         'This will not change the product selling price.'
    //     )) {
    //         return;
    //     }

    //     const submitButton = form.querySelector('[type="submit"]');
    //     submitButton.disabled = true;

    //     try {
    //         await saveAction({
    //             action: 'add',
    //             productId,
    //             supplierId,
    //             supplierPrice
    //         });

    //         form.reset();

    //         await refreshAfterSave(
    //             'Supplier offer added. The selling price was not changed.'
    //         );
    //     } catch (error) {
    //         setMessage(error.message, true);
    //     } finally {
    //         submitButton.disabled = false;
    //     }
    // });

    // tableBody.addEventListener('click', async event => {
    //     const saveButton = event.target.closest('[data-save-price]');
    //     const toggleButton = event.target.closest('[data-toggle-offer]');

    //     if (!saveButton && !toggleButton) return;

    //     const button = saveButton || toggleButton;
    //     button.disabled = true;

    //     try {
    //         if (saveButton) {
    //             const offerId = Number(saveButton.dataset.savePrice);
    //             const input = tableBody.querySelector(
    //                 `[data-price-input="${offerId}"]`
    //             );
    //             const supplierPrice = Number(input?.value);

    //             if (!Number.isFinite(supplierPrice) || supplierPrice <= 0) {
    //                 throw new Error('Enter a purchase price greater than zero.');
    //             }

    //             const offer = managementData.offers.find(
    //                 item => Number(item.ProductSupplier_ID) === offerId
    //             );

    //             if (!offer) {
    //                 throw new Error('Supplier offer not found.');
    //             }

    //             if (!confirm(
    //                 'Update this supplier purchase price?\n\n' +
    //                 `Product: ${offer.ProductName}\n` +
    //                 `Supplier: ${offer.CompanyName}\n` +
    //                 `Previous cost: ${money(offer.SupplierPrice)}\n` +
    //                 `New cost: ${money(supplierPrice)}\n\n` +
    //                 'The product selling price and POS price will remain unchanged.'
    //             )) {
    //                 return;
    //             }

    //             await saveAction({
    //                 action: 'update_price',
    //                 offerId,
    //                 supplierPrice
    //             });

    //             await refreshAfterSave(
    //                 'Purchase price updated. The selling price was not changed.'
    //             );
    //         }

    //         if (toggleButton) {
    //             const offerId = Number(toggleButton.dataset.toggleOffer);
    //             const currentActive = Number(toggleButton.dataset.active);
    //             const newActive = currentActive === 1 ? 0 : 1;

    //             const offer = managementData.offers.find(
    //                 item => Number(item.ProductSupplier_ID) === offerId
    //             );

    //             if (!offer) {
    //                 throw new Error('Supplier offer not found.');
    //             }

    //             if (!confirm(
    //                 `${newActive ? 'Activate' : 'Deactivate'} this supplier offer?\n\n` +
    //                 `Product: ${offer.ProductName}\n` +
    //                 `Supplier: ${offer.CompanyName}`
    //             )) {
    //                 return;
    //             }

    //             await saveAction({
    //                 action: 'toggle_active',
    //                 offerId,
    //                 isActive: newActive
    //             });

    //             await refreshAfterSave(
    //                 newActive
    //                     ? 'Supplier offer activated.'
    //                     : 'Supplier offer deactivated.'
    //             );
    //         }
    //     } catch (error) {
    //         setMessage(error.message, true);
    //     } finally {
    //         button.disabled = false;
    //     }
    // });




    form.addEventListener('submit', event => {
        event.preventDefault();

        const productId = Number(productSelect.value);
        const supplierId = Number(supplierSelect.value);
        const supplierPrice = Number(priceInput.value);

        if (!productId || !supplierId) {
            setMessage('Select both a product and a supplier.', true);
            return;
        }

        if (!Number.isFinite(supplierPrice) || supplierPrice <= 0) {
            setMessage('Enter a purchase price greater than zero.', true);
            return;
        }

        const product = managementData.products.find(
            item => Number(item.Product_ID) === productId
        );

        const supplier = managementData.suppliers.find(
            item => Number(item.Supplier_ID) === supplierId
        );

        if (!product || !supplier) {
            setMessage('The selected product or supplier was not found.', true);
            return;
        }

        const summaryHtml = `
            <p>Are you sure you want to add this supplier offer?</p>
            <p><strong>Product:</strong> ${escapeHtml(product.ProductName)}</p>
            <p><strong>Supplier:</strong> ${escapeHtml(supplier.CompanyName)}</p>
            <p><strong>Purchase Price:</strong> ${money(supplierPrice)}</p>
            <hr>
            <p>The product selling price and POS price will remain unchanged.</p>
        `;

        openConfirmation(summaryHtml, async () => {
            const submitButton = form.querySelector('[type="submit"]');
            if (submitButton.disabled) return;

            submitButton.disabled = true;

            try {
                await saveAction({
                    action: 'add',
                    productId,
                    supplierId,
                    supplierPrice
                });

                form.reset();

                await refreshAfterSave(
                    'Supplier offer added. The selling price was not changed.'
                );
            } catch (error) {
                setMessage(error.message, true);
            } finally {
                submitButton.disabled = false;
            }
        });
    });

    tableBody.addEventListener('click', event => {
        const saveButton = event.target.closest('[data-save-price]');
        const toggleButton = event.target.closest('[data-toggle-offer]');

        if (!saveButton && !toggleButton) return;

        if (saveButton) {
            const offerId = Number(saveButton.dataset.savePrice);
            const input = tableBody.querySelector(
                `[data-price-input="${offerId}"]`
            );
            const supplierPrice = Number(input?.value);

            if (!Number.isFinite(supplierPrice) || supplierPrice <= 0) {
                setMessage('Enter a purchase price greater than zero.', true);
                return;
            }

            const offer = managementData.offers.find(
                item => Number(item.ProductSupplier_ID) === offerId
            );

            if (!offer) {
                setMessage('Supplier offer not found.', true);
                return;
            }

            const previousPrice = Number(offer.SupplierPrice);

            if (Math.abs(supplierPrice - previousPrice) < 0.000001) {
                setMessage('The purchase price has not changed.', true);
                return;
            }

            const summaryHtml = `
                <p>Are you sure you want to update this supplier's purchase price?</p>
                <p><strong>Product:</strong> ${escapeHtml(offer.ProductName)}</p>
                <p><strong>Supplier:</strong> ${escapeHtml(offer.CompanyName)}</p>
                <p><strong>Previous Cost:</strong> ${money(previousPrice)}</p>
                <p><strong>New Cost:</strong> ${money(supplierPrice)}</p>
                <hr>
                <p>The product selling price and POS price will remain unchanged.</p>
            `;

            openConfirmation(summaryHtml, async () => {
                if (saveButton.disabled) return;
                saveButton.disabled = true;

                try {
                    await saveAction({
                        action: 'update_price',
                        offerId,
                        supplierPrice
                    });

                    await refreshAfterSave(
                        'Purchase price updated. The selling price was not changed.'
                    );
                } catch (error) {
                    setMessage(error.message, true);
                } finally {
                    saveButton.disabled = false;
                }
            });

            return;
        }

        if (toggleButton) {
            const offerId = Number(toggleButton.dataset.toggleOffer);
            const currentActive = Number(toggleButton.dataset.active);
            const newActive = currentActive === 1 ? 0 : 1;

            const offer = managementData.offers.find(
                item => Number(item.ProductSupplier_ID) === offerId
            );

            if (!offer) {
                setMessage('Supplier offer not found.', true);
                return;
            }

            const actionName = newActive ? 'Activate' : 'Deactivate';

            const summaryHtml = `
                <p>Are you sure you want to ${actionName.toLowerCase()} this supplier offer?</p>
                <p><strong>Product:</strong> ${escapeHtml(offer.ProductName)}</p>
                <p><strong>Supplier:</strong> ${escapeHtml(offer.CompanyName)}</p>
                <p><strong>Current Status:</strong> ${currentActive ? 'Active' : 'Inactive'}</p>
                <p><strong>New Status:</strong> ${newActive ? 'Active' : 'Inactive'}</p>
            `;

            openConfirmation(summaryHtml, async () => {
                if (toggleButton.disabled) return;
                toggleButton.disabled = true;

                try {
                    await saveAction({
                        action: 'toggle_active',
                        offerId,
                        isActive: newActive
                    });

                    await refreshAfterSave(
                        newActive
                            ? 'Supplier offer activated.'
                            : 'Supplier offer deactivated.'
                    );
                } catch (error) {
                    setMessage(error.message, true);
                } finally {
                    toggleButton.disabled = false;
                }
            });
        }
    });






    

    // Start on the existing pricing rules view.
    showTab('rules');
});