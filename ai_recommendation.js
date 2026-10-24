
let aiRecommendationItems = [];
let aiSuppliers = [];

/* =========================================================
   HELPERS
========================================================= */

function formatNumber(value) {
    return Number(value || 0).toLocaleString("en-US", {
        maximumFractionDigits: 2
    });
}

function formatCurrency(value) {
    return Number(value || 0).toLocaleString("en-PH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function escapeHTML(value) {
    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function getLocalDateString() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    const day = String(now.getDate()).padStart(2, "0");

    return `${year}-${month}-${day}`;
}

function getItemTotal(item) {
    return (
        (Number(item.orderedQuantity) || 0) *
        (Number(item.unitCost) || 0)
    );
}

function getAIGrandTotal() {
    return aiRecommendationItems.reduce(
        (total, item) => total + getItemTotal(item),
        0
    );
}

function getSupplierName(item) {
    return item.supplierName || "No Supplier";
}

function getSupplierAddress(item) {
    return item.supplierAddress ||
        item.supplierLocation ||
        "";
}

function getSupplierCity(item) {
    return item.supplierCity || "";
}

function getSupplierOffers(item) {
    return Array.isArray(item.supplierOffers)
        ? item.supplierOffers
        : [];
}

function findSupplierOffer(item, productSupplierId) {
    return getSupplierOffers(item).find(
        offer =>
            Number(offer.productSupplierId) ===
            Number(productSupplierId)
    );
}

function groupItemsBySupplier(items) {
    const grouped = {};

    items.forEach(item => {
        const supplierId = String(item.supplierId ?? "none");

        if (!grouped[supplierId]) {
            grouped[supplierId] = {
                supplierId,
                supplierName: getSupplierName(item),
                supplierAddress: getSupplierAddress(item),
                supplierCity: getSupplierCity(item),
                supplierPhone: item.supplierPhone || "",
                supplierEmail: item.supplierEmail || "",
                expectedReceiveDate: item.expectedReceiveDate || "",
                items: []
            };
        }

        grouped[supplierId].items.push(item);

        if (!grouped[supplierId].expectedReceiveDate) {
            grouped[supplierId].expectedReceiveDate =
                item.expectedReceiveDate || "";
        }
    });

    return Object.values(grouped);
}

function updateItemSupplier(item, offer) {
    item.productSupplierId = Number(offer.productSupplierId);
    item.productId = Number(offer.productId);
    item.supplierId = Number(offer.supplierId);
    item.supplierName = offer.supplierName || "No Supplier";
    item.supplierAddress = offer.supplierAddress || "";
    item.supplierCity = offer.supplierCity || "";
    item.supplierLocation = offer.supplierLocation ||
        [
            offer.supplierAddress,
            offer.supplierCity
        ].filter(Boolean).join(", ");

    item.supplierPhone = offer.supplierPhone || "";
    item.supplierEmail = offer.supplierEmail || "";
    item.unitCost = Number(offer.unitCost) || 0;
}

/* =========================================================
   LOAD AI RECOMMENDATIONS + PRODUCT-SPECIFIC OFFERS
========================================================= */

async function openAIRecommendationModal() {
    const modal = document.getElementById("aiRecommendationModal");
    const container = document.getElementById("aiSupplierSections");

    if (!modal || !container) {
        console.error("AI Recommendation modal elements not found.");
        return;
    }

    modal.style.display = "flex";
    container.innerHTML = "<p>Loading AI recommendations...</p>";

    try {
        const response = await fetch(
            "get_ai_recommendations.php?t=" + Date.now()
        );

        if (!response.ok) {
            throw new Error(
                `AI recommendation request failed: ${response.status}`
            );
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(
                result.message ||
                result.error ||
                "Failed to load AI recommendations."
            );
        }

        aiRecommendationItems = Array.isArray(result.data)
            ? result.data
            : [];

        if (aiRecommendationItems.length === 0) {
            container.innerHTML = `
                <p style="padding:30px;text-align:center;color:#666;">
                    No AI recommendations available.
                </p>
            `;
            return;
        }

        // Retrieve the active supplier offers for every recommended product.
        const uniqueProductIds = [
            ...new Set(
                aiRecommendationItems.map(item => Number(item.productId))
            )
        ];

        const offerResults = await Promise.all(
            uniqueProductIds.map(async productId => {
                const offerResponse = await fetch(
                    "get_product_supplier_offers.php?product_id=" +
                    encodeURIComponent(productId) +
                    "&t=" + Date.now()
                );

                if (!offerResponse.ok) {
                    throw new Error(
                        `Could not load supplier offers for Product ID ${productId}.`
                    );
                }

                const offerResult = await offerResponse.json();

                if (!offerResult.success) {
                    throw new Error(
                        offerResult.message ||
                        `Could not load supplier offers for Product ID ${productId}.`
                    );
                }

                return {
                    productId,
                    offers: Array.isArray(offerResult.data)
                        ? offerResult.data
                        : []
                };
            })
        );

        const offersByProduct = new Map(
            offerResults.map(result => [
                result.productId,
                result.offers
            ])
        );

        // Match each recommendation to its real active supplier offer.
        for (const item of aiRecommendationItems) {
            const offers = offersByProduct.get(Number(item.productId)) || [];

            if (offers.length === 0) {
                throw new Error(
                    `No active supplier offer exists for ${item.productName}. ` +
                    "Please add or activate a supplier offer before ordering."
                );
            }

            item.supplierOffers = offers;

            let selectedOffer = findSupplierOffer(
                item,
                item.productSupplierId
            );

            // Fallback: retain the recommended supplier if it has an
            // active offer; otherwise use the lowest-priced active offer.
            if (!selectedOffer) {
                selectedOffer = offers.find(
                    offer =>
                        Number(offer.supplierId) ===
                        Number(item.supplierId)
                );
            }

            if (!selectedOffer) {
                selectedOffer = [...offers].sort(
                    (a, b) => Number(a.unitCost) - Number(b.unitCost)
                )[0];
            }

            updateItemSupplier(item, selectedOffer);

            const packSize = Number(item.packSize) || 1;
            const quantity = Number(item.orderedQuantity) || 0;

            if (quantity <= 0 || quantity % packSize !== 0) {
                item.orderedQuantity =
                    Math.max(1, Math.ceil(quantity / packSize)) * packSize;
            }
        }

        renderAIRecommendations();

    } catch (error) {
        console.error("AI Recommendation Error:", error);

        container.innerHTML = `
            <p style="color:red;padding:20px;">
                Failed to load AI recommendations.<br>
                ${escapeHTML(error.message)}
            </p>
        `;
    }
}

/* =========================================================
   RENDER RECOMMENDATIONS
========================================================= */

function renderAIRecommendations() {
    const container = document.getElementById("aiSupplierSections");
    if (!container) return;

    const groups = groupItemsBySupplier(aiRecommendationItems);
    let html = "";

    groups.forEach(group => {
        html += `
            <div class="ai-supplier-group"
                 style="margin-bottom:25px;">

                <div style="
                    padding:15px;
                    margin-bottom:15px;
                    border:1px solid #ddd;
                    border-radius:8px;
                    background:#fff;
                    color:#222;
                ">
                    <h3 style="
                        margin:0 0 15px;
                        padding:10px;
                        background:rgb(255,203,203);
                        text-transform:uppercase;
                        border-radius:5px;
                    ">
                        ${escapeHTML(group.supplierName)}
                    </h3>

                    <div style="
                        display:grid;
                        grid-template-columns:repeat(2,minmax(0,1fr));
                        gap:15px 25px;
                    ">
                        <div>
                            <strong>Address</strong>
                            <div style="margin-top:5px;overflow-wrap:anywhere;">
                                ${escapeHTML(group.supplierAddress || "No address provided")}
                            </div>
                        </div>

                        <div>
                            <strong>City</strong>
                            <div style="margin-top:5px;overflow-wrap:anywhere;">
                                ${escapeHTML(group.supplierCity || "No city provided")}
                            </div>
                        </div>

                        <div>
                            <strong>Phone Number</strong>
                            <div style="margin-top:5px;color:rgb(13,99,46);overflow-wrap:anywhere;">
                                ${escapeHTML(group.supplierPhone || "No phone number")}
                            </div>
                        </div>

                        <div>
                            <strong>Email</strong>
                            <div style="margin-top:5px;color:blue;overflow-wrap:anywhere;">
                                ${escapeHTML(group.supplierEmail || "No email")}
                            </div>
                        </div>

                        <div style="grid-column:1/-1;">
                            <label style="color:green;">
                                <strong>Expected Receive Date</strong>
                            </label>

                            <div style="margin-top:5px;">
                                <input
                                    type="date"
                                    class="ai-expected-date"
                                    data-supplier-id="${escapeHTML(group.supplierId)}"
                                    value="${escapeHTML(group.expectedReceiveDate)}"
                                    min="${getLocalDateString()}"
                                    required
                                    style="
                                        height:35px;
                                        max-width:250px;
                                        width:100%;
                                        font-weight:bold;
                                    "
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div style="overflow-x:auto;">
                    <table class="ai-recommendation-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Current Stock</th>
                                <th>Predicted Demand</th>
                                <th>AI Recommendation</th>
                                <th>Order Quantity</th>
                                <th>Unit Cost</th>
                                <th>Estimated Total</th>
                                <th>Supplier</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        group.items.forEach(item => {
            const index = aiRecommendationItems.indexOf(item);
            const packSize = Number(item.packSize) || 1;
            const quantity = Number(item.orderedQuantity) || packSize;
            const unitCost = Number(item.unitCost) || 0;
            const itemTotal = getItemTotal(item);

            let supplierOptions = "";

            getSupplierOffers(item).forEach(offer => {
                const selected =
                    Number(offer.productSupplierId) ===
                    Number(item.productSupplierId)
                        ? "selected"
                        : "";

                supplierOptions += `
                    <option
                        value="${Number(offer.productSupplierId)}"
                        ${selected}
                    >   
                        ₱${formatCurrency(offer.unitCost)}
                        — ${escapeHTML(offer.supplierName)}
                        
                        ${offer.supplierCity
                            ? " (" + escapeHTML(offer.supplierCity) + ")"
                            : ""}
                    </option>
                `;
            });

            html += `
                <tr data-index="${index}">
                    <td>
                        <strong>${escapeHTML(item.productName)}</strong>
                        <br>
                        <small>ID: ${Number(item.productId)}</small>
                    </td>

                    <td>${formatNumber(item.currentStock)}</td>
                    <td>${formatNumber(item.predictedDemand)}</td>

                    <td>
                        ${formatNumber(item.recommendedQuantity)}
                        <br>
                        <small>Pack Size: ${packSize}</small>
                    </td>

                    <td>
                        <input
                            type="number"
                            class="ai-order-quantity"
                            data-index="${index}"
                            value="${quantity}"
                            min="${packSize}"
                            step="${packSize}"
                            required
                            style="width:100px;"
                        >
                    </td>

                    <td class="ai-unit-cost">
                        ₱${formatCurrency(unitCost)}
                    </td>

                    <td class="ai-row-total">
                        ₱${formatCurrency(itemTotal)}
                    </td>

                    <td>
                        <select
                            class="ai-supplier-select"
                            data-index="${index}"
                        >
                            ${supplierOptions}
                        </select>
                    </td>
                </tr>
            `;
        });

        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    });

    // html += `
    //     <div style="
    //         display:flex;
    //         justify-content:flex-end;
    //         align-items:center;
    //         gap:15px;
    //         padding:18px;
    //         margin:15px 0;
    //         border-top:2px solid #ddd;
    //         font-size:18px;
    //         font-weight:bold;
    //     ">
    //         <span>Estimated Grand Total:</span>
    //         <span id="aiOrderGrandTotal" style="color:#146c43;">
    //             ₱${formatCurrency(getAIGrandTotal())}
    //         </span>
    //     </div>
    // `;






    

    container.innerHTML = html;

    updateAIGrandTotal();

    /* Order quantity changes */
    container.querySelectorAll(".ai-order-quantity").forEach(input => {
        input.addEventListener("input", function () {
            const index = Number(this.dataset.index);
            const item = aiRecommendationItems[index];

            if (!item) return;

            const packSize = Number(item.packSize) || 1;
            const quantity = Number(this.value);

            if (
                !Number.isInteger(quantity) ||
                quantity < packSize ||
                quantity % packSize !== 0
            ) {
                this.setCustomValidity(
                    `Quantity must be a whole-number multiple of ${packSize}.`
                );
                this.style.borderColor = "red";
                return;
            }

            this.setCustomValidity("");
            this.style.borderColor = "";

            item.orderedQuantity = quantity;

            const row = this.closest("tr");
            const totalCell = row?.querySelector(".ai-row-total");

            if (totalCell) {
                totalCell.textContent =
                    `₱${formatCurrency(getItemTotal(item))}`;
            }

            updateAIGrandTotal();
        });
    });

    /* Product-specific supplier offer changes */
    container.querySelectorAll(".ai-supplier-select").forEach(select => {
        select.addEventListener("change", function () {
            const index = Number(this.dataset.index);
            const item = aiRecommendationItems[index];

            if (!item) return;

            const offer = findSupplierOffer(item, this.value);

            if (!offer) {
                alert("The selected supplier offer is no longer available.");
                renderAIRecommendations();
                return;
            }

            // Update supplier, offer ID, contact details, and purchase cost.
            updateItemSupplier(item, offer);

            // Keep the existing expected date if one was selected.
            renderAIRecommendations();
        });
    });

    /* One expected date per supplier group */
    container.querySelectorAll(".ai-expected-date").forEach(input => {
        input.addEventListener("change", function () {
            const supplierId = String(this.dataset.supplierId);
            const selectedDate = this.value;

            aiRecommendationItems.forEach(item => {
                if (String(item.supplierId) === supplierId) {
                    item.expectedReceiveDate = selectedDate;
                }
            });
        });
    });
}

// function updateAIGrandTotal() {
//     const totalElement = document.getElementById("aiOrderGrandTotal");

//     if (totalElement) {
//         totalElement.textContent =
//             `₱${formatCurrency(getAIGrandTotal())}`;
//     }
// }

function updateAIGrandTotal() {
    const totalElement = document.getElementById("aiOrderGrandTotal");

    if (!totalElement) {
        console.error("AI Grand Total element not found.");
        return;
    }

    const grandTotal = (aiRecommendationItems || []).reduce(
        (sum, item) => sum + Number(getItemTotal(item) || 0),
        0
    );

    totalElement.textContent = `₱${formatCurrency(grandTotal)}`;
}




/* =========================================================
   CLOSE RECOMMENDATION MODAL
========================================================= */

function closeAIRecommendationModal() {
    const modal = document.getElementById("aiRecommendationModal");

    if (modal) {
        modal.style.display = "none";
    }
}

/* =========================================================
   CONFIRMATION MODAL
========================================================= */

function openAIOrderConfirmation() {
    if (aiRecommendationItems.length === 0) {
        alert("There are no AI recommendations to confirm.");
        return;
    }

    // Validate every quantity and expected receive date.
    for (const item of aiRecommendationItems) {
        const quantity = Number(item.orderedQuantity) || 0;
        const packSize = Number(item.packSize) || 1;

        if (
            !Number.isInteger(quantity) ||
            quantity < packSize ||
            quantity % packSize !== 0
        ) {
            alert(
                `Invalid order quantity for ${item.productName}.\n\n` +
                `Quantity must be a multiple of ${packSize}.`
            );
            return;
        }

        if (!item.expectedReceiveDate) {
            alert(
                `Please select an Expected Receive Date for supplier:\n\n` +
                `${getSupplierName(item)}`
            );
            return;
        }

        if (item.expectedReceiveDate < getLocalDateString()) {
            alert(
                `The Expected Receive Date for ${getSupplierName(item)} ` +
                "cannot be in the past."
            );
            return;
        }

        const selectedOffer = findSupplierOffer(
            item,
            item.productSupplierId
        );

        if (
            !selectedOffer ||
            Number(selectedOffer.supplierId) !== Number(item.supplierId)
        ) {
            alert(
                `The supplier offer for ${item.productName} is invalid. ` +
                "Please reopen the AI recommendations and try again."
            );
            return;
        }
    }

    const confirmationModal =
        document.getElementById("aiOrderConfirmationModal");

    const confirmationContent =
        document.getElementById("aiOrderConfirmationContent");

    if (!confirmationModal || !confirmationContent) {
        console.error("AI confirmation modal elements not found.");
        return;
    }

    const groups = groupItemsBySupplier(aiRecommendationItems);
    let html = "";

    groups.forEach(group => {
        let groupTotal = 0;

        html += `
            <div style="
                margin:15px;
                padding:15px;
                border:1px solid #ddd;
                border-radius:8px;
                background:#fff;
                color:#222;
            ">
                <h3 style="
                    margin-top:0;
                    margin-bottom:15px;
                    text-transform:uppercase;
                    background-color:rgb(255,203,203);
                    padding:12px;
                    border-radius:5px;
                ">
                    ${escapeHTML(group.supplierName)}
                </h3>

                <div style="
                    display:grid;
                    grid-template-columns:repeat(2,minmax(0,1fr));
                    gap:15px 25px;
                    margin-bottom:15px;
                ">
                    <div>
                        <strong>Address</strong>
                        <div style="margin-top:5px;overflow-wrap:anywhere;">
                            ${escapeHTML(group.supplierAddress || "No address provided")}
                        </div>
                    </div>

                    <div>
                        <strong>City</strong>
                        <div style="margin-top:5px;overflow-wrap:anywhere;">
                            ${escapeHTML(group.supplierCity || "No city provided")}
                        </div>
                    </div>

                    <div>
                        <strong>Phone Number</strong>
                        <div style="margin-top:5px;color:rgb(13,99,46);overflow-wrap:anywhere;">
                            ${escapeHTML(group.supplierPhone || "No phone number")}
                        </div>
                    </div>

                    <div>
                        <strong>Email</strong>
                        <div style="margin-top:5px;color:blue;overflow-wrap:anywhere;">
                            ${escapeHTML(group.supplierEmail || "No email")}
                        </div>
                    </div>

                    <div style="grid-column:1/-1;">
                        <strong>Expected Receive Date</strong>
                        <div style="margin-top:5px;font-weight:bold;">
                            ${escapeHTML(group.expectedReceiveDate || "No date selected")}
                        </div>
                    </div>
                </div>

                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd;">
                                    Product
                                </th>
                                <th style="text-align:center;padding:8px;border-bottom:1px solid #ddd;">
                                    Order Qty
                                </th>
                                <th style="text-align:center;padding:8px;border-bottom:1px solid #ddd;">
                                    Pack Size
                                </th>
                                <th style="text-align:right;padding:8px;border-bottom:1px solid #ddd;">
                                    Unit Cost
                                </th>
                                <th style="text-align:right;padding:8px;border-bottom:1px solid #ddd;">
                                    Estimated Total
                                </th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        group.items.forEach(item => {
            const quantity = Number(item.orderedQuantity) || 0;
            const packSize = Number(item.packSize) || 1;
            const itemTotal = getItemTotal(item);

            groupTotal += itemTotal;

            html += `
                <tr>
                    <td style="padding:8px;border-bottom:1px solid #eee;">
                        <strong>${escapeHTML(item.productName)}</strong>
                        <br>
                        <small>Product ID: ${Number(item.productId)}</small>
                    </td>

                    <td style="text-align:center;padding:8px;border-bottom:1px solid #eee;">
                        ${formatNumber(quantity)}
                    </td>

                    <td style="text-align:center;padding:8px;border-bottom:1px solid #eee;">
                        ${formatNumber(packSize)}
                    </td>

                    <td style="text-align:right;padding:8px;border-bottom:1px solid #eee;">
                        ₱${formatCurrency(item.unitCost)}
                    </td>

                    <td style="text-align:right;padding:8px;border-bottom:1px solid #eee;">
                        ₱${formatCurrency(itemTotal)}
                    </td>
                </tr>
            `;
        });

        html += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align:right;padding:12px;font-weight:bold;">
                                    Supplier Subtotal:
                                </td>
                                <td style="text-align:right;padding:12px;font-weight:bold;">
                                    ₱${formatCurrency(groupTotal)}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        `;
    });

    html += `
        <div style="
            margin:15px;
            padding:18px;
            display:flex;
            justify-content:space-between;
            gap:15px;
            border-top:2px solid #ddd;
            font-size:18px;
            font-weight:bold;
        ">
            <span>Estimated Grand Total:</span>
            <span style="color:#146c43;">
                ₱${formatCurrency(getAIGrandTotal())}
            </span>
        </div>
        <p style="margin:15px;color:#666;">
            Review the quantities, supplier offers, and estimated costs before placing the order.
        </p>
    `;

    confirmationContent.innerHTML = html;
    confirmationModal.style.display = "flex";
}

/* =========================================================
   SAVE DIRECTLY TO RESTOCK
========================================================= */

async function confirmAIOrder() {
    if (aiRecommendationItems.length === 0) {
        alert("There are no AI recommendations to confirm.");
        return;
    }

    // Final client-side checks. The PHP endpoint validates again.
    for (const item of aiRecommendationItems) {
        const quantity = Number(item.orderedQuantity) || 0;
        const packSize = Number(item.packSize) || 1;

        if (
            !Number.isInteger(quantity) ||
            quantity < packSize ||
            quantity % packSize !== 0
        ) {
            alert(
                `Invalid order quantity for ${item.productName}.\n\n` +
                `Quantity must be a multiple of ${packSize}.`
            );
            return;
        }

        if (!item.expectedReceiveDate) {
            alert(
                `Please select an Expected Receive Date for ${getSupplierName(item)}.`
            );
            return;
        }

        if (!item.productSupplierId || !item.supplierId) {
            alert(
                `A valid supplier offer is required for ${item.productName}.`
            );
            return;
        }
    }

    const confirmButton = document.getElementById(
        "aiConfirmationConfirmBtn"
    );

    if (confirmButton) {
        confirmButton.disabled = true;
        confirmButton.textContent = "Placing Order...";
    }

    try {
        const response = await fetch("save_ai_restock.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                items: aiRecommendationItems.map(item => ({
                    productId: Number(item.productId),
                    productSupplierId: Number(item.productSupplierId),
                    supplierId: Number(item.supplierId),
                    orderedQuantity: Number(item.orderedQuantity),
                    expectedReceiveDate: item.expectedReceiveDate
                }))
            })
        });

        const rawResponse = await response.text();
        console.log("SAVE AI RESTOCK RESPONSE:", rawResponse);

        let result;

        try {
            result = JSON.parse(rawResponse);
        } catch (error) {
            throw new Error("The server returned an invalid response.");
        }

        if (!response.ok || !result.success) {
            throw new Error(
                result.message ||
                result.error ||
                "Failed to save the AI order."
            );
        }

        // alert(result.message || "AI order saved successfully.");

        // const confirmationModal = document.getElementById(
        //     "aiOrderConfirmationModal"
        // );

        // if (confirmationModal) {
        //     confirmationModal.style.display = "none";
        // }

        // closeAIRecommendationModal();
        // location.reload();



        alert(result.message || "AI order saved successfully.");

        // Open the receipt prepared by save_ai_restock.php.
        window.location.href = "purchase_order.php";
        return;


    } catch (error) {
        console.error("Error confirming AI order:", error);

        alert(
            "Failed to place the AI order:\n\n" +
            error.message
        );

    } finally {
        if (confirmButton) {
            confirmButton.disabled = false;
            confirmButton.textContent = "Confirm & Place Order";
        }
    }
}

/* =========================================================
   BUTTON EVENTS
========================================================= */

document.addEventListener("DOMContentLoaded", function () {
    const closeButton = document.getElementById("aiOrderCloseBtn");
    const cancelButton = document.getElementById("aiOrderCancelBtn");
    const confirmButton = document.getElementById("aiOrderConfirmBtn");

    const confirmationCloseButton =
        document.getElementById("aiConfirmationCloseBtn");

    const confirmationCancelButton =
        document.getElementById("aiConfirmationCancelBtn");

    const confirmationConfirmButton =
        document.getElementById("aiConfirmationConfirmBtn");

    if (confirmationCloseButton) {
        confirmationCloseButton.addEventListener("click", function () {
            const modal = document.getElementById(
                "aiOrderConfirmationModal"
            );

            if (modal) modal.style.display = "none";
        });
    }

    if (confirmationCancelButton) {
        confirmationCancelButton.addEventListener("click", function () {
            const modal = document.getElementById(
                "aiOrderConfirmationModal"
            );

            if (modal) modal.style.display = "none";
        });
    }

    if (confirmationConfirmButton) {
        confirmationConfirmButton.addEventListener(
            "click",
            confirmAIOrder
        );
    }

    if (closeButton) {
        closeButton.addEventListener(
            "click",
            closeAIRecommendationModal
        );
    }

    if (cancelButton) {
        cancelButton.addEventListener(
            "click",
            closeAIRecommendationModal
        );
    }

    if (confirmButton) {
        confirmButton.addEventListener(
            "click",
            openAIOrderConfirmation
        );
    }
});
