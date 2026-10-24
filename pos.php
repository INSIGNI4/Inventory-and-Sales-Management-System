
<?php
include('product.php');
$products = getProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    
    <!-- Bootstrap 3.4.1 CSS Only (Fixes enlarged UI) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap-theme.min.css">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>

    <!-- Bootstrap Dialog CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.35.4/css/bootstrap-dialog.min.css">
    
    <!-- Custom POS CSS -->
    <link rel="stylesheet" href="posStyle.css?v=<?= time() ?>">
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-9 col-sm-9 col-md-9">
                <div class="searchImputContainer">
                    <input type="text" id="searchInput" placeholder="Search product name or scan barcode..." >
                    <div id="searchResultContainerMain"></div>
                </div>

                <div class="searchResultContainer">
                    <div class="row">
                        <?php foreach($products as $index => $product){ ?>
                        <div class="col-xs-4 col-sm-4 col-md-4 productColContainer" data-pid="<?= $product['Product_ID']?>">
                            <div class="productResultContainer">
                                <img src="uploads/<?= $product['Image']?>" class="productImage" alt="">
                                <div class="productInfoContainer">    
                                    <div class="row">
                                        <div class="col-xs-8 col-md-8">
                                            <p class="productName"><?= $product['ProductName']?></p>
                                        </div>
                                        <div class="col-xs-4 col-md-4">
                                            <p class="productPrice">PHP: <?= $product['StorePrice']?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>        
                        </div>
                        <?php } ?>      
                    </div>   
                </div>       
            </div>

            <div class="col-xs-3 col-sm-3 col-md-3 posOrderContainer">
                <div class="pos_header">
                    <div class="setting alignRight">
                        <a href="javascript:void(0);"><i class="fa fa-gear"></i></a>
                    </div>
                    <p class="logo">IMS</p>
                    <p class="timeAndDate">XX XX,XXXX XX:XX:XX XX</p>
                </div>
                <div class="pos_items_container">
                    <div class="pos_items">
                        <p class="itemNoData">No Data</p>
                    </div>
                    <div class="item_total_container">
                        <p class="item_total">
                            <span class="item_total--label">TOTAL</span>
                            <span class="item_total--value">PHP: 0.00</span>
                        </p>
                    </div>            
                </div>
                <div class="checkoutBtnContainer">
                    <a href="javascript:void(0);" class="checkoutBtn">CHECKOUT</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    let productsJson = <?= json_encode($products) ?>;
    var products = {};

 

    productsJson.forEach((product) => {
    let pid = product.Product_ID;

    // Use available stock count directly from the database response
    let availableStock = product.CurrentStock !== undefined 
        ? parseInt(product.CurrentStock) 
        : (product.Quantity !== undefined 
            ? parseInt(product.Quantity) 
            : (parseInt(product.UnitOrdered || 0) - parseInt(product.UnitSold || 0)));

        products[pid] = {
            name: product.ProductName,
            stock: availableStock,
            price: parseFloat(product.StorePrice),
            barcode: product.Barcode || ''
        };
    });

    // Global Scanner Detection Variables
    let barcodeBuffer = '';
    let lastKeyTime = 0;
    var typingTimer;
    var doneTypingInterval = 400;

    // INTERCEPT SCANNER KEYS GLOBALLY (Prevents page shifts and unwanted auto-focusing)
    document.addEventListener('keydown', function(ev) {
        let activeEl = document.activeElement;
        let currentTime = new Date().getTime();
        let timeDiff = currentTime - lastKeyTime;
        lastKeyTime = currentTime;

        // 1. IF USER IS MANUALLY TYPING INSIDE SEARCH BAR
        if (activeEl && activeEl.id === 'searchInput') {
            if (ev.key === 'Enter') {
                ev.preventDefault();
                let code = activeEl.value.trim();
                if (code.length > 0) {
                    processBarcodeAdd(code);
                    activeEl.value = '';
                    document.getElementById('searchResultContainerMain').style.display = 'none';
                }
            }
            return; // Allow normal text typing inside input
        }

        // 2. IF SCANNER IS OPERATING IN BACKGROUND (UNFOCUSED)
        
        // Hardware scanners enter characters < 30ms apart
        if (ev.key === 'Enter' || ev.key === 'Tab') {
            if (barcodeBuffer.length > 2) {
                ev.preventDefault(); // Stop scanner from focusing the search bar or jumping UI
                if (activeEl && typeof activeEl.blur === 'function') {
                    activeEl.blur(); // Unfocus any element
                }
                processBarcodeAdd(barcodeBuffer.trim());
                barcodeBuffer = '';
                return;
            }
        }

        // Reset buffer if delay > 100ms (indicates a human typing, not a scanner)
        if (timeDiff > 100) {
            barcodeBuffer = '';
        }

        // Buffer readable alphanumeric characters
        if (ev.key.length === 1) {
            barcodeBuffer += ev.key;
        }
    }, true); // Use capture phase to intercept before browser default behavior

    // Live Search Keyup (Manual typing inside search bar)
    document.getElementById('searchInput').addEventListener('keyup', function(ev) {
        if (ev.key !== 'Enter') {
            let searchTerm = this.value;
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                searchDb(searchTerm);
            }, doneTypingInterval);
        }
    });

    // Live Search Function
    function searchDb(searchTerm) {          
        let searchResult = document.getElementById('searchResultContainerMain');

        if (searchTerm.length) {          
            searchResult.style.display = 'block';
            $.ajax({
                type: 'GET',
                data: { search_term: searchTerm },
                url: 'pos_live-search.php',
                dataType: 'json',
                success: function(response) {
                    if (!response.data || response.data.length === 0) {
                        searchResult.innerHTML = '<p class="nodatafound">no data found</p>';
                    } else {
                        let html = '';
                        response.data.forEach((row) => {
                            html += `       
                                <div class="row searchResultEntry" data-pid="${row['Product_ID']}">
                                    <div class="col-xs-3">                                            
                                        <img class="searchResultImg" src="uploads/${row['Image']}" alt="">                                        
                                    </div>
                                    <div class="col-xs-9">
                                        <p class="searchResultProductName">${row['ProductName']}</p>
                                        <p class="searchResultProductPrice">
                                            ${(!row['CurrentStock'] || row['CurrentStock'] <= 0) ? '<span style="color:red;">Out of Stock</span>' : '<span style="color:green;">Available </span>' + row['CurrentStock']}
                                        </p>                                            
                                        <p class="searchResultProductPrice">Price: PHP: ${row['StorePrice']}</p>
                                        <p class="searchResultProductPrice">BARCODE: ${row['Barcode']}</p>
                                    </div>                                    
                                </div>`;
                        });
                        searchResult.innerHTML = html;
                    }
                }
            });
        } else {
            searchResult.style.display = 'none';
        }
    }

    // Process and add item into IMS
    function processBarcodeAdd(scannedCode) {
    let inputEl = document.getElementById('searchInput');
    let searchResult = document.getElementById('searchResultContainerMain');

    scannedCode = String(scannedCode).trim();
    let foundPid = null;

    // Exact Barcode Matching
    for (let pid in products) {
        let b = String(products[pid].barcode || '').trim();
        if (b !== '' && b === scannedCode) {
            foundPid = pid;
            break;
        }
    }

    // Exact Product ID Fallback
    if (!foundPid && products[scannedCode]) {
        foundPid = scannedCode;
    }

    if (foundPid) {
        let p = products[foundPid];

        // Do not clear search input if item is out of stock
        if (p.stock <= 0) {
            // loadScript.dialogError(`Product "${p.name}" is Out of Stock.`);
            loadScript.dialogError(`Product: <strong>(${p.name})</strong> is Out of Stock`);
            return;
        }

        // Add item to IMS sidebar
        if (typeof script !== 'undefined' && script.addToOrder) {
            script.addToOrder(p, foundPid, 1);
        } else if (typeof loadScript !== 'undefined' && loadScript.addToOrder) {
            loadScript.addToOrder(p, foundPid, 1);
        }

        // Clear input only on success
        if (inputEl) inputEl.value = '';
        if (searchResult) searchResult.style.display = 'none';

    } else {
        alert(`Product with Barcode / ID "${scannedCode}" not found.`);
    }
    }
    </script>

    <!-- Dependencies -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.35.4/js/bootstrap-dialog.min.js"></script>
    <script src="posScript.js?v=<?= time() ?>"></script>
</body>
</html>