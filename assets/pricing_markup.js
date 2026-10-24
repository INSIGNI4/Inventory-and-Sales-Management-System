
document.addEventListener('DOMContentLoaded', () => {
    const section = document.getElementById('pricing-markup');
    if (!section) return;

    const message = document.getElementById('pricing-message');
    const generalInput = document.getElementById('general-markup');
    const categorySelect = document.getElementById('markup-category');
    const categoryInput = document.getElementById('category-markup');
    const productSelect = document.getElementById('markup-product');
    const productInput = document.getElementById('product-markup');
    const previewBody = document.getElementById('supplier-price-preview');
    const rulesSummary = document.getElementById('pricing-rules-summary');

    let pricingData = null;

    const money = value =>
        '₱' + Number(value).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[char]);
    }

    function setMessage(text, isError = false) {
        message.textContent = text;
        message.style.color = isError ? '#b00020' : '#236b35';
    }

    async function loadPricingData() {
        setMessage('Loading pricing rules...');

        try {
            const response = await fetch(
                'get_pricing_rules.php?t=' + Date.now(),
                { cache: 'no-store' }
            );

            const data = await response.json();

            if (!response.ok || data.status !== 'success') {
                throw new Error(data.message || 'Unable to load pricing rules.');
            }

            pricingData = data;
            generalInput.value = Number(data.generalMarkup).toFixed(2);

            categorySelect.innerHTML =
                '<option value="">Select category</option>';

            data.categories.forEach(category => {
                categorySelect.add(new Option(category, category));
            });

            // productSelect.innerHTML =
            //     '<option value="">Select product</option>';

            // data.products.forEach(product => {
            //     productSelect.add(new Option(
            //         product.ProductName,
            //         product.Product_ID
            //     ));
            // });



            const selectedProductId = productSelect.value;

            productSelect.innerHTML =
                '<option value="">Select product</option>';

            data.products.forEach(product => {
                productSelect.add(new Option(
                    product.ProductName,
                    product.Product_ID
                ));
            });

            productSelect.value = selectedProductId;



            renderRules();
            renderPreview();
            setMessage('Pricing information loaded successfully.');
        } catch (error) {
            setMessage(error.message, true);
        }
    }

    function renderRules() {
        if (!pricingData) return;

        let html = `
            <p><strong>General:</strong>
                ${Number(pricingData.generalMarkup).toFixed(2)}%</p>
            <h4>Category Rules</h4>
        `;

        const categories = Object.entries(pricingData.categoryRules || {});

        html += categories.length
            ? '<ul>' + categories.map(([name, markup]) =>
                `<li>${escapeHtml(name)}: ${Number(markup).toFixed(2)}%</li>`
              ).join('') + '</ul>'
            : '<p>No category-specific rules configured.</p>';

        html += '<h4>Product Rules</h4>';

        const products = Object.entries(pricingData.productRules || {});

        html += products.length
            ? '<ul>' + products.map(([id, markup]) => {
                const product = pricingData.products.find(
                    item => Number(item.Product_ID) === Number(id)
                );

                return `<li>${escapeHtml(product?.ProductName || 'Product ' + id)}:
                    ${Number(markup).toFixed(2)}%</li>`;
            }).join('') + '</ul>'
            : '<p>No product-specific rules configured.</p>';

        rulesSummary.innerHTML = html;
    }

    // function renderPreview() {
    //     if (!pricingData || !productSelect.value) {
    //         previewBody.innerHTML = `
    //             <tr><td colspan="5">Select a product to preview prices.</td></tr>
    //         `;
    //         return;
    //     }

    //     const product = pricingData.products.find(
    //         item => Number(item.Product_ID) === Number(productSelect.value)
    //     );

    //     if (!product) return;

    //     const offers = pricingData.supplierOffers.filter(offer =>
    //         Number(offer.Product_ID) === Number(product.Product_ID) &&
    //         Number(offer.IsActive) === 1
    //     );

    //     const markup = Number(product.EffectiveMarkup);
    //     const source = product.MarkupSource;

    //     productInput.value =
    //         product.ProductMarkup === null
    //             ? ''
    //             : Number(product.ProductMarkup).toFixed(2);

    //     if (!offers.length) {
    //         previewBody.innerHTML = `
    //             <tr><td colspan="5">
    //                 No active supplier offers exist for this product.
    //             </td></tr>
    //         `;
    //         return;
    //     }

    //     previewBody.innerHTML = offers.map(offer => {
    //         const cost = Number(offer.SupplierPrice);
    //         const suggested = Math.round((cost * (1 + markup / 100)) * 100) / 100;

    //         return `
    //             <tr>
    //                 <td>
    //                     <strong>${escapeHtml(offer.CompanyName)}</strong><br>
    //                     <small>${escapeHtml(offer.ContactPerson)}</small>
    //                 </td>
    //                 <td>${money(cost)}</td>
    //                 <td>${markup.toFixed(2)}%</td>
    //                 <td>${escapeHtml(source)}</td>
    //                 <td><strong>${money(suggested)}</strong></td>
    //             </tr>
    //         `;
    //     }).join('');
    // }



    function renderPreview() {
        if (!pricingData || !productSelect.value) {
            previewBody.innerHTML = `
                <tr>
                    <td colspan="7">Select a product to preview prices.</td>
                </tr>
            `;
            return;
        }

        const product = pricingData.products.find(
            item => Number(item.Product_ID) === Number(productSelect.value)
        );

        if (!product) return;

        const offers = pricingData.supplierOffers.filter(offer =>
            Number(offer.Product_ID) === Number(product.Product_ID) &&
            Number(offer.IsActive) === 1
        );

        const markup = Number(product.EffectiveMarkup);
        const source = product.MarkupSource;

        productInput.value =
            product.ProductMarkup === null
                ? ''
                : Number(product.ProductMarkup).toFixed(2);

        if (!offers.length) {
            previewBody.innerHTML = `
                <tr>
                    <td colspan="7">
                        No active supplier offers exist for this product.
                    </td>
                </tr>
            `;
            return;
        }

        const currentPrice = Number(product.StorePrice);

        previewBody.innerHTML = offers.map(offer => {
            const cost = Number(offer.SupplierPrice);
            const suggested =
                Math.round(cost * (1 + markup / 100) * 100) / 100;

            return `
                <tr>
                    <td>
                        <strong>${escapeHtml(offer.CompanyName)}</strong><br>
                        <small>${escapeHtml(offer.ContactPerson)}</small>
                    </td>
                    <td>${money(cost)}</td>
                    <td>${markup.toFixed(2)}%</td>
                    <td>${escapeHtml(source)}</td>
                    <td>${money(currentPrice)}</td>
                    <td><strong>${money(suggested)}</strong></td>
                    <td>
                        <button
                            type="button"
                            class="na-btn na-btn-add"
                            data-apply-offer="${Number(offer.ProductSupplier_ID)}"
                        >
                            Apply Suggested Price
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }




    // previewBody.addEventListener('click', async event => {
    //     const button = event.target.closest('[data-apply-offer]');

    //     if (!button) return;

    //     const offerId = Number(button.dataset.applyOffer);

    //     const product = pricingData?.products.find(
    //         item => Number(item.Product_ID) === Number(productSelect.value)
    //     );

    //     const offer = pricingData?.supplierOffers.find(
    //         item => Number(item.ProductSupplier_ID) === offerId
    //     );

    //     if (!product || !offer) {
    //         setMessage('Could not identify the selected product or supplier.', true);
    //         return;
    //     }

    //     const cost = Number(offer.SupplierPrice);
    //     const markup = Number(product.EffectiveMarkup);
    //     const suggested = Math.round(cost * (1 + markup / 100) * 100) / 100;
    //     const current = Number(product.StorePrice);

    //     const confirmed = confirm(
    //         `Apply this suggested selling price?\n\n` +
    //         `Product: ${product.ProductName}\n` +
    //         `Supplier: ${offer.CompanyName}\n` +
    //         `Supplier cost: ${money(cost)}\n` +
    //         `Markup: ${markup.toFixed(2)}% (${product.MarkupSource})\n` +
    //         `Current selling price: ${money(current)}\n` +
    //         `New selling price: ${money(suggested)}\n\n` +
    //         `This updates the product's shared StorePrice for all suppliers. ` +
    //         `It does not change supplier costs or markup rules.`
    //     );

    //     if (!confirmed) return;

    //     button.disabled = true;
    //     setMessage('Applying suggested selling price...');

    //     try {
    //         const response = await fetch('apply_suggested_price.php', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json'
    //             },
    //             body: JSON.stringify({
    //                 productSupplierId: offerId
    //             })
    //         });

    //         const result = await response.json();

    //         if (!response.ok || result.status !== 'success') {
    //             throw new Error(
    //                 result.message || 'Unable to apply suggested selling price.'
    //             );
    //         }

    //         await loadPricingData();

    //         setMessage(
    //             `Selling price updated: ${result.productName} is now ` +
    //             `${money(result.newPrice)}.`
    //         );

    //     } catch (error) {
    //         setMessage(error.message, true);
    //         button.disabled = false;
    //     }
    // });

        

    // REVISION FOR MARKUP


    previewBody.addEventListener('click', event => {
        const button = event.target.closest('[data-apply-offer]');
        if (!button) return;

        const offerId = Number(button.dataset.applyOffer);

        const product = pricingData?.products.find(
            item => Number(item.Product_ID) === Number(productSelect.value)
        );

        const offer = pricingData?.supplierOffers.find(
            item => Number(item.ProductSupplier_ID) === offerId
        );

        if (!product || !offer) {
            setMessage('Could not identify the selected product or supplier.', true);
            return;
        }

        const cost = Number(offer.SupplierPrice);
        const markup = Number(product.EffectiveMarkup);
        const suggested = Math.round(cost * (1 + markup / 100) * 100) / 100;
        const current = Number(product.StorePrice);

        const summaryHtml = `
            <div class="confirmation-supplier-group">
                <p><strong>Product:</strong> ${escapeHtml(product.ProductName)}</p>
                <p><strong>Supplier:</strong> ${escapeHtml(offer.CompanyName)}</p>
                <p><strong>Purchase Cost:</strong> ${money(cost)}</p>
                <p><strong>Effective Markup:</strong>
                    ${markup.toFixed(2)}% (${escapeHtml(product.MarkupSource)})
                </p>
                <p><strong>Current Selling Price:</strong> ${money(current)}</p>
                <p style="color: #236b35;">
                    <strong>New Selling Price: ${money(suggested)}</strong>
                </p>
            </div>
        `;

        if (typeof window.openPricingConfirmation !== 'function') {
            setMessage('Confirmation modal is not ready. Refresh the page.', true);
            return;
        }

        window.openPricingConfirmation(summaryHtml, async () => {
            setMessage('Applying suggested selling price...');

            try {
                const response = await fetch('apply_suggested_price.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        productSupplierId: offerId
                    })
                });

                const result = await response.json();

                if (!response.ok || result.status !== 'success') {
                    throw new Error(
                        result.message || 'Unable to apply suggested selling price.'
                    );
                }

                // await loadPricingData();

                // setMessage(
                //     `Selling price updated: ${result.productName} is now ` +
                //     `${money(result.newPrice)}.`
                // );

                // return true;


                // REVISION FOR CONFIRMATION RELOAD

                await loadPricingData();

                // Close the confirmation modal first.
                document.getElementById('orderConfirmationModal').style.display = 'none';

                // Reload the page after the successful update.
                window.location.reload();

                return true;

            } catch (error) {
                setMessage(error.message, true);
                return false;
            }
        });
    });



    async function saveRule(ruleType, markupPercent, extra = {}) {
        const markup = Number(markupPercent);

        if (!Number.isFinite(markup) || markup < 0 || markup > 10000) {
            setMessage('Enter a valid markup between 0 and 10000.', true);
            return;
        }

        const payload = {
            ruleType,
            markupPercent: markup,
            ...extra
        };

        setMessage('Saving markup rule...');

        try {
            const response = await fetch('save_pricing_rule.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (!response.ok || result.status !== 'success') {
                throw new Error(result.message || 'Unable to save markup rule.');
            }

            await loadPricingData();
            setMessage('Markup rule saved successfully.');
        } catch (error) {
            setMessage(error.message, true);
        }
    }

    document.getElementById('save-general-markup')
        .addEventListener('click', () => {
            if (!confirm('Save the general markup percentage?')) return;

            saveRule('general', generalInput.value);
        });


    document.getElementById('save-category-markup')
        .addEventListener('click', () => {
            if (!categorySelect.value) {
                setMessage('Select a product category first.', true);
                return;
            }

            if (!confirm('Save markup for this category?')) return;

            saveRule('category', categoryInput.value, {
                categoryName: categorySelect.value
            });
        });


    document.getElementById('save-product-markup')
        .addEventListener('click', () => {
            if (!productSelect.value) {
                setMessage('Select a product first.', true);
                return;
            }

            if (!confirm('Save markup for this product?')) return;

            saveRule('product', productInput.value, {
                productId: Number(productSelect.value)
            });
        });

    categorySelect.addEventListener('change', () => {
        if (!pricingData) return;

        const value = categorySelect.value;
        const rules = pricingData.categoryRules || {};

        categoryInput.value =
            Object.prototype.hasOwnProperty.call(rules, value)
                ? Number(rules[value]).toFixed(2)
                : '';
    });

    productSelect.addEventListener('change', renderPreview);

    // loadPricingData();



    window.refreshPricingMarkup = loadPricingData;

    loadPricingData();    
});
