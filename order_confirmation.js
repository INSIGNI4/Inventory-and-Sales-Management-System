document.addEventListener("DOMContentLoaded", () => {

    const restockForm =
        document.getElementById("restockForm");

    const confirmationModal =
        document.getElementById("orderConfirmationModal");

    // Allow the same modal to work outside the Create Order modal.
    document.body.appendChild(confirmationModal);

    const confirmationSummary =
        document.getElementById("order-confirmation-summary");



    const confirmationGrandTotal =
        document.getElementById("confirmationGrandTotal");

    const confirmationGrandTotalContainer =
        confirmationModal.querySelector(".order-confirmation-grand-total");

    const confirmationCurrency = new Intl.NumberFormat("en-PH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    function formatConfirmationCurrency(value) {
        return confirmationCurrency.format(Number(value) || 0);
    }


    const closeConfirmation =
        document.getElementById("closeOrderConfirmation");

    const backToOrderBtn =
        document.getElementById("backToOrderBtn");

    const confirmFinalOrderBtn =
        document.getElementById("confirmFinalOrderBtn");

    const confirmationTitle = confirmationModal.querySelector(
        ".order-confirmation-header h3"
    );
    const confirmationMessage = confirmationModal.querySelector(
        ".order-confirmation-message"
    );
    const confirmationWarning = confirmationModal.querySelector(
        ".order-confirmation-warning"
    );

    const originalConfirmation = {
        title: confirmationTitle.textContent,
        message: confirmationMessage.textContent,
        warning: confirmationWarning.textContent,
        button: confirmFinalOrderBtn.textContent.trim()
    };

    let pricingApplyCallback = null;

    window.openPricingConfirmation = function (summaryHtml, callback) {
        pricingApplyCallback = callback;


        if (confirmationGrandTotalContainer) {
            confirmationGrandTotalContainer.style.display = "none";
        }          

        confirmationTitle.textContent = "Review Selling Price";
        confirmationMessage.textContent =
            "Review the proposed selling price before applying it.";
        confirmationWarning.textContent =
            "This updates the shared selling price for this product across all suppliers. " +
            "Supplier costs and markup rules will not change.";

        confirmationSummary.innerHTML = summaryHtml;
        confirmFinalOrderBtn.textContent = "Apply Suggested Price";
        confirmFinalOrderBtn.disabled = false;

        confirmationModal.style.display = "flex";

      
    };

    function resetConfirmationModal() {
        pricingApplyCallback = null;



        if (confirmationGrandTotalContainer) {
            confirmationGrandTotalContainer.style.display = "flex";
        }


        confirmationTitle.textContent = originalConfirmation.title;
        confirmationMessage.textContent = originalConfirmation.message;
        confirmationWarning.textContent = originalConfirmation.warning;
        confirmFinalOrderBtn.textContent = originalConfirmation.button;
        confirmFinalOrderBtn.disabled = false;
    }        


    /*
     * Open confirmation modal
     */
    // restockForm.addEventListener("submit", function (event) {

    //     /*
    //      * Stop the form from submitting immediately.
    //      */
    //     event.preventDefault();

    //     /*
    //      * Build the confirmation summary.
    //      */
    //     buildOrderConfirmation();

    //     /*
    //      * Show confirmation modal.
    //      */
    //     confirmationModal.style.display = "flex";

    // });


    /*
     * Close confirmation modal
     */
    closeConfirmation.addEventListener("click", function () {

        confirmationModal.style.display = "none";

    });


    /*
     * Back button
     *
     * This closes the confirmation screen
     * and returns to the order form.
     */
    backToOrderBtn.addEventListener("click", function () {

        confirmationModal.style.display = "none";

    });


    /*
     * Close when clicking outside the confirmation box
     */
    confirmationModal.addEventListener("click", function (event) {

        if (event.target === confirmationModal) {

            confirmationModal.style.display = "none";

        }

    });


    // FOR CONFIRMATION IN MARK UP

    resetConfirmationModal();


    /*
     * Build the order summary
     */

    function buildOrderConfirmation() {
        confirmationSummary.innerHTML = "";

        if (confirmationGrandTotal) {
            confirmationGrandTotal.textContent = "₱0.00";
        }

        const rows = Array.from(
            document.querySelectorAll(
                "#restock-items-container .restock-item-row"
            )
        );

        const supplierGroups = {};

        rows.forEach(function (row) {
            const productID =
                row.querySelector(".restock-product-id").value;

            const productName =
                row.querySelector(".restock-product-name").value;

            const quantity =
                Number(row.querySelector(".restock-quantity").value || 0);

            const supplierSelect =
                row.querySelector(".restock-supplier");

            const supplierID = supplierSelect.value;

            if (!productID || !supplierID) {
                return;
            }

            const selectedOption =
                supplierSelect.options[supplierSelect.selectedIndex];

            if (!selectedOption) {
                return;
            }

            const supplierCompany =
                selectedOption.dataset.company || "Not provided";

            const supplierName =
                selectedOption.dataset.name || "Not provided";

            const supplierPhone =
                selectedOption.dataset.phone || "Not provided";

            const supplierEmail =
                selectedOption.dataset.email || "Not provided";

            const supplierLocation =
                selectedOption.dataset.location || "Not provided";

            const packSize =
                row.querySelector(".restock-pack-size").value;

            const unitCost =
                Number(selectedOption.dataset.unitCost || 0);

            const itemTotal =
                quantity > 0 ? quantity * unitCost : 0;

            const expectedDateInput =
                row.querySelector(".restock-expected-date");

            const expectedDate = expectedDateInput
                ? expectedDateInput.value
                : "";

            if (!supplierGroups[supplierID]) {
                supplierGroups[supplierID] = {
                    company: supplierCompany,
                    name: supplierName,
                    location: supplierLocation,
                    phone: supplierPhone,
                    email: supplierEmail,
                    expectedDate: expectedDate,
                    items: [],
                    subtotal: 0
                };
            } else if (
                expectedDate &&
                !supplierGroups[supplierID].expectedDate
            ) {
                supplierGroups[supplierID].expectedDate = expectedDate;
            }

            supplierGroups[supplierID].items.push({
                productName: productName,
                quantity: quantity,
                packSize: packSize,
                unitCost: unitCost,
                itemTotal: itemTotal
            });

            supplierGroups[supplierID].subtotal += itemTotal;
        });

        const supplierIDs = Object.keys(supplierGroups);

        if (supplierIDs.length === 0) {
            confirmationSummary.innerHTML = `
                <p>No valid order items were found.</p>
            `;
            return;
        }

        let grandTotal = 0;

        supplierIDs.forEach(function (supplierID) {
            const group = supplierGroups[supplierID];

            grandTotal += group.subtotal;

            const supplierDiv = document.createElement("div");
            supplierDiv.className = "confirmation-supplier-group";

            supplierDiv.innerHTML = `
                <div class="confirmation-supplier-info"
                    style="background-color: rgba(214, 208, 208, 0.92);">

                    <div class="supplier-info-row">
                        <div class="supplier-info-item">
                            <strong>Company Name: </strong>
                            <span style="font-weight: bolder;">
                                ${group.company}
                            </span>
                        </div>

                        <div class="supplier-info-item">
                            <strong>Contact Person: </strong>
                            <span style="font-weight: bolder;">
                                ${group.name}
                            </span>
                        </div>
                    </div>

                    <div class="supplier-info-row supplier-location-row">
                        <div class="supplier-info-item">
                            <strong>Location: </strong>
                            <span style="font-weight: bolder;">
                                ${group.location}
                            </span>
                        </div>
                    </div>

                    <div class="supplier-info-row">
                        <div class="supplier-info-item">
                            <strong>Phone Number: </strong>
                            <span style="font-weight: bolder; color: rgba(1, 46, 130, 0.92);">
                                📞 ${group.phone}
                            </span>
                        </div>

                        <div class="supplier-info-item">
                            <strong>Email: </strong>
                            <span style="font-weight: bolder; color: rgba(3, 95, 34, 0.92);">
                                ✉️ ${group.email}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="confirmation-supplier-expected-date"
                    style="margin-top: -20px">
                    <label style="font-weight: bolder; font-size: 15px;">
                        Expected Receive Date:
                    </label>

                    <strong style="font-weight: bolder;">
                        ${group.expectedDate || "Not selected"}
                    </strong>
                </div>

                <div class="confirmation-cost-header"
                    style="display: grid;
                            grid-template-columns: minmax(120px, 2fr) repeat(4, minmax(80px, 1fr));
                            gap: 10px;
                            padding: 10px;
                            font-weight: bold;
                            border-bottom: 1px solid #ccc;">
                    <span>Product</span>
                    <span>Quantity</span>
                    <span>Pack Size</span>
                    <span>Unit Cost</span>
                    <span>Item Total</span>
                </div>
            `;

            group.items.forEach(function (item) {
                const itemDiv = document.createElement("div");
                itemDiv.className = "confirmation-order-item";

                itemDiv.style.display = "grid";
                itemDiv.style.gridTemplateColumns =
                    "minmax(120px, 2fr) repeat(4, minmax(80px, 1fr))";
                itemDiv.style.gap = "10px";
                itemDiv.style.alignItems = "center";
                itemDiv.style.padding = "10px";
                itemDiv.style.borderBottom = "1px solid #eee";

                const productSpan = document.createElement("span");
                productSpan.className = "confirmation-product-name";
                productSpan.style.fontWeight = "bold";
                productSpan.textContent = item.productName;

                const quantitySpan = document.createElement("span");
                quantitySpan.textContent = item.quantity || "-";

                const packSizeSpan = document.createElement("span");
                packSizeSpan.textContent = item.packSize || "-";

                const unitCostSpan = document.createElement("span");
                unitCostSpan.textContent =
                    `₱${formatConfirmationCurrency(item.unitCost)}`;

                const itemTotalSpan = document.createElement("span");
                itemTotalSpan.style.fontWeight = "bold";
                itemTotalSpan.textContent =
                    `₱${formatConfirmationCurrency(item.itemTotal)}`;

                itemDiv.append(
                    productSpan,
                    quantitySpan,
                    packSizeSpan,
                    unitCostSpan,
                    itemTotalSpan
                );

                supplierDiv.appendChild(itemDiv);
            });

            const subtotalDiv = document.createElement("div");
            subtotalDiv.className = "confirmation-supplier-subtotal";

            subtotalDiv.style.display = "flex";
            subtotalDiv.style.justifyContent = "flex-end";
            subtotalDiv.style.gap = "12px";
            subtotalDiv.style.padding = "12px 10px";
            subtotalDiv.style.fontWeight = "bold";
            subtotalDiv.style.borderTop = "2px solid #ccc";

            subtotalDiv.innerHTML = `
                <span>Supplier Subtotal:</span>
                <span>₱${formatConfirmationCurrency(group.subtotal)}</span>
            `;

            supplierDiv.appendChild(subtotalDiv);
            confirmationSummary.appendChild(supplierDiv);
        });

        const totalProducts = supplierIDs.reduce(function (count, supplierID) {
            return count + supplierGroups[supplierID].items.length;
        }, 0);

        const totalDiv = document.createElement("div");
        totalDiv.className = "confirmation-total";
        totalDiv.style.padding = "12px 10px";
        totalDiv.style.fontWeight = "bold";

        totalDiv.textContent =
            `${totalProducts} product(s) from ${supplierIDs.length} supplier(s)`;

        confirmationSummary.appendChild(totalDiv);

        // Update the footer total beside Back and Confirm & Place Order.
        if (confirmationGrandTotal) {
            confirmationGrandTotal.textContent =
                `₱${formatConfirmationCurrency(grandTotal)}`;
        }
    }



    /*
     * Final confirmation
     *
     * This is where the form is finally submitted.
     */
    // confirmFinalOrderBtn.addEventListener(
    //     "click",
    //     function () {

    //         /*
    //          * Remove the submit listener temporarily
    //          * so the confirmation does not open again.
    //          */
    //         restockForm.removeEventListener(
    //             "submit",
    //             arguments.callee
    //         );

    //         /*
    //          * Submit the form normally.
    //          */
    //         restockForm.submit();

    //     }
    // );



    let finalSubmission = false;

    restockForm.addEventListener("submit", function (event) {

        if (finalSubmission) {
            return;
        }

        event.preventDefault();

        buildOrderConfirmation();

        confirmationModal.style.display = "flex";

    });


    // confirmFinalOrderBtn.addEventListener(
    //     "click",
    //     function () {

    //         finalSubmission = true;

    //         restockForm.submit();

    //     }
    // );    




    // confirmFinalOrderBtn.addEventListener("click", async function () {
    //     if (pricingApplyCallback) {
    //         try {
    //             confirmFinalOrderBtn.disabled = true;

    //             const success = await pricingApplyCallback();

    //             if (success) {
    //                 confirmationModal.style.display = "none";
    //                 resetConfirmationModal();
    //             } else {
    //                 confirmFinalOrderBtn.disabled = false;
    //             }
    //         } catch (error) {
    //             console.error(error);
    //             confirmFinalOrderBtn.disabled = false;
    //         }

    //         return;
    //     }

    //     // Keep the existing Create Order behavior.
    //     finalSubmission = true;
    //     restockForm.submit();
    // });
    







    confirmFinalOrderBtn.addEventListener("click", async function () {
        if (pricingApplyCallback) {
            const callback = pricingApplyCallback;

            // Close the modal immediately after confirmation.
            confirmationModal.style.display = "none";
            resetConfirmationModal();

            try {
                await callback();
            } catch (error) {
                console.error('Pricing confirmation action failed:', error);
            }

            return;
        }

        // Keep the existing Create Order behavior.
        finalSubmission = true;
        restockForm.submit();
    });







});