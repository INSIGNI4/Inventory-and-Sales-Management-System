
<?php


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../connect.php';
include_once 'product_test.php';

$products = getProducts();

$currentUserName = 'Unknown User';
$userId = (int) ($_SESSION['user_id'] ?? 0);

if ($userId > 0 && isset($conn) && $conn instanceof mysqli) {
    $stmt = $conn->prepare(
        "SELECT userName FROM users WHERE id = ? LIMIT 1"
    );

    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->bind_result($userName);

        if ($stmt->fetch() && trim((string) $userName) !== '') {
            $currentUserName = trim($userName);
        }

        $stmt->close();
    }
}









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
    <link rel="stylesheet" href="posStyle_test.css?v=<?= time() ?>">
    <!-- Font Awesome v6.5.1 CDN -->
    

        <!-- Font Awesome 6.5.1 -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
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
                                <img src="../uploads/<?= $product['Image']?>" class="productImage" alt="">
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
                    

                    <div style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        gap: 8px;
                        flex-wrap: wrap;
                        margin-bottom: 10px;
                    ">


                    <?php
                    $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                    if (in_array($accountType, ['manager', 'super admin'], true)) {
                    ?>
                    <!-- <a class="nav-link na-btn-add" href="../homepage2.php">INVENTORY</a> -->
                    <!-- <button class="nav-link na-btn-return" onclick="window.location.href='POS_TEST/pos_test.php'"></button> -->


                    <a href="../homepage2.php"
                    class="pos-header-icon"
                    title="Return to Inventory"
                    
                    aria-label="Return to Inventory">
                        <!-- <i class="fa-solid fa-rotate-left" aria-hidden="true"></i> -->
                        <i class="fa-solid fa-right-from-bracket fa-flip-horizontal"></i>
                        
                    </a>

                    <?php } ?>    


                

                        <div style="display: flex; gap: 6px; align-items: center;">

                            <button type="button"
                                    id="pauseCashierShiftBtn"
                                    class="btn btn-warning"
                                    style="font-weight: bold; display: none;">
                                <i class="fa-solid fa-pause"></i> Pause Shift
                            </button>

                            <button type="button"
                                    id="resumeCashierShiftBtn"
                                    class="btn btn-success"
                                    style="font-weight: bold; display: none;">
                                <i class="fa-solid fa-play"></i> Resume Shift
                            </button>


                            <button type="button"
                                    id="closeCashierShiftBtn"
                                    class="btn na-btn-add"
                                    style="font-weight: bold; display: none;"">
                                Close Shift
                            </button>
                                
                            <a href="logout.php"
                                class="pos-header-icon"
                                title="Log Out"
                                aria-label="Log Out"
                                

                                
                                onclick="return confirm('Are you sure you want to log out?');">
                                    <!-- <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> -->
                                    <!-- <i class="fa-solid fa-right-from-bracket"></i> -->
                                    <i class="fa-solid fa-door-open"></i>
                            </a>
                        </div>
                    </div>

                    

                    <div style="
                        color: white;
                        font-weight: bold;
                        text-align: center;
                        margin-bottom: 8px;
                    ">
                    

               
                




                        Cashier: <?= htmlspecialchars($currentUserName, ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="setting alignRight"></div>

                    <img src="../sidebarlogo.png"
                        alt="Lobo 2 Celle Logo"
                        style="height: 140px; width: 235px; padding: 0 0 0.6rem;">

                    <p class="timeAndDate">XX XX,XXXX XX:XX:XX XX</p>
                    <span id="currentShiftInfo"
                        
                            style="color: white; font-weight: bolder; font-size: 15px;">
                        </span>
                </div>
                

                <div class="pos_items_container" >
                    <div class="pos_items">
                        <p class="itemNoData">No Data</p>
                    </div>
           
                </div>
                    
                <div class="checkoutBtnContainer" >
                    <div class="item_total_container">
                        <p class="item_total">
                            <span class="item_total--label">TOTAL</span>
                            <span class="item_total--value">PHP: 0.00</span>
                        </p>
                    </div> 
                    <a href="javascript:void(0);" class="checkoutBtn">CHECKOUT</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    let productsJson = <?= json_encode($products) ?>;
    var products = {};

    // Map database fields using Product_ID
    // productsJson.forEach((product) => {
    //     let pid = product.Product_ID;
    //     products[pid] = {
    //         name: product.ProductName,
    //         stock: parseInt(product.UnitOrdered || 0) - parseInt(product.UnitSold || 0),
    //         price: parseFloat(product.StorePrice),
    //         barcode: product.Barcode || ''
    //     };
    // });

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
        // if (ev.key.length === 1) {
        //     barcodeBuffer += ev.key;
        // }
        if (typeof ev.key === 'string' && ev.key.length === 1) {
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
                                        <img class="searchResultImg" src="../uploads/${row['Image']}" alt="">                                        
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






    <!-- Cashier Opening Shift Modal -->
    <div class="modal fade"
        id="openCashierShiftModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="openCashierShiftTitle"
        aria-hidden="true"
        data-backdrop="static"
        data-keyboard="false">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header" style="display: flex !important; justify-content: space-between !important; align-items: center; width: 100%;">
                    <h5 class="modal-title" id="openCashierShiftTitle" style="margin: 0;"> Open Cashier Shift </h5>
                    <?php 
                    $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                    if (in_array($accountType, ['manager', 'super admin'], true)) { 
                    ?>
                        <a href="../homepage2.php" class="pos-header-icon" title="Return to Inventory" style="color: black; margin-left: auto;" aria-label="Return to Inventory">
                            <i class="fa-solid fa-right-from-bracket fa-flip-horizontal"></i>
                        </a>
                    <?php } ?>
                    <?php 
                    $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                    if (in_array($accountType, ['cashier'], true)) { 
                    ?>
                        <a href="logout.php"
                                class="pos-header-icon"
                                title="Log Out"
                                style="color: black; margin-left: auto;"
                                aria-label="Log Out"
                                
                                onclick="return confirm('Are you sure you want to log out?');">
                                    <!-- <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> -->
                                    <!-- <i class="fa-solid fa-right-from-bracket"></i> -->
                                    <i class="fa-solid fa-door-open"></i>
                        </a>
                        
                    <?php } ?>
                </div>



                

                <div class="modal-body">
                    <p>
                        Count the starting cash in your drawer before beginning
                        transactions.
                    </p>

                    <form id="openCashierShiftForm">
                        <div class="form-group">
                            <label for="openingFloat">Opening Cash Float (₱)</label>
                            <input
                                type="number"
                                class="form-control"
                                id="openingFloat"
                                name="opening_float"
                                min="500"
                                max="99999999.99"
                                step="0.01"
                                value="0.00"
                                required
                            >
                            <small class="form-text text-muted">
                                Enter the actual starting cash amount.
                            </small>
                        </div>

                        <div id="openShiftMessage"
                            class="alert alert-danger d-none"
                            role="alert"></div>

                        <button type="submit"
                                id="openCashierShiftBtn"
                                class="btn btn-primary btn-block">
                            Open Shift
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>


    

    <div class="modal fade" id="closeCashierShiftModal" tabindex="-1"
        role="dialog" aria-labelledby="closeCashierShiftTitle"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="closeCashierShiftTitle">
                        Close Cashier Shift
                    </h4>
                </div>

                <div class="modal-body">
                    <p>Count the physical cash in your drawer before closing.</p>

                    <div id="closeShiftSummary" class="well">
                        <p>Opening Float: <strong id="closeOpeningFloat">₱0.00</strong></p>
                        <p>Cash Sales: <strong id="closeShiftSales">₱0.00</strong></p>
                        <p>Expected Cash: <strong id="closeExpectedCash">Calculated when closing</strong></p>
                    </div>

                    <form id="closeCashierShiftForm">
                        <div class="form-group">
                            <label for="actualCash">Actual Cash Count (₱)</label>
                            <input type="number"
                                id="actualCash"
                                name="actual_cash"
                                class="form-control"
                                min="0"
                                max="99999999.99"
                                step="0.01"
                                required>
                        </div>

                        <div id="closeShiftMessage"
                            class="alert alert-danger"
                            style="display:none;"
                            role="alert"></div>

                        <button type="submit"
                                id="confirmCloseShiftBtn"
                                class="btn btn-warning btn-block">
                            Confirm and Close Shift
                        </button>

                        <button type="button"
                                id="cancelCloseShiftBtn"
                                class="btn btn-default btn-block">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Interim Cash Count Modal -->
    <div class="modal fade"
        id="interimCashCountModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="interimCashCountTitle"
        data-backdrop="static"
        data-keyboard="false">

        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="interimCashCountTitle">
                        Pause Shift &amp; Count Cash
                    </h4>
                </div>

                <div class="modal-body">
                    <p>
                        Count the physical cash in the drawer. This is an
                        interim count; it will not close your shift.
                    </p>

                    <div class="well">
                        <p>
                            Shift:
                            <strong id="interimShiftId">—</strong>
                        </p>
                        <p>
                            Opening Float:
                            <strong id="interimOpeningFloat">₱0.00</strong>
                        </p>
                        <p>
                            Expected Cash:
                            <strong id="interimExpectedCash">
                                Calculating...
                            </strong>
                        </p>
                    </div>

                    <form id="interimCashCountForm">
                        <div class="form-group">
                            <label for="interimActualCash">
                                Actual Cash Count (₱)
                            </label>
                            <input type="number"
                                id="interimActualCash"
                                name="actual_cash"
                                class="form-control"
                                min="0"
                                max="99999999.99"
                                step="0.01"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="interimCashCountNotes">
                                Notes (optional)
                            </label>
                            <textarea id="interimCashCountNotes"
                                    name="notes"
                                    class="form-control"
                                    rows="3"
                                    maxlength="5000"
                                    placeholder="Explain any cash difference, if applicable."></textarea>
                        </div>

                        <div id="interimCashCountMessage"
                            class="alert alert-danger"
                            style="display:none;"
                            role="alert"></div>

                        <button type="submit"
                                id="saveInterimCashCountBtn"
                                class="btn btn-primary btn-block">
                            Save Cash Count
                        </button>

                        <button type="button"
                                id="cancelInterimCashCountBtn"
                                class="btn btn-default btn-block">
                            Cancel
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>    



    <script>

        $(function () {
            const shiftEndpoint = 'cashier_shift.php';


            let currentShift = null;
            let interimPauseRequested = false;

            function showShiftModal() {
                $('#openCashierShiftModal').modal('show');
            }

            function checkCashierShift() {
                $.ajax({
                    url: shiftEndpoint,
                    type: 'GET',
                    data: { action: 'status' },
                    dataType: 'json',

                    success: function (response) {
                        if (!response.success) {
                            $('#openShiftMessage')
                                .removeClass('d-none')
                                .text(response.message || 'Unable to check your shift.');
                            showShiftModal();
                            return;
                        }

                        if (!response.has_open_shift) {
                            showShiftModal();
                        }
                    },

                    // error: function (xhr) {
                    //     const message =
                    //         xhr.responseJSON?.message ||
                    //         'Unable to verify your shift. Refresh the page or log in again.';

                    //     $('#openShiftMessage')
                    //         .removeClass('d-none')
                    //         .text(message);

                    //     showShiftModal();
                    // }



                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Cashier shift status check failed');
                        console.error('HTTP status:', xhr.status);
                        console.error('Status text:', textStatus);
                        console.error('Error:', errorThrown);
                        console.error('Response:', xhr.responseText);

                        const message = xhr.responseJSON?.message ||
                            'Shift check failed (HTTP ' + xhr.status + '). Check F12 Console.';

                        $('#openShiftMessage')
                            .removeClass('d-none')
                            .text(message);

                        showShiftModal();
                    }

                });
            }

            $('#openCashierShiftForm').on('submit', function (event) {
                event.preventDefault();

                const openingFloat = $('#openingFloat').val();
                const $button = $('#openCashierShiftBtn');

                $('#openShiftMessage').addClass('d-none').text('');

                if (
                    openingFloat === '' ||
                    !Number.isFinite(Number(openingFloat)) ||
                    Number(openingFloat) < 0
                ) {
                    $('#openShiftMessage')
                        .removeClass('d-none')
                        .text('Enter a valid opening cash float.');
                    return;
                }

                $button.prop('disabled', true).text('Opening Shift...');

                $.ajax({
                    url: shiftEndpoint,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'open',
                        opening_float: openingFloat
                    },

                    success: function (response) {
                        if (!response.success) {
                            $('#openShiftMessage')
                                .removeClass('d-none')
                                .text(response.message || 'Unable to open shift.');
                            return;
                        }

                        // Reload so the POS starts with the newly opened shift.
                        window.location.reload();
                    },

                    error: function (xhr) {
                        const message =
                            xhr.responseJSON?.message ||
                            'Unable to open shift. Check the PHP error log.';

                        $('#openShiftMessage')
                            .removeClass('d-none')
                            .text(message);
                    },

                    complete: function () {
                        $button.prop('disabled', false).text('Open Shift');
                    }
                });
            });

            checkCashierShift();




            // Display the Close Shift button only when this cashier has an open shift.
            $('#closeCashierShiftBtn').hide();

            // Refresh shift status and update the toolbar.
            const originalCheckCashierShift = checkCashierShift;

            function refreshCashierShiftStatus() {
                $.ajax({
                    url: shiftEndpoint,
                    type: 'GET',
                    data: { action: 'status' },
                    dataType: 'json',

                    // success: function (response) {
                    //     if (!response.success) {
                    //         $('#closeCashierShiftBtn').hide();
                    //         return;
                    //     }

                    //     if (response.has_open_shift && response.shift) {
                    //         $('#closeCashierShiftBtn').show();

                    //         const openingFloat =
                    //             Number(response.shift.Opening_Float || 0);

                    //         $('#currentShiftInfo').text(
                    //             'Shift #' + response.shift.Shift_ID +
                    //             ' | Opening Float: ₱' + openingFloat.toFixed(2)
                    //         );

                    //         $('#closeOpeningFloat').text(
                    //             '₱' + openingFloat.toFixed(2)
                    //         );
                    //     } else {
                    //         $('#closeCashierShiftBtn').hide();
                    //         $('#currentShiftInfo').text('');
                    //     }
                    // },

                                


                    success: function (response) {
                        if (!response.success) {
                            currentShift = null;
                            $('#closeCashierShiftBtn, #pauseCashierShiftBtn').hide();
                            return;
                        }

                        if (response.has_open_shift && response.shift) {
                            currentShift = response.shift;

                            const shift = response.shift;
                            const isPaused = Number(shift.Is_Paused) === 1;
                            const openingFloat = Number(shift.Opening_Float || 0);

                            $('#closeCashierShiftBtn').toggle(!isPaused);
                            $('#pauseCashierShiftBtn').toggle(!isPaused);
                            $('#resumeCashierShiftBtn').toggle(isPaused);

                            $('#currentShiftInfo').text(
                                'Shift #' + shift.Shift_ID +
                                ' | Opening Float: ₱' + openingFloat.toFixed(2) +
                                (isPaused ? ' | PAUSED — Cash count required' : '')
                            );

                            $('#closeOpeningFloat').text(
                                '₱' + openingFloat.toFixed(2)
                            );

                            if (isPaused && interimPauseRequested) {
                                interimPauseRequested = false;
                                openInterimCashCountModal(shift);
                            }
                        } else {
                            currentShift = null;
                            $('#closeCashierShiftBtn, #pauseCashierShiftBtn').hide();
                            $('#currentShiftInfo').text('');
                        }
                    },                    

                    error: function () {
                        console.error('Unable to refresh cashier shift status.');
                    }
                });
            }

            // Open the closing modal.
            $('#closeCashierShiftBtn').on('click', function () {
                $('#closeShiftMessage')
                    .hide()
                    .text('');

                $('#actualCash').val('');

                $('#closeCashierShiftModal').modal('show');
            });

            // Cancel closing without changing the shift.
            $('#cancelCloseShiftBtn').on('click', function () {
                $('#closeCashierShiftModal').modal('hide');
            });

            // Submit the physical cash count to the server.
            $('#closeCashierShiftForm').on('submit', function (event) {
                event.preventDefault();

                const rawActualCash = $('#actualCash').val();
                const actualCash = Number(rawActualCash);
                const $button = $('#confirmCloseShiftBtn');

                if (
                    rawActualCash === '' ||
                    !Number.isFinite(actualCash) ||
                    actualCash < 0 ||
                    actualCash > 99999999.99
                ) {
                    $('#closeShiftMessage')
                        .text('Enter a valid physical cash count.')
                        .show();
                    return;
                }

                if (!window.confirm(
                    'Are you sure you want to close this shift? ' +
                    'Make sure the physical cash count is correct.'
                )) {
                    return;
                }

                $('#closeShiftMessage').hide().text('');
                $button.prop('disabled', true).text('Closing Shift...');

                $.ajax({
                    url: shiftEndpoint,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'close',
                        actual_cash: rawActualCash
                    },

                    success: function (response) {
                        if (!response.success) {
                            $('#closeShiftMessage')
                                .text(response.message || 'Unable to close the shift.')
                                .show();
                            return;
                        }

                        const variance = Number(response.cash_variance);
                        const varianceLabel =
                            (variance > 0 ? '+' : '') + '₱' + variance.toFixed(2);

                        window.alert(
                            'Shift closed successfully.\n\n' 
                            // 'Shift ID: ' + response.shift_id + '\n' +
                            // 'Opening Float: ₱' + response.opening_float + '\n' +
                            // 'Cash Sales: ₱' + response.shift_sales + '\n' +
                            // 'Expected Cash: ₱' + response.expected_cash + '\n' +
                            // 'Actual Cash: ₱' + response.actual_cash + '\n' +
                            // 'Cash Variance: ' + varianceLabel
                        );

                        window.location.reload();
                    },

                    error: function (xhr) {
                        const message =
                            xhr.responseJSON?.message ||
                            'Unable to close the shift. Check the browser console and PHP error log.';

                        $('#closeShiftMessage').text(message).show();
                    },

                    complete: function () {
                        $button
                            .prop('disabled', false)
                            .text('Confirm and Close Shift');
                    }
                });
            });

            // Refresh the toolbar status after the page loads.
            refreshCashierShiftStatus();



            function showInterimCashCountMessage(message) {
                $('#interimCashCountMessage')
                    .text(message)
                    .show();
            }

            function openInterimCashCountModal(shift) {
                $('#interimShiftId').text(shift.Shift_ID);
                $('#interimOpeningFloat').text(
                    '₱' + Number(shift.Opening_Float || 0).toFixed(2)
                );

                // Do not calculate expected cash from the browser.
                // The server will return the authoritative amount.
                $('#interimExpectedCash').text(
                    'Will be calculated by the server when saved.'
                );

                $('#interimActualCash').val('');
                $('#interimCashCountNotes').val('');
                $('#interimCashCountMessage').hide().text('');

                $('#interimCashCountModal').modal('show');
            }

            // Pause the shift first, then show the cash-count modal.
            $('#pauseCashierShiftBtn').on('click', function () {
                if (!currentShift || Number(currentShift.Is_Paused) === 1) {
                    return;
                }

                if (!window.confirm(
                    'Pause this shift now? Checkout will be unavailable until ' +
                    'you save the interim cash count and resume the shift.'
                )) {
                    return;
                }

                const $button = $(this);
                $button.prop('disabled', true).text('Pausing...');

                $.ajax({
                    url: shiftEndpoint,
                    type: 'POST',
                    dataType: 'json',
                    data: { action: 'pause' },

                    success: function (response) {
                        if (!response.success) {
                            window.alert(response.message || 'Unable to pause the shift.');
                            return;
                        }

                        interimPauseRequested = true;
                        refreshCashierShiftStatus();
                    },

                    error: function (xhr) {
                        window.alert(
                            xhr.responseJSON?.message ||
                            'Unable to pause the shift. Check the PHP error log.'
                        );
                    },

                    complete: function () {
                        $button.prop('disabled', false)
                            .html('<i class="fa-solid fa-pause"></i> Pause Shift');

                        refreshCashierShiftStatus();
                    }
                });
            });

            // Save the interim cash count.
            $('#interimCashCountForm').on('submit', function (event) {
                event.preventDefault();

                const rawCash = $('#interimActualCash').val();
                const actualCash = Number(rawCash);
                const $button = $('#saveInterimCashCountBtn');

                if (
                    rawCash === '' ||
                    !Number.isFinite(actualCash) ||
                    actualCash < 0 ||
                    actualCash > 99999999.99
                ) {
                    showInterimCashCountMessage('Enter a valid physical cash count.');
                    return;
                }

                if (!window.confirm(
                    'Save this physical cash count? Check the amount before confirming.'
                )) {
                    return;
                }

                $('#interimCashCountMessage').hide().text('');
                $button.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: shiftEndpoint,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'save_cash_count',
                        actual_cash: rawCash,
                        notes: $('#interimCashCountNotes').val()
                    },

                    success: function (response) {
                        if (!response.success) {
                            showInterimCashCountMessage(
                                response.message || 'Unable to save the cash count.'
                            );
                            return;
                        }

                        $('#interimExpectedCash').text(
                            '₱' + Number(response.expected_cash).toFixed(2)
                        );

                        const variance = Number(response.cash_variance);
                        const varianceText =
                            (variance > 0 ? '+' : '') + '₱' + variance.toFixed(2);

                        window.alert(
                            'Interim cash count saved.\n\n' 
                            // 'Expected Cash: ₱' + response.expected_cash + '\n' +
                            // 'Actual Cash: ₱' + response.actual_cash + '\n' +
                            // 'Variance: ' + varianceText
                        );

                        // Close the count form, but keep the shift paused
                        // until the cashier explicitly chooses to resume.
                        $('#interimCashCountModal').modal('hide');

                        $('#resumeCashierShiftBtn').show();
                        $('#pauseCashierShiftBtn').hide();

                        $('#currentShiftInfo').text(
                            'Shift #' + response.shift_id +
                            ' | PAUSED — Cash count saved'
                        );
                    },

                    error: function (xhr) {
                        showInterimCashCountMessage(
                            xhr.responseJSON?.message ||
                            'Unable to save the cash count. Check the PHP error log.'
                        );
                    },

                    complete: function () {
                        $button.prop('disabled', false).text('Save Cash Count');
                    }
                });
            });

            // Cancel the count form without resuming the shift.
            // $('#cancelInterimCashCountBtn').on('click', function () {
            //     if (window.confirm(
            //         'Leave the shift paused without resuming? Checkout will remain unavailable.'
            //     )) {
            //         $('#interimCashCountModal').modal('hide');
            //         refreshCashierShiftStatus();
            //     }
            // });






            // Cancel the interim count and restore the shift to active.
            $('#cancelInterimCashCountBtn').on('click', function () {
                if (!window.confirm(
                    'Cancel the cash count and resume the shift without saving a count?'
                )) {
                    return;
                }

                const $button = $(this);
                $button.prop('disabled', true).text('Cancelling...');

                $.ajax({
                    url: shiftEndpoint,
                    type: 'POST',
                    dataType: 'json',
                    data: { action: 'cancel_pause' },

                    success: function (response) {
                        if (!response.success) {
                            showInterimCashCountMessage(
                                response.message || 'Unable to cancel the pause.'
                            );
                            return;
                        }

                        // Prevent a pending status refresh from reopening the modal.
                        interimPauseRequested = false;

                        $('#interimCashCountModal').modal('hide');

                        window.alert(
                            response.message ||
                            'Cash count cancelled. Your shift has resumed.'
                        );

                        refreshCashierShiftStatus();
                    },

                    error: function (xhr) {
                        showInterimCashCountMessage(
                            xhr.responseJSON?.message ||
                            'Unable to cancel the pause. Check the PHP error log.'
                        );
                    },

                    complete: function () {
                        $button.prop('disabled', false).text('Cancel');
                    }
                });
            });






            // Resume only after the backend confirms that a count was saved.
            $('#resumeCashierShiftBtn').on('click', function () {
                if (!window.confirm(
                    'Resume this shift and allow checkout again?'
                )) {
                    return;
                }

                const $button = $(this);
                $button.prop('disabled', true).text('Resuming...');

                $.ajax({
                    url: shiftEndpoint,
                    type: 'POST',
                    dataType: 'json',
                    data: { action: 'resume' },

                    success: function (response) {
                        if (!response.success) {
                            window.alert(response.message || 'Unable to resume the shift.');
                            return;
                        }

                        $button.hide();
                        refreshCashierShiftStatus();
                        window.alert('Shift resumed. You can continue checkout.');
                    },

                    error: function (xhr) {
                        window.alert(
                            xhr.responseJSON?.message ||
                            'Unable to resume the shift. Check the PHP error log.'
                        );
                    },

                    complete: function () {
                        $button.prop('disabled', false).text('Resume Shift');
                    }
                });
            });





        });

    </script>

    <script src="posScript_test.js"></script>


</body>
</html>