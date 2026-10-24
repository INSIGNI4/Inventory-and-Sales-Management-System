document.addEventListener("DOMContentLoaded", function () {

    const container =
        document.getElementById("restock-items-container");

    const addItemButton =
        document.getElementById("add-restock-item-btn");



    const supplierSummaryContent =
        document.getElementById(
            "supplier-summary-content"
        );




    // =========================================
    // PRODUCT SEARCH ELEMENTS
    // =========================================

    const productSearchModal =
        document.getElementById("productSearchModal");

    const productSearchInput =
        document.getElementById("productSearchInput");

    const productSearchResults =
        document.getElementById("productSearchResults");

    const closeProductSearch =
        document.getElementById("closeProductSearch");


    // This stores the row that opened the search popup
    let activeRow = null;


    // Stores all products fetched from database
    let allProducts = [];


    let itemCount = 0;


    function formatOrderCurrency(value) {
        return Number(value || 0).toLocaleString("en-PH", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }



    // =========================================
    // LOAD PRODUCTS
    // =========================================

    async function loadProducts() {

        try {

            const response =
                await fetch("get_product.php");

            if (!response.ok) {
                throw new Error("Failed to load products");
            }

            allProducts =
                await response.json();

            console.log(
                "Products loaded:",
                allProducts
            );

        } catch (error) {

            console.error(
                "Failed to fetch products:",
                error
            );

        }

    }









    // =========================================
    // UPDATE SUPPLIER SUMMARY
    // =========================================

    // =========================================
    // UPDATE SUPPLIER SUMMARY
    // =========================================

    function updateSupplierSummary() {
        supplierSummaryContent.innerHTML = "";

        const rows = Array.from(
            container.querySelectorAll(".restock-item-row")
        );

        if (rows.length === 0) {
            supplierSummaryContent.innerHTML = `
                <p class="supplier-summary-empty">
                    No products added yet.
                </p>
            `;
            return;
        }

        const supplierGroups = {};

        rows.forEach(function (row) {
            const productID =
                row.querySelector(".restock-product-id").value;

            const productName =
                row.querySelector(".restock-product-name").value;

            const quantityInput =
                row.querySelector(".restock-quantity");

            const quantity = Number(quantityInput.value || 0);

            const supplierSelect =
                row.querySelector(".restock-supplier");

            const supplierID = supplierSelect.value;

            // Product hasn't been selected yet.
            if (!productID) return;

            // Supplier hasn't been selected yet.
            if (!supplierID) return;

            const selectedOption =
                supplierSelect.options[supplierSelect.selectedIndex];

            if (!selectedOption) return;

            const supplierName =
                selectedOption.dataset.name || "Not provided";

            const companyName =
                selectedOption.dataset.company || "Not provided";

            const supplierPhone =
                selectedOption.dataset.phone || "Not provided";

            const supplierEmail =
                selectedOption.dataset.email || "Not provided";

            const supplierLocation =
                selectedOption.dataset.location || "Not provided";

            // Purchase price from the selected supplier's offer.
            const unitCost = Number(
                selectedOption.dataset.unitCost || 0
            );

            // Do not calculate a cost for an invalid/empty quantity.
            const itemTotal =
                quantity > 0 ? unitCost * quantity : 0;

            const expectedDateInput =
                row.querySelector(".restock-expected-date");

            const expectedDate = expectedDateInput
                ? expectedDateInput.value
                : "";

            if (!supplierGroups[supplierID]) {
                supplierGroups[supplierID] = {
                    id: supplierID,
                    name: supplierName,
                    company: companyName,
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
                supplierGroups[supplierID].expectedDate =
                    expectedDate;
            }

            supplierGroups[supplierID].items.push({
                productName: productName,
                quantity: quantity,
                unitCost: unitCost,
                itemTotal: itemTotal
            });

            supplierGroups[supplierID].subtotal += itemTotal;



            
        });

        const supplierIDs = Object.keys(supplierGroups);

        if (supplierIDs.length === 0) {
            supplierSummaryContent.innerHTML = `
                <p class="supplier-summary-empty">
                    Select a product and supplier to see the order summary.
                </p>
            `;
            return;
        }

        let grandTotal = 0;

        supplierIDs.forEach(function (supplierID) {
            const group = supplierGroups[supplierID];

            grandTotal += group.subtotal;

            const groupDiv = document.createElement("div");
            groupDiv.className = "supplier-summary-group";
            groupDiv.dataset.supplierId = supplierID;

            groupDiv.innerHTML = `
                <div class="supplier-summary-header"
                     style="background-color: rgba(214, 208, 208, 0.92);">

                    <div class="supplier-summary-info-row">
                        <div class="supplier-summary-info-item">
                            <strong>Company Name: </strong>
                            <span style="font-weight: bolder;">
                                ${group.company}
                            </span>
                        </div>

                        <div class="supplier-summary-info-item">
                            <strong>Contact Person: </strong>
                            <span style="font-weight: bolder;">
                                ${group.name}
                            </span>
                        </div>
                    </div>

                    <div class="supplier-summary-info-row supplier-location-row">
                        <div class="supplier-summary-info-item">
                            <strong>Location: </strong>
                            <span style="font-weight: bolder;">
                                ${group.location}
                            </span>
                        </div>
                    </div>

                    <div class="supplier-summary-info-row">
                        <div class="supplier-summary-info-item">
                            <strong>Phone Number: </strong>
                            <span style="font-weight: bolder; color: rgba(1, 46, 130, 0.92);">
                                📞 ${group.phone}
                            </span>
                        </div>

                        <div class="supplier-summary-info-item">
                            <strong>Email: </strong>
                            <span style="font-weight: bolder; color: rgba(3, 95, 34, 0.92);">
                                ✉️ ${group.email}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="supplier-summary-expected-date"
                     style="margin-top: -20px">
                    <label style="font-weight: bolder;">
                        Expected Receive Date:
                    </label>

                    <input
                        type="date"
                        class="supplier-expected-date"
                        style="width: 30%; font-weight: bolder;"
                        value="${group.expectedDate || ""}"
                        min="${getLocalDateString()}"
                        required>









                </div>

                <div class="supplier-summary-cost-header"
                     style="display: grid;
                            grid-template-columns: minmax(120px, 2fr) repeat(3, minmax(90px, 1fr));
                            gap: 10px;
                            padding: 10px;
                            font-weight: bold;
                            border-bottom: 1px solid #ccc;">
                    <span>Product</span>
                    <span>Quantity</span>
                    <span>Unit Cost</span>
                    <span>Item Total</span>
                </div>
            `;

            group.items.forEach(function (item) {
                const itemDiv = document.createElement("div");
                itemDiv.className = "supplier-summary-item";

                itemDiv.style.display = "grid";
                itemDiv.style.gridTemplateColumns =
                    "minmax(120px, 2fr) repeat(3, minmax(90px, 1fr))";
                itemDiv.style.gap = "10px";
                itemDiv.style.alignItems = "center";
                itemDiv.style.padding = "10px";
                itemDiv.style.borderBottom = "1px solid #eee";

                const productSpan = document.createElement("span");
                productSpan.className = "supplier-summary-product";
                productSpan.style.fontWeight = "bolder";
                productSpan.textContent = item.productName;

                const quantitySpan = document.createElement("span");
                quantitySpan.className = "supplier-summary-quantity";
                quantitySpan.style.fontWeight = "bolder";
                quantitySpan.style.color = "green";
                quantitySpan.style.marginRight = "40px";
                quantitySpan.textContent = item.quantity || "-";

                const unitCostSpan = document.createElement("span");
                unitCostSpan.style.fontWeight = "bolder";
                unitCostSpan.textContent =
                    `₱${formatOrderCurrency(item.unitCost)}`;

                const itemTotalSpan = document.createElement("span");
                itemTotalSpan.style.fontWeight = "bolder";
                itemTotalSpan.textContent =
                    `₱${formatOrderCurrency(item.itemTotal)}`;

                itemDiv.append(
                    productSpan,
                    quantitySpan,
                    unitCostSpan,
                    itemTotalSpan
                );

                groupDiv.appendChild(itemDiv);
            });

            // Subtotal for this supplier.
            const subtotalDiv = document.createElement("div");
            subtotalDiv.className = "supplier-summary-subtotal";
            subtotalDiv.style.display = "flex";
            subtotalDiv.style.justifyContent = "flex-end";
            subtotalDiv.style.gap = "12px";
            subtotalDiv.style.padding = "12px 10px";
            subtotalDiv.style.fontWeight = "bold";
            subtotalDiv.style.borderTop = "2px solid #ccc";

            subtotalDiv.innerHTML = `
                <span>Supplier Subtotal:</span>
                <span>₱${formatOrderCurrency(group.subtotal)}</span>
            `;

            groupDiv.appendChild(subtotalDiv);
            supplierSummaryContent.appendChild(groupDiv);
        });
        const grandTotalDisplay =
            document.getElementById("restockGrandTotal");

        if (grandTotalDisplay) {
            grandTotalDisplay.textContent =
                `₱${formatOrderCurrency(grandTotal)}`;
        }

        // Overall total across all suppliers.
        const grandTotalDiv = document.createElement("div");
        grandTotalDiv.className = "supplier-summary-grand-total";

        grandTotalDiv.style.display = "flex";
        grandTotalDiv.style.justifyContent = "space-between";
        grandTotalDiv.style.alignItems = "center";
        grandTotalDiv.style.gap = "12px";
        grandTotalDiv.style.padding = "16px";
        grandTotalDiv.style.marginTop = "16px";
        grandTotalDiv.style.borderTop = "3px solid #333";
        grandTotalDiv.style.fontSize = "1.1rem";
        grandTotalDiv.style.fontWeight = "bold";

        grandTotalDiv.innerHTML = `
            <span stlye="color: black;">GRAND TOTAL</span>
            <span stlye="color: green;">₱${formatOrderCurrency(grandTotal)}</span>
        `;

        supplierSummaryContent.appendChild(grandTotalDiv);
    }


    // =========================================
    // SUPPLIER CHANGED
    // =========================================

    container.addEventListener(
        "change",
        function (event) {

            if (
                event.target.classList.contains(
                    "restock-supplier"
                )
            ) {

                updateSupplierSummary();

            }

        }
    );


    supplierSummaryContent.addEventListener(
        "change",
        function (event) {

            if (
                !event.target.classList.contains(
                    "supplier-expected-date"
                )
            ) {
                return;
            }

            const groupDiv =
                event.target.closest(
                    ".supplier-summary-group"
                );

            if (!groupDiv) {
                return;
            }

            const supplierID =
                groupDiv.dataset.supplierId;

            const expectedDate =
                event.target.value;

            const rows =
                Array.from(
                    container.querySelectorAll(
                        ".restock-item-row"
                    )
                );

            rows.forEach(function (row) {

                const supplierSelect =
                    row.querySelector(
                        ".restock-supplier"
                    );

                if (
                    Number(supplierSelect.value) ===
                    Number(supplierID)
                ) {

                    const dateInput =
                        row.querySelector(
                            ".restock-expected-date"
                        );

                    if (dateInput) {
                        dateInput.value =
                            expectedDate;
                    }
                }
            });
        }
    );    










    


    // =========================================
    // CREATE A NEW RESTOCK ROW
    // =========================================

    function createRestockRow() {

        itemCount++;

        const row =
            document.createElement("div");

        row.className =
            "restock-item-row";

        row.dataset.rowId =
            itemCount;


        row.innerHTML = `

            <div class="restock-row-field product-field">

                <label>Product</label>

                <button
                    type="button"
                    class="restock-product-search">

                    🔍 Search Product

                </button>

                <input
                    type="hidden"
                    name="items[${itemCount}][Product_ID]"
                    class="restock-product-id">

                <input
                    type="text"
                    class="restock-product-name"
                    placeholder="No product selected"
                    readonly>

            </div>


            <div class="restock-row-field">

                <label>Current Stock</label>

                <input
                    type="text"
                    class="restock-current-stock"
                    placeholder="-"
                    readonly>

            </div>


            <div class="restock-row-field">

                <label>Pack Size</label>

                <input
                    type="text"
                    class="restock-pack-size"
                    placeholder="-"
                    readonly>

            </div>


            <div class="restock-row-field">

                <label>Supplier</label>

 


                <select
                    name="items[${itemCount}][Supplier_ID]"
                    class="restock-supplier"
                    required
                    disabled>

                    <option
                        value=""
                        disabled
                        selected>

                        Select Product First

                    </option>

                </select>


            </div>


            <div class="restock-row-field">

                <label>Order Quantity</label>

        

                <input
                    type="number"
                    name="items[${itemCount}][Quantity]"
                    class="restock-quantity"
                    min="1"
                    required
                    placeholder="Select Product First"
                    disabled>



            </div>


            <div class="restock-row-action">

                <button
                    type="button"
                    class="remove-restock-item"
                    title="Remove item">

                    🗑

                </button>

            </div>
            <input
                type="hidden"
                name="items[${itemCount}][Expected_Receive_Date]"
                
                class="restock-expected-date">

        `;


        container.appendChild(row);


        // loadSuppliersForRow(row);




    }


    // =========================================
    // ADD ITEM BUTTON
    // =========================================

    addItemButton.addEventListener(
        "click",
        function () {

            createRestockRow();

        }
    );


    // =========================================
    // REMOVE ITEM
    // =========================================

    container.addEventListener(
        "click",
        function (event) {

            const removeButton =
                event.target.closest(
                    ".remove-restock-item"
                );


            if (!removeButton) {
                return;
            }


            const row =
                removeButton.closest(
                    ".restock-item-row"
                );


            if (row) {

                row.remove();


                updateSupplierSummary();


            }

        }
    );


    // =========================================
    // QUANTITY VALIDATION
    // =========================================

    container.addEventListener(
        "input",
        function (event) {

            const quantityInput =
                event.target.closest(
                    ".restock-quantity"
                );


            if (!quantityInput) {
                return;
            }


            const row =
                quantityInput.closest(
                    ".restock-item-row"
                );


            if (!row) {
                return;
            }


            const packSizeInput =
                row.querySelector(
                    ".restock-pack-size"
                );


            const packSize =
                Number(packSizeInput.value);


            const quantity =
                Number(quantityInput.value);


            // No product selected
            if (!packSize || packSize <= 0) {

                quantityInput.setCustomValidity("");

                return;

            }


            // Empty quantity
            if (!quantity) {

                quantityInput.setCustomValidity("");

                return;

            }


            // Quantity must be a multiple of pack size
            if (quantity % packSize !== 0) {

                quantityInput.setCustomValidity(
                    `Quantity must be a multiple of the pack size (${packSize}).`
                );

            } else {

                quantityInput.setCustomValidity("");

            }

            updateSupplierSummary();

        }
    );    


    // =========================================
    // SEARCH PRODUCT BUTTON
    // =========================================

    container.addEventListener(
        "click",
        function (event) {

            const searchButton =
                event.target.closest(
                    ".restock-product-search"
                );


            if (!searchButton) {
                return;
            }


            // Remember which row opened the popup
            activeRow =
                searchButton.closest(
                    ".restock-item-row"
                );


            // Clear previous search
            productSearchInput.value = "";


            // Display products
            displayProducts(
                allProducts
            );


            // Show popup
            productSearchModal.style.display =
                "flex";


            // Automatically focus search box
            setTimeout(function () {

                productSearchInput.focus();

            }, 50);

        }
    );


    // =========================================
    // SEARCH PRODUCTS
    // =========================================

    productSearchInput.addEventListener(
        "input",
        function () {

            const searchText =
                this.value
                    .toLowerCase()
                    .trim();


            const filteredProducts =
                allProducts.filter(
                    function (product) {

                        const productName =
                            String(
                                product.ProductName
                            ).toLowerCase();

                        const productID =
                            String(
                                product.Product_ID
                            );


                        return (
                            productName.includes(
                                searchText
                            )
                            ||
                            productID.includes(
                                searchText
                            )
                        );

                    }
                );


            displayProducts(
                filteredProducts
            );

        }
    );


    // =========================================
    // DISPLAY PRODUCTS
    // =========================================

    function displayProducts(products) {

        productSearchResults.innerHTML = "";


        if (products.length === 0) {

            const noResults =
                document.createElement("div");

            noResults.className =
                "product-search-no-results";

            noResults.textContent =
                "No products found.";

            productSearchResults.appendChild(
                noResults
            );

            return;
        }


        products.forEach(
            function (product) {

                const result =
                    document.createElement("div");


                result.className =
                    "product-search-result";


                // =========================================
                // CHECK IF PRODUCT IS ALREADY SELECTED
                // =========================================

                const alreadySelected =
                    Array.from(
                        container.querySelectorAll(
                            ".restock-product-id"
                        )
                    ).some(function (input) {

                        // Ignore the row currently being edited
                        if (
                            input.closest(".restock-item-row")
                            === activeRow
                        ) {
                            return false;
                        }

                        return Number(input.value) ===
                            Number(product.Product_ID);

                    });


                // =========================================
                // CHANGE APPEARANCE IF ALREADY SELECTED
                // =========================================

                if (alreadySelected) {

                    result.classList.add(
                        "product-already-selected"
                    );

                }


                result.innerHTML = `

                    <div class="product-search-result-name">

                        ${alreadySelected ? "✓ " : ""}
                        ${product.ProductName}

                    </div>

                    <div class="product-search-result-details">

                        Product ID:
                        ${product.Product_ID}

                        &nbsp; | &nbsp;

                        Stock:
                        ${product.CurrentStock}

                        &nbsp; | &nbsp;

                        Pack Size:
                        ${product.Pack_Size}

                    </div>

                `;


                // =========================================
                // ONLY ALLOW UNSELECTED PRODUCTS
                // =========================================

                if (!alreadySelected) {

                    result.addEventListener(
                        "click",
                        function () {

                            selectProduct(product);

                        }
                    );

                }


                productSearchResults.appendChild(
                    result
                );

            }
        );        

        // products.forEach(
        //     function (product) {

        //         const result =
        //             document.createElement("div");

        //         result.className =
        //             "product-search-result";


        //         result.innerHTML = `

        //             <div class="product-search-result-name">

        //                 ${product.ProductName}

        //             </div>

        //             <div class="product-search-result-details">

        //                 Product ID:
        //                 ${product.Product_ID}

        //                 &nbsp; | &nbsp;

        //                 Stock:
        //                 ${product.CurrentStock}

        //                 &nbsp; | &nbsp;

        //                 Pack Size:
        //                 ${product.Pack_Size}

        //             </div>

        //         `;


        //         // Select product
        //         result.addEventListener(
        //             "click",
        //             function () {

        //                 selectProduct(
        //                     product
        //                 );

        //             }
        //         );


        //         productSearchResults.appendChild(
        //             result
        //         );

        //     }
        // );

    }


    // =========================================
    // SELECT PRODUCT
    // =========================================

    async function selectProduct(product) {

        if (!activeRow) {
            return;
        }

        const row = activeRow;

        // =========================================
        // CHECK FOR DUPLICATE PRODUCT
        // =========================================

        const duplicateProduct =
            Array.from(
                container.querySelectorAll(
                    ".restock-product-id"
                )
            ).some(function (input) {

                // Ignore the current row
                if (input.closest(".restock-item-row") === activeRow) {
                    return false;
                }

                return Number(input.value) ===
                    Number(product.Product_ID);

            });


        if (duplicateProduct) {

            alert(
                `${product.ProductName} is already added to this order.`
            );

            return;
        }




        // Product ID
        const productIDInput =
            row.querySelector(
                ".restock-product-id"
            );


        // Product Name
        const productNameInput =
            row.querySelector(
                ".restock-product-name"
            );


        // Current Stock
        const currentStockInput =
            row.querySelector(
                ".restock-current-stock"
            );


        // Pack Size
        const packSizeInput =
            row.querySelector(
                ".restock-pack-size"
            );


        // Fill the selected row
        productIDInput.value =
            product.Product_ID;

        productNameInput.value =
            product.ProductName;

        currentStockInput.value =
            product.CurrentStock;

        packSizeInput.value =
            product.Pack_Size;


        // Change button text because a product is selected
        const searchButton =
            row.querySelector(
                ".restock-product-search"
            );

        searchButton.textContent =
            "🔄 Change Product";



        // Enable supplier
        // const supplierSelect =
        //     row.querySelector(
        //         ".restock-supplier"
        //     );

        // supplierSelect.disabled = false;


        // // Enable quantity
        // const quantityInput =
        //     row.querySelector(
        //         ".restock-quantity"
        //     );

        // quantityInput.disabled = false;

        // quantityInput.placeholder =
        //     "Quantity";




        // =========================================
        // PREPARE SUPPLIER AND QUANTITY INPUTS
        // =========================================

        const supplierSelect =
            row.querySelector(".restock-supplier");

        const quantityInput =
            row.querySelector(".restock-quantity");

        // Keep supplier disabled until its offers load.
        supplierSelect.disabled = true;

        supplierSelect.innerHTML = `
            <option value="" selected disabled>
                Loading supplier offers...
            </option>
        `;

        // Reset quantity for the newly selected product.
        quantityInput.value = "";
        quantityInput.setCustomValidity("");
        quantityInput.placeholder = "Quantity";
        quantityInput.disabled = true;

        // Use the selected product's pack size.
        quantityInput.setAttribute("step", product.Pack_Size);
        quantityInput.setAttribute("min", product.Pack_Size);

        // Load only suppliers that actively offer this product.
        await loadSuppliersForRow(row, product.Product_ID);






            

        // quantityInput.value = "";

        // quantityInput.setCustomValidity("");




        // // Set quantity step based on pack size
        // quantityInput.setAttribute(
        //     "step",
        //     product.Pack_Size
        // );

        // quantityInput.setAttribute(
        //     "min",
        //     product.Pack_Size
        // );


        // =========================================
        // QUANTITY VALIDATION
        // =========================================

        // quantityInput.addEventListener(
        //     "input",
        //     function () {

        //         const packSize =
        //             Number(packSizeInput.value);

        //         const quantity =
        //             Number(this.value);


        //         // No pack size selected
        //         if (!packSize || packSize <= 0) {

        //             this.setCustomValidity("");

        //             return;

        //         }


        //         // No quantity entered yet
        //         if (!quantity) {

        //             this.setCustomValidity("");

        //             return;

        //         }


        //         // Check if quantity is a multiple
        //         if (quantity % packSize !== 0) {

        //             this.setCustomValidity(
        //                 `Quantity must be a multiple of the pack size (${packSize}).`
        //             );

        //         } else {

        //             this.setCustomValidity("");

        //         }

        //     }
        // );        


        // Close search popup
        productSearchModal.style.display =
            "none";


        // Forget active row
        activeRow = null;


        updateSupplierSummary();

    }


    // =========================================
    // CLOSE PRODUCT SEARCH
    // =========================================

    closeProductSearch.addEventListener(
        "click",
        function () {

            productSearchModal.style.display =
                "none";

            activeRow = null;

        }
    );


    // =========================================
    // CLICK OUTSIDE PRODUCT SEARCH
    // =========================================

    productSearchModal.addEventListener(
        "click",
        function (event) {

            if (
                event.target ===
                productSearchModal
            ) {

                productSearchModal.style.display =
                    "none";

                activeRow = null;

            }

        }
    );


    // =========================================
    // SUPPLIER LOADING
    // =========================================


    // =========================================
    // LOAD ACTIVE SUPPLIER OFFERS FOR A PRODUCT
    // =========================================

    async function loadSuppliersForRow(row, productID) {
        const supplierSelect = row.querySelector(".restock-supplier");
        const quantityInput = row.querySelector(".restock-quantity");

        supplierSelect.disabled = true;
        quantityInput.disabled = true;

        supplierSelect.innerHTML = `
            <option value="">Loading supplier offers...</option>
        `;

        try {
            const response = await fetch(
                `get_product_supplier_offers.php?product_id=${encodeURIComponent(productID)}&t=${Date.now()}`
            );

            if (!response.ok) {
                throw new Error("Failed to load supplier offers.");
            }

            const result = await response.json();

            if (!result.success || !Array.isArray(result.data)) {
                throw new Error(
                    result.message || "Unable to load supplier offers."
                );
            }

            supplierSelect.innerHTML = `
                <option value="" selected disabled>Select Supplier</option>
            `;

            if (result.data.length === 0) {
                supplierSelect.innerHTML = `
                    <option value="" selected disabled>
                        No active supplier offers
                    </option>
                `;

                alert(
                    "This product has no active supplier offers. " +
                    "Please add or activate a supplier offer first."
                );

                return;
            }

            result.data.forEach(function (offer) {
                const option = document.createElement("option");

                option.value = offer.supplierId;

                option.textContent =
                    `₱${formatOrderCurrency(offer.unitCost)} - ${offer.supplierName} || ` +
                    `${offer.contactPerson || "Not provided"} ` +
                    `(${offer.supplierCity || "No city"}) — `
                    ;

                option.dataset.productSupplierId = offer.productSupplierId;
                option.dataset.unitCost = offer.unitCost;
                option.dataset.company = offer.supplierName || "";
                option.dataset.name = offer.contactPerson || "";
                option.dataset.phone = offer.supplierPhone || "";
                option.dataset.email = offer.supplierEmail || "";
                option.dataset.location = offer.supplierLocation || "";

                supplierSelect.appendChild(option);
            });

            supplierSelect.disabled = false;
            quantityInput.disabled = false;
            supplierSelect.value = "";

        } catch (error) {
            console.error("Supplier offer loading error:", error);

            supplierSelect.innerHTML = `
                <option value="" selected disabled>
                    Unable to load supplier offers
                </option>
            `;

            supplierSelect.disabled = true;
            quantityInput.disabled = true;

            alert(
                "Unable to load supplier offers. " +
                "Please try selecting the product again."
            );
        }
    }



    // =========================================
    // CREATE FIRST ROW
    // =========================================

    createRestockRow();


    // =========================================
    // LOAD PRODUCT DATA
    // =========================================

    loadProducts();

});