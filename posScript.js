// POS SCRIPT
let script = function() {

    // Store list of order items
    this.orderItems = {};
    // Store total order amount
    this.totalOrderAmount = 0.00;
    
    this.userChange = -1;
    //tendered amt
    this.tenderedAmt = 0;
    
    // Dynamic products object (initialized empty)
    this.products = {};

    // Fetch products and live stock directly from the database
    this.loadProductsFromDatabase = function() {
        fetch('get_pos_products.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    loadScript.products = data.products;
                    console.log("POS Inventory Stock Loaded:", loadScript.products);
                } else {
                    loadScript.dialogError("Failed to fetch product inventory: " + data.message);
                }
            })
            .catch(error => {
                console.error("Fetch error:", error);
                loadScript.dialogError("Database Connection Error.");
            });
    };
    

    this.showClock = function() {
        let dateObj = new Date();
        let months = ['January','February','March','April','May','June','July',
            'August','September','October','November','December'];

        let year = dateObj.getFullYear();
        let monthNum = dateObj.getMonth();
        let dateCal = dateObj.getDate();
        let hour = dateObj.getHours();
        let min = dateObj.getMinutes();
        let sec = dateObj.getSeconds();

        let timeFormatted = loadScript.toTwelveHourFormat(hour);

        let clockEl = document.querySelector('.timeAndDate');
        if (clockEl) {
            clockEl.innerHTML = 
                months[monthNum] + ' ' + dateCal + ', ' + year + ' || ' + timeFormatted['time'] + ':' + min + ':' + sec + ' ' + timeFormatted['am_pm'];
        }
    };

    this.toTwelveHourFormat = function(time) {
        let am_pm = 'AM';
        if (time > 12) {
            time = time - 12;
            am_pm = 'PM';
        } else if (time === 0) {
            time = 12;
        }
        return {
            time: time,
            am_pm: am_pm
        };
    };
    // $(document).on('click', '.searchResultEntry', function() {
    // let pid = $(this).data('pid');
    // let productObj = window.products ? window.products[pid] : null;

    // if (productObj) {
    //     // 1. CHECK STOCK FIRST (DO NOT CLEAR SEARCH BAR IF OUT OF STOCK)
    //     if (productObj.stock <= 0) {
    //         alert(`Product "${productObj.name}" is Out of Stock.`);
    //         return; // Stops here, search bar remains filled
    //     }

    //     // 2. ADD TO IMS ORDER
    //     if (typeof script !== 'undefined' && script.addToOrder) {
    //         script.addToOrder(productObj, pid, 1);
    //     } else if (typeof loadScript !== 'undefined' && loadScript.addToOrder) {
    //         loadScript.addToOrder(productObj, pid, 1);
    //     }

    //     // 3. CLEAR SEARCH BAR ONLY AFTER SUCCESSFUL ADDITION
    //     $('#searchInput').val('');
    //     $('#searchResultContainerMain').hide();
    // }
    // });

    // $(document).on('click', '.searchResultEntry', function() {
    // let pid = $(this).data('pid');
    // let productObj = window.products ? window.products[pid] : null;

    // if (productObj) {
    //     if (productObj.stock <= 0) {
    //         alert(`Product "${productObj.name}" is Out of Stock.`);
    //         return;
    //     }

    //     // Target the active script object instance
    //     let activeScript = typeof script !== 'undefined' ? script : (typeof loadScript !== 'undefined' ? loadScript : null);

    //     if (activeScript && activeScript.addToOrder) {
    //         // Adds +1 to existing quantity in IMS instead of overwriting
    //         activeScript.addToOrder(productObj, pid, 1);
    //     }

    //     // Clear search box only after successful addition
    //     $('#searchInput').val('');
    //     $('#searchResultContainerMain').hide();
    // }
    // });
    $(document).on('click', '.searchResultEntry', function(e) {
    e.preventDefault();
    e.stopPropagation(); // Prevents parent containers from blocking the event

    let pid = $(this).data('pid');
    let productObj = window.products ? window.products[pid] : null;

    if (productObj) {
        // 1. Hide the search result container immediately so it stops stealing clicks
        $('#searchResultContainerMain')
        // .hide();

        // 2. Check stock before showing prompt
        if (productObj.stock <= 0) {
            // alert(`Product "${productObj.name}" is Out of Stock.`);
            return;
            
        }

        // 3. Trigger your original Add to Order prompt/dialog
        let activeScript = typeof script !== 'undefined' ? script : (typeof loadScript !== 'undefined' ? loadScript : null);

        if (activeScript) {
            if (typeof activeScript.productClick === 'function') {
                activeScript.productClick(productObj, pid);
            } else if (typeof activeScript.showAddToOrderDialog === 'function') {
                activeScript.showAddToOrderDialog(productObj, pid);
            }
        }
    }
    });

    

    this.registerEvents = function() {

        document.addEventListener('click', function(e) {
            let targetEl = e.target;
            let targetElClassList = targetEl.classList;

            // Add Product to Cart
            if (targetElClassList.contains('productImage') || 
                targetElClassList.contains('productName') || 
                targetElClassList.contains('productPrice') ||
                targetElClassList.contains('searchResultEntry')
            ) {

                let productContainer = targetElClassList.contains('searchResultEntry') ? targetEl : targetEl.closest('div.productColContainer');
                let pid = productContainer.dataset.pid;
                let productInfo = loadScript.products[pid];

                if (!productInfo) {
                    loadScript.dialogError("Product details not found.");
                    return;
                }

                let currentStock = productInfo['stock'];
                if (currentStock === 0) {
                    loadScript.dialogError("Product Selected is currently Out of Stock");
                    return;
                }
                
                let dialogForm = `
                    <h6 class="dialogProductName">${productInfo['name']}<br><br><span class="floatLeft">QTY: ${loadScript.addCommas(productInfo['stock'])}</span> <span class="floatRight">₱ ${loadScript.addCommas(productInfo['price'])}</span></h6>
                    <input type="number" id="orderQty" class="form-control" placeholder="Enter quantity..." min="1" max="${currentStock}"/>
                `;

                BootstrapDialog.confirm({
                    title: 'Add to Order',
                    type: BootstrapDialog.TYPE_DEFAULT,
                    cssClass: 'pos-dark-dialog',
                    message: dialogForm,
                    callback: function(addOrder) {
                        if (addOrder) {
                            let orderQty = parseInt(document.getElementById('orderQty').value);

                            if (isNaN(orderQty) || orderQty <= 0) {
                                loadScript.dialogError("Please Input Valid Quantity");
                                return;
                            }    

                            if (orderQty > currentStock) {
                                loadScript.dialogError(`Order is Higher than Current Stock: <strong>(${currentStock})</strong>`);
                                return;
                            }   

                            loadScript.addToOrder(productInfo, pid, orderQty);
                        }
                    }
                });
            }   

            // Delete Order Item from Cart
            if (targetElClassList.contains('daleteOrderItem')) {
                let pid = targetEl.dataset.id;
                let productInfo = loadScript.orderItems[pid];
                

                BootstrapDialog.confirm({
                    type: BootstrapDialog.TYPE_DANGER,
                    title: `<strong>Delete Order Item</strong>`,
                    cssClass: 'pos-dark-dialog',
                    message: `Are you sure to delete: <strong>${productInfo['name']}</strong>`,
                    callback: function(toDelete) {
                        if (toDelete) {
                            let orderedQuantity = productInfo['orderQty'];
                            loadScript.products[pid]['stock'] += orderedQuantity;
                            delete loadScript.orderItems[pid];
                            loadScript.updateOrderItemTable();
                        }
                    }
                });
            }

            // UPDATE/Decrease Quantity
            if (targetElClassList.contains('quantityUpdateBtn_minus')) {
                let pid = targetEl.dataset.id;
                loadScript.products[pid]['stock']++;
                loadScript.orderItems[pid]['orderQty']--;
                loadScript.orderItems[pid]['amount'] = loadScript.orderItems[pid]['orderQty'] * loadScript.orderItems[pid]['price'];

                if (loadScript.orderItems[pid]['orderQty'] === 0) delete loadScript.orderItems[pid];
                loadScript.updateOrderItemTable();
            }

            // UPDATE/Increase Quantity
            if (targetElClassList.contains('quantityUpdateBtn_plus')) {
                let pid = targetEl.dataset.id;

                if (loadScript.products[pid]['stock'] === 0) {
                    loadScript.dialogError('Product is OUT OF STOCK');
                } else {
                    loadScript.products[pid]['stock']--;
                    loadScript.orderItems[pid]['orderQty']++;
                    loadScript.orderItems[pid]['amount'] = loadScript.orderItems[pid]['orderQty'] * loadScript.orderItems[pid]['price'];
                    loadScript.updateOrderItemTable();
                }
            }

            //CHECKOUT
            if (targetElClassList.contains('checkoutBtn')){
                if(Object.keys(loadScript.orderItems).length){

                    let orderItemsHtml = '';
                    let counter = 1;
                    let totalAmt = 0.00;
                    for (const [pid, orderItem] of Object.entries(loadScript.orderItems)){
                        orderItemsHtml +='\
                            <div class="row checkoutTblContentContainer">\
                                <div class="col-md-2 checkoutTblContent">'+ counter +'</div>\
                                <div class="col-md-4 checkoutTblContent">'+ orderItem['name'] +'</div>\
                                <div class="col-md-3 checkoutTblContent">'+ loadScript.addCommas(orderItem['orderQty']) +'</div>\
                                <div class="col-md-3 checkoutTblContent">₱ '+ loadScript.addCommas(orderItem['amount'].toFixed(2)) +'</div>\
                            </div>';

                        totalAmt += orderItem['amount'];
                        counter++;
                    }

                    let content ='\
                        <div class="row">\
                            <div class="col-md-7">\
                                <p class="checkoutTblHeaderContainer_title">Items</p>\
                                <div class="row checkoutTblHeaderContainer">\
                                    <div class="col-md-2 checkoutTblHeader">#</div>\
                                    <div class="col-md-4 checkoutTblHeader">Product Name:</div>\
                                    <div class="col-md-3 checkoutTblHeader">Ordered Qty:</div>\
                                    <div class="col-md-3 checkoutTblHeader">Amount:</div>\
                                </div>'+ orderItemsHtml +'\
                            </div>\
                            <div class="col-md-5">\
                                <div class="checkoutTotalAmountContainer">\
                                    <span class="checkout_amt">\
                                        ₱ '+ loadScript.addCommas(totalAmt.toFixed(2)) +' \
                                    </span>\
                                    <span class="checkout_amt_title">\
                                        TOTAL AMOUNT\
                                    </span>\
                                </div>\
                                <hr>\
                                <div class="checkoutUserAmt">\
                                    <input class="form-control" id="userAmt" type="number" placeholder="Enter amount.">\
                                </div>\
                                <div class="checkoutUserChangeContainer">\
                                    <p class="checkoutUserChange"><small>CHANGE: ₱ </small><span class="changeAmt"> 0.00 </span></p>\
                                </div>\
                                <hr/>\
                                <div class="checkoutCustomer">\
                                    <h4>Customer Details</h4>\
                                    <div class="form-group">\
                                        <label for="fName">First Name</label>\
                                        <input type="text" id="fName" placeholder="Enter first name..." class="form-control" />\
                                    </div>\
                                    <div class="form-group">\
                                        <label for="lName">Last Name</label>\
                                        <input type="text" id="lName" placeholder="Enter last name..." class="form-control" />\
                                    </div>\
                                    <div class="form-group">\
                                        <label for="address">Address</label>\
                                        <input type="text" id="address" placeholder="Enter address..." class="form-control" />\
                                    </div>\
                                    <div class="form-group">\
                                        <label for="contact">Contact</label>\
                                        <input type="text" id="contact" placeholder="Enter contact..." class="form-control" />\
                                    </div>\
                                </div>\
                            </div>\
                        </div>';

                    BootstrapDialog.confirm({
                        type: BootstrapDialog.TYPE_INFO,
                        title: `<strong>CHECKOUT</strong>`,
                        cssClass: 'checkoutDialog pos-dark-dialog',
                        message: content,
                        btnOKLabel: `Checkout`,
                        callback: function(checkout){
                            if(checkout){
                                if(loadScript.userChange < 0){
                                    loadScript.dialogError('Please input correct amount!');
                                    return false; // Fixed: returned false instead of undefined variable 'a'
                                } 
                                else {
                                    // Save to database
                                    // Safely query input values directly inside the active modal dialog container
                                    let modalBody = $('.checkoutDialog');
                                    let tenderedInput = parseFloat(modalBody.find('#userAmt').val()) || loadScript.tenderedAmt || 0;

                                    let customerData = {
                                        firstName: modalBody.find('#fName').val() || '',
                                        lastName: modalBody.find('#lName').val() || '',
                                        contact: modalBody.find('#contact').val() || '',
                                        address: modalBody.find('#address').val() || ''
                                    };

                                    $.ajax({
                                        url: 'product.php?action=checkout',
                                        type: 'POST',
                                        contentType: 'application/json',
                                        data: JSON.stringify({
                                            data: loadScript.orderItems,
                                            totalAmt: loadScript.totalOrderAmount,
                                            change: loadScript.userChange,
                                            tenderedAmt: tenderedInput,
                                            customer: customerData
                                        }),
                                        dataType: 'json',
                                        success: function(response){
                                            let type = response.success ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER;

                                            BootstrapDialog.alert({
                                                type: type,
                                                cssClass: 'pos-dark-dialog',
                                                title: response.success ? 'Success' : 'Error',
                                                message: response.message,
                                                callback: function(isOk){
                                                    if(response.success === true){
                                                        loadScript.resetData(response);
                                                    }
                                                }
                                            });
                                        }
                                    });
                                    


                                    // let modalBody = $('.checkoutDialog');
                                    // let tenderedInput = parseFloat(modalBody.find('#userAmt').val()) || loadScript.tenderedAmt || 0;

                                    // $.post('product.php?action=checkout', {
                                    //     data: loadScript.orderItems,
                                    //     totalAmt: loadScript.totalOrderAmount,
                                    //     change: loadScript.userChange,
                                    //     tenderedAmt: tenderedInput,
                                    //     customer: {
                                    //         firstName: $('#fName').val(),
                                    //         lastName: $('#lName').val(),
                                    //         contact: $('#contact').val(),
                                    //         address: $('#address').val()
                                    //     }
                                    // }, function(response) {
                                    //     let type = response.success ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER;

                                    //     BootstrapDialog.alert({
                                    //         type: type,
                                    //         title: response.success ? 'Success' : 'Error',
                                    //         message: response.message,
                                    //         callback: function() {
                                    //             if (response.success) {
                                    //                 loadScript.resetData(response);
                                    //             }
                                    //         }
                                    //     });
                                    // }, 'json');

                                    // let tenderedInput = parseFloat(document.getElementById('userAmt').value) || 0;
                                    // $.post('product.php?action=checkout', {
                                    //     data: loadScript.orderItems,
                                    //     totalAmt: loadScript.totalOrderAmount,
                                    //     change: loadScript.userChange,
                                    //     tenderedAmt: tenderedInput, // Reads directly from the input field
                                    //     customer: {
                                    //         firstName: document.getElementById('fName').value,
                                    //         lastName: document.getElementById('lName').value,
                                    //         contact: document.getElementById('contact').value,
                                    //         address: document.getElementById('address').value,
                                    //     }
                                    // },
                                    // function(response){
                                    //     let type = response.success ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER;

                                    //     BootstrapDialog.alert({
                                    //         type: type,
                                    //         title: response.success ? 'Success' : 'Error',
                                    //         message: response.message,
                                    //         callback: function(isOk){
                                    //             if(response.success === true){
                                    //                 loadScript.resetData(response);
                                    //             }
                                    //         }
                                    //     });

                                    // }, 'json');
                                }
                            }
                        }
                    });
                }
            }
        });

        // document.addEventListener('keyup', function(e){
        //     let targetEl = e.target;
            
        //     if(targetEl.id === 'userAmt'){
        //         if(isNaN(targetEl.value)) {
        //             targetEl.value = '';
        //         }

        //         let userAmt = targetEl.value == '' ? 0 : parseFloat(targetEl.value);
        //         loadScript.tenderedAmt = userAmt;

        //         let change = userAmt - loadScript.totalOrderAmount;
        //         loadScript.userChange = change;

        //         document.querySelector('.checkoutUserChange .changeAmt')
        //             .innerHTML = loadScript.addCommas(change.toFixed(2));

        //         let el = document.querySelector('.checkoutUserChange');

        //         if(change < 0) el.classList.add('text-danger');
        //         else el.classList.remove('text-danger');
        //     }
        // });
        document.addEventListener('input', function(e){
            let targetEl = e.target;
            
            if(targetEl.id === 'userAmt'){
                let userAmt = targetEl.value === '' ? 0 : parseFloat(targetEl.value);
                // if (isNaN(userAmt)) userAmt = 0;

                loadScript.tenderedAmt = userAmt;
                let change = userAmt - loadScript.totalOrderAmount;
                loadScript.userChange = change;

                let changeEl = document.querySelector('.checkoutUserChange .changeAmt');
                if (changeEl) {
                    changeEl.innerHTML = loadScript.addCommas(change.toFixed(2));
                }

                let containerEl = document.querySelector('.checkoutUserChange');
                if (containerEl) {
                    if(change < 0) containerEl.classList.add('text-danger');
                    else containerEl.classList.remove('text-danger');
                }
            }
        });
    };

    // this.resetData = function(response){
    //     let productsJson = response.products;
    //     loadScript.products = {};

    //     if (Array.isArray(productsJson)) {
    //         productsJson.forEach((product) => {
                
    //             loadScript.products[product.Product_ID] = {
                    
    //                 name: product.ProductName,
    //                 stock: product.UnitsOrdered -product.UnitSold , // Fixed: referenced product.UnitSold safely
    //                 price: product.StorePrice
    //             };
    //         });
    //     }
        
        
    //     loadScript.orderItems = {};
    //     loadScript.totalOrderAmount = 0.00;
    //     loadScript.userChange = -1;
    //     loadScript.tenderedAmt = 0;
        
        
        

    //     loadScript.updateOrderItemTable();
    // // Check all possible key naming conventions returned from PHP
    //     let receiptId = response.receipt_id || response.sales_id || response.salesId || response.id;

    //     if (receiptId) {
    //         window.open('pos_receipt.php?receipt_id=' + receiptId, '_blank');
    //     } else {
    //         console.error("Missing receipt/sales ID in server response:", response);
    //         loadScript.dialogError("Checkout completed, but failed to retrieve receipt ID.");
    //     }
    // };

    this.resetData = function(response) {
    let productsJson = response.products;
    loadScript.products = {};

        if (Array.isArray(productsJson)) {
            productsJson.forEach((product) => {
                // Safely parse numbers from the PHP response
                let unitsOrdered = parseInt(product.UnitsOrdered) || 0;
                let unitSold = parseInt(product.UnitSold) || 0;
                
                // Check if backend directly provides 'stock' or calculate remaining stock
                let computedStock = (product.stock !== undefined && product.stock !== null) 
                    ? parseInt(product.stock) 
                    : (unitsOrdered - unitSold);

                // Ensure stock never falls below 0
                let finalStock = Math.max(0, computedStock);

                loadScript.products[product.Product_ID] = {
                    name: product.ProductName,
                    // stock: (product.CurrentStock <= 0 ? 'OUT OF STOCK' : product.CurrentStock ), 
                    stock: finalStock, 
                    price: parseFloat(product.StorePrice) || 0
                };
            });
        }

        // Reset cart states
        loadScript.orderItems = {};
        loadScript.totalOrderAmount = 0.00;
        loadScript.userChange = -1;
        loadScript.tenderedAmt = 0;

        loadScript.updateOrderItemTable();
        

        let receiptId = response.receipt_id || response.sales_id || response.salesId || response.id;
        if (receiptId) {
            window.open('pos_receipt.php?receipt_id=' + receiptId, '_blank');
        } else {
            console.error("Missing receipt/sales ID in server response:", response);
            loadScript.dialogError("Checkout completed, but failed to retrieve receipt ID.");
        }

    // Add this line at the bottom of your resetData() or clear order function:
    document.getElementById('searchInput').focus();
    };
    

    this.updateOrderItemTable = function() {
        loadScript.totalOrderAmount = 0.00;

        let ordersContainer = document.querySelector('.pos_items');
        let html = `<p class="itemNoData">No Data</p>`;

        if (Object.keys(loadScript.orderItems).length > 0) {
            let tableHtml = `
                <table class="table" id="pos_items_tbl">
                    <thead>
                        <tr style="border-bottom: 2px;">
                            <th>#</th>
                            <th>PRODUCT</th>
                            <th>PRICE</th>
                            <th>QTY</th>
                            <th>AMOUNT</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        ____ROWS____
                    </tbody>
                </table>`;

            let rows = '';
            let rowNum = 1;
            for (const [pid, orderItem] of Object.entries(loadScript.orderItems)) {
                rows += `
                    <tr> 
                        <td>${rowNum}</td>
                        <td>${orderItem['name']}</td>
                        <td>₱ ${loadScript.addCommas(orderItem['price'])}</td>
                        <td>
                            <a href="javascript:void(0);" data-id="${pid}" class="quantityUpdateBtn quantityUpdateBtn_minus">
                                <i class="fa fa-minus quantityUpdateBtn quantityUpdateBtn_minus" data-id="${pid}"></i>
                            </a>
                            ${loadScript.addCommas(orderItem['orderQty'])}
                            <a href="javascript:void(0);" data-id="${pid}" class="quantityUpdateBtn quantityUpdateBtn_plus">
                                <i class="fa fa-plus quantityUpdateBtn quantityUpdateBtn_plus" data-id="${pid}"></i>
                            </a>
                        </td>
                        <td>₱ ${loadScript.addCommas(orderItem['amount'].toFixed(2))}</td>
                        <td>
                            <a href="javascript:void(0);" class="daleteOrderItem" data-id="${pid}">
                                <i class="fa fa-trash daleteOrderItem" data-id="${pid}"></i>
                            </a>
                        </td>
                    </tr>
                `;

                rowNum++;
                loadScript.totalOrderAmount += orderItem['amount'];
            }
            html = tableHtml.replace('____ROWS____', rows);
        }

        ordersContainer.innerHTML = html;
        loadScript.updateTotalOrderAmount();
    };

    this.updateTotalOrderAmount = function() {
        let totalValEl = document.querySelector('.item_total--value');
        if (totalValEl) {
            totalValEl.innerHTML = `Php: ` + loadScript.addCommas(loadScript.totalOrderAmount.toFixed(2));
        }
    };

    this.addCommas = function(nStr) {
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    };
    

    this.addToOrder = function(productInfo, pid, orderQty) {
        let curItemIds = Object.keys(loadScript.orderItems);
        let totalAmount = productInfo['price'] * orderQty;

        if (curItemIds.indexOf(pid) > -1) {
            loadScript.orderItems[pid]['amount'] += totalAmount;
            loadScript.orderItems[pid]['orderQty'] += orderQty;
        } else {
            loadScript.orderItems[pid] = {
                name: productInfo['name'],
                price: productInfo['price'],
                orderQty: orderQty,
                amount: totalAmount
            };
        }
        loadScript.products[pid]['stock'] -= orderQty;
        this.updateOrderItemTable();
    };

    this.dialogError = function(message) {
        BootstrapDialog.alert({
            title: '<strong>Warning!</strong>',
            type: BootstrapDialog.TYPE_DANGER,
            cssClass: 'pos-dark-dialog',
            message: message
        });
    };

    this.addByBarcode = function(barcode) {
        let foundPid = null;
        let cleanBarcode = String(barcode).trim();

        // Check if barcode matches Product_ID directly or product.barcode
        for (let pid in loadScript.products) {
            let product = loadScript.products[pid];
            if (String(pid) === cleanBarcode || (product.barcode && String(product.barcode).trim() === cleanBarcode)) {
                foundPid = pid;
                break;
            }
        }

        if (foundPid) {
            let productInfo = loadScript.products[foundPid];
            let currentStock = parseInt(productInfo['stock']);

            if (currentStock <= 0) {
                // loadScript.dialogError(`Product <strong>${productInfo['name']}</strong> is Out of Stock.`);
                loadScript.dialogError("Out of Stock");
                return;
            }

            // Directly add 1 unit to order on barcode scan
            loadScript.addToOrder(productInfo, foundPid, 1);
        } else {
            loadScript.dialogError(`Product with Barcode / ID <strong>${barcode}</strong> not found.`);
        }
    };

    this.initialize = function() {
        this.loadProductsFromDatabase();
        this.showClock(); 
        setInterval(this.showClock, 1000);
        this.registerEvents();
    };
};


let loadScript = new script();
loadScript.initialize();