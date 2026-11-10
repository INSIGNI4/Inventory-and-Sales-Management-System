<?php
session_start();

//    $GLOBALS['conn_pos'] = $conn_pos;


// 🚫 Prevent access if user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// 🧠 Prevent browser from showing cached version after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");



if (isset($_SESSION['response'])) {
    echo "<script>alert('" . $_SESSION['response']['message'] . "');</script>";
    unset($_SESSION['response']);



}




// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";


include('show_data.php');

$sales_item_test = fetchTableData($conn_pos, 'sales_item_test');
$sales_test = fetchTableData($conn_pos, 'sales_test');

$product = fetchTableData($conn, 'product');
$supplier = fetchTableData($conn, 'supplier');
$customers = fetchTableData($conn, 'customers');

// $sql = "
//     SELECT
//         r.*,
//         p.ProductName,
//         s.SupplierFName,
//         s.SupplierLName
//     FROM restock r
//     LEFT JOIN product p
//         ON r.Product_ID = p.Product_ID
//     LEFT JOIN supplier s
//         ON r.Supplier_ID = s.Supplier_ID
//     ORDER BY r.Orestock_ID DESC
// ";

// $result = $conn->query($sql);

// $restock = [];

// while ($row = $result->fetch_assoc()) {
//     $restock[] = $row;
// }


$restock = fetchTableData($conn, 'restock');


$customersreturns = fetchTableData($conn, 'customersreturns');
$supplierreturns = fetchTableData($conn, 'supplierreturns');
$sales = fetchTableData($conn, 'sales');
$transaction = fetchTableData($conn, 'transactions');
$forecast = fetchTableData($conn, 'forecast');

$itemToOrder = fetchTableData($conn, 'item_to_order');
$listToOrder = fetchTableData($conn, 'list_to_order');
$inventoryBatch = fetchTableData($conn, 'inventory_batch');

$pulledoutitems = fetchTableData($conn, 'pulledoutitems');
$salesaggregration = fetchTableData($conn, 'salesaggregration');
$newaddition = fetchTableData($conn, 'newaddition');
$inventory = fetchTableData($conn, 'inventory');

$dailysalesP = fetchTableData($conn, 'daily_sales');
$weeklysalesP = fetchTableData($conn, 'weekly_sales');
$monthlysalesP = fetchTableData($conn, 'monthly_sales');

$dailyforecast = fetchTableData($conn, 'daily_forecast');
$weeklyforecast = fetchTableData($conn, 'weekly_forecast');
$monthlyforecast = fetchTableData($conn, 'monthly_forecast');

$dailysalesE = fetchTableData($conn, 'daily_total_sales');
$weeklysalesE = fetchTableData($conn, 'weekly_total_sales');
$monthlysalesE = fetchTableData($conn, 'monthly_total_sales');





$analytics = fetchTableData($conn, 'analytics');

// $users = fetchTableData($conn, 'users');


if (!is_array($sales_item_test)) {
    $sales_item_test = [];  
}

if (!is_array($sales_test)) {
    $sales_test = [];  
}

if (!is_array($product)) {
    $product = [];  
}

if (!is_array($supplier)) {
    $supplier = [];  
}

if (!is_array($customers)) {
    $customers = [];  
}

if (!is_array($restock)) {
    $restock = [];  
}

if (!is_array($customersreturns)) {
    $customersreturns = [];  
}

if (!is_array($supplierreturns)) {
    $supplierreturns = [];  
}

if (!is_array($sales)) {
    $sales = [];  
}

if (!is_array($transaction)) {
    $transaction = [];  
}

if (!is_array($forecast)) {
    $forecast = [];  
}





if (!is_array($itemToOrder)) {
    $itemToOrder = [];  
}

if (!is_array($listToOrder)) {
    $listToOrder = [];  
}

if (!is_array($inventoryBatch)) {
    $inventoryBatch = [];  
}




// if (!is_array($dailyforecast)) {
//     $dailyforecast = [];  
// }
// if (!is_array($weeklyforecast)) {
//     $weeklyforecast = [];  
// }
// if (!is_array($monthlyforecast)) {
//     $monthlyforecast = [];  
// }


if (!is_array($analytics)) {
    $analytics = [];  
}

if (!is_array($pulledoutitems)) {
    $pulledoutitems = [];  
}

if (!is_array($salesaggregration)) {
    $salesaggregration = [];  
}

if (!is_array($newaddition)) {
    $newaddition = [];  
}

if (!is_array($inventory)) {
    $inventory = [];  
}

if (!is_array($dailysalesP)) {
    $dailysalesP = [];  
}

if (!is_array($weeklysalesP)) {
    $weeklysalesP = [];  
}

if (!is_array($monthlysalesP)) {
    $monthlysalesP = [];  
}


if (!is_array($dailysalesE)) {
    $dailysalesE = [];  
}

if (!is_array($weeklysalesE)) {
    $weeklysalesE = [];  
}

if (!is_array($monthlysalesE)) {
    $monthlysalesE = [];  
}

// if (!is_array($users)) {
//     $users = [];  
// }





if(isset($_SESSION['email'])){
    $email=$_SESSION['email'];
    $query=mysqli_query($conn, "SELECT users.* FROM users WHERE users.email='$email'");

    $row=mysqli_fetch_array($query);
    
}


// Get all users/accounts
// $usersQuery = "SELECT id, userName, email, phone, accountType, image FROM users";

//  ACTIVE USER
$usersQuery = "
    SELECT id, userName, email, phone, accountType, image
    FROM users
    WHERE is_active = 1
    ORDER BY userName ASC
";

$usersResult = mysqli_query($conn, $usersQuery);

if (!$usersResult) {
    die("Users query failed: " . mysqli_error($conn));
}
// ACTIVE USER

// $usersResult = mysqli_query($conn, $usersQuery);

// if (!$usersResult) {
//     die("Users query failed: " . mysqli_error($conn));
// }








?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobo 2 Celle - Inventory Management System</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="homepagestyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- <meta http-equiv="refresh" content="5"> -->
    <link rel="stylesheet" href="ai_recommendation.css">

    <link rel="stylesheet" href="archive_restore.css">


    <!-- <script>
    setInterval(function() {
        location.reload('home');
    }, 8000); // Reloads every 5 seconds (5000 milliseconds)
    </script> -->

</head>
<body>





    <!-- ======================= MAIN DASHBOARD ======================= -->
    <div id="dashboard-container" class="dashboard-container" >
    
        <div class="sidebar"  id="sidebar">
            
            <img src="sidebarlogo.png" alt="Lobo 2 Celle Logo" style="height: 140px; width: 235px; padding: 0rem 0rem 0.6rem;">
            
            <div class="sidebar-nav" >
                <a class="nav-link" onclick="showContentSection('home')">Home</a>
                
                <a class="nav-link" onclick="showContentSection('inventory')">Inventory</a>
                <!-- <a class="nav-link" onclick="showContentSection('new-additions')">New Additions</a> -->
                <a class="nav-link" onclick="showContentSection('products')">Products</a>
                <a class="nav-link" onclick="showContentSection('suppliers')">Suppliers</a>
                <!-- <a class="nav-link" onclick="showContentSection('customer')">Customer</a> -->
                <a class="nav-link has-dropdown" onclick="showContentSection('returns')">Returns</a>

                <a class="nav-link" onclick="showContentSection('order-restock')">Restock</a>
                <a class="nav-link has-dropdown" onclick="showContentSection('transaction-sales')">Transaction & Sales</a>
                

                <!-- <a class="nav-link" onclick="showContentSection('sales-aggregation')">Sales Aggregation</a> -->
                <!-- <a class="nav-link has-dropdown" onclick="showContentSection('forecast-analytics')">Forecast & Analytics</a> -->

                <a class="nav-link" onclick="showContentSection('stock-adjustments')">Reports</a>
                
                
                <?php
                $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                if (in_array($accountType, ['super admin'], true)) {
                ?>
                    <a class="nav-link" onclick="showContentSection('account')">Account</a>
                <?php } ?> 



                <!-- REVISION TAX SETTING BTN -->

                <?php
                $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                if (in_array($accountType, ['manager'], true)) {
                ?>
                    <!-- <a class="nav-link" onclick="showContentSection('tax-settings')">
                        Tax Settings
                    </a> -->
                    <a class="nav-link" onclick="showContentSection('real-stock-adjustments')">Stock Adjustment</a>

                    

                        
                    <a class="nav-link" onclick="showContentSection('pricing-markup')">
                        Pricing &amp; Markup
                    </a>


                    <a class="nav-link" onclick="showContentSection('shift-review')">
                        Shift Review
                    </a>


                    <a class="nav-link" onclick="showContentSection('archive-restore')">
                        <i class="fa-solid fa-box-archive"></i> Archive &amp; Restore
                    </a>
                <?php } ?>     

               
                <a class="nav-link" href="POS_TEST/pos_test.php">POS</a>




                
                
                






                
            </div>
            <div class="sidebar-footer">
                <!-- <a class="footer-link" onclick="showContentSection('settings')"><span>⚙️</span> Settings</a> -->
                <!-- <a href="logout.php" class="footer-link" ><span>↪</span> Log Out</a> -->
                <!-- <a href="logout.php" class="footer-link" onclick="return confirmLogout(event)">
                <span>↪</span> Log Out
                </a> -->
                <a href="#" class="footer-link" id="logoutBtn">
                <span>↪</span> Log Out
                </a>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                document.getElementById("logoutBtn").addEventListener("click", function(event) {
                event.preventDefault(); // stop normal link behavior

                Swal.fire({
                    title: "Are you sure?",
                    text: "You’ll be logged out of your account.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, log me out",
                    cancelButtonText: "No, stay here"
                }).then((result) => {
                    if (result.isConfirmed) {
                    // Redirect to logout.php if user confirms
                    window.location.href = "logout.php";
                    }
                });
                });
                </script>


            </div>
        </div>

        <!-- NEW: Sidebar Toggle Button -->
        <div id="sidebar-toggle">«
        </div> <!-- end of #dashboard-container -->

        

        <div class="main-content">
            <div class="content-area" style="background:linear-gradient(to top,rgba(192, 190, 190, 1),rgba(110, 110, 110, 1));">
                <link rel="stylesheet" href="assets/dashboard.css">
                <link rel="stylesheet" href="dashboardAssets/analytics_dh.css">
                <link rel="stylesheet" href="dashboardAssets/forecast_dh.css">
                <link rel="stylesheet" href="dashboardAssets/topcustomer_dh.css">
                <link rel="stylesheet" href="dashboardAssets/summary.css">
                <link rel="stylesheet" href="dashboardAssets/stock_avail.css">
                <link rel="stylesheet" href="dashboardAssets/return.css ">
                <link rel="stylesheet" href="dashboardAssets/products_summary.css">
                <link rel="stylesheet" href="dashboardAssets/confidence.css">
                <link rel="stylesheet" href="dashboardAssets/projected.css">
                <link rel="stylesheet" href="ml_prediction-main/dashboardAssets/js/demand_dashboard.css">
                
                
                
                
                <div id="home" class="content-section" style=" margin: -25px;">
                    <div class="custom-header" style="background-image: url(topbarlogo.png);background-repeat: no-repeat; background-size: cover; height: 100px;">
                        <div class="top-bar">
                            <div class="tab">HOME</div>
                            <!-- <div class="user-controls">
                                <div>
                                <?php         

                                        echo $row['userName'];
                            
                                ?></div>
                                <div class="user-icon">👤</div>
                            </div> -->
                        </div>
                        <div class="home-header" style="display: flex; justify-content:space-between;font-weight:bolder;">


                        
                            <div style="display: flex;">
                            <p style="font-display: none; margin: 20px; font-size:30px;">Welcome Back!                                 
                            </p>
                            <p style="font-display: none; margin: 13px; margin-left: -5px; margin-top: 20px; font-size:30px; font-weight:bolder;color:beige;">
                            <?php
                                echo $row['userName'];
                            ?>
                            </p>
                            </div>
                            <div class="header-icons" style="margin-top: 20px; margin-right: 1rem;">
                                <button style="height: 30px;"
                                    type="button"
                                    class="na-btn na-btn-add"
                                    onclick="openAIRecommendationModal()">

                                    AI RECOMMENDATION

                                </button>
                                <!-- <span class="icon-wrapper"><i class="fas fa-print"></i></span> -->
                                <span class="icon-wrapper" onclick="openNotifications()"><i class="fas fa-envelope"></i><span class="badge">0</span></span>
                                <!-- <span class="icon-wrapper" onclick="openCalendarModal()"><i class="fa-regular fa-calendar-days"></i><span class="badge">2</span></span> -->
                            </div>


                        </div>

                    </div>

                        <div class="parent_dashboard" style="background-color: black;">
                            <!-- ANALYTICS DASHBOARD JS -->
                            <script src="https://code.highcharts.com/highcharts.js"></script>
                            <script src="https://code.highcharts.com/modules/exporting.js"></script>
                            <script src="https://code.highcharts.com/modules/export-data.js"></script>
                            <script src="https://code.highcharts.com/modules/accessibility.js"></script>
                            <script src="https://code.highcharts.com/themes/adaptive.js"></script>

                            <div class="analytics_dashboard">
                                <figure class="highcharts-figure">
                                    <div id="container"></div>
                                </figure>
                            </div>

                            <!-- FORECAST DASHBOARD JS -->
                            <!-- <script src="https://code.highcharts.com/highcharts.js"></script> -->
                            <script src="https://code.highcharts.com/modules/data.js"></script>
                            <script src="https://code.highcharts.com/modules/series-label.js"></script>
                            <script src="https://code.highcharts.com/modules/exporting.js"></script>
                            <script src="https://code.highcharts.com/modules/export-data.js"></script>
                            <script src="https://code.highcharts.com/modules/accessibility.js"></script>
                            <script src="https://code.highcharts.com/themes/adaptive.js"></script>

                            <div class="forecast_dashboard">
   
                                <figure class="highcharts-figure">
                                    <!-- <div id="container1"></div> -->

                                    <div style="position: relative;">
                                    <div id="salesChart"></div>
                                    <select id="forecastType" 
                                            style="
                                                position: absolute;
                                                top: 10px;
                                                left: 20px;
                                                z-index: 10;
                                                padding: 5px;
                                                border-radius: 6px;
                                            ">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                    </div>


                                    <!-- <div id="salesChart"></div> -->

                                    <!-- <script src="https://code.highcharts.com/highcharts.js"></script> -->
                                    <script>
                                    function formatPeriodLabel(type, period) {
                                    if (type === 'weekly') {
                                        // Example: 202542 → "Week 42 (2025)"
                                        const year = period.toString().substring(0, 4);
                                        const week = period.toString().substring(4);
                                        return `Week ${week} (${year})`;
                                    } else if (type === 'monthly') {
                                        // Example: 2025-10 → "Oct 2025"
                                        const [year, month] = period.split('-');
                                        const date = new Date(`${year}-${month}-01`);
                                        return date.toLocaleString('default', { month: 'short', year: 'numeric' });
                                    } else {
                                        // Daily: display as "Oct 16, 2025"
                                        const date = new Date(period);
                                        return date.toLocaleDateString('default', { month: 'short', day: 'numeric', year: 'numeric' });
                                    }
                                    }

                                    function loadForecast(type = 'daily') {
                                    fetch('get_sales_forecast.php?type=' + type)
                                        .then(res => res.json())
                                        .then(data => {
                                        if (!data.length) {
                                            document.getElementById('salesChart').innerHTML = '<p>No data available.</p>';
                                            return;
                                        }

                                        const actual = data.map(r => parseFloat(r.TotalQuantity));
                                        const forecast = data.map(r => parseFloat(r.MovingAverage3));
                                        const categories = data.map(r => formatPeriodLabel(type, r.Period));




                                        Highcharts.chart('salesChart', {
                                            chart: { type: 'line', backgroundColor: '#ffffff' },
                                            title: { 
                                            text: `Store Sales Forecast (${type.charAt(0).toUpperCase() + type.slice(1)} 3-Moving Average)` , 
                                                style: {
                                                    color: '#333333'
                                                }
                                            },
                                            subtitle: { text: 'Actual Sales vs Forecasted Trend',
                                                style: {
                                                    color: '#555555'
                                                }
                                             },
                                            xAxis: {
                                                categories: categories,
                                                title: { 
                                                    text: type === 'daily' ? 'Date' : type === 'weekly' ? 'Week' : 'Month',
                                                    style: {
                                                        color: '#333333'
                                                    }
                                                },
                                                labels: {
                                                    style: {
                                                        color: '#333333'
                                                    }
                                                }
                                            },
                                            yAxis: {
                                                title: { 
                                                    text: 'Total Quantity Sold',
                                                    style: {
                                                        color: '#333333'
                                                    }
                                                },
                                                labels: {
                                                    style: {
                                                        color: '#333333'
                                                    }
                                                }
                                            },
                                            legend: {
                                                layout: 'horizontal',
                                                align: 'center',
                                                verticalAlign: 'bottom',
                                                itemStyle: {
                                                    color: '#333333'
                                                }
                                            },
                                            tooltip: { shared: true, valueSuffix: ' units' },
                                            series: [
                                            { name: 'Actual Sales', data: actual, color: '#007bff' },
                                            { name: '3-Moving Average', data: forecast, dashStyle: 'ShortDot', color: '#28a745' }
                                            ]
                                        });
                                        });
                                    }

                                    document.getElementById('forecastType').addEventListener('change', function() {
                                    loadForecast(this.value);
                                    });

                                    loadForecast(); // load daily by default
                                    </script>
                                    
                                </figure>
                            </div>

                          


                            <div class="return_dashboard">
                                <div class="return_header">
                                    <h3>Sales Performance <span id="sales-month">(MONTH)</span></h3>
                                </div>

                                <div class="return_content">
                                    <p>
                                        <b>Total Sales:</b>
                                        <span id="sales-total"></span>
                                    </p>

                                    <p>
                                        <b>Transactions:</b>
                                        <span id="sales-transactions"></span>
                                    </p>

                                    <p>
                                        <b>Units Sold:</b>
                                        <span id="sales-units"></span>
                                    </p>

                                    <p>
                                        <b>Average Sale:</b>
                                        <span id="sales-average"></span>
                                    </p>
                                </div>

                            </div>





                            <div class="products_dashboard">
                                <div class="products_header">
                                    <h3>Product</h3>
                                    <div class="product-controls">
                                        <select id="time-select">
                                            <option value="daily">Daily</option>
                                            <option value="weekly" selected>Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                        <select id="sort-select">
                                            <option value="Bestseller" selected>Bestseller</option>
                                            <option value="alphabetical">A–Z</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="product-categories">
                                    <button data-type="All" class="active">All</button>
                                    <button data-type="Noodles and Instant Meals">Noodles and Instant Meals</button>
                                    <button data-type="Frozen Hotpot Goods">Frozen Hotpot Goods</button>
                                    <button data-type="Side Dishes">Side Dishes</button>
                                    <button data-type="Canned Goods">Canned Goods</button>
                                    <button data-type="Beverages">Beverages</button>
                                    <button data-type="Sweets and Snacks">Sweets and Snacks</button>
                                    <button data-type="Ice Cream">Ice Cream</button>
                                    <button data-type="Beauty & Personal Care">Beauty & Personal Care</button>

                                </div>

                                <table class="product_table">
                                    <tbody id="products-body"></tbody>
                                </table>
                            </div>

                            <div class="confidence_dashboard">
                                <div class="confidenceInterval">
                                    <select id="confiType" 
                                        style="
                                            position: absolute;
                                            top: 10px;
                                            left: 20px;
                                            z-index: 10;
                                            padding: 5px;
                                            border-radius: 6px;
                                        ">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>

                                    <div class="confidence-header">
                                        <p>Confidence Level:</p>
                                        <span id="confidence-level">--</span>%
                                    </div>

                                    <div class="interval-row">
                                        <div class="interval-value">
                                            Interval: <span id="confidence-interval">--</span>
                                        </div>

                                        <div class="week-box">
                                            <div class="week-up">
                                                Week Sales ⬆: <span id="week-upper">--</span>
                                            </div>
                                            <div class="week-down">
                                                Week Sales ⬇: <span id="week-lower">--</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const confiType = document.getElementById('confiType');

                                function fetchConfidenceData(type = 'daily') {
                                    fetch(`get_confidence.php?type=${type}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            if (data.error) {
                                                console.error(data.error);
                                                return;
                                            }

                                            document.getElementById('confidence-level').textContent = `${data.confidenceLevel}%`;
                                            document.getElementById('confidence-interval').textContent = data.interval;
                                            document.getElementById('week-upper').textContent = data.upper;
                                            document.getElementById('week-lower').textContent = data.lower;
                                        })
                                        .catch(err => console.error('Error fetching confidence data:', err));
                                }

                                fetchConfidenceData(confiType.value);

                                confiType.addEventListener('change', function() {
                                    fetchConfidenceData(confiType.value);
                                });
                            });
                            </script>

                            
                            <div class="projected_dashboard">
                                <div class="projected-sales">
                                    <h3>Projected Sales: <span id="projected-value"></h3>
                                    <select id="salesType" style="
                                            position: absolute;
                                            top: 10px;
                                            left: 20px;
                                            z-index: 10;
                                            padding: 5px;
                                            border-radius: 6px;
                                        ">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>

                                    <!-- <p><b>Projected:</b> --</span></p> -->
                                    <p><b>Current:</b> <span id="current-value">--</span></p>

                                    <div class="progress_bar">
                                        <div id="progress-fill" class="progress_fill"></div>
                                    </div>

                                    <p><span id="progress-percent">0%</span></p>
                                </div>
                            </div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    const typeSelect = document.getElementById('salesType');
                                    loadProjectedSales(typeSelect.value);

                                    typeSelect.addEventListener('change', function() {
                                        loadProjectedSales(this.value);
                                    });
                                });

                                function loadProjectedSales(type) {
                                    fetch(`get_sales_forecast.php?type=${type}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            if (!data.length) return;

                                            const latest = data[data.length - 1];
                                            const projected = parseFloat(latest.MovingAverage3);
                                            const current = parseFloat(latest.TotalQuantity);
                                            const percent = projected > 0 ? ((current / projected) * 100).toFixed(1) : 0;

                                            document.getElementById('projected-value').textContent = projected.toLocaleString();
                                            document.getElementById('current-value').textContent = current.toLocaleString();
                                            document.getElementById('progress-percent').textContent = percent + '%';

                                            const fill = document.getElementById('progress-fill');
                                            fill.style.width = percent + '%';
                                        })
                                        .catch(err => console.error('Error fetching projected sales:', err));
                                }
                            </script>


                            <div class="totalproduct_dashboard card">
                                <div class="card-title">Total Products</div>
                                <div class="card-body">
                                    <div class="card-value" id="total-products">0</div>
                                </div>

                                <div class="card-footer" id="update-products">Update --</div>
                            </div>

                            <div class="growth_dashboard card">
                                <div class="card-title">Growth Rate</div>
                                <div class="card-body">
                                    <div class="card-value" id="growth-rate">0%</div>
                                </div>

                                <div class="card-footer" id="update-growth">Update --</div>
                                </div>

                            <div class="totalsold_dashboard card">
                                <div class="card-title">Total Sold</div>
                                <div class="card-body">
                                    <div class="card-value" id="total-sold">0</div>
                                </div>
                                <div class="card-footer" id="update-sold">Update --</div>
                            </div>

                            <div class="totalcustomer_dashboard card">
                                <div class="card-title">Total Transactions</div>
                                <div class="card-body">
                                    <div class="card-value" id="total-transactions">0</div>
                                </div>

                                <div class="card-footer" id="update-transactions">Update --</div>
                            </div>

                            <div class="stock_dashboard">
                                <div class="stock_header">
                                    <div class="card-title">Stock Availability</div>
                                </div>

                                <div class="stock-row">
                                    <div class="stock-left">
                                        <div class="stock-total">
                                            <div>Total Stock</div>
                                            <div id="total-stock" class="stock-number">0</div>
                                        </div>
                                        <canvas id="myChart" width="200" height="200"></canvas>
                                        <div class="stock-legend">
                                            <span><span class="dot green"></span> Availability</span>
                                            <span><span class="dot yellow"></span> Low Stock</span>
                                            <span><span class="dot red"></span> Out of Stock</span>
                                        </div>
                                    </div>

                                    <div class="stock-right" id="low-stock-list">
                                        <h3>Low Stock</h3>
                                        <ul></ul>
                                    </div>
                                </div>
                            </div>
                                      
                            

                            <!-- END OF DASHBOARD -->
                        </div>

                            <!-- AI DEMAND PREDICTION DASHBOARD -->

                            <div class="demand_prediction_dashboard">

                                <div class="demand_header">
                                    <h2>AI Demand Prediction</h2>
                                    <p>14-Day Forecast</p>
                                </div>



                                <div class="demand_summary">

                                    <div class="demand_kpi">
                                        <h4>Forecast Period</h4>
                                        <strong id="forecastPeriod">14 Days</strong>
                                    </div>

                                    <div class="demand_kpi">
                                        <h4>Products Forecasted</h4>
                                        <strong id="productsForecasted">0</strong>
                                    </div>

                                    <div class="demand_kpi">
                                        <h4>Total Predicted Demand</h4>
                                        <strong id="totalPredictedDemand">0</strong>
                                        <span>Units</span>
                                    </div>

                                    <div class="demand_kpi">
                                        <h4>Peak Forecast Day</h4>
                                        <strong id="peakForecastDay">--</strong>
                                        <span id="peakForecastValue">0 units</span>
                                    </div>

                                </div>




                                <div class="demand_charts">

                                    <div class="demand_chart_box">
                                        <h3>Predicted Demand by Product</h3>
                                        <div class="demand_canvas">
                                            <canvas id="productChart"></canvas>
                                        </div>
                                    </div>

                                    <div class="demand_chart_box">
                                        <h3>Predicted Demand by Category</h3>
                                        <div class="demand_canvas">
                                            <canvas id="categoryChart"></canvas>
                                        </div>
                                    </div>

                                    <div class="demand_chart_box demand_trend_box">
                                        <h3>14-Day Predicted Demand Trend</h3>
                                        <div class="demand_canvas">
                                            <canvas id="trendChart"></canvas>
                                        </div>
                                    </div>

                                </div>

                            </div>


                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                        <!-- ORIGINAL PARENT DASHBOARD JS -->
                        <script src="dashboardAssets/stock_avail.js"></script>
                        <script src="dashboardAssets/summary.js"></script>
                        <!-- <script src="dashboardAssets/forecast_dh.js"></script> -->
                        <script src="dashboardAssets/analytics_dh.js"></script>
                        <!-- <script src="dashboardAssets/topcustomer_dh.js"></script> -->
                        <script src="dashboardAssets/return.js"></script>
                        <script src="dashboardAssets/products_summary.js"></script>

                        <!-- AI DEMAND DASHBOARD -->
                        <script src="ml_prediction-main/dashboardAssets/js/demand_dashboard.js"></script>

                        <script>
                            loadDemandChart("productChart", "product");
                            loadDemandChart("categoryChart", "category");
                            loadDemandChart("trendChart", "trend");
                        </script>

                </div>

                <div id="inventory" class="content-section">
                    <div class="custom-header sticky-div-1" style="background-image: url(topbarlogo.png);background-repeat: no-repeat;background-size: cover; 
                    height: 80px;">
                        <div class="top-bar">
                            <div class="tab">INVENTORY</div>
                            <div class="user-controls">
                                <div>
                                <?php
                                    echo $row['userName'];
                                ?>
                                </div>
                                <div class="user-icon">👤</div>
                            </div>
                        </div>
                        <div class="controls-bar">
                            <div class="controls">
                            </div>

                            <div class="header-icons">
                                <!-- <span class="icon-wrapper"><i class="fas fa-print"></i></span> -->
                                <span class="icon-wrapper" onclick="openNotifications()"><i class="fas fa-envelope"></i><span class="badge"></span></span>
                                <!-- <span class="icon-wrapper" onclick="openCalendarModal()"><i class="fa-regular fa-calendar-days"></i><span class="badge">2</span></span> -->
                            </div>
                        </div>

                    </div>

                    <div class="toolbar sticky-div-2" style="display: flex; justify-content:space-between;">
                        <div>
                            
                                <?php 
                                $totalRows = count($inventory);
                                ?>
                                <span id="products-count" class="na-count"><?= $totalRows ?></span>
                                <!-- <span style="background-color: cadetblue; padding: .5rem;"><?= $totalRows ?></span> -->
                                <!-- <span class="in-stock-label">In Stock: </span> -->
                            
                        </div>
                        <div style="display: flex; justify-content:space-between;" >
                            <!-- <div class="center-controls"><button class="last-updated-button">🔄 Last Updated</button></div> -->
                            <!-- <div class="na-quick-search">🔍 Quick Search</div> -->
                        </div>
                    </div>
                    <div class="table-container">
                        <table class="inventory-table">

                            <thead class="sticky-div">
                            <tr>
                                
                                <th>Product Name</th>
                                <th colspan="2">Location</th>
                                <th>Inventory</th>
                                <th>Status</th>
                                <th>Supplier</th>
                                
                                
                            </tr>
                            </thead>
                            <tbody>


                                <?php 
                                usort($inventory, function ($a, $b) {
                                    return $a['Inventory'] <=> $b['Inventory'];
                                }
                                );

                                foreach($inventory as $index => $inventory){ ?>
                                    <tr>
                                    <!-- <td><?= $index + 1?></td> -->
                                   <td class="product-id-cell">
                                        <?php
                                        foreach ($product as $prod) {
                                            if ($prod['Product_ID'] == $inventory['Product_ID']) {
                                                echo htmlspecialchars($prod['ProductName']);
                                                break;
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td><span class="Location-tag shelf"><?= $inventory['LocationS']?></span></td>
                                    <td><span class="Location-tag row"><?= $inventory['LocationR']?></span></td>
                                    <!-- <td><?= $inventory['Price']?></td> -->
                                    <td><?= $inventory['Inventory'] <= 0 ? 0 : $inventory['Inventory'] ?></td>
                                    
                                    <td><span class="inventory-status status-<?= strtolower($inventory['Status']) ?>">
                                                                        <?= htmlspecialchars($inventory['Status']) ?>
                                        </span>
                                    
                                    
                                    </td>
                                    <!-- <td style="color: red;"><?= empty($inventory['ExpirationDate']) ? '- - - N/A - - -' : date('F d, Y', strtotime($inventory['ExpirationDate'])) ?></td> -->
                                    <td style="font-weight: bolder;"><?= $inventory['SupplierName'], "  (",$inventory['City'] , ")" ?></td>
                                    

                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                
                

                <!--- PRODUCTS SECTION --->
                <div id="products" class="content-section" >
                    <div class="custom-header sticky-div-1" style="background-image: url(topbarlogo.png);background-repeat: no-repeat;background-size: cover;
                    height: 80px;">
                        <div class="top-bar">
                            <div class="tab">PRODUCTS</div>
                            <div class="user-controls">
                                <div>
                                <?php
                                    echo $row['userName'];
                                ?>
                                </div>
                                <div class="user-icon">👤</div>
                            </div>
                        </div>

                        <div class="controls-bar">
                            <div class="controls">
                                <!-- <div class="control-group"><label>📊 Group by:</label><select><option></option></select></div>
                                <div class="control-group"><label>⇅ Sort by:</label><select><option></option></select></div>
                                <button class="status-button">STATUS</button> -->
                                <!-- <div class="stock-info"><span>30</span><span class="in-stock-label">In Stock</span></div> -->
                            </div>
                            <div class="header-icons">
                                <!-- <span class="icon-wrapper"><i class="fas fa-print"></i></span> -->
                                <span class="icon-wrapper" onclick="openNotifications()"><i class="fas fa-envelope"></i><span class="badge">0</span></span>
                                <!-- <span class="icon-wrapper" onclick="openCalendarModal()"><i class="fa-regular fa-calendar-days"></i><span class="badge">2</span></span> -->
                            </div>
                        </div>
                    </div>


                    <div class="toolbar sticky-div-2" style="display: flex; justify-content:space-between;">
                        <div>
                        <?php 
                        $totalRows = count($product);
                        ?>
                    
                        <!-- <input type="checkbox"> -->

                        <span id="products-count" class="na-count"><?= $totalRows ?></span>
                        <?php
                            $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                            if (in_array($accountType, ['manager'], true)) {
                            ?>
                        

                        <button id="edit-product-btn" class="na-btn na-btn-add1" type="button"><i class="fa-solid fa-pen-to-square"></i></button>
                        <!-- <button id="delete-product-btn" class="na-btn na-btn-add1" type="button"><i class="fa-solid fa-trash"></i></button> -->
                        <!-- <span class="na-icon-btn">📝</span>
                        <span class="na-icon-btn">🗑️</span> -->
                        <!-- <span class="na-icon-btn">🖨️</span> -->
                        <button id="products-add-btn" class="na-btn na-btn-add" type="button">ADD</button>
                        <?php } ?>
                        </div>
                        <!-- <div>
                            <div class="na-quick-search">🔍 Quick Search</div>
                        </div>    -->
                        
                        <div class="search-container" style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                        <input 
                            type="text" 
                            id="quickSearch" 
                            placeholder="🔍 Quick Search..." 
                            style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc; width: 250px;">
                        </div>


                        <div id="addProductModal" class="modal">
                            <div class="modal-content">
                                <h3>Add New Product</h3>
                                <form id="productForm" action="add.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="table" value="product">
                                    <!-- <label>Product ID:</label>
                                    <input type="text" name="Product_ID" required> -->

                                    <label>Product Name:</label>
                                    <input type="text" name="ProductName" required>

                                    <!-- <label>Type:</label>
                                    <input type="text" name="Type" required> -->




                                    <!-- <label>Reorder Points:</label>
                                    <input type="number" name="ReordingPoints" required> -->

                                    
                                    <input type="hidden" name="UnitsOrdered" value="0">

                                    
                                    <input type="hidden" name="UnitSold" value="0" >

                                    <!-- <label>Supplier Price (PHP):</label>
                                    <input type="number" name="SupplierPrice" required> -->

                                    <label>Store Price (PHP):</label>
                                    <input type="number" name="StorePrice" required>



                                    <label>Image:</label>
                                    <!-- <input type="file" id="profile-upload" accept="image/*" style="display: none;"> -->
                                    <input type="file" accept="image/*" name="Image">

                                    <!-- <label>Supplier ID:</label>
                                    <input type="text" name="Supplier_ID" required> -->

                                    <label>Type:</label>
                                    <div style="display: flex; justify-content:space-between; align-items:center;">
                                
                                    <select class="type-select" name="Type" required>
                                        <option value="NONE">...</option>
                                        <option value="Noodles and Instant Meals">Noodles and Instant Meals</option>
                                        <option value="Frozen Hotpot Goods">Frozen Hotpot Goods</option>
                                        <option value="Side Dishes">Side Dishes</option>
                                        <option value="Canned Goods">Canned Goods</option>
                                        <option value="Beverages">Beverages</option>
                                        <option value="Sweets and Snacks">Sweets and Snacks</option>
                                        <option value="Ice Cream">Ice Cream</option>
                                        <option value="Beauty & Personal Care">Beauty & Personal Care</option>
                                        
                                    </select>
                                    </div>

                                    <!-- <label for="Supplier_ID">Choose Supplier ID:</label>
                                    <select id="Supplier_IDPROD" name="Supplier_ID" class="id-select" style="width: 60%;" required>
                                        <option value="" disabled selected>Loading...</option>
                                    </select> -->

                                    <!-- <label>With Expiration? :</label>
                                    <div style="display: flex; justify-content:space-between; align-items:center;">
                                    

                                    <label>Expiration Date:
                                        <button type="button" id="toggleNullBtn" style="color: red;">NO ?</button>
                                        <button type="button" id="toggleNotNullBtn" style="display: none;">YES ?</button>
                                    </label>
                                    <input type="date" name="ExpirationDate" id="expiration_date" style="width: 50%;">
                                    </div> -->

                                    <!-- <label>Expiration Date:</label>
                                    <input type="date" name="ExpirationDate"> -->

                                    <label>Pack Size:</label>
                                    <input type="number" name="Pack_Size" required>

                                    <label>Barcode:</label>
                                    <input type="text" name="Barcode" required>



                                    <label>Location:</label>
                                    <div style="display: flex; justify-content:space-between; align-items:center;">
                                    <label>Shelf:</label>
                                    <select class="shelf-row-select" name="LocationS" required>
                                        <option value="Shelf A">Shelf A</option>
                                        <option value="Shelf B">Shelf B</option>
                                        <option value="Shelf C">Shelf C</option>
                                        <option value="Shelf D">Shelf D</option>
                                    </select>
                                    
                                    <!-- <input type="text" name="LocationS" required> -->

                                    <label>Row:</label>

                                    <select class="shelf-row-select" name="LocationR" required>
                                        <option value="Row A">Row A</option>
                                        <option value="Row B">Row B</option>
                                        <option value="Row C">Row C</option>
                                        <option value="Row D">Row D</option>
                                    </select>
                                    <!-- <input type="text" name="LocationR" required> -->
                                    </div>

                                    

                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">Save</button>
                                        <button type="button" id="cancel-btn" class="na-btn na-btn-cancel">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <link rel="stylesheet" href="assets/add_product.css">
                        <script src="assets/add_product.js" defer></script>
                        <script src="get/get_supplierPROD.js" defer></script>
                        

                    

                        
                        <div id="editProductModal" class="modal">
                            <div class="modal-content">
                                <h3>Edit Product</h3>
                                <form action="update.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="table" value="product">
                                    <input type="hidden" name="id" id="edit-id">
                                    
                                    <label>Product Name:</label>
                                    <input type="text" name="ProductName" id="edit-name">

                                    <!-- <label>Type:</label>
                                    <input type="text" name="Type" id="edit-type"> -->

                                    <label>Type:</label>
                                    <div style="display: flex; justify-content:space-between; align-items:center;">
                                    <select class="type-select" name="Type"  id="edit-type" required>
                                   
                                        <option value="NONE">...</option>
                                        <option value="Noodles and Instant Meals">Noodles and Instant Meals</option>
                                        <option value="Frozen Hotpot Goods">Frozen Hotpot Goods</option>
                                        <option value="Side Dishes">Side Dishes</option>
                                        <option value="Canned Goods">Canned Goods</option>
                                        <option value="Beverages">Beverages</option>
                                        <option value="Sweets and Snacks">Sweets and Snacks</option>
                                        <option value="Ice Cream">Ice Cream</option>
                                        <option value="Beauty & Personal Care">Beauty & Personal Care</option>
                                        
                                    </select>
                                    </div>

                                    <!-- <label>Reorder Points:</label>
                                    <input type="number" name="ReordingPoints" id="edit-reorder"> -->

                                    <!-- <label>Ordered:</label>
                                    <input type="number" name="UnitsOrdered" id="edit-unitsordered"> -->

                                    <!-- <label>Sold:</label>
                                    <input type="number" name="UnitSold" id="edit-sold"> -->

                                    <!-- <label>Supplier Price (PHP):</label>
                                    <input type="number" name="SupplierPrice" id="edit-supplierprice"> -->

                                    <label>Store Price (PHP):</label>
                                    <input type="number" name="StorePrice" id="edit-storeprice">



                                    <label>Image:</label>
                                    <!-- <input type="file" id="profile-upload" accept="image/*" style="display: none;"> -->
                                    <input type="file" accept="image/*" name="Image" id="edit-image">

                                    <!-- <label>Supplier ID:</label>
                                    <input type="text" name="Supplier_ID" id="edit-supplierid"> -->

                                    <!-- <label for="Supplier_ID">Choose Supplier ID:</label>
                                    <select id="edit-supplierid" name="Supplier_ID" class="id-select" style="width: 100%;" required>
                                        <option disabled selected>Loading...</option>
                                    </select> -->
                                    

                                    <!-- <label>Expiration Date:</label>
                                    <input type="date" name="ExpirationDate" id="edit-expirationdate"> -->

                                    <!-- <label>Expiration Date:
                                        <button type="button" id="toggleNullBtn1">NULL?</button>
                                        <button type="button" id="toggleNotNullBtn1" style="display: none;">NOT NULL?</button>
                                    </label>
                                    <input type="date" name="ExpirationDate" id="edit-expirationdate"required> -->

                                    <label>Pack Size:</label>
                                    <input type="number" name="Pack_Size" id="edit-packsize" required>    

                                    <label>Barcode:</label>
                                    <input type="text" name="Barcode" id="edit-barcode" required>

                                    
                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">Save</button>
                                        <button type="button" id="editprod-cancel-btn" class="na-btn na-btn-cancel">Cancel</button>
                                    </div>
                                </form>
                            </div>
                            <link rel="stylesheet" href="assets/add_product.css">
                            <script src="assets/edit_product.js"></script>
                            <script src="get/edit_supplierPROD.js"></script>
                        </div>

                    </div>
                
                    <div class="table-container products-table-container">
                        <table class="products-table">
                            <thead class="sticky-div">
                                <tr>
                                    <!-- <th>#</th> -->
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Type</th>
                                    <!-- <th>Reorder Points</th> -->
                                    <th>Ordered</th>
                                    <th>Sold</th>
                                    <th>Price</th>
                                    <!-- <th>Supplier Price</th> -->
                                    <th>Image</th>
                                    <!-- <th>Supplier</th> -->
                                    <th>Pack Size</th>
                                    <th>Barcode</th>
                                    <th colspan="2">Location</th>
                                    <th>       </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($product as $index => $products){ ?>
                                    <tr>
                                        <!-- <td><?= $index + 1?></td> -->
                                        <td class="product-id-cell"><?= $products['Product_ID']?></td>
                                        <td><?= $products['ProductName']?></td>
                                        <td><span class="type-tag-purple"><?= $products['Type']?></span></td>
                                        <!-- <td><?= $products['ReordingPoints']?></td> -->
                                        <td class="units-ordered"><?= $products['UnitsOrdered']?></td>
                                        <td class="units-sold"><?= $products['UnitSold']?></td>
                                        <td><span style="color: darkgreen; font-weight: bolder; float:left">PHP: </span><?= $products['StorePrice']?></td>
                                        <!-- <td><span style="color: darkgreen; font-weight: bolder; float:left">PHP: </span><?= $products['SupplierPrice']?></td> -->
                                        <td>
                                            <?php if (!empty($products['Image']) && file_exists("uploads/" . $products['Image'])): ?>
                                                <a href="uploads/<?= $products['Image']?>" target="_blank">
                                                    <img src="uploads/<?= $products['Image']?>" alt="Product Image" style="max-width: 50px; max-height: 50px; object-fit: cover;">
                                                </a>
                                            <?php else: ?>
                                                No Image    
                                            <?php endif; ?>
                                        </td>
                                        <!-- <td class="supplier-id-cell"><?= $products['SupplierName']?></td> -->
                                        <!-- <td style="color: red;"><?= empty($products['ExpirationDate']) ? '- - - N/A - - -' : date('F d, Y', strtotime($products['ExpirationDate'])) ?></td> -->
                                        <!-- <td><?= date('F d,Y', strtotime($products['ExpirationDate']))?></td> -->
                                        <td><?= $products['Pack_Size']?></td>
                                        <td><?= $products['Barcode']?></td>
                                        <td><span class="Location-tag shelf"><?= $products['LocationS']?></span></td>
                                        <td><span class="Location-tag row"><?= $products['LocationR']?></span></td>
                                    
                                            <!-- data-reorder="<?= $products['ReordingPoints'] ?>" -->
                                             <!-- data-unitsordered="<?= $products['UnitsOrdered'] ?>" -->
                                              <!-- data-expirationdate="<?= $products['ExpirationDate'] ?>" -->
                                        <td class="action-cell" >
                                            <!-- edit & delete action -->
                                            <button style="display: none;" class="select-product-btn na-btn na-btn-edit"
                                                data-id="<?= $products['Product_ID'] ?>"
                                                data-name="<?= $products['ProductName'] ?>"
                                                data-type="<?= $products['Type'] ?>"
                                                
                                                
                                                data-unitsold="<?= $products['UnitSold'] ?>"
                                                
                                                data-storeprice="<?= $products['StorePrice'] ?>"
                                                data-packsize="<?= $products['Pack_Size'] ?>"
                                                
                                                data-barcode="<?= $products['Barcode'] ?>">
                                                
                                                <!-- data-supplierid="<?= $products['Supplier_ID'] ?>"> -->
                                                
                                                
                                                📝 Edit
                                            </button>
                                            <!-- data-supplierprice="<?= $products['SupplierPrice'] ?>" -->
                                            <!-- data-storeprice="<?= $products['StorePrice'] ?>" -->

                                            <form style="display: none;" id="display-delete" action="delete.php" class="delete-product-form" method="POST" style="display: inline;">
                                                <input type="hidden" name="table" value="product">
                                                <input type="hidden" name="id" value="<?= $products['Product_ID']?>">
                                                <button type="submit"  onclick="return confirm('Delete this product?')">🗑️ Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>


                    <script>
                    document.getElementById("quickSearch").addEventListener("keyup", function() {
                        const filter = this.value.toLowerCase();
                        const rows = document.querySelectorAll(".products-table tbody tr");

                        rows.forEach(row => {
                            const rowText = row.innerText.toLowerCase();
                            row.style.display = rowText.includes(filter) ? "" : "none";
                        });
                    });
                    </script>





                </div>

                <div id="suppliers" class="content-section">
                    <div class="custom-header sticky-div-1" style="background-image: url(topbarlogo.png);background-repeat: no-repeat;background-size: cover;
                    height: 80px;">
                        <div class="top-bar">
                            <div class="tab">SUPPLIERS</div>
                            <div class="user-controls">
                                <div>
                                <?php
                                    echo $row['userName'];
                                ?>
                                </div>
                                <div class="user-icon">👤</div>
                            </div>
                        </div>
                        <div class="controls-bar">
                            <div class="controls">
                                <!-- <div class="control-group"><label>📊 Group by:</label><select></select></div>
                                <div class="control-group"><label>⇅ Sort by:</label><select><option>SUPPLIER ID</option></select></div> -->
                            </div>
                            <div class="header-icons">
                                <!-- <span class="icon-wrapper"><i class="fas fa-print"></i></span> -->
                                <span class="icon-wrapper" onclick="openNotifications()"><i class="fas fa-envelope"></i><span class="badge">0</span></span>
                                <!-- <span class="icon-wrapper" onclick="openCalendarModal()"><i class="fa-regular fa-calendar-days"></i><span class="badge">2</span></span> -->
                            </div>
                        </div>
                    </div>
                    
                                   

                    <div class="toolbar sticky-div-2" style="display: flex; justify-content:space-between;">
                        <div>
                            <?php 
                            $totalRows = count($supplier);
                            ?>

                            <span id="products-count" class="na-count"><?= $totalRows ?></span>
                            <?php
                            $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                            if (in_array($accountType, ['manager'], true)) {
                            ?>

                            <button id="edit-supplier-btn" class="na-btn na-btn-add1" type="button">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            <!-- <button id="delete-supplier-btn" class="na-btn na-btn-add1" type="button">
                                <i class="fa-solid fa-trash"></i>
                            </button> -->


                            <button id="sup-add-btn" class="na-btn na-btn-add" type="button">ADD</button>
                            <?php } ?>
                        </div>

                        <div>
                            <!-- <div class="na-quick-search">🔍 Quick Search</div> -->
                        </div>      


                        <div id="addSupplierModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <h3>Add New Supplier</h3>
                                <form id="supplierForm" action="add.php" method="POST">
                                    <input type="hidden" name="table" value="supplier">

                                   



                                    <label>Company Name:</label>
                                    <input
                                        type="text"
                                        name="CompanyName"
                                        style="text-transform: uppercase;"
                                        required
                                    >

                                    <label>Contact Person:</label>
                                    <input
                                        type="text"
                                        name="ContactPerson"
                                        style="text-transform: uppercase;"
                                        required
                                    >

                                    <label>Address:</label>
                                    <input
                                        type="text"
                                        name="Address"
                                        style="text-transform: uppercase;"
                                        required
                                    >

                                    <label>City:</label>
                                    <input
                                        type="text"
                                        name="City"
                                        id="add-supplier-city"
                                        style="text-transform: uppercase;"
                                        required
                                    >

                                    <label>Email:</label>
                                    <input
                                        type="email"
                                        name="Email"
                                        style="text-transform: lowercase;"
                                        required
                                    >

                                    <label>Phone Number:</label>
                                    <input
                                        type="tel"
                                        name="PhoneNumber"
                                        required
                                    >

                                    <label>Product Category:</label>
                                    <select
                                        class="shelf-row-select"
                                        name="ProductCategory"
                                        style="width: max-content;"
                                        required
                                    >
                                        <option value="">Select a category</option>
                                        <option value="Noodles and Instant Meals">Noodles and Instant Meals</option>
                                        <option value="Frozen Hotpot Goods">Frozen Hotpot Goods</option>
                                        <option value="Side Dishes">Side Dishes</option>
                                        <option value="Canned Goods">Canned Goods</option>
                                        <option value="Beverages">Beverages</option>
                                        <option value="Sweets and Snacks">Sweets and Snacks</option>
                                        <option value="Ice Cream">Ice Cream</option>
                                        <option value="Beauty & Personal Care">Beauty &amp; Personal Care</option>
                                    </select>                                    






                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">Save</button>
                                        <!-- <button type="button" id="cancel-btn" class="na-btn na-btn-cancel close-modal-btn">Cancel</button> -->
                                        <button type="button" id="sup-cancel-btn" class="na-btn na-btn-cancel">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <link rel="stylesheet" href="assets/add_product.css">
                        <script src="assets/add_supplier2.js" defer></script>




                        <div id="editSupplierModal" class="modal" style="display: none;">
                            <div class="modal-content">

                                <h3>Edit Supplier</h3>

                                <form action="update.php" method="POST">

                                    <input type="hidden" name="table" value="supplier">
                                    <input type="hidden" name="id" id="edit-supplier-id">

                                    





                                    <label>Company Name:</label>
                                    <input
                                        type="text"
                                        name="CompanyName"
                                        id="edit-supplier-company"
                                        style="text-transform: uppercase;"
                                        required
                                    >

                                    <label>Contact Person:</label>
                                    <input
                                        type="text"
                                        name="ContactPerson"
                                        id="edit-supplier-contact"
                                        style="text-transform: uppercase;"
                                        required
                                    >

                                    <label>Address:</label>
                                    <input
                                        type="text"
                                        name="Address"
                                        id="edit-supplier-address"
                                        style="text-transform: uppercase;"
                                        required
                                    >

                                    <label>City:</label>
                                    <input
                                        type="text"
                                        name="City"
                                        id="edit-supplier-city"
                                        style="text-transform: uppercase;"
                                        required
                                    >

                                    <label>Email:</label>
                                    <input
                                        type="email"
                                        name="Email"
                                        id="edit-supplier-email"
                                        style="text-transform: lowercase;"
                                        required
                                    >

                                    <label>Phone Number:</label>
                                    <input
                                        type="tel"
                                        name="PhoneNumber"
                                        id="edit-supplier-phone"
                                        required
                                    >

                                    <label>Product Category:</label>
                                    <select
                                        class="shelf-row-select"
                                        
                                        style="width: max-content;"

                                        name="ProductCategory"
                                        id="edit-supplier-category"
                                        required
                                    >
                                        <option value="Noodles and Instant Meals">Noodles and Instant Meals</option>
                                        <option value="Frozen Hotpot Goods">Frozen Hotpot Goods</option>
                                        <option value="Side Dishes">Side Dishes</option>
                                        <option value="Canned Goods">Canned Goods</option>
                                        <option value="Beverages">Beverages</option>
                                        <option value="Sweets and Snacks">Sweets and Snacks</option>
                                        <option value="Ice Cream">Ice Cream</option>
                                        <option value="Beauty & Personal Care">Beauty &amp; Personal Care</option>
                                        <option value="NONE">NONE</option>
                                    </select>



                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">
                                            Save Changes
                                        </button>

                                        <button
                                            type="button"
                                            id="cancel-edit-supplier-btn"
                                            class="na-btn na-btn-cancel"
                                        >
                                            Cancel
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>

                        <script src="assets/edit_supplier.js" defer></script>

                        





                    </div>

                    <div class="table-container suppliers-table-container">
                        <table class="suppliers-table">
                           <thead class="sticky-div">
                                <tr>
                                    
                                    <th>Supplier ID</th>
                                    <th>Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Address</th>
                                    <th>City</th>
                                    <th>Product Category</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th class="supplier-action-header" style="display: none;">Action</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php foreach($supplier as $index => $supplier){ ?>
                                    <tr>
                                        <!-- <td><?= $index + 1?></td> -->
                                        





                                        <td class="supplier-id-cell">
                                            <?= htmlspecialchars((string)$supplier['Supplier_ID']) ?>
                                        </td>

                                        <td><?= htmlspecialchars($supplier['CompanyName'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($supplier['ContactPerson'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($supplier['Address'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($supplier['City'] ?? '') ?></td>

                                        <td>
                                            <span class="type-tag-purple">
                                                <?= htmlspecialchars($supplier['ProductCategory'] ?? '') ?>
                                            </span>
                                        </td>

                                        <td>
                                            <a href="mailto:<?= htmlspecialchars($supplier['Email'] ?? '') ?>"
                                            class="email-link">
                                                <?= htmlspecialchars($supplier['Email'] ?? '') ?>
                                            </a>
                                        </td>

                                        <td><?= htmlspecialchars($supplier['PhoneNumber'] ?? '') ?></td>

                                        <td class="supplier-action-cell" style="display: none;">
                                            <button
                                                type="button"
                                                class="supplier-row-edit-btn na-btn na-btn-edit"
                                                data-id="<?= htmlspecialchars((string)$supplier['Supplier_ID'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-company="<?= htmlspecialchars($supplier['CompanyName'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                data-contact="<?= htmlspecialchars($supplier['ContactPerson'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                data-address="<?= htmlspecialchars($supplier['Address'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                data-city="<?= htmlspecialchars($supplier['City'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                data-email="<?= htmlspecialchars($supplier['Email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                data-phone="<?= htmlspecialchars((string)($supplier['PhoneNumber'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                data-category="<?= htmlspecialchars($supplier['ProductCategory'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            >
                                                📝 Edit
                                            </button>
                                        </td>                                        
                                        


                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div id="returns" class="content-section">
                    <div class="custom-header sticky-div-1"  style="background-image: url(topbarlogo.png);background-repeat: no-repeat;background-size: cover;
                    height: 80px;">
                        <div class="top-bar">
                            <div class="tab">RETURNS</div>
                            <div class="user-controls">
                                <div>      
                                <?php
                                    echo $row['userName'];                        
                                ?>
                                </div>
                                <div class="user-icon">👤</div>
                            </div>
                        </div>
                        <div class="controls-bar">

                            <div class="controls">
                                <!-- <div class="control-group"><label>📊 Group by:</label><select></select></div>
                                <div class="control-group"><label>⇅ Sort by:</label><select><option>SUPPLIER ID</option></select></div> -->
                                <div class="toggle-buttons">
                                    <button id="customer-returns-btn" class="toggle-btn active">Customer</button>
                                    <button id="supplier-returns-btn" class="toggle-btn ">Supplier</button>
                                </div>
                            </div>

                            <div class="header-icons">
                                <!-- <span class="icon-wrapper"><i class="fas fa-print"></i></span> -->
                                <span class="icon-wrapper" onclick="openNotifications()"><i class="fas fa-envelope"></i><span class="badge">0</span></span>
                                <!-- <span class="icon-wrapper" onclick="openCalendarModal()"><i class="fa-regular fa-calendar-days"></i><span class="badge">2</span></span> -->
                            </div>



                        </div>
                    </div>
                    <!-- <div class="toolbar">
                        <?php 
                        $totalRows = count($customers);
                        ?>


                        <span id="products-count" class="na-count"><?= $totalRows ?></span> 

                        <span class="na-icon-btn">📝</span>
                        <span class="na-icon-btn">🗑️</span>
                        <span class="na-icon-btn">🖨️</span>

                        
                        <button id="cusreturns-add-btn" class="na-btn na-btn-add" style="display: flex;">ADD</button>

                        <div>
                            <div class="na-quick-search">🔍 Quick Search</div>
                        </div>   -->



                    <div class="toolbar sticky-div-2" style="display: flex; justify-content:space-between;">
                        <div>
                            <?php 
                            $totalRows = count($customersreturns);
                            ?>
                            <span id="products-count" class="na-count">#</span>
                            


                            

                            <?php
                            $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                            if (in_array($accountType, ['manager'], true)) {
                            ?>
                            
                        

                            <!-- <span id="products-count" class="na-count"><?= $totalRows ?></span> -->
                            <button id="edit-cus-return-btn" class="na-btn na-btn-add1" type="button" title="Edit Customer Return">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            <button id="edit-sup-return-btn" class="na-btn na-btn-add1" type="button" title="Edit Supplier Return">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <!-- <button id="delete-product-btn" class="na-btn na-btn-add1" type="button"><i class="fa-solid fa-trash"></i></button> -->
                            <!-- <span class="na-icon-btn">🖨️</span> -->

                            <button id="cusreturns-add-btn" class="na-btn na-btn-add" style="display: flex;">ADD</button>
                            <button id="supreturns-add-btn" class="na-btn na-btn-add" style="display: flex;">ADD</button>

                            <?php } ?>
                        </div>

                        <div>
                            <!-- <div class="na-quick-search">🔍 Quick Search</div> -->
                        </div>  





                        
                        <div id="addCusReturnsModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <h3>Add New Customer Returns</h3>
                                <form id="cusReturnsForm" action="add.php" method="POST">
                                    <input type="hidden" name="table" value="customersreturns">

                                    <!-- <label>Customer ID:</label>
                                    <input type="text" name="Customer_ID"> -->

                                    <!-- <label for="Customer_IDRETURN">Choose Customer ID:</label>
                                    <select id="Customer_IDRETURN" name="Customer_ID" class="id-select" style="width: 60%;">
                                        
                                        <option value="" disabled selected>Loading...</option>
                                    </select> -->

                                    <label>ReferenceNo:</label>
                                    <!-- <input type="number" name="ReferenceNo" maxlength="11" required> -->
                                    <input type="text" name="ReferenceNo" inputmode="numeric" pattern="\d{1,11}" maxlength="11" style="width: 55%;" >

                                    <!-- <label>Product ID:</label>
                                    <input type="text" name="Product_ID" required> -->

                                    <label for="Product_IDCRETURN">Choose Product ID:</label>
                                    <select id="Product_IDCRETURN" name="Product_ID" class="id-select" style="width: 60%;" required>
                                        <option value="" disabled selected>Loading...</option>
                                    </select>

                                    <label>Quantity:</label>
                                    <input type="number" name="Quantity" style="width: 55%;" required>

                                    <!-- <label>Returned Date:</label>
                                    <input type="datetime-local" name="ReturnedDate" required> -->

                                    <!-- <label>Reason For Return:</label>
                                    <input type="text" name="ReasonForReturn" required> -->

                                    <label>Reason For Return:</label>
                                    <div style="display: flex; justify-content:space-between; align-items:center;">
                                    <select class="creturns-reason-select" name="ReasonForReturn" required>
                                        <option value="">-- Reason --</option>
                                        <option value="Wrong-Item">Wrong Item</option>
                                        <option value="Damaged">Damaged</option>
                                        <option value="Expired">Expired</option>
                                        <option value="Other">Other</option>
                                        <!-- <option value="Accidental">Accidental</option> -->
                                        <!-- <option value="Not-Match">Not Match</option> -->
                                        <!-- <option value="Faulty">Faulty</option> -->
                                        <!-- <option value="Missing-Parts">Missing Parts</option> -->
                                        <!-- <option value="Not-Fit">Not Fit</option> -->
                                        
                                    </select>
                                    
                                    
                                    </div>
                                    

                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">Save</button>
                                        <!-- <button type="button" id="cancel-btn" class="na-btn na-btn-cancel close-modal-btn">Cancel</button> -->
                                        <button type="button" id="cus-return-cancel-btn" class="na-btn na-btn-cancel">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <link rel="stylesheet" href="assets/add_product.css">
                        <script src="assets/add_cusreturn.js" defer></script>
                        <script src="get/get_customerRETURN.js" defer></script>
                        <script src="get/get_productCRETURN.js" defer></script>
                        
                        
                        <div id="addSupReturnsModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <h3>Add New Supplier Returns</h3>
                                <form id="supReturnsForm" action="add.php" method="POST">
                                    <input type="hidden" name="table" value="supplierreturns">

                                    <!-- <label>Supplier ID:</label>
                                    <input type="text" name="Supplier_ID" required> -->

                                    <label for="Supplier_IDSRETURN">Choose Supplier ID:</label>
                                    <select id="Supplier_IDSRETURN" name="Supplier_ID" class="id-select" style="width: 60%;" required>
                                        <option value="" disabled selected>Loading...</option>
                                    </select>

                                    <!-- <label>Product ID:</label>
                                    <input type="text" name="Product_ID" required> -->

                                    <label for="Product_IDSRETURN">Choose Product ID:</label>
                                    <select id="Product_IDSRETURN" name="Product_ID" class="id-select" style="width: 60%;" required>
                                        <option value="" disabled selected>Loading...</option>
                                    </select>

                                    <label>Quantity:</label>
                                    <input type="number" name="Quantity"  style="width: 55%;"required>

                                    <!-- <label>Returned Date:</label>
                                    <input type="datetime-local" name="ReturnedDate"> -->

                                    <!-- <label>Status:</label>
                                    <input type="text" name="Status" required> -->

                                    <label>Status:</label>
                                    <div style="display: flex; justify-content:space-between; align-items:center;">
                                    <select class="creturns-reason-select" name="Status" required>
                                        <option value="">-- Status --</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Out-for-Delivery">Out for Delivery</option>
                                        <option value="Cancelled">Cancelled</option>
                                        <option value="Delivered">Delivered</option>
                                        
                                    </select>
                                    </div>



                                    

                                    <!-- <label>Reason For Return:</label>
                                    <input type="text" name="Reason" required> -->

                                    <label>Reason For Return:</label>
                                    <div style="display: flex; justify-content:space-between; align-items:center;">
                                    <select class="creturns-reason-select" name="Reason" required>
                                        <option value="">-- Reason --</option>
                                        <option value="Wrong-Item">Wrong Item</option>
                                        <option value="Damaged">Damaged</option>
                                        <option value="Expired">Expired</option>
                                        <!-- <option value="Accidental">Accidental</option> -->
                                        <option value="Other">Other</option>
                                    </select>
                                    
                                    </div>

                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">Save</button>
                                        <!-- <button type="button" id="cancel-btn" class="na-btn na-btn-cancel close-modal-btn">Cancel</button> -->
                                        <button type="button" id="sup-returns-cancel-btn" class="na-btn na-btn-cancel">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <link rel="stylesheet" href="assets/add_product.css">
                        <script src="assets/add_supreturn.js" defer></script>
                        <script src="get/get_supplierSRETURN.js" defer></script>
                        <script src="get/get_productSRETURN.js" defer></script>








                        <!-- <div id="editProductModal" class="modal">
                            <div class="modal-content">
                                <h3>Edit Product</h3>
                                <form action="update.php" method="POST" enctype="multipart/form-data">
                        -->

                        <!-- EDIT CUSTOMER RETURN MODAL -->
                        <div id="editCustomerReturnModal" class="modal">
                            <div class="modal-content">
                                <h2>Edit Customer Return</h2>

                                <form action="update.php" method="POST">
                                    <input type="hidden" name="table" value="customersreturns">
                                    <input type="hidden" name="id" id="edit_CReturn_ID">

                                    <label for="edit_customer_reference">Reference Number</label>
                                    <input
                                        type="text"
                                        id="edit_customer_reference"
                                        name="ReferenceNo"
                                        required
                                    >

                                    <label for="edit_customer_product">Product</label>
                                    <select
                                        id="edit_customer_product"
                                        name="Product_ID"
                                        style="width: max-content; height: 34px"
                                        required
                                    >
                                        <option value="">Loading products...</option>
                                    </select>

                                    <label for="edit_customer_quantity">Quantity</label>
                                    <input
                                        type="number"
                                        id="edit_customer_quantity"
                                        name="Quantity"
                                        min="1"
                                        required
                                    >

                                    <label for="edit_customer_date">Return Date</label>
                                    <input
                                        type="date"
                                        id="edit_customer_date"
                                        name="ReturnedDate"
                                        required
                                    >

                                    <label for="edit_customer_reason">Reason for Return</label>
                                    <select
                                        id="edit_customer_reason"
                                        name="ReasonForReturn"
                                        style="width: max-content; height: 34px"
                                        required
                                    >
                                        <option value="Wrong-Item">Wrong Item</option>
                                        <option value="Damaged">Damaged</option>
                                        <!-- <option value="Defective">Defective</option> -->
                                        <option value="Expired">Expired</option>
                                        <option value="Other">Other</option>
                                    </select>

                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-edit">
                                            Save Changes
                                        </button>

                                        <button
                                            type="button"
                                            id="cancel-edit-customer-return"
                                            class="na-btn na-btn-cancel"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- EDIT SUPPLIER RETURN MODAL -->
                        <div id="editSupplierReturnModal" class="modal">
                            <div class="modal-content">
                                <h2 style="color: black;">Edit Supplier Return</h2>

                                <form action="update.php" method="POST">
                                    <input type="hidden" name="table" value="supplierreturns">
                                    <input type="hidden" name="id" id="edit_SReturns_ID">

                                    <label for="edit_supplier_id">Supplier</label>
                                    <select
                                        id="edit_supplier_id"
                                        name="Supplier_ID"
                                        class="shelf-row-select"
                                        
                                        style="width: max-content; height: 34px"
                                        required
                                    >
                                        <option value="">Loading suppliers...</option>
                                    </select>

                                    <label for="edit_supplier_product">Product</label>
                                    <select
                                        id="edit_supplier_product"
                                        name="Product_ID"
                                        class="shelf-row-select"
                                        
                                        style="width: max-content; height: 34px"
                                        required
                                    >
                                        <option value="">Loading products...</option>
                                    </select>

                                    <label for="edit_supplier_quantity">Quantity</label>
                                    <input
                                        type="number"
                                        id="edit_supplier_quantity"
                                        name="Quantity"
                                        min="1"
                                        required
                                    >

                                    <label for="edit_supplier_date">Return Date</label>
                                    <input
                                        type="date"
                                        id="edit_supplier_date"
                                        name="ReturnedDate"
                                        required
                                    >
                                    

                                    <label for="edit_supplier_status">Status</label>
                                    <select
                                        id="edit_supplier_status"
                                        name="Status"
                                        style="width: max-content; height: 34px"
                                        required
                                    >
                                        <option value="Pending">Pending</option>
                                        <option value="Out-for-Delivery">Out-for-Delivery</option>
                                        <option value="Cancelled">Cancelled</option>
                                        <option value="Delivered">Delivered</option>
                                        
                                    </select>

                                    <label for="edit_supplier_reason">Reason for Return</label>
                                    <select
                                        id="edit_supplier_reason"
                                        style="width: max-content; height: 34px"
                                        name="Reason"
                                        required
                                    >
                                        <option value="Wrong-Item">Wrong-Item</option>
                                        <option value="Damaged">Damaged</option>
                                        <option value="Expired">Expired</option>
                                        <!-- <option value="Accidental">Accidental</option> -->
                                        <option value="Other">Other</option>
                                        
                                    </select>

                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-edit">
                                            Save Changes
                                        </button>

                                        <button
                                            type="button"
                                            id="cancel-edit-supplier-return"
                                            class="na-btn na-btn-cancel"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <script src="assets/edit_product.js"></script>                    
                        <script src="get/edit_supplierPROD.js"></script>                        
                        <script src="returns_edit.js"></script>

                        

                        


                        

                        
                    </div>

                    <div id="supplier-returns-table-container" class="table-container returns-table-container">
                        <table class="returns-table">
                            <thead class="sticky-div">
                                <tr>
                                    <!-- <th>#</th> -->
                                    <th>SRETURNS ID</th>
                                    <th>Supplier ID</th>
                                    <th>Product ID</th>
                                    <th>Quantity</th>
                                    <th>Returned Date</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                    <th class="supplier-return-action-header" style="display: none;">
                                        Action
                                    </th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($supplierreturns as $index => $supplierreturns){ ?>
                                    <tr>
                                        <!-- <td><?= $index + 1?></td> -->
                                        <td><?= $supplierreturns['SReturns_ID']?></td>
                                        <td class="supplier-id-cell"><?= $supplierreturns['Supplier_ID']?></td>
                                        <td class="product-id-cell"><?= $supplierreturns['Product_ID']?></td>
                                        <td><?= $supplierreturns['Quantity']?></td>
                                        <td><?= date('F d,Y', strtotime($supplierreturns['ReturnedDate']))?></td>
                                        <!-- <td><span class="status-tag status-pending"><?= $supplierreturns['Status']?></span></td> -->

                                        <td><span class="status-tag status-<?= strtolower($supplierreturns['Status']) ?>">
                                                                            <?= htmlspecialchars($supplierreturns['Status']) ?>
                                            </span>
                                        
                                        </td>


                                        <td><span class="reason-tag reason-<?= strtolower($supplierreturns['Reason']) ?>">
                                                                            <?= htmlspecialchars($supplierreturns['Reason']) ?>
                                            </span>
                                        
                                        </td>

                                        <!-- <td><span class="reason-tag reason-defective"><?= $supplierreturns['Reason']?></span></td> -->


                                        <td class="supplier-return-action-cell" style="display:none;">
                                            <button
                                                type="button"
                                                class="select-supplier-return-btn na-btn na-btn-edit"
                                                data-id="<?= htmlspecialchars($supplierreturns['SReturns_ID'] ?? '') ?>"
                                                data-supplierid="<?= htmlspecialchars($supplierreturns['Supplier_ID'] ?? '') ?>"
                                                data-productid="<?= htmlspecialchars($supplierreturns['Product_ID'] ?? '') ?>"
                                                data-quantity="<?= htmlspecialchars($supplierreturns['Quantity'] ?? '') ?>"
                                                data-date="<?= htmlspecialchars($supplierreturns['ReturnedDate'] ?? '') ?>"
                                                data-status="<?= htmlspecialchars($supplierreturns['Status'] ?? '') ?>"
                                                data-reason="<?= htmlspecialchars($supplierreturns['Reason'] ?? '') ?>"
                                            >
                                                Edit
                                            </button>
                                        </td>


                                        


                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div id="customer-returns-table-container" class="table-container returns-table-container" style="display:none;">
                        <table class="returns-table">
                            <thead class="sticky-div">
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <!-- <th>Customer ID</th> -->
                                    <th>Reference No.</th>
                                    <th>Product ID</th>
                                    <th>Quantity</th>
                                    <th>Returned Date</th>
                                    <th>Reason</th>
                                    <th class="customer-return-action-header" style="display: none;">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                           <tbody>
                                <?php foreach($customersreturns as $index => $customersreturns){ ?>
                                    <tr>
                                        <td><?= $index + 1?></td>
                                        <td><?= $customersreturns['CReturn_ID']?></td>
                                        <!-- <td class="customer-id-cell"><?= $customersreturns['Customer_ID']?></td> -->
                                        <td style="color: rgb(1, 39, 152);"><?= $customersreturns['ReferenceNo']?></td>
                                        <td class="procduct-id-cell"><?= $customersreturns['Product_ID']?></td>
                                        <td><?= $customersreturns['Quantity']?></td>
                                        <td><?= date('F d, Y h:i A', strtotime($customersreturns['ReturnedDate']))?></td>
                                        <!-- <td><span class="reason-tag reason-incompatible"><?= $customersreturns['ReasonForReturn']?></span></td> -->
                                        <td><span class="reason-tag reason-<?= strtolower($customersreturns['ReasonForReturn']) ?>">
                                                                            <?= htmlspecialchars($customersreturns['ReasonForReturn']) ?>
                                            </span>
                                        
                                        </td>



                                        <td class="customer-return-action-cell" style="display:none;">
                                            <button
                                                type="button"
                                                class="select-customer-return-btn na-btn na-btn-edit"
                                                data-id="<?= htmlspecialchars($customersreturns['CReturn_ID'] ?? '') ?>"
                                                data-reference="<?= htmlspecialchars($customersreturns['ReferenceNo'] ?? '') ?>"
                                                data-productid="<?= htmlspecialchars($customersreturns['Product_ID'] ?? '') ?>"
                                                data-quantity="<?= htmlspecialchars($customersreturns['Quantity'] ?? '') ?>"
                                                data-date="<?= htmlspecialchars($customersreturns['ReturnedDate'] ?? '') ?>"
                                                data-reason="<?= htmlspecialchars($customersreturns['ReasonForReturn'] ?? '') ?>"
                                            >
                                                Edit
                                            </button>
                                        </td>




                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="order-restock" class="content-section">
                    <div class="custom-header sticky-div-1" style="background-image: url(topbarlogo.png);background-repeat: no-repeat;background-size: cover; 
                    height: 80px; ">
                        <div class="top-bar">
                            <div class="tab">RESTOCK</div>
                            <div class="user-controls">
                                <div>      
                                <?php
                                    echo $row['userName'];                        
                                ?>
                                </div>
                                <div class="user-icon">👤</div>
                            </div>
                        </div>
                        <div class="controls-bar">
                             <div class="controls">
                                <div class="toggle-buttons">
                                    

                                    <button id="receive-restock-btn" class="toggle-btn active">
                                        Item to Receive
                                    </button>
                                </div>
                                <!-- <div class="control-group"><label>📊 Group by:</label><select></select></div>
                                <div class="control-group"><label>⇅ Sort by:</label><select><option>DATE</option></select></div> -->
                            </div>
                            <!-- <div class="na-quick-search">🔍 Quick Search</div> -->
                            <div class="header-icons">
                                <!-- <span class="icon-wrapper"><i class="fas fa-print"></i></span> -->
                                <span class="icon-wrapper" onclick="openNotifications()"><i class="fas fa-envelope"></i><span class="badge">0</span></span>
                                <!-- <span class="icon-wrapper" onclick="openCalendarModal()"><i class="fa-regular fa-calendar-days"></i><span class="badge">2</span></span> -->
                            </div>
                        </div>
                    </div>
                   
                    <div class="toolbar sticky-div-2" style="display: flex; justify-content:space-between;">
                        



                        <div>
                            <?php
                            $totalRows = count($restock);
                            ?>

                            <span id="products-count" class="na-count"><?= $totalRows ?></span>

                            <button id="edit-restock-btn" class="na-btn na-btn-add1" type="button">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            <button id="delete-restock-btn" class="na-btn na-btn-add1" type="button">
                                <i class="fa-solid fa-trash"></i>
                            </button>

                            <!-- <button id="order-restock-add-btn" class="na-btn na-btn-add" type="button">
                                ADD
                            </button> -->

                            
                            <?php
                            $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                            if (in_array($accountType, ['manager'], true)) {
                            ?>

                            <button id="order-restock-add-btn" class="na-btn na-btn-add" type="button">
                                CREATE ORDER
                            </button>

                            <button id="place-order-btn" class="na-btn na-btn-update" type="button" style="display:none;">
                                PLACE ORDER
                            </button>

                            <button id="update-restock-status-btn" class="na-btn na-btn-update" type="button" style="display:inline-block;">
                                UPDATE
                            </button>
                            <?php } ?>  
                        </div>






                        <div>
                            <!-- <div class="na-quick-search">🔍 Quick Search</div> -->
                        </div>  




                        <div id="addOrderRestockModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <h3>Add New Order/Restock</h3>
                                

                                <!-- <form id="restockForm" action="add.php" method="POST"> -->
                                <form id="restockForm" action="add_restock.php" method="POST">

                                    <input type="hidden" name="table" value="restock">

                                    <!-- MULTIPLE RESTOCK ITEMS -->
                                    <div id="restock-items-container">

                                        <!-- Rows will be created by JavaScript -->

                                    </div>



                                    <!-- SUPPLIER ORDER SUMMARY -->
                                    <div id="restock-supplier-summary">

                                        <h4>Order Summary by Supplier</h4>

                                        <div id="supplier-summary-content">

                                            <p class="supplier-summary-empty">
                                                No products added yet.
                                            </p>

                                        </div>

                                    </div>



                                    <!-- ADD ANOTHER PRODUCT -->
                                    <button type="button" id="add-restock-item-btn" class="na-btn na-btn-add">
                                        + Add Item
                                    </button>

                                    <input type="hidden" name="Status" value="Requested">

                                    <!-- <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">
                                            Confirm Order
                                        </button>

                                        <button type="button" id="ord-cancel-btn" class="na-btn na-btn-cancel">
                                            Cancel
                                        </button>
                                    </div> -->



                                    <div class="restock-order-footer">
                                        <div class="restock-grand-total">
                                            <span>GRAND TOTAL</span>
                                            <strong id="restockGrandTotal">₱0.00</strong>
                                        </div>

                                        <div class="restock-order-actions">
                                            <button type="submit" class="na-btn na-btn-add">
                                                Confirm Order
                                            </button>

                                            <button type="button" id="ord-cancel-btn" class="na-btn na-btn-cancel">
                                                Cancel
                                            </button>                                            
                                            <!-- Keep your existing Confirm Order button here -->
                                            <!-- Keep your existing Cancel button here -->
                                        </div>
                                    </div>
                                    

                                </form>
                                <!-- PRODUCT SEARCH POPUP -->
                                <div id="productSearchModal" class="product-search-overlay" style="display: none;">

                                    <div class="product-search-box">

                                        <div class="product-search-header">

                                            <h3>Select Product</h3>

                                            <button
                                                type="button"
                                                id="closeProductSearch"
                                                class="product-search-close">
                                                ×
                                            </button>

                                        </div>

                                        <input
                                            type="text"
                                            id="productSearchInput"
                                            class="product-search-input"
                                            placeholder="Search product name..."
                                            autocomplete="off">

                                        <div id="productSearchResults" class="product-search-results">

                                            <!-- Products will appear here -->

                                        </div>

                                    </div>

                                </div>     
                                
                                <!-- Order Confirmation Modal -->
                                <div id="orderConfirmationModal" class="product-search-overlay" style="display: none;">

                                    <div class="order-confirmation-box">

                                        <div class="order-confirmation-header">

                                            <h3>Review Order</h3>

                                            <button
                                                type="button"
                                                id="closeOrderConfirmation"
                                                class="product-search-close">
                                                ×
                                            </button>

                                        </div>

                                        <p class="order-confirmation-message">
                                            Please review the order details below before placing the order.
                                        </p>

                                        <div id="order-confirmation-summary">

                                            <!-- Summary will be generated by JavaScript -->

                                        </div>

                                        <div class="order-confirmation-warning">

                                            Please make sure the product, supplier, and quantity
                                            are correct before confirming.

                                        </div>


                                        <div class="order-confirmation-footer">

                                            <div class="order-confirmation-grand-total">
                                                <span>GRAND TOTAL</span>
                                                <strong id="confirmationGrandTotal">₱0.00</strong>
                                            </div>

                                            <div class="modal-buttons">
                                                <button
                                                    type="button"
                                                    id="backToOrderBtn"
                                                    class="na-btn na-btn-cancel">
                                                    Back
                                                </button>

                                                <button
                                                    type="button"
                                                    id="confirmFinalOrderBtn"
                                                    class="na-btn na-btn-add">
                                                    Confirm &amp; Place Order
                                                </button>
                                            </div>

                                        </div>



                                        

                                        <!-- <div class="modal-buttons">

                                            <button
                                                type="button"
                                                id="backToOrderBtn"
                                                class="na-btn na-btn-cancel">

                                                Back

                                            </button>

                                            <button
                                                type="button"
                                                id="confirmFinalOrderBtn"
                                                class="na-btn na-btn-add">

                                                Confirm & Place Order

                                            </button>

                                        </div> -->

                                    </div>

                                </div>                                

                            </div>
                        </div>
                        
                        <link rel="stylesheet" href="assets/add_product.css">
                        <!-- <script src="assets/add_order.js" defer></script> -->
                        <!-- <script src="get_product.php" defer></script> -->
                        <!-- <script src="get/get_supplierORDRES.js" defer></script>
                        <script src="get/get_productORDRES.js" defer></script> -->

                        <script src="assets/add_order.js" defer></script>
                        <script src="assets/restock_items.js" defer></script>
                        <script src="order_confirmation.js"></script>







                        <div id="addListToOrderModal" class="modal" style="display: none;">
                            <!-- <div class="modal-content"> -->
                            <div class="modal-content">
                                <!-- <form id="listForm" action="add.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="table" value="list_to_order"> -->

                                <h3>Add New List to Order</h3>

                                <form id="listToOrderForm">
                                    <!-- SUPPLIER -->
                                    <label for="Supplier_IDLISTORDER">Choose Supplier ID:</label>
                                    <select id="Supplier_IDLISTORDER"name="Supplier_ID"class="id-select"style="width: 60%;"required>
                                        <option disabled selected>Loading...</option>
                                    </select>

                                    <!-- EXPECTED RECEIVE DATE -->

                                    <label>
                                        Expected Receive Date:
                                    </label>

                                    <input
                                        type="date"
                                        name="Expected_Receive_Date"
                                        id="list-expected-receive-date">

                                    <!-- FORECAST DATES -->

                                    <label>
                                        Forecast Dates:

                                        <button
                                            type="button"
                                            id="toggleNullBtnListOrder"
                                            style="color: red;">
                                            NO ?
                                        </button>

                                        <button
                                            type="button"
                                            id="toggleNotNullBtnListOrder"
                                            style="display: none;">
                                            YES ?
                                        </button>
                                    </label>

                                    <!-- FORECAST START DATE -->

                                    <label>
                                        Forecast Start Date:
                                    </label>

                                    <input
                                        type="date"
                                        name="Forecast_Start_Date"
                                        id="list-forecast-start-date"
                                        disabled>

                                    <!-- FORECAST END DATE -->

                                    <label>
                                        Forecast End Date:
                                    </label>

                                    <input
                                        type="date"
                                        name="Forecast_End_Date"
                                        id="list-forecast-end-date"
                                        disabled>

                                    <!-- NOTES -->

                                    <label>
                                        Notes:
                                    </label>

                                    <textarea
                                        name="Notes"
                                        id="list-order-notes"
                                        rows="4"
                                        style="width: 90%;"
                                        placeholder="Enter notes..."></textarea>

                                    <!-- BUTTONS -->

                                    <div class="modal-buttons">

                                        <button
                                            type="submit"
                                            class="na-btn na-btn-add">
                                            Save
                                        </button>

                                        <button
                                            type="button"
                                            id="list-order-cancel-btn"
                                            class="na-btn na-btn-cancel">
                                            Cancel
                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                        <link rel="stylesheet" href="assets/add_product.css">
                        <script src="add_list_restock.js" defer></script>
                        <script src="get/get_supplierLIST.js" defer></script>


                        <div id="addItemToOrderModal" class="modal" style="display:none;">
                            <div class="modal-content">
                                <h3>Add New Item to Order</h3>

                                <form id="itemToOrderForm">
                                    <label for="Item_ListToOrder">Choose List to Order:</label>
                                    <select id="Item_ListToOrder" name="ListToOrder_ID" class="id-select" style="width:60%;" required>
                                        <option disabled selected>Loading...</option>
                                    </select>

                                    <label for="Item_ProductID">Choose Product ID:</label>
                                    <select id="Item_ProductID" name="Product_ID" class="id-select" style="width:60%;" required>
                                        <option disabled selected>Loading...</option>
                                    </select>

                                    <!-- <label for="Item_SupplierID">Choose Supplier ID:</label>
                                    <select id="Item_SupplierID" name="Supplier_ID" class="id-select" style="width:60%;" required>
                                        <option disabled selected>Loading...</option>
                                    </select> -->

                                    <label id="Current_Stock_label">Current Stock:</label>

                                    <input type="number" name="Current_Stock" id="item-current-stock" style="font-weight: bolder;" value="0" readonly>
                                    <!-- <label id="Current_Stock_label">Current Stock Quantity:</label> -->
                                    <!-- <input type="number" id="Current_Stock" style="font-weight: bolder;" readonly> -->

                                    <label>Predicted Demand:</label>
                                    <input type="number" name="Predicted_Demand" id="item-predicted-demand" value="0" readonly>

                                    <label style="color: blue">Pack Size:</label>
                                    <input type="number" name="Pack_Size" id="item-pack-size" style="font-weight: bolder;color: blue" value="1" min="1" readonly>

                                    <label>Recommended Order Quantity:</label>
                                    <input type="number" name="Recommended_Order_Quantity" id="item-recommended-quantity" value="0" min="0" readonly>

                                    <label>Ordered Quantity:</label>
                                    <!-- <input
                                        type="number"
                                        name="Ordered_Quantity"
                                        id="item-ordered-quantity"
                                        value="0"
                                        min="1"
                                        step="1"
                                        required
                                    > -->
                                    <input type="number" name="Ordered_Quantity" id="item-ordered-quantity" value="0" min="1" step="1" required>

                                    <label style="color: green" >Unit Cost:</label>
                                    <input type="number" name="Unit_Cost" id="item-unit-cost" value="0" min="0" step="0.01" required>

                                    <label>Notes:</label>
                                    <textarea name="Notes" id="item-order-notes" rows="3" style="width:90%;" placeholder="Enter notes..."></textarea>

                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">Save</button>
                                        <button type="button" id="item-order-cancel-btn" class="na-btn na-btn-cancel">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <script src="add_list_restock.js" defer></script>
                        <script src="get/get_supplierLIST.js" defer></script>                        
                        <script src="add_item_to_order.js" defer></script>
                        <!-- <script src="get_item_order_products.php" defer></script> -->

                                <!-- //EDIT RESTOCK -->

                        <div id="editRestockModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <h3>Edit Restock</h3>
                                <form id="restockForm" action="update.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="table" value="restock">
                                    <input type="number" name="id" id="edit-order-id" readonly>

                                    <!-- <label>Select Product ID</label>
                                    <input type="text" name="Product_ID" required> -->

                                    <label for="Product_IDORDRES">Choose Product ID:</label>
                                    <select id="edit-productidRESTOCK" name="Product_ID" class="id-select" required>
                                        <option disabled selected>Loading...</option>
                                    </select>

                                    <label for="Supplier_IDORDRES">Choose Supplier ID:</label>
                                    <select id="edit-supplieridRESTOCK" name="Supplier_ID" class="id-select" required>
                                        <option disabled selected>Loading...</option>
                                    </select>

                                    <label>Quantity:</label>
                                    <input type="number" name="Quantity" id="edit-quantity" required>

                                    <input type="hidden"  name="Status" value="Requested">

                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">Save</button>
                                        <!-- <button type="button" id="cancel-btn" class="na-btn na-btn-cancel close-modal-btn">Cancel</button> -->
                                        <button type="button" id="editorder-cancel-btn" class="na-btn na-btn-cancel">Cancel</button>
                                    </div>
                                </form>
                            </div>
                            <link rel="stylesheet" href="assets/add_product.css">
                            <script src="assets/edit_restock.js"></script>
                            <script src="get/edit_supplierRESTOCK.js"></script>
                            <script src="get/edit_productRESTOCK.js"></script>
                        </div>

                        <!-- <script src="assets/add_order.js" defer></script>

                        <script src="get/get_supplierORDRES.js" defer></script>
                        <script src="get/get_productORDRES.js" defer></script> -->

                        <div id="updateOrderModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <h3>Update Order/Restock</h3>
                                <form action="update.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="table" value="restock">
                                    <!-- <input type="number" name="id" id="update-order-id"> -->
                                    <input type="hidden" name="id" id="update-order-id">

                                    <label>Proof of Transaction:</label>
                                    <input type="file" accept="image/*" name="Image" id="edit-image">

                                    <label>Status::</label>
                                    <!-- <div style="display: flex; justify-content:space-between; align-items:center;"></div>-->
                                    <!-- <select class="status-select" name="Status" id="update-status" required>
                                        <option value="">...</option>
                                        <option value="Requested">Requested</option>
                                        <option value="Out-for-Delivery">Out for Delivery</option>
                                        <option value="Cancelled">Cancelled</option>
                                        <option value="Received">Received</option>

                                    </select> -->

                                    <select class="status-select" name="Status" id="update-status" required>

                                        <option value="">...</option>

                                        <option value="Requested">
                                            Requested
                                        </option>

                                        <option value="Out for Delivery">
                                            Out for Delivery
                                        </option>

                                        <option value="Partially-Received">
                                            Partially-Received
                                        </option>

                                        <option value="Backorder-Fulfillment">
                                            Backorder-Fulfillment
                                        </option>

                                        <option value="Cancelled">
                                            Cancelled
                                        </option>

                                        <option value="P-R-With-Issue">
                                            P-R-With Issue
                                        </option>

                                        <option value="Received">
                                            Received
                                        </option>

                                    </select>


                                    <!-- <label>Delivery Status::</label>
                                    <select class="status-select" name="DeliveryStatus" id="update-delivery-status">
                                        <option value="">...</option>
                                        <option value="On-Time">On Time</option>
                                        <option value="Delayed">Delayed</option>
                                        <option value="Early">Early</option>
                                    </select> -->

                                    <label style="color: red;">With Expiration? :</label>
                                    <div style="display: flex; justify-content:space-between; align-items:center;">
                                    <!-- <label>With Expiration?:</label>
                                    <select class="status-select" name="LocationS" id="Expiration_Status" required>
                                        <option value="yes">Yes</option>
                                        <option value="no ">No</option>
                                    </select> -->

                                    <label>Expiration Date:
                                        <button type="button" id="toggleNullBtnOrder_restock" style="color: red;">NO ?</button>
                                        <button type="button" id="toggleNotNullBtnOrder_restock" style="display: none;">YES ?</button>
                                    </label>
                                    <!-- <input type="date" name="ExpirationDate" id="update-datereceived" style="width: 50%;" required> -->
                                    <input type="date" name="ExpirationDate" id="update-expiration-date" style="width: 50%;" required>
                                    </div>

                                    <!-- <label>Expiration Date:</label>
                                    <input type="datetime-local" name="Date_Received" id="update-datereceived"> -->

                                    <label>Ordered Quantity:</label>
                                    <input type="number" name="Quantity" id="update-orderedQuantity" readonly>

                                    <label>Without Issue:</label>
                                    <input type="number"  name="TotalReceived" id="update-received">

                                    <label>With Issue:</label>
                                    <input type="number"  name="withIssue" id="update-issue">

                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">Save</button>
                                        <!-- <button type="button" id="cancel-btn" class="na-btn na-btn-cancel close-modal-btn">Cancel</button> -->
                                        <button type="button" id="updateorder-cancel-btn" class="na-btn na-btn-cancel">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <link rel="stylesheet" href="assets/add_product.css">
                        <!-- <script src="assets/add_order.js" defer></script> -->
                        <script src="assets/update_restock.js" defer></script>
                        <script src="get/get_productORDRES.js" defer></script>
                        <script src="compare_quantity.js" defer></script>

















                        
                    </div>
 

                    <div id="receive-restock-table-container" class="table-container order-restock-table-container">
                        <table class="order-restock-table">
                            <thead class="sticky-div">
                                <tr>
                                    <!-- <th>#</th> -->
                                    <!-- <th>ITEM ORDER ID</th> -->
                                    <th>PRODUCT</th>
                                    <!-- <th>SUPPLIER ID</th> -->
                                     <th>SUPPLIER</th>
                                    
                                    <th>ORDER DATE</th>
                                    <!-- <th>PROOF OF TRANSACTION</th> -->
                                    <th>STATUS</th>
                                    <!-- <th>DELIVERY STATUS</th> -->
                                    <th>DATE RECEIVED</th>
                                    <th>ORDER QTY.</th>
                                    <th>RECEIVED</th>
                                    <!-- <th>WITH ISSUE</th> -->
                                    <!-- <th>EXPIRATION DATE</th> -->
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                usort($restock, function ($a, $b) {
                                    return $b['Orestock_ID'] <=> $a['Orestock_ID'];
                                });

                                foreach ($restock as $index => $restocks) {
                                ?>
                                    <tr>
                                        <!-- <td><?= $restocks['Orestock_ID'] ?></td> -->
                                        <!-- <td><?= $restocks['ItemToOrder_ID'] ?></td> -->
                                        <!-- <td class="product-id-cell"><?= $restocks['Product_ID'] ?></td> -->
                                        <!-- <td class="supplier-id-cell"><?= $restocks['Supplier_ID'] ?></td> -->


                                        <!-- <td><?= $restocks['Orestock_ID'] ?></td> -->

                                        <td class="product-id-cell">
                                            <strong>
                                                <?= htmlspecialchars($restocks['ProductName'] ?? 'Unknown Product') ?>
                                            </strong>
                                            <br>
                                            <small>
                                                ID: <?= htmlspecialchars($restocks['Product_ID']) ?>
                                            </small>
                                        </td>

                                        <!-- <td>
                                            <strong>
                                                <?= htmlspecialchars($restocks['SupplierName'] ?? 'Unknown Supplier') ?>
                                            </strong>
                                            <br>
                                            <small>
                                                <?= htmlspecialchars($restocks['ContactPerson']) ?>
                                            </small>
                                            <br>
                                            <small>
                                                ID: :<?= htmlspecialchars($restocks['Supplier_ID']) ?>
                                            </small>
                                        </td> -->


                                        <td class="supplier-info-cell">
                                            <strong>
                                                <?= htmlspecialchars($restocks['SupplierName'] ?? 'Unknown Supplier') ?>
                                            </strong>
                                            <br>

                                            <!-- <small>
                                                Contact:
                                                <?= htmlspecialchars($restocks['ContactPerson'] ?? 'N/A') ?>
                                            </small>
                                            <br> -->

                                            <!-- <small>
                                                Address:
                                                <?= htmlspecialchars($restocks['Address'] ?? 'N/A') ?>
                                            </small>
                                            <br> -->

                                            <small>
                                                <!-- City: -->
                                                <?= htmlspecialchars($restocks['City'] ?? 'N/A') ?>
                                            </small>
                                            <br>

                                            <small>
                                                Supplier ID:
                                                <?= htmlspecialchars($restocks['Supplier_ID'] ?? '') ?>
                                            </small>
                                        </td>                                        

                                        


                                        <td><?= date('F d, Y h:i A', strtotime($restocks['OrderDate'])) ?></td>
                                        <!-- <td>
                                            <?php if (!empty($restocks['Image']) && file_exists("uploads/" . $restocks['Image'])): ?>
                                                <a href="uploads/<?= $restocks['Image'] ?>" target="_blank">
                                                    <img src="uploads/<?= $restocks['Image'] ?>" alt="Product Image" style="max-width: 50px; max-height: 50px; object-fit: cover;">
                                                </a>
                                            <?php else: ?>
                                                No Image
                                            <?php endif; ?>
                                        </td> -->
                                        <td>
                                            <span class="status-tag status-<?= strtolower($restocks['Status']) ?>">
                                                <?= htmlspecialchars($restocks['Status']) ?>
                                            </span>
                                        </td>
                                        <!-- <td>
                                            <span class="delivery-status-tag delivery-status-<?= strtolower($restocks['DeliveryStatus']) ?>">
                                                <?= htmlspecialchars($restocks['DeliveryStatus']) ?>
                                            </span>
                                        </td> -->
                                        <td style="color:red;">
                                            <?= empty($restocks['Date_Received']) ? '- - - N/A - - -' : date('F d, Y h:i A', strtotime($restocks['Date_Received'])) ?>
                                        </td>
                                        <!-- <td><?= $restocks['Quantity'] ?></td> -->




                                        <td class="product-info-cell">
                                            <?php
                                                $quantity = (int) ($restocks['Quantity'] ?? 0);
                                                $purchaseUnitCost = $restocks['PurchaseUnitCost'] ?? null;
                                                $orderCost = $restocks['OrderCost'] ?? null;

                                                $hasPurchaseCost = $purchaseUnitCost !== null
                                                    && $purchaseUnitCost !== '';

                                                $hasOrderCost = $orderCost !== null
                                                    && $orderCost !== '';
                                            ?>

                                            <strong>
                                                Qty: <?= number_format($quantity) ?>
                                            </strong>

                                            <br>

                                            
                                                Unit Cost:
                                                <?php if ($hasPurchaseCost): ?>
                                                    
                                                    <span style="color: green; font-weight: bolder;">₱<?= number_format((float) $purchaseUnitCost, 2) ?></span> 
                                                <?php else: ?>
                                                    —
                                                <?php endif; ?>
                                            

                                            <br>

                                            
                                                Order Cost:
                                                <?php if ($hasOrderCost): ?>
                                
                                                    <span style="color: blue; font-weight: bolder;">₱<?= number_format((float) $orderCost, 2) ?></span> 
                                                <?php else: ?>
                                                    —
                                                <?php endif; ?>
                                            
                                        </td>
 
                                                    
                                        <td>
                                            GOOD:                                        
                                            <span style="color: green; font-weight: bolder;"><?= $restocks['TotalReceived'] ?></span> 
                                            <hr>
                                            ISSUE:
                                            <span style="color: red; font-weight: bolder;"><?= $restocks['withIssue'] ?></span> 
                                             
                                        </td>                    

                                        
                                        <!-- <td>
                                            <?= !empty($restocks['ExpirationDate']) ? date('F d, Y', strtotime($restocks['ExpirationDate'])) : '- - -' ?>
                                        </td> -->
                                        <td class="action-cell">
                                            
                                            <?php
                                                $restockStatus = trim($restocks['Status'] ?? '');

                                                $disableUpdate = in_array(
                                                    $restockStatus,
                                                    ['Received', 'Partially-Received', 'P-R-With-Issue','Received-With-Issue'],
                                                    true
                                                );
                                            ?>

                                            <button
                                                type="button"
                                                style="display:none;"
                                                class="update-order-btn na-btn na-btn-update"
                                                data-id="<?= htmlspecialchars((string)$restocks['Orestock_ID'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-proof="<?= htmlspecialchars((string)($restocks['Image'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                data-status="<?= htmlspecialchars($restockStatus, ENT_QUOTES, 'UTF-8') ?>"
                                                data-deliverystatus="<?= htmlspecialchars((string)($restocks['DeliveryStatus'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                data-expirationdate="<?= htmlspecialchars((string)($restocks['ExpirationDate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                data-quantity="<?= htmlspecialchars((string)$restocks['Quantity'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-received="<?= htmlspecialchars((string)($restocks['TotalReceived'] ?? 0), ENT_QUOTES, 'UTF-8') ?>"
                                                data-issue="<?= htmlspecialchars((string)($restocks['withIssue'] ?? 0), ENT_QUOTES, 'UTF-8') ?>"
                                                <?= $disableUpdate ? 'disabled title="This restock row cannot be updated again."' : '' ?>
                                            >
                                                📝 Update
                                            </button>

                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>  
                    <!-- ADD LIST TO ORDER MODAL -->              
                        
                                

                </div>

                <div id="transaction-sales" class="content-section" >
                    <div class="custom-header sticky-div-1" style="background-image: url(topbarlogo.png);background-repeat: no-repeat;background-size: cover;">
                        <div class="top-bar">
                            <div class="tab">TRANSACTIONS & SALES</div>
                            <div class="user-controls">
                                <div>      
                                <?php
                                    echo $row['userName'];                        
                                ?>
                                </div>
                                <div class="user-icon">👤</div>
                            </div>
                        </div>


                        <div class="controls-bar">

                            <div class="controls">
                                <!-- <div class="control-group"><label>📊 Group by:</label><select></select></div>
                                <div class="control-group"><label>⇅ Sort by:</label><select><option>SUPPLIER ID</option></select></div> -->
                                <div class="toggle-buttons">
                                    <button id="transaction-view-btn" class="toggle-btn active">Transaction</button>
                                    <button id="sales-view-btn" class="toggle-btn">Sales</button>
                                </div>
                            </div>

                            <div class="header-icons">
                                <!-- <span class="icon-wrapper"><i class="fas fa-print"></i></span> -->
                                <span class="icon-wrapper" onclick="openNotifications()"><i class="fas fa-envelope"></i><span class="badge">0</span></span>
                                <!-- <span class="icon-wrapper" onclick="openCalendarModal()"><i class="fa-regular fa-calendar-days"></i><span class="badge">2</span></span> -->
                            </div>



                        </div>
                    </div>
                    <div class="toolbar sticky-div-2" style="display: flex; justify-content:space-between;">
                        <div>

                 
                            
                        </div>

                      

                        <div class="search-container" style="display:flex; justify-content:flex-end; margin-bottom:10px;">
                        <input 
                            type="text" 
                            id="transactionQuickSearch" 
                            placeholder="🔍 Quick Search..." 
                            style="padding:8px 12px; border-radius:6px; border:1px solid #ccc; width:250px;"
                        >
                        </div>  




   
                        
                            <!-- <button id="edit-product-btn" class="na-btn na-btn-add1" type="button"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button id="delete-product-btn" class="na-btn na-btn-add1" type="button"><i class="fa-solid fa-trash"></i></button>
                        
                        <button id="transaction-add-btn" class="na-btn na-btn-add">ADD</button> -->
                        <div id="addTransactionModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <h3>Add New Transaction</h3>
                                <form id="transactionForm" action="add.php" method="POST">
                                    <input type="hidden" name="table" value="transactions">

                                    

                                    

                                    <label>ReferenceNo:</label>
                                    <input type="number" name="ReferenceNo" required maxlength=11>

                                    

                                    
                                     




                                    <label>Payment Method:</label>
                                    <!-- <div style="display: flex; justify-content:space-between; align-items:center;"></div>-->
                                    <select class="payment-method-select" name="PaymentMethod" required>
                                        <option value="">- - - Choose Payment Method - - -</option>
                                        <option value="Cash">Cash</option>
                                        <option value="eWallet">eWallet</option>


                                    </select>    
                                    
                                    <!-- <label>Service Type:</label>
                                    <input type="text" name="DeliveryType" required> -->

                                    <!-- <label>Service Type:</label> -->
                                    <!-- <div style="display: flex; justify-content:space-between; align-items:center;"></div>-->
                                    <!-- <select class="payment-method-select" name="ServiceType" required>
                                        <option value="">- - - Choose Service Type - - -</option>
                                        <option value="Pick Up">Pick Up</option>
                                        <option value="Delivery">Delivery</option>


                                    </select>   -->

                                    <label>Transaction Date:</label>
                                    <input type="datetime-local" name="Transaction_Date" required>
                                    

                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">Save</button>
                                        <!-- <button type="button" id="cancel-btn" class="na-btn na-btn-cancel close-modal-btn">Cancel</button> -->
                                        <button type="button" id="transaction-cancel-btn" class="na-btn na-btn-cancel">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <link rel="stylesheet" href="assets/add_product.css">
                        <script src="assets/add_transaction.js" defer></script>
                        <!-- <script src="get_productTRANS.js" defer></script> -->
                        <script src="get/get_customerTRANS.js" defer></script>
                        
                        
                        <div id="addSalesModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <h3>Add New Sales</h3>
                                <form id="salesForm" action="add.php" method="POST">
                                    <input type="hidden" name="table" value="sales">

                                    <!-- <label>Transaction ID:</label>
                                    <input type="text" name="Transaction_ID" required> -->

                                    <label for="Transaction_IDSALES">Choose Transaction ID:</label>
                                    <select id="Transaction_IDSALES" name="Transaction_ID" class="id-select"  style="width: 60%;"required>
                                        <option value="" disabled selected>Loading...</option>
                                    </select>

                                    <label for="Product_ID">Choose Product ID:</label>
                                    <select id="Product_IDSALES" name="Product_ID" class="id-select" style="width: 60%;" required>
                                        <option value="" disabled selected>Loading...</option>
                                    </select>

                                    <!-- <label for="Product_ID">Choose Batch ID:</label>
                                    <select id="Product_IDSALES_Batch" name="Product_ID" class="id-select" required>
                                        <option disabled selected>Loading...</option>
                                    </select> -->

                                    <label for="BatchNum">Choose Batch ID:</label>
                                    <select id="Product_IDSALES_Batch" name="BatchNum" class="id-select" style="width: 60%;" required>
                                    <option value="" disabled selected>Loading...</option>
                                    </select>





                                    <label>Product Name:</label>
                                    <input type="text" id="ProductName" name="ProductName" readonly>

                                    <label>Barcode:</label>
                                    <input type="text" id="Barcode" name="Barcode" readonly>

                                    <label>Unit Price (PHP):</label>
                                    <input type="number" step="0.01" id="Unit_Price" name="Unit_Price" readonly>

                        
                                    <label id="Current_Stock_label">Current Stock Quantity:</label>
                                    <input type="number" id="Current_Stock" style="font-weight: bolder;" readonly>
                                    
                                    

                                    <label>Quantity:</label>
                                    <!-- <input type="number" id="Quantity" name="Quantity" required> -->
                                    <input type="number" id="Quantity" name="Quantity" required min="1">


                                    <label>Total Price (PHP):</label>
                                    <input type="number" step="0.01" id="TotalPrice" name="TotalPrice" readonly>

                                    

                                    <!-- <label>Quantity:</label>
                                    <input type="number" name="Quantity" required>

                                    <label>Total Price:</label>
                                    <input type="text" name="TotalPrice" readonly>

                                    <label>Barcode:</label>
                                    <input type="number" name="Barcode" required> -->

                                    <!-- <label>Sales Date:</label>
                                    <input type="datetime-local" name="SalesDate" required> -->

                                    <!-- <label>Account ID:</label>
                                    <input type="text" name="Account_ID" required> -->
                                    

                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">Save</button>
                                        <!-- <button type="button" id="cancel-btn" class="na-btn na-btn-cancel close-modal-btn">Cancel</button> -->
                                        <button type="button" id="sales-cancel-btn" class="na-btn na-btn-cancel">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <link rel="stylesheet" href="assets/add_product.css">
                        <script src="assets/add_sales.js" defer></script>
                        <script src="get/get_productSALES.js" defer></script>
                        <script src="get_product_details.js" defer></script>
                        <!-- <script src="get_batches.php" defer></script> -->
                        <script src="get/get_transactionSALES.js" defer></script>

                    </div>
                    
                    <!-- SALES VIEW CONTAINER -->
                    <div id="sales-view-container" class="table-container">
                        <table class="transaction-sales-table">
                            <thead class="sticky-div">
                                <tr>
                                    <th>#</th>
                                    <th>Sales Item ID</th>   
                                    <th>Transaction ID</th>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Batch Number</th>
                                    <th>Unit Price</th>
                                    <th>Total Quantity</th>
                                    <th>Total Price</th>
                                    <th>Date Created</th>
                                    <!-- <th>Date Uppated</th> -->
                                    <!-- <th>Barcode</th> -->
                                    <!-- <th>Sales Date</th> -->
                                    <!-- <th>Account ID</th> -->
                                </tr>
                            </thead>

                            <tbody>
                                
                                <?php 
                                usort($sales_item_test, function ($a, $b) {
                                    return $b['SalesItem_ID'] <=> $a['SalesItem_ID'];
                                });

                                





                                /* =========================================================
                                LOAD PRODUCT NAMES ONCE
                                ========================================================= */

                                $productNames = [];

                                $productQuery = mysqli_query(
                                    $conn,
                                    "SELECT Product_ID, ProductName FROM product"
                                );

                                while ($productRow = mysqli_fetch_assoc($productQuery)) {
                                    $productNames[$productRow['Product_ID']] =
                                        $productRow['ProductName'];
                                }



                                $sales_item_test = array_slice($sales_item_test, 0, 50);
                                
                                foreach($sales_item_test as $index => $sales_item_test){ ?>
                                    <tr>
                                        <td><?= $index + 1?></td>
                                        <td>SALLE-ITEM-<?= $sales_item_test['SalesItem_ID'] ?></td>
                                        <td>SALES-<?= $sales_item_test['Sales_ID'] ?></td>
                                        <td class="product-id-cell"><?= $sales_item_test['Product_ID'] ?></td>

                                        <td>
                                            <?= $productNames[$sales_item_test['Product_ID']] ?? 'Unknown Product' ?>
                                        </td>

                                        <!-- <td><?= $sales_item_test['ProductName'] ?></td> -->
                                        <!-- <td><?= $sales_item_test['BatchNum'] ?></td> -->
                                        <td style="color: red;">
                                        <?= empty($sales_item_test['BatchNum']) ? '- - - N/A - - -' : $sales_item_test['BatchNum']; ?>
                                        </td>
                                        <!-- <td>PHP <?= $sales_item_test['Unit_Price'] ?></td> -->
                                        <td >
                                            <span style="color: darkgreen; font-weight: bolder; float:left">PHP </span> 
                                            <?= number_format($sales_item_test['Unit_Price'], 2) ?>
                                        </td>
                                        <td><?= $sales_item_test['Quantity'] ?></td>

                                        <td >
                                            <span style="color: darkgreen; font-weight: bolder; float:left">PHP </span> 
                                            <?= number_format($sales_item_test['TotalPrice'], 2) ?>
                                        </td>

                                        <td>
                                            <?= date('M d, Y h:i A', strtotime($sales_item_test['date_created'])) ?>
                                        </td>
                                        

                                        <!-- <td>
                                            <?= date('M d, Y h:i A', strtotime($sales_item_test['date_updated'])) ?>
                                        </td> -->


                                        <!-- <td style="color: green; font-weight: bolder;">PHP <?= $sales['TotalPrice'] ?></td> -->
                                        <!-- <td><?= $sales['Barcode'] ?></td> -->
                                        <!-- <td><?= date('F d,Y', strtotime($sales['SalesDate']))?></td> -->
                                        <!-- <td style="color: red;"><?= empty($sales['SalesDate']) ? '- - - N/A - - -' : date('F d, Y h:i A', strtotime($sales['SalesDate'])) ?></td> -->
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- TRANSACTION VIEW CONTAINER -->
                    <div id="transaction-view-container" class="table-container" style="display:none;">
                        <table class="transaction-sales-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transaction ID</th>
                                    <th>Reference No.</th>
                                    <th>User</th>
                                    <th>Total</th>
                                    <th>Amount Received</th>
                                    <th>Change Amount</th>
                                    <th>Date Created</th>
                                </tr>
                            </thead>
                           <tbody>
                                <?php 
                                usort($sales_test, function ($a, $b) {
                                    return $b['Sales_ID'] <=> $a['Sales_ID'];
                                });

                                // $sales_item_test = array_slice($sales_item_test, 0, 50);



                                foreach($sales_test as $index => $sales_test){ ?>
                                    <tr>
                                        <td><?= $index + 1?></td>
                                        <td>SALES-<?= $sales_test['Sales_ID'] ?></td>
                                        <td style="color: rgb(1, 39, 152);"><?= $sales_test['ReferenceNo'] ?></td>
                                        <td>
                                            <?php 
                                            
                                            $username_id = $sales_test['User_ID'];
                                            $username_query = mysqli_query($conn, "SELECT userName FROM users where id = '$username_id'");
                                            $username = mysqli_fetch_assoc($username_query);
                                            echo $username['userName'] ?? 'Unknown Product';

                                            ?>
                                        </td>

                                        <td><span style="color: darkgreen; font-weight: bolder; float:left">PHP: </span><?= $sales_test['total_amount'] ?></td>
                                        <td><span style="color: darkgreen; font-weight: bolder; float:left">PHP: </span><?= $sales_test['amount_tendered'] ?></td>
                                        <td><span style="color: darkgreen; font-weight: bolder; float:left">PHP: </span><?= $sales_test['change_amt'] ?></td>

                                        <!-- <td><?= $sales_test['date_created'] ?></td> -->
                                        <td>
                                            <?= date('M d, Y h:i A', strtotime($sales_test['date_created'])) ?>
                                        </td>
                                        <!-- <td><?= $sales_test['date_updated'] ?></td> -->


                                        <!-- <td style="color: red;"><?= empty($transaction['Transaction_Date']) ? '- - - N/A - - -' : date('F d, Y h:i A', strtotime($transaction['Transaction_Date'])) ?></td>
                                        <td >
                                            <span style="color: darkgreen; font-weight: bolder; float:left">PHP </span> <?= number_format($transaction['Total_Price'], 2) ?>
                                        </td> -->
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <script src="quick_search_transaction.js"></script>
                    </div>
                </div>    

                
                    
                    <div id="stock-adjustments" class="content-section">
                        <link rel="stylesheet" href="reports/reports-page.css">

                        <div class="custom-header" style="background-image: url(topbarlogo.png); background-repeat: no-repeat; background-size: cover;">
                            <div class="top-bar">
                                <div class="tab">Reports</div>
                                <div class="user-controls">
                                    <div><?php echo htmlspecialchars($row['userName'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="user-icon">👤</div>
                                </div>
                            </div>
                        </div>

                        <div id="report-content" class="report-cards">
                            <div class="report-filter">
                                <label for="reportType" style="color: white;">Report Type:</label>
                                <select id="reportType">
                                    <option value="historical" selected>Historical Inventory Report</option>
                                    <option value="sales-profit">Sales and Profit Report</option>
                                    <option value="sales-summary">Sales Summary Report</option>
                                    <option value="purchase">Purchase Order Report</option>
                                    <option value="received">Stock Received Report</option>
                                    <option value="demand">Demand Forecast &amp; Recommendation</option>
                                </select>
                            </div>

                            <div class="report-card report-option" id="historicalReport">
                                <h3>Historical Inventory Report</h3>
                                <p>View opening stock, stock received, units sold, returns, pulled-out items, and closing stock.</p>
                                <div class="report-date-fields">
                                    <label for="historicalStart">Start Date:</label>
                                    <input type="date" id="historicalStart">
                                    <label for="historicalEnd">End Date:</label>
                                    <input type="date" id="historicalEnd">
                                    <label for="historicalRecipient">Prepared For (Optional):</label>
                                    <input type="text" id="historicalRecipient" placeholder="Recipient's name">
                                </div>
                                <div class="export-buttons">
                                    <button type="button" class="export-btn pdf generate-report" data-report="historical">PDF</button>
                                </div>
                            </div>

                            <div class="report-card report-option" id="salesProfitReport" hidden>
                                <h3>Sales and Profit Report</h3>
                                <p>View total revenue, estimated product cost, gross profit, and units sold.</p>
                                <div class="report-date-fields">
                                    <label for="salesStart">Start Date:</label>
                                    <input type="date" id="salesStart">
                                    <label for="salesEnd">End Date:</label>
                                    <input type="date" id="salesEnd">
                                    <label for="salesRecipient">Prepared For (Optional):</label>
                                    <input type="text" id="salesRecipient" placeholder="Recipient's name">
                                </div>
                                <div class="export-buttons">
                                    <button type="button" class="export-btn pdf generate-report" data-report="sales-profit">PDF</button>
                                </div>
                            </div>

                            <div class="report-card report-option" id="salesSummaryReport" hidden>
                                <h3>Sales Summary Report</h3>
                                <p>View total sales revenue, transaction count, units sold, product sales, and daily sales totals.</p>
                                <div class="report-date-fields">
                                    <label for="summaryStart">Start Date:</label>
                                    <input type="date" id="summaryStart">
                                    <label for="summaryEnd">End Date:</label>
                                    <input type="date" id="summaryEnd">
                                    <label for="summaryRecipient">Prepared For (Optional):</label>
                                    <input type="text" id="summaryRecipient" placeholder="Recipient's name">
                                </div>
                                <div class="export-buttons">
                                    <button type="button" class="export-btn pdf generate-report" data-report="sales-summary">PDF</button>
                                </div>
                            </div>

                            <div class="report-card report-option" id="purchaseReport" hidden>
                                <h3>Purchase Order Report</h3>
                                <p>View products ordered, suppliers, order quantities, purchase costs, and order status.</p>
                                <div class="report-date-fields">
                                    <label for="purchaseStart">Start Date:</label>
                                    <input type="date" id="purchaseStart">
                                    <label for="purchaseEnd">End Date:</label>
                                    <input type="date" id="purchaseEnd">
                                    <label for="purchaseRecipient">Prepared For (Optional):</label>
                                    <input type="text" id="purchaseRecipient" placeholder="Recipient's name">
                                </div>
                                <div class="export-buttons">
                                    <button type="button" class="export-btn pdf generate-report" data-report="purchase">PDF</button>
                                </div>
                            </div>

                            <div class="report-card report-option" id="receivedReport" hidden>
                                <h3>Stock Received Report</h3>
                                <p>View products received, actual quantities received, suppliers, receiving dates, and received stock value.</p>
                                <div class="report-date-fields">
                                    <label for="receivedStart">Start Date:</label>
                                    <input type="date" id="receivedStart">
                                    <label for="receivedEnd">End Date:</label>
                                    <input type="date" id="receivedEnd">
                                    <label for="receivedRecipient">Prepared For (Optional):</label>
                                    <input type="text" id="receivedRecipient" placeholder="Recipient's name">
                                </div>
                                <div class="export-buttons">
                                    <button type="button" class="export-btn pdf generate-report" data-report="received">PDF</button>
                                </div>
                            </div>

                            <div class="report-card report-option" id="demandReport" hidden>
                                <h3>Demand Forecast &amp; Recommendation <span style="font-size: 14px; font-weight: normal;">— 2 Weeks</span></h3>
                                <div class="export-buttons">
                                    <a href="reports/export_demand_reorder_pdf.php" class="export-btn pdf">PDF</a>
                                </div>
                            </div>

                        </div>
                    </div>

                    <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const reportType = document.getElementById("reportType");

                        const reportCards = {
                            historical: document.getElementById("historicalReport"),
                            "sales-profit": document.getElementById("salesProfitReport"),
                            "sales-summary": document.getElementById("salesSummaryReport"),
                            purchase: document.getElementById("purchaseReport"),
                            received: document.getElementById("receivedReport"),
                            demand: document.getElementById("demandReport")
                        };

                        reportType.addEventListener("change", function () {
                            Object.keys(reportCards).forEach(function (type) {
                                reportCards[type].hidden = type !== reportType.value;
                            });
                        });

                        document.querySelectorAll(".generate-report").forEach(function (button) {
                            button.addEventListener("click", function () {
                                const report = this.dataset.report;
                                let startDate;
                                let endDate;
                                let recipient;
                                let url;

                                const fields = {
                                    historical: {
                                        start: "historicalStart",
                                        end: "historicalEnd",
                                        recipient: "historicalRecipient",
                                        url: "reports/export_historical_inventory_pdf.php"
                                    },
                                    "sales-profit": {
                                        start: "salesStart",
                                        end: "salesEnd",
                                        recipient: "salesRecipient",
                                        url: "reports/export_sales_profit_pdf.php"
                                    },
                                    "sales-summary": {
                                        start: "summaryStart",
                                        end: "summaryEnd",
                                        recipient: "summaryRecipient",
                                        url: "reports/export_sales_summary_pdf.php"
                                    },
                                    purchase: {
                                        start: "purchaseStart",
                                        end: "purchaseEnd",
                                        recipient: "purchaseRecipient",
                                        url: "reports/export_purchase_pdf.php"
                                    },
                                    received: {
                                        start: "receivedStart",
                                        end: "receivedEnd",
                                        recipient: "receivedRecipient",
                                        url: "reports/export_received_pdf.php"
                                    }
                                };

                                const config = fields[report];

                                if (!config) {
                                    alert("Invalid report selection.");
                                    return;
                                }

                                startDate = document.getElementById(config.start).value;
                                endDate = document.getElementById(config.end).value;
                                recipient = document.getElementById(config.recipient).value.trim();
                                url = config.url;

                                if (!startDate || !endDate) {
                                    alert("Please select both the start date and end date.");
                                    return;
                                }

                                if (startDate > endDate) {
                                    alert("The start date cannot be later than the end date.");
                                    return;
                                }

                                url += "?date_from=" + encodeURIComponent(startDate);
                                url += "&date_to=" + encodeURIComponent(endDate);
                                url += "&recipient=" + encodeURIComponent(recipient);

                                window.open(url, "_blank");
                            });
                        });
                    });
                    </script>

                    <!-- END OF REPORTS-->
                    <script src="reports/daily_reports.js"></script>


                    
                    <div id="real-stock-adjustments" class="content-section">
                        <link rel="stylesheet" href="dashboardAssets/stock_adjustment.css">
                        
                        <div class="custom-header" style="background-image: url(topbarlogo.png); background-repeat: no-repeat; background-size: cover;">
                            <div class="top-bar">
                                <div class="tab">Stock Adjustment</div>
                                <div class="user-controls">
                                    <div><?php echo htmlspecialchars($row['userName'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="user-icon">👤</div>
                                </div>
                            </div>
                        </div>

                        <div class="stock-adjustment-page">
                            <div class="stock-adjustment-header">
                                <!-- <h3>Stock Adjustment</h3> -->
                                <button type="button" class="stock-adjustment-add-btn"
                                        onclick="openStockAdjustmentForm()">
                                    + Add Adjustment
                                </button>
                            </div>

                            <div id="stockAdjustmentForm" class="stock-adjustment-card" hidden>
                                <h3>New Stock Adjustment</h3>

                                <form id="stockAdjustmentEntryForm">
                                    <label for="adjustmentProduct">Product</label>
                                    <select id="adjustmentProduct" name="product_id" required>
                                        <option value="">Select a product</option>
                                    </select>

                                    <label for="adjustmentType">Adjustment Type</label>
                                    <select id="adjustmentType" name="adjustment_type" required>
                                        <option value="increase">Add Stock</option>
                                        <option value="decrease">Deduct Stock</option>
                                    </select>

                                    <label for="adjustmentBatch">Batch Number</label>
                                    <select id="adjustmentBatch" name="batch_id" required>
                                        <option value="">Select a product first</option>
                                    </select>

                                    <div id="newBatchFields" hidden>
                                        <label for="adjustmentExpiryDate">Expiry Date</label>
                                        <input type="date" id="adjustmentExpiryDate" name="expiry_date">
                                    </div>

                                    <label for="adjustmentQuantity">Quantity</label>
                                    <input type="number" id="adjustmentQuantity"
                                        name="quantity" min="1" step="1" required>

                                    <label for="adjustmentReason">Reason</label>
                                    <select id="adjustmentReason" name="reason" required>
                                        <option value="">Select Reason</option>
                                        <option value="Damaged Products">Damaged Products</option>
                                        <option value="Expired Products">Expired Products</option>
                                        <option value="Missing Stock">Missing Stock</option>
                                        <option value="Incorrect Stock Count">Incorrect Stock Count</option>
                                        <option value="Physical Inventory Correction">Physical Inventory Correction</option>
                                        <option value="Lost or Stolen Products">Lost or Stolen Products</option>
                                        <option value="Internal Transfer">Internal Transfer</option>
                                        <option value="System Error Correction">System Error Correction</option>
                                        <option value="Other">Other</option>
                                    </select>

                                    <label for="adjustmentNotes">Notes</label>
                                    <textarea id="adjustmentNotes" name="notes"
                                            placeholder="Additional details (optional)"></textarea>

                                    <div id="stockAdjustmentMessage" role="status"></div>

                                    <div class="stock-adjustment-actions">
                                        <button type="submit" class="stock-adjustment-btn">
                                            Save Adjustment
                                        </button>
                                        <button type="button" class="stock-adjustment-cancel"
                                                onclick="closeStockAdjustmentForm()">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="stock-adjustment-card">
                                <h3>Adjustment History</h3>
                                <div class="stock-adjustment-table-wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Product</th>
                                                <th>Previous Stock</th>
                                                <th>Change</th>
                                                <th>New Stock</th>
                                                <th>Reason</th>
                                                <th>Adjusted By</th>
                                            </tr>
                                        </thead>
                                        <tbody id="stockAdjustmentHistory">
                                            <tr>
                                                <td colspan="7">Adjustment history will appear here.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        const stockAdjustmentEndpoint = 'dashboardQuery/stock_adjustments.php';

                        const adjustmentForm = document.getElementById('stockAdjustmentEntryForm');
                        const adjustmentProduct = document.getElementById('adjustmentProduct');
                        const adjustmentType = document.getElementById('adjustmentType');
                        const adjustmentBatch = document.getElementById('adjustmentBatch');
                        const adjustmentExpiry = document.getElementById('adjustmentExpiryDate');
                        const newBatchFields = document.getElementById('newBatchFields');
                        const adjustmentMessage = document.getElementById('stockAdjustmentMessage');
                        const adjustmentHistory = document.getElementById('stockAdjustmentHistory');
                        const stockAdjustmentForm = document.getElementById('stockAdjustmentForm');

                        function escapeAdjustmentHTML(value) {
                            return String(value ?? '').replace(/[&<>"']/g, character => ({
                                '&': '&amp;',
                                '<': '&lt;',
                                '>': '&gt;',
                                '"': '&quot;',
                                "'": '&#039;'
                            })[character]);
                        }

                        async function adjustmentRequest(url, options = {}) {
                            const response = await fetch(url, options);
                            const data = await response.json();

                            if (!response.ok || !data.success) {
                                throw new Error(data.message || 'Request failed.');
                            }

                            return data;
                        }

                        function showAdjustmentMessage(message, success = false) {
                            adjustmentMessage.textContent = message;
                            adjustmentMessage.style.color = success ? 'green' : 'red';
                        }

                        async function openStockAdjustmentForm() {
                            stockAdjustmentForm.hidden = false;
                            showAdjustmentMessage('');

                            try {
                                const data = await adjustmentRequest(
                                    stockAdjustmentEndpoint + '?action=products'
                                );

                                adjustmentProduct.innerHTML =
                                    '<option value="">Select a product</option>';

                                data.products.forEach(product => {
                                    const option = document.createElement('option');
                                    option.value = product.Product_ID;
                                    option.textContent = product.ProductName;
                                    adjustmentProduct.appendChild(option);
                                });

                                await loadStockAdjustmentHistory();
                            } catch (error) {
                                showAdjustmentMessage(error.message);
                                console.error('Unable to load products:', error);
                            }
                        }

                        function closeStockAdjustmentForm() {
                            stockAdjustmentForm.hidden = true;
                            adjustmentForm.reset();

                            adjustmentBatch.innerHTML =
                                '<option value="">Select a product first</option>';

                            newBatchFields.hidden = true;
                            adjustmentExpiry.required = false;

                            showAdjustmentMessage('');
                        }

                        async function loadAdjustmentBatches() {
                            const productId = adjustmentProduct.value;

                            adjustmentBatch.innerHTML =
                                '<option value="">Loading batches...</option>';

                            newBatchFields.hidden = true;
                            adjustmentExpiry.required = false;

                            if (!productId) {
                                adjustmentBatch.innerHTML =
                                    '<option value="">Select a product first</option>';
                                return;
                            }

                            try {
                                const data = await adjustmentRequest(
                                    stockAdjustmentEndpoint + '?action=batches&product_id=' +
                                    encodeURIComponent(productId)
                                );

                                adjustmentBatch.innerHTML =
                                    '<option value="">Select a batch</option>';

                                if (adjustmentType.value === 'increase') {
                                    const newOption = document.createElement('option');
                                    newOption.value = 'new';
                                    newOption.textContent = 'Create New Batch';
                                    adjustmentBatch.appendChild(newOption);
                                }

                                data.batches.forEach(batch => {
                                    const option = document.createElement('option');
                                    option.value = batch.Expiration_ID;
                                    option.textContent =
                                        batch.BatchNum + ' | Expiry: ' + batch.ExpirationDate +
                                        ' | Quantity: ' + batch.Quantity;

                                    adjustmentBatch.appendChild(option);
                                });
                            } catch (error) {
                                adjustmentBatch.innerHTML =
                                    '<option value="">Unable to load batches</option>';

                                showAdjustmentMessage(error.message);
                                console.error('Unable to load batches:', error);
                            }
                        }

                        adjustmentProduct.addEventListener('change', loadAdjustmentBatches);

                        adjustmentType.addEventListener('change', async () => {
                            await loadAdjustmentBatches();
                        });

                        adjustmentBatch.addEventListener('change', () => {
                            const isNewBatch = adjustmentBatch.value === 'new';

                            newBatchFields.hidden = !isNewBatch;
                            adjustmentExpiry.required = isNewBatch;

                            if (!isNewBatch) {
                                adjustmentExpiry.value = '';
                            }
                        });

                        async function loadStockAdjustmentHistory() {
                            adjustmentHistory.innerHTML =
                                '<tr><td colspan="7">Loading adjustment history...</td></tr>';

                            try {
                                const data = await adjustmentRequest(
                                    stockAdjustmentEndpoint + '?action=history'
                                );

                                if (!Array.isArray(data.history) || data.history.length === 0) {
                                    adjustmentHistory.innerHTML =
                                        '<tr><td colspan="7">No adjustments recorded yet.</td></tr>';
                                    return;
                                }

                                adjustmentHistory.innerHTML = data.history.map(item => {
                                    const change = item.Adjustment_Type === 'increase'
                                        ? '+' + item.Quantity
                                        : '-' + item.Quantity;

                                    return `
                                        <tr>
                                            <td>${escapeAdjustmentHTML(item.Date_Created)}</td>
                                            <td>${escapeAdjustmentHTML(item.ProductName)}</td>
                                            <td>${escapeAdjustmentHTML(item.Previous_Stock)}</td>
                                            <td>${escapeAdjustmentHTML(change)}</td>
                                            <td>${escapeAdjustmentHTML(item.New_Stock)}</td>
                                            <td>${escapeAdjustmentHTML(item.Reason)}</td>
                                            <td>${escapeAdjustmentHTML(item.User_ID ?? 'System')}</td>
                                        </tr>
                                    `;
                                }).join('');
                            } catch (error) {
                                adjustmentHistory.innerHTML =
                                    '<tr><td colspan="7">Unable to load adjustment history. Check the console for details.</td></tr>';

                                console.error('Stock adjustment history error:', error);
                            }
                        }

                        adjustmentForm.addEventListener('submit', async event => {
                            event.preventDefault();

                            showAdjustmentMessage('Saving adjustment...');

                            const submitButton = adjustmentForm.querySelector(
                                'button[type="submit"]'
                            );

                            submitButton.disabled = true;

                            try {
                                const formData = new FormData(adjustmentForm);

                                const data = await adjustmentRequest(
                                    stockAdjustmentEndpoint + '?action=save',
                                    {
                                        method: 'POST',
                                        body: formData
                                    }
                                );

                                showAdjustmentMessage(
                                    data.message + ' Total stock: ' +
                                    data.previous_stock + ' → ' + data.new_stock,
                                    true
                                );

                                adjustmentForm.reset();
                                newBatchFields.hidden = true;
                                adjustmentExpiry.required = false;

                                await loadAdjustmentBatches();
                                await loadStockAdjustmentHistory();
                            } catch (error) {
                                showAdjustmentMessage(error.message);
                                console.error('Unable to save stock adjustment:', error);
                            } finally {
                                submitButton.disabled = false;
                            }
                        });

                        loadStockAdjustmentHistory();
                    </script>
                <!--END OF STOCK ADJUSTMENT-->


                <!-- ACOUount -->
                <div id="account" class="content-section">
                    <div class="custom-header" style="background-image: url(topbarlogo.png);background-repeat: no-repeat;background-size: cover;">
                        <div class="top-bar"><div class="tab">ACCOUNT</div>
                            <div class="user-controls">
                                <div>      
                                <?php
                                    echo $row['userName'];                        
                                ?>
                                </div>
                                <div class="user-icon">👤</div>

                            </div>
                            
                        </div>
                        <div style="height: 50px;"></div>
                    </div>
                    
                    <div class="account-page-container">
                        <div class="account-card">
                            <div class="account-info-left">
                                <div class="info-section">
                                    <h3>Basic Info</h3>
                                    <div class="profile-picture-section">
                                        <span class="info-label">Profile Picture</span>
                                        <div class="profile-pic-controls">
                                            <div class="profile-pic-display">
                                                <span>👤</span>
                                            </div>
                                            <div class="upload-links">
                                            <form id="userimage" action="userimage.php" method="POST" >
                                                <input type="hidden" name="table" value="users">
                                                <input type="file" name="image" id="profile-upload" accept="image/*" style="display: none;">
                                                <a href="#" id="upload-link">Upload new picture</a>
                                                <a href="#" id="remove-link" class="remove-link">Remove</a>
                                                <button type="submit">SAVE</button>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Name</span>
                                        <span class="info-value"><?php echo $row['userName'];?><span>&rsaquo;</span></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Date of Birth</span>
                                        <span class="info-value">December 24, 1900<span>&rsaquo;</span></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Gender</span>
                                        <span class="info-value">Male <span>&rsaquo;</span></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Email</span>
                                        <span class="info-value"><?php echo $row['email'];?><span>&rsaquo;</span></span>
                                    </div>
                                </div>
                                <div class="info-section">
                                    <h3>Account Info</h3>
                                    <div class="info-row">
                                        <span class="info-label">Account ID</span>
                                        <span class="info-value"><?php echo $row['id'];?> <span>&rsaquo;</span></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Contact</span>
                                        <span class="info-value"><?php echo $row['phone'];?><<span>&rsaquo;</span></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Password</span>
                                        <span class="info-value">
                                        <?php 
                                            $passwordLength = strlen($row['password']);
                                            echo str_repeat('•', $passwordLength); 
                                        ?>
                                        <span>&rsaquo;</span></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">User Type</span>
                                        <span class="info-value">Administrator <span>&rsaquo;</span></span>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                        
                        <?php if (isset($_SESSION['accountType']) && strtolower(trim($_SESSION['accountType'])) === 'super admin') { ?>

                        

                        <div class="users-section">

                            <div class="users-header">
                                <h2>Users / Accounts</h2>

                                <button type="button" id="users-add-btn">
                                    Add User
                                </button>

                                <button type="button" id="edit-user-btn">
                                    Edit User
                                </button>
                            </div>

                            <table id="users-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Account Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php while ($user = mysqli_fetch_assoc($usersResult)) { ?>

                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($user['id']) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($user['userName']) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($user['email']) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($user['phone']) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($user['accountType']) ?>
                                            </td>

                                            <td>
                                                <button
                                                    type="button"
                                                    class="select-user-btn"
                                                    data-id="<?= htmlspecialchars($user['id']) ?>"
                                                    data-username="<?= htmlspecialchars($user['userName']) ?>"
                                                    data-email="<?= htmlspecialchars($user['email']) ?>"
                                                    data-phone="<?= htmlspecialchars($user['phone']) ?>"
                                                    data-account-type="<?= htmlspecialchars($user['accountType']) ?>">
                                                    📝 Select
                                                </button>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                            </table>

                        </div>

                        <!-- ADD USER MODAL -->
                        <div id="addUserModal" class="modal" style="display: none;">
                            <div class="modal-content">

                                <h3>Add New User</h3>

                                <form id="userForm" action="register.php" method="POST">

                                    <input type="hidden" name="signup" value="1">

                                    <label>Username:</label>
                                    <input type="text" name="userName" required>

                                    <label>Email:</label>
                                    <input type="email" name="email" required>

                                    <label>Password:</label>
                                    <input type="password" name="password" required>

                                    <label>Phone:</label>
                                    <input
                                        type="tel"
                                        name="phone"
                                        maxlength="11"
                                        pattern="[0-9]{11}"
                                        required
                                    >
                                    
                                    <!-- USER TYPE -->
                                    <label>Account Type:</label>
                                    <select name="accountType" required>
                                        <option value="" disabled selected>Select Account Type</option>
                                        <option value="Manager">Manager</option>
                                        <option value="Cashier">Cashier</option>
                                        <option value="Super Admin">Super Admin</option>
                                    </select>

                                    <div class="modal-buttons">
                                        <button type="submit" class="na-btn na-btn-add">
                                            Create Account
                                        </button>

                                        <button
                                            type="button"
                                            id="user-cancel-btn"
                                            class="na-btn na-btn-cancel"
                                        >
                                            Cancel
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>

                        <!-- EDIT USER MODAL -->
                        <div id="editUserModal" class="modal" style="display: none; ">
                            <div class="modal-content">

                                <h3>Edit User</h3>

                                <form id="editUserForm" action="update.php" method="POST">

                                    <input type="hidden" name="table" value="users">

                                    <input
                                        type="hidden"
                                        name="id"
                                        id="edit-user-id">

                                    <label>Username:</label>
                                    <input
                                        type="text"
                                        name="userName"
                                        id="edit-user-username"
                                        required>

                                    <label>Email:</label>
                                    <input
                                        type="email"
                                        name="email"
                                        id="edit-user-email"
                                        required>

                                    <label>Phone:</label>
                                    <input
                                        type="tel"
                                        name="phone"
                                        id="edit-user-phone"
                                        maxlength="11"
                                        required>


                                    <p style="font-size: 12px; color: gray;">
                                        Leave the password unchanged when editing.
                                    </p>

                                    <!-- EDIT USER ACCOUNT TYPE -->
                                    <label>Account Type:</label>
                                    <select name="accountType" id="edit-user-account-type" required>
                                        <option value="Manager">Manager</option>
                                        <option value="Cashier">Cashier</option>
                                        <option value="Super Admin">Super Admin</option>
                                    </select>

                                    <div class="modal-buttons">
                                        <button
                                            type="submit"
                                            class="na-btn na-btn-add">

                                            Save Changes

                                        </button>

                                        <button
                                            type="button"
                                            id="edit-user-cancel-btn"
                                            class="na-btn na-btn-cancel">

                                            Cancel

                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>

                        <?php } ?>
                            
                    </div>
                </div>






                <!-- REVISION TAX SETTING -->
                
                <?php
                $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                if (in_array($accountType, ['manager', 'super admin'], true)) {
                ?>
                <div id="tax-settings" class="content-section">
                    <div class="custom-header"
                        style="background-image:url(topbarlogo.png);
                                background-repeat:no-repeat;
                                background-size:cover;
                                height:80px;">
                        <div class="top-bar">
                            <div class="tab">TAX SETTINGS</div>
                            <div class="user-controls">
                                <div><?= htmlspecialchars($row['userName'] ?? '') ?></div>
                                <div class="user-icon">👤</div>
                            </div>
                        </div>
                    </div>

                    <div style="max-width:650px; margin:30px auto; padding:25px;
                                background:#fff; border-radius:12px;
                                box-shadow:0 3px 12px rgba(0,0,0,.12);">

                        <h2>POS Tax Configuration</h2>
                        <p style="color:#666;">
                            Configure how tax is calculated during checkout.
                        </p>

                        <form id="taxSettingsForm">
                            <label for="tax-name">Tax Name</label>
                            <input type="text" id="tax-name" maxlength="50"
                                required style="width:100%; padding:10px;
                                margin:6px 0 16px; box-sizing:border-box;">

                            <label for="tax-rate">Tax Rate (%)</label>
                            <input type="number" id="tax-rate" min="0" max="100"
                                step="0.01" required
                                style="width:100%; padding:10px;
                                margin:6px 0 16px; box-sizing:border-box;">

                            <label for="tax-mode">Tax Mode</label>
                            <select id="tax-mode" required
                                    style="width:100%; padding:10px;
                                    margin:6px 0 16px; box-sizing:border-box;">
                                <option value="exclusive">
                                    Exclusive — tax is added to the subtotal
                                </option>
                                <option value="inclusive">
                                    Inclusive — tax is included in the displayed price
                                </option>
                            </select>

                            <label style="display:flex; gap:10px; align-items:center;
                                        margin:10px 0 20px;">
                                <input type="checkbox" id="tax-enabled">
                                Enable tax during checkout
                            </label>

                            <p id="tax-settings-message"
                            role="status" style="margin:12px 0;"></p>

                            <button type="submit" class="na-btn na-btn-add"
                                    id="save-tax-settings-btn">
                                Save Tax Settings
                            </button>
                        </form>
                    </div>
                </div>





                
                <!-- ================= ARCHIVE & RESTORE ================= -->
                <?php
                $archiveAccountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                if (in_array($archiveAccountType, ['manager', 'super admin'], true)) {
                ?>

                <div id="archive-restore" class="content-section">

                    <!-- HEADER: MATCH TRANSACTIONS & SALES -->
                    <div class="custom-header sticky-div-1"
                        style="background-image:url('topbarlogo.png'); background-repeat:no-repeat; background-size:cover;">

                        <div class="top-bar">
                            <div class="tab">ARCHIVE &amp; RESTORE</div>

                            <div class="user-controls">
                                <div>
                                    <?php echo htmlspecialchars($row['userName'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                                <div class="user-icon">👤</div>
                            </div>
                        </div>

                        <div class="controls-bar">
                            <div class="controls">
                                <div class="toggle-buttons" role="tablist">
                                    <button type="button"
                                            class="toggle-btn active"
                                            data-ar-type="product"
                                            aria-selected="true">
                                        Products
                                    </button>

                                    <button type="button"
                                            class="toggle-btn"
                                            data-ar-type="supplier"
                                            aria-selected="false">
                                        Suppliers
                                    </button>

                                    <button type="button"
                                            class="toggle-btn"
                                            data-ar-type="user"
                                            aria-selected="false">
                                        Users
                                    </button>
                                </div>
                            </div>

                            <div class="header-icons">
                                <span class="icon-wrapper"
                                    onclick="openNotifications()"
                                    title="Notifications">
                                    <i class="fas fa-envelope"></i>
                                    <span class="badge">0</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- TOOLBAR: MATCH EXISTING TABS -->
                    <div class="toolbar sticky-div-2"
                        style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">

                        <div class="toggle-buttons ar-status-controls">
                            <button type="button"
                                    class="toggle-btn active"
                                    data-ar-status="active">
                                Active Records
                            </button>

                            <button type="button"
                                    class="toggle-btn"
                                    data-ar-status="archived">
                                Archived / Deactivated
                            </button>
                        </div>

                        <div class="search-container"
                            style="display:flex; justify-content:flex-end; align-items:center; gap:8px; flex-wrap:wrap;">

                            <input type="text"
                                id="ar-search"
                                placeholder="🔍 Quick Search..."
                                style="padding:8px 12px; border-radius:6px; border:1px solid #ccc; width:250px; max-width:100%;">

                            <button type="button"
                                    id="ar-refresh-btn"
                                    class="na-btn na-btn-add1"
                                    title="Refresh records">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STATUS MESSAGE -->
                    <div id="ar-message" class="ar-message" role="status" aria-live="polite" style="color: white;">
                        Loading records...
                    </div>

                    <!-- TABLE: USE EXISTING TABLE CONTAINER -->
                    <div class="table-container ar-table-container">
                        <table class="transaction-sales-table ar-table">
                            <thead class="sticky-div">
                                <tr>
                                    <th>#</th>
                                    <th>Record Name</th>
                                    <th id="ar-details-heading">Category</th>
                                    <th id="ar-identifier-heading">Barcode</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody id="ar-table-body">
                                <tr>
                                    <td colspan="6" class="ar-empty">Loading records...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

                <?php } ?>








                                
                <?php
                $shiftReviewRole = strtolower(trim($_SESSION['accountType'] ?? ''));

                if (in_array($shiftReviewRole, ['manager', 'super admin'], true)) {
                ?>

                <div id="shift-review" class="content-section">

                    <!-- Custom Header: matching Pricing & Markup -->
                    <div class="custom-header sticky-div-1 shift-review-header"
                        style="background-image:url(topbarlogo.png);
                                background-repeat:no-repeat;
                                background-size:cover;">

                        <div class="top-bar">
                            <div class="tab">CASHIER SHIFT REVIEW</div>

                            <div class="user-controls">
                                <div><?= htmlspecialchars($row['userName'] ?? '') ?></div>
                                <div class="user-icon">👤</div>
                            </div>
                        </div>


                        <div class="controls-bar">
                            <div class="controls">
                                <div class="toggle-buttons">
                                    <button type="button"
                                        id="closed-shifts-view-btn"
                                        class="toggle-btn active">
                                        Closed Shifts
                                    </button>

                                    <button type="button"
                                        id="interim-cash-count-view-btn"
                                        class="toggle-btn">
                                        Interim Cash Count
                                    </button>
                                </div>

                                <!-- <span class="shift-review-subtitle">
                                    Review cashier shifts, cash reconciliation, and interim pause counts.
                                </span> -->
                            </div>

                            <!-- <div class="header-icons">
                                <span class="icon-wrapper"
                                    onclick="openNotifications()">
                                    <i class="fas fa-envelope"></i>
                                    <span class="badge">0</span>
                                </span>
                            </div> -->
                        </div>

                    </div>

                    <!-- Content padding -->
                    <div class="shift-review-content">


                        <div id="closed-shifts" class="shift-review-tab-content">
                            <div class="pricing-panel">
                                <div class="shift-review-panel-heading">
                                    <div>
                                        <h3>Shift Records</h3>
                                        <button type="button"
                                            class="na-btn na-btn-add"
                                            id="refresh-shifts-btn">
                                            <i class="fas fa-sync-alt"></i> Refresh Shifts
                                        </button>
                                        <p>
                                            Review opening float, cash sales, expected cash,
                                            actual cash, and variance before finalizing a shift.
                                        </p>
                                        <span class="shift-review-subtitle">
                                        Review cashier shifts, cash reconciliation, and finalization.
                                        </span>
                                    </div>
                                </div>

                            </div>     
                            <br>
                                            
                            <div class="pricing-panel" style="padding: 20px;">                            

                                <div id="shift-review-message"
                                    role="status"
                                    class="shift-review-message"></div>

                                <div class="shift-review-table-wrapper">
                                    <table class="products-table"
                                        id="shift-review-table">
                                        <thead>
                                            <tr>
                                                <th>Shift ID</th>
                                                <th>Cashier</th>
                                                <th>Opened</th>
                                                <th>Closed</th>
                                                <th>Opening Float</th>
                                                <th>Cash Sales</th>
                                                <th>Expected Cash</th>
                                                <th>Actual Cash</th>
                                                <th>Variance</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody id="shift-review-tbody">
                                            <tr>
                                                <td colspan="11" class="shift-review-empty">
                                                    Click Refresh Shifts to load records.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>




                        <!-- Interim Cash Count tab content -->
                        <div id="interim-cash-count" class="shift-review-tab-content"
                            style="display: none;">

                            <div class="pricing-panel">
                                <div class="shift-review-panel-heading">
                                    <div>
                                        <h3>Interim Cash Counts</h3>
                                        <p>Review cash counts saved when cashiers pause their shifts.</p>
                                    </div>

                                    <button type="button"
                                            class="na-btn na-btn-add"
                                            id="refresh-cash-counts-btn">
                                        <i class="fas fa-sync-alt"></i> Refresh Counts
                                    </button>
                                </div>
                            </div>
                            <br>

                            <div class="pricing-panel" style="padding: 20px;">
                                <div id="cash-count-review-message"
                                    role="status"
                                    class="shift-review-message"></div>

                                <div class="shift-review-table-wrapper">
                                    <table class="products-table" id="cash-count-review-table">
                                        <thead>
                                            <tr>
                                                <th>Count ID</th>
                                                <th>Shift ID</th>
                                                <th>Cashier</th>
                                                <th>Counted At</th>
                                                <th>Expected Cash</th>
                                                <th>Actual Cash</th>
                                                <th>Variance</th>
                                                <th>Notes</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cash-count-review-tbody">
                                            <tr>
                                                <td colspan="9" style="text-align: center;">
                                                    Click Refresh Counts to load records.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>






                    </div>
                </div>

                <?php } ?>


                <!-- SHIFT REVIEW CLOSED -->

                <script>
                    (function () {
                        const refreshButton = document.getElementById('refresh-shifts-btn');
                        const tbody = document.getElementById('shift-review-tbody');
                        const message = document.getElementById('shift-review-message');

                        // This script may load on pages where the manager-only section is absent.
                        if (!refreshButton || !tbody || !message) return;

                        const endpoint = 'POS_TEST/cashier_shift.php';

                        function escapeHtml(value) {
                            return String(value ?? '').replace(/[&<>"']/g, function (char) {
                                return {
                                    '&': '&amp;',
                                    '<': '&lt;',
                                    '>': '&gt;',
                                    '"': '&quot;',
                                    "'": '&#039;'
                                }[char];
                            });
                        }

                        function money(value) {
                            return '₱' + Number(value ?? 0).toLocaleString('en-PH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }

                        function renderShifts(shifts) {
                            if (!Array.isArray(shifts) || shifts.length === 0) {
                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="11" style="text-align:center;">
                                            No closed shifts found.
                                        </td>
                                    </tr>`;
                                return;
                            }

                            tbody.innerHTML = shifts.map(function (shift) {
                                const variance = Number(shift.Cash_Variance ?? 0);
                                const varianceColor = variance < 0 ? 'red' :
                                                    variance > 0 ? 'green' : 'inherit';

                                // const action = shift.Status === 'Closed'
                                //     ? `<button type="button"
                                //             class="na-btn na-btn-add finalize-shift-btn"
                                //             data-shift-id="${escapeHtml(shift.Shift_ID)}">
                                //         Finalize
                                //     </button>`
                                //     : '<span>Already finalized</span>';




                                const shiftId = escapeHtml(shift.Shift_ID);

                                const reportButton = `
                                    <button type="button"
                                        class="na-btn na-btn-add view-z-report-btn"
                                        data-shift-id="${shiftId}">
                                        <i class="fas fa-file-alt"></i> View Report
                                    </button>`;

                                const finalizeButton = shift.Status === 'Closed'
                                    ? `<button type="button"
                                            class="na-btn na-btn-add finalize-shift-btn"
                                            data-shift-id="${shiftId}">
                                            <i class="fas fa-check"></i> Finalize
                                    </button>`
                                    : '<span>Already finalized</span>';

                                const action = `
                                    <div style="display:flex;gap:8px;align-items:center;">
                                        ${reportButton}
                                        ${finalizeButton}
                                    </div>`;






                                return `
                                    <tr>
                                        <td>${escapeHtml(shift.Shift_ID)}</td>
                                        <td>${escapeHtml(shift.CashierName || 'Unknown')}</td>
                                        <td>${escapeHtml(shift.Opened_At || '—')}</td>
                                        <td>${escapeHtml(shift.Closed_At || '—')}</td>
                                        <td>${money(shift.Opening_Float)}</td>
                                        <td>${money(shift.ShiftSales)}</td>
                                        <td>${money(shift.Expected_Cash)}</td>
                                        <td>${money(shift.Actual_Cash)}</td>
                                        <td style="color:${varianceColor};font-weight:bold;">
                                            ${variance > 0 ? '+' : ''}${money(variance)}
                                        </td>
                                        <td>${escapeHtml(shift.Status)}</td>
                                        <td>${action}</td>
                                    </tr>`;
                            }).join('');
                        }

                        async function loadShifts() {
                            refreshButton.disabled = true;
                            message.textContent = 'Loading shifts...';

                            try {
                                const response = await fetch(
                                    endpoint + '?action=list_closed&t=' + Date.now(),
                                    { cache: 'no-store' }
                                );

                                const result = await response.json();

                                if (!response.ok || !result.success) {
                                    throw new Error(result.message || 'Unable to load shifts.');
                                }

                                renderShifts(result.shifts);
                                message.textContent =
                                    'Loaded ' + result.shifts.length + ' shift(s).';

                            } catch (error) {
                                console.error('Shift review loading failed:', error);

                                message.textContent = error.message;
                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="11" style="text-align:center;color:red;">
                                            Failed to load shifts. Check the browser console.
                                        </td>
                                    </tr>`;
                            } finally {
                                refreshButton.disabled = false;
                            }
                        }

                        refreshButton.addEventListener('click', loadShifts);


                        tbody.addEventListener('click', function (event) {
                            const button = event.target.closest('.view-z-report-btn');

                            if (!button) return;

                            const shiftId = button.dataset.shiftId;

                            if (!shiftId) {
                                message.textContent = 'Invalid shift ID.';
                                return;
                            }

                            window.open(
                                'POS_TEST/z_report.php?shift_id=' + encodeURIComponent(shiftId),
                                '_blank',
                                'noopener'
                            );
                        });




                        // Finalize buttons are created dynamically when the table loads.
                        tbody.addEventListener('click', async function (event) {
                            const button = event.target.closest('.finalize-shift-btn');
                            if (!button) return;

                            const shiftId = button.dataset.shiftId;

                            if (!confirm(
                                'Finalize cashier shift #' + shiftId + '? This should only be done after reviewing its cash reconciliation.'
                            )) {
                                return;
                            }

                            button.disabled = true;
                            message.textContent = 'Finalizing shift #' + shiftId + '...';



                            try {
                                const body = new URLSearchParams({
                                    action: 'finalize',
                                    shift_id: shiftId
                                });

                                const response = await fetch(endpoint, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: body
                                });

                                const result = await response.json();

                                if (!response.ok || !result.success) {
                                    throw new Error(result.message || 'Unable to finalize shift.');
                                }

                                message.textContent = result.message;
                                await loadShifts();

                            } catch (error) {
                                console.error('Shift finalization failed:', error);
                                message.textContent = error.message;
                                button.disabled = false;
                            }
                        });
                    })();
                </script>

                <!-- SHIFT REVIEW PAUSED -->

                <script>
                    (function () {
                        const closedButton = document.getElementById('closed-shifts-view-btn');
                        const interimButton = document.getElementById('interim-cash-count-view-btn');
                        const closedContent = document.getElementById('closed-shifts');
                        const interimContent = document.getElementById('interim-cash-count');

                        const refreshButton = document.getElementById('refresh-cash-counts-btn');
                        const tbody = document.getElementById('cash-count-review-tbody');
                        const message = document.getElementById('cash-count-review-message');

                        const endpoint = 'POS_TEST/cashier_shift.php';

                        // Avoid errors if the manager-only section is not present.
                        if (
                            !closedButton || !interimButton ||
                            !closedContent || !interimContent ||
                            !refreshButton || !tbody || !message
                        ) {
                            return;
                        }

                        let cashCounts = [];

                        function escapeHtml(value) {
                            return String(value ?? '').replace(/[&<>"']/g, function (char) {
                                return {
                                    '&': '&amp;',
                                    '<': '&lt;',
                                    '>': '&gt;',
                                    '"': '&quot;',
                                    "'": '&#039;'
                                }[char];
                            });
                        }

                        function money(value) {
                            return '₱' + Number(value ?? 0).toLocaleString('en-PH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }

                        function showTab(tab) {
                            const showClosed = tab === 'closed';

                            closedContent.style.display = showClosed ? '' : 'none';
                            interimContent.style.display = showClosed ? 'none' : '';

                            closedButton.classList.toggle('active', showClosed);
                            interimButton.classList.toggle('active', !showClosed);

                            if (!showClosed && cashCounts.length === 0) {
                                loadCashCounts();
                            }
                        }

                        closedButton.addEventListener('click', function () {
                            showTab('closed');
                        });

                        interimButton.addEventListener('click', function () {
                            showTab('interim');
                        });

                        function renderCashCounts(records) {
                            if (!Array.isArray(records) || records.length === 0) {
                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="9" style="text-align:center;">
                                            No interim cash-count records found.
                                        </td>
                                    </tr>`;
                                return;
                            }

                            tbody.innerHTML = records.map(function (record) {
                                const variance = Number(record.Cash_Variance ?? 0);
                                const varianceColor = variance < 0
                                    ? 'red'
                                    : variance > 0
                                        ? 'green'
                                        : 'inherit';

                                return `
                                    <tr>
                                        <td>${escapeHtml(record.Cash_Count_ID)}</td>
                                        <td>${escapeHtml(record.Shift_ID)}</td>
                                        <td>${escapeHtml(record.CashierName || 'Unknown')}</td>
                                        <td>${escapeHtml(record.Counted_At || '—')}</td>
                                        <td>${money(record.Expected_Cash)}</td>
                                        <td>${money(record.Actual_Cash)}</td>
                                        <td style="color:${varianceColor};font-weight:bold;">
                                            ${variance > 0 ? '+' : ''}${money(variance)}
                                        </td>
                                        <td>${escapeHtml(record.Notes || '—')}</td>
                                        <td>
                                            <button type="button"
                                                class="na-btn na-btn-add print-cash-count-btn"
                                                data-count-id="${escapeHtml(record.Cash_Count_ID)}">
                                                <i class="fas fa-print"></i> Print
                                            </button>
                                        </td>
                                    </tr>`;
                            }).join('');
                        }

                        async function loadCashCounts() {
                            refreshButton.disabled = true;
                            message.textContent = 'Loading interim cash counts...';

                            try {
                                const response = await fetch(
                                    endpoint + '?action=list_cash_counts&t=' + Date.now(),
                                    { cache: 'no-store' }
                                );

                                const result = await response.json();

                                if (!response.ok || !result.success) {
                                    throw new Error(
                                        result.message || 'Unable to load interim cash counts.'
                                    );
                                }

                                cashCounts = Array.isArray(result.cash_counts)
                                    ? result.cash_counts
                                    : [];

                                renderCashCounts(cashCounts);

                                message.textContent =
                                    'Loaded ' + cashCounts.length + ' interim cash-count record(s).';

                            } catch (error) {
                                console.error('Cash-count review loading failed:', error);

                                message.textContent = error.message;

                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="9" style="text-align:center;color:red;">
                                            Failed to load interim cash counts. Check the browser console.
                                        </td>
                                    </tr>`;
                            } finally {
                                refreshButton.disabled = false;
                            }
                        }

                        refreshButton.addEventListener('click', loadCashCounts);

                        // Print one selected cash-count record.
                        tbody.addEventListener('click', function (event) {
                            const button = event.target.closest('.print-cash-count-btn');

                            if (!button) return;

                            const countId = button.dataset.countId;

                            const record = cashCounts.find(function (item) {
                                return String(item.Cash_Count_ID) === String(countId);
                            });

                            if (!record) {
                                message.textContent = 'Cash-count record not found. Refresh the table and try again.';
                                return;
                            }

                            const variance = Number(record.Cash_Variance ?? 0);
                            const varianceLabel = variance > 0
                                ? '+' + money(variance)
                                : money(variance);

                            const printWindow = window.open(
                                '',
                                '_blank',
                                'width=850,height=700'
                            );

                            if (!printWindow) {
                                message.textContent = 'The print window was blocked. Allow pop-ups for this site and try again.';
                                return;
                            }

                            const html = `
                                <!DOCTYPE html>
                                <html lang="en">
                                <head>
                                    <meta charset="UTF-8">
                                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                    <title>Interim Cash Count #${escapeHtml(record.Cash_Count_ID)}</title>
                                    <style>
                                        body {
                                            font-family: Arial, sans-serif;
                                            color: #222;
                                            max-width: 760px;
                                            margin: 35px auto;
                                            padding: 20px;
                                        }
                                        h1 { text-align: center; margin-bottom: 5px; }
                                        .subtitle { text-align: center; color: #555; margin-bottom: 30px; }
                                        table { width: 100%; border-collapse: collapse; }
                                        th, td {
                                            border: 1px solid #ccc;
                                            padding: 12px;
                                            text-align: left;
                                        }
                                        th { width: 38%; background: #f3f3f3; }
                                        .variance { font-weight: bold; }
                                        .signatures {
                                            display: flex;
                                            justify-content: space-between;
                                            gap: 40px;
                                            margin-top: 65px;
                                        }
                                        .signature {
                                            flex: 1;
                                            border-top: 1px solid #333;
                                            padding-top: 8px;
                                            text-align: center;
                                        }
                                        .print-button {
                                            margin-top: 25px;
                                            padding: 10px 18px;
                                            cursor: pointer;
                                        }
                                        @media print {
                                            body { margin: 0; padding: 10px; }
                                            .print-button { display: none; }
                                        }
                                    </style>
                                </head>
                                <body>
                                    <h1>Interim Cash Count</h1>
                                    <div class="subtitle">Cashier Shift Review Record</div>

                                    <table>
                                        <tr>
                                            <th>Cash Count ID</th>
                                            <td>${escapeHtml(record.Cash_Count_ID)}</td>
                                        </tr>
                                        <tr>
                                            <th>Shift ID</th>
                                            <td>${escapeHtml(record.Shift_ID)}</td>
                                        </tr>
                                        <tr>
                                            <th>Cashier</th>
                                            <td>${escapeHtml(record.CashierName || 'Unknown')}</td>
                                        </tr>
                                        <tr>
                                            <th>Counted At</th>
                                            <td>${escapeHtml(record.Counted_At || '—')}</td>
                                        </tr>
                                        <tr>
                                            <th>Expected Cash</th>
                                            <td>${money(record.Expected_Cash)}</td>
                                        </tr>
                                        <tr>
                                            <th>Actual Cash</th>
                                            <td>${money(record.Actual_Cash)}</td>
                                        </tr>
                                        <tr>
                                            <th>Cash Variance</th>
                                            <td class="variance">${varianceLabel}</td>
                                        </tr>
                                        <tr>
                                            <th>Notes</th>
                                            <td>${escapeHtml(record.Notes || 'None')}</td>
                                        </tr>
                                        <tr>
                                            <th>Confirmed By</th>
                                            <td>${escapeHtml(record.ConfirmedByName || 'Not recorded')}</td>
                                        </tr>
                                    </table>

                                    <div class="signatures">
                                        <div class="signature">Cashier</div>
                                        <div class="signature">Manager / Reviewer</div>
                                    </div>

                                    <button class="print-button" onclick="window.print()">Print Record</button>
                                </body>
                                </html>`;

                            printWindow.document.open();
                            printWindow.document.write(html);
                            printWindow.document.close();

                            printWindow.onload = function () {
                                printWindow.focus();
                                printWindow.print();
                            };
                        });

                    })();
                </script>                



                <script>
                    (function () {
                        const form = document.getElementById('taxSettingsForm');
                        if (!form) return;

                        const nameInput = document.getElementById('tax-name');
                        const rateInput = document.getElementById('tax-rate');
                        const modeInput = document.getElementById('tax-mode');
                        const enabledInput = document.getElementById('tax-enabled');
                        const message = document.getElementById('tax-settings-message');
                        const saveButton = document.getElementById('save-tax-settings-btn');

                        async function loadTaxSettings() {
                            message.textContent = 'Loading tax settings...';

                            try {
                                const response = await fetch(
                                    'POS_TEST/get_tax_settings.php?t=' + Date.now(),
                                    { cache: 'no-store' }
                                );
                                const result = await response.json();

                                if (!response.ok || result.status !== 'success') {
                                    throw new Error(result.message || 'Unable to load tax settings.');
                                }

                                const settings = result.settings;
                                nameInput.value = settings.Tax_Name ?? 'VAT';
                                rateInput.value = settings.Tax_Rate ?? '0.00';
                                modeInput.value = settings.Tax_Mode ?? 'exclusive';
                                enabledInput.checked = Number(settings.Is_Enabled) === 1;

                                message.textContent = 'Tax settings loaded.';
                            } catch (error) {
                                message.textContent = error.message;
                            }
                        }

                        form.addEventListener('submit', async function (event) {
                            event.preventDefault();

                            const taxRate = Number(rateInput.value);

                            if (!Number.isFinite(taxRate) || taxRate < 0 || taxRate > 100) {
                                message.textContent = 'Enter a tax rate from 0 to 100.';
                                return;
                            }

                            const payload = {
                                Tax_Name: nameInput.value.trim(),
                                Tax_Rate: taxRate,
                                Tax_Mode: modeInput.value,
                                Is_Enabled: enabledInput.checked ? 1 : 0
                            };

                            if (!payload.Tax_Name) {
                                message.textContent = 'Enter a tax name.';
                                return;
                            }

                            if (!confirm('Save these tax settings?')) return;

                            saveButton.disabled = true;
                            message.textContent = 'Saving...';

                            try {
                                const response = await fetch('POS_TEST/save_tax_settings.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify(payload)
                                });

                                const result = await response.json();

                                if (!response.ok || result.status !== 'success') {
                                    throw new Error(result.message || 'Unable to save tax settings.');
                                }

                                // message.textContent = 'Tax settings saved successfully.';
                                await loadTaxSettings();
                                message.textContent = 'Tax settings saved successfully.';
                            } catch (error) {
                                message.textContent = error.message;
                            } finally {
                                saveButton.disabled = false;
                            }
                        });

                        loadTaxSettings();
                    })();
                </script>
                <?php } ?>



                <!-- REVISION FOR MARK UP -->


                <?php
                $accountType = strtolower(trim($_SESSION['accountType'] ?? ''));
                if (in_array($accountType, ['manager', 'super admin'], true)) {
                ?>
                <div id="pricing-markup" class="content-section">

                                


                    <div class="custom-header sticky-div-1"
                        style="background-image:url(topbarlogo.png);
                                background-repeat:no-repeat;
                                background-size:cover;">

                        <div class="top-bar">
                            <div class="tab">PRICING &amp; MARKUP</div>

                            <div class="user-controls">
                                <div><?= htmlspecialchars($row['userName'] ?? '') ?></div>
                                <div class="user-icon">👤</div>
                            </div>
                        </div>

                        <div class="controls-bar">
                            <div class="controls">
                                <div class="toggle-buttons">
                                    <button type="button"
                                            class="toggle-btn active"
                                            
                                            data-pricing-tab="rules"
                                            aria-pressed="true">
                                        Pricing Rules
                                    </button>

                                    <button type="button"
                                            class="toggle-btn"
                                            data-pricing-tab="suppliers"
                                            aria-pressed="false">
                                        Supplier Products
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>


                        <div style="padding:2px;">
                            <div id="pricing-rules-panel">
                                <!-- Keep your existing pricing controls and preview here. -->

                                <div class="pricing-panel">
                                        <h3>Pricing Rules</h3>
                                        <p style="color: black;">
                                            Configure markup percentages and preview suggested selling
                                            prices using each supplier's purchase price.
                                        </p>
                                </div>

                                <div id="pricing-message"
                                    role="status"
                                    
                                    style="margin:15px 0;" hidden>
                                    <!-- Loading pricing information... -->
                                    
                                </div>

                                <div class="pricing-rule-grid">
                                    <!-- GENERAL MARKUP -->
                                    <div class="pricing-panel">
                                        <h3>General Markup</h3>
                                        <p>Default markup used when no category or product rule exists.</p>

                                        <label for="general-markup">Markup (%)</label>
                                        <input type="number" id="general-markup"
                                            min="0" max="10000" step="0.01" value="20">

                                        <button type="button" id="save-general-markup"
                                                class="na-btn na-btn-add">
                                            Save General Markup
                                        </button>
                                    </div>

                                    <!-- CATEGORY MARKUP -->
                                    <div class="pricing-panel">
                                        <h3>Category Markup</h3>

                                        <label for="markup-category">Product Category</label>
                                        <select id="markup-category">
                                            <option value="">Select category</option>
                                        </select>

                                        <label for="category-markup">Markup (%)</label>
                                        <input type="number" id="category-markup"
                                            min="0" max="10000" step="0.01">

                                        <button type="button" id="save-category-markup"
                                                class="na-btn na-btn-add">
                                            Save Category Markup
                                        </button>
                                    </div>

                                    <!-- PRODUCT MARKUP -->
                                    <div class="pricing-panel">
                                        <h3>Product-Specific Markup</h3>

                                        <label for="markup-product">Product</label>
                                        <select id="markup-product">
                                            <option value="">Select product</option>
                                        </select>

                                        <label for="product-markup">Markup (%)</label>
                                        <input type="number" id="product-markup"
                                            min="0" max="10000" step="0.01">

                                        <button type="button" id="save-product-markup"
                                                class="na-btn na-btn-add">
                                            Save Product Markup
                                        </button>
                                    </div>
                                </div>

                                <!-- PRICE PREVIEW -->
                                <div class="pricing-panel">
                                    <h3>Supplier Price &amp; Selling Price Preview</h3>

                                    <p>
                                        Suggested selling price = supplier purchase price
                                        × (1 + markup percentage ÷ 100).
                                    </p>

                                    <p>
                                        The preview does not change the product's current StorePrice.
                                    </p>

                                    <div style="overflow-x:auto;">
                                        <table class="products-table" style="width:100%;">

                                            <thead>
                                                <tr>
                                                    <th>Supplier</th>
                                                    <th>Purchase Price</th>
                                                    <th>Effective Markup</th>
                                                    <th>Markup Source</th>
                                                    <th>Current Store Price</th>
                                                    <th>Suggested Price</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="supplier-price-preview">
                                                <tr>
                                                    <td colspan="7">Select a product to preview prices.</td>
                                                </tr>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>

                                <!-- CURRENT RULES -->
                                <div class="pricing-panel">
                                    <h3>Current Markup Rules</h3>
                                    <div id="pricing-rules-summary">
                                        No rules loaded.
                                    </div>
                                </div>
                            </div>



                            <div id="supplier-products-panel" style="display: none;">

                                <div class="pricing-panel supplier-products-intro">
                                    <h3>Supplier–Product Management</h3>
                                    <p>
                                        Link products to their suppliers and maintain each supplier's
                                        purchase price independently. Updating a purchase price does
                                        not automatically change the product's selling price.
                                    </p>
                                </div>

                                <div class="pricing-panel">
                                    <h3>Add Supplier Offer</h3>

                                    <form id="supplier-product-form">

                                        <div class="supplier-product-form-grid">

                                            <div class="supplier-product-field">
                                                <label for="sp-product">Product</label>
                                                <select id="sp-product" required>
                                                    <option value="">Select product</option>
                                                </select>
                                            </div>

                                            <div class="supplier-product-field">
                                                <label for="sp-supplier">Supplier</label>
                                                <select id="sp-supplier" required>
                                                    <option value="">Select supplier</option>
                                                </select>
                                            </div>

                                            <div class="supplier-product-field">
                                                <label for="sp-price">Purchase Price (₱)</label>
                                                <input
                                                    id="sp-price"
                                                    type="number"
                                                    min="0.01"
                                                    step="0.01"
                                                    placeholder="Enter purchase price"
                                                    required>
                                            </div>

                                        </div>

                                        <div class="supplier-product-form-actions">
                                            <button type="submit" class="na-btn na-btn-add">
                                                Add Supplier Offer
                                            </button>
                                        </div>

                                    </form>

                                    <p id="supplier-product-message" role="status"></p>
                                </div>

                                <div class="pricing-panel">
                                    <h3>Supplier Offers</h3>
                                    <p>
                                        Each row represents one product offered by one supplier.
                                        You can update its purchase price or change its active status.
                                    </p>

                                    <div class="supplier-product-table-wrap">
                                        <table class="products-table supplier-product-table">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Supplier</th>
                                                    <th>Purchase Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>

                                            <tbody id="supplier-product-table">
                                                <tr>
                                                    <td colspan="5">Loading supplier offers...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                            <link rel="stylesheet" href="assets/supplier_product_management.css">

                        </div>
                    
                </div>
                
                <style>

                    /* Three markup panels in a single row */
                    #pricing-markup .pricing-rule-grid {
                        display: grid;
                        grid-template-columns: repeat(3, minmax(0, 1fr));
                        gap: 20px;
                        align-items: stretch;
                    }

                    /* Individual pricing panels */
                    #pricing-markup .pricing-panel {
                        background: #fff;
                        padding: 22px;
                        margin: 20px 0;
                        border-radius: 10px;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.10);
                        min-width: 0;
                        box-sizing: border-box;
                    }

                    /* Remove extra vertical margins inside the grid */
                    #pricing-markup .pricing-rule-grid .pricing-panel {
                        margin: 0;
                    }

                    /* Form labels */
                    #pricing-markup .pricing-panel label {
                        display: block;
                        margin: 14px 0 6px;
                        font-weight: 600;
                    }

                    /* Inputs and dropdowns */
                    #pricing-markup .pricing-panel input,
                    #pricing-markup .pricing-panel select {
                        box-sizing: border-box;
                        width: 100%;
                        max-width: 100%;
                        padding: 10px;
                        margin-bottom: 12px;
                    }

                    /* Buttons */
                    #pricing-markup .pricing-panel button {
                        display: block;
                        margin-top: 8px;
                        max-width: 100%;
                    }

                    /* Preview table */
                    #pricing-markup table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    #pricing-markup table th,
                    #pricing-markup table td {
                        padding: 10px;
                        text-align: left;
                        border-bottom: 1px solid #ddd;
                    }

                    /* Tablet: two columns */
                    @media (max-width: 900px) {
                        #pricing-markup .pricing-rule-grid {
                            grid-template-columns: repeat(2, minmax(0, 1fr));
                        }
                    }

                    /* Mobile: one column */
                    @media (max-width: 600px) {
                        #pricing-markup .pricing-rule-grid {
                            grid-template-columns: 1fr;
                        }
                    }


                    /* =========================================
                    PRICING & MARKUP TAB CONTROLS
                    ========================================= */

                    #pricing-markup .controls-bar {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 12px 18px;
                    }

                    #pricing-markup .toggle-buttons {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 8px;
                    }

                    /* #pricing-markup .toggle-btn {
                        cursor: pointer;
                        padding: 9px 18px;
                        border: 1px solid #d5d5d5;
                        border-radius: 6px;
                        background: #fff;
                        color: #333;
                        font-weight: 600;
                        transition: background 0.2s, color 0.2s;
                    }

                    #pricing-markup .toggle-btn.active {
                        background: #263b55;
                        border-color: #263b55;
                        color: #fff;
                    } */

                    #pricing-markup .toggle-btn:hover {
                        filter: brightness(0.96);
                    }

                    /* =========================================
                    SUPPLIER PRODUCTS FORM
                    ========================================= */

                    #pricing-markup .supplier-product-form-grid {
                        display: grid;
                        grid-template-columns: repeat(3, minmax(0, 1fr));
                        gap: 18px;
                        align-items: end;
                    }

                    #pricing-markup .supplier-product-field {
                        min-width: 0;
                    }

                    #pricing-markup .supplier-product-field label {
                        display: block;
                        margin: 10px 0 7px;
                        font-weight: 600;
                    }

                    #pricing-markup .supplier-product-field input,
                    #pricing-markup .supplier-product-field select {
                        display: block;
                        box-sizing: border-box;
                        width: 100%;
                        max-width: 100%;
                        min-height: 42px;
                        padding: 10px;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        background: #fff;
                    }

                    #pricing-markup .supplier-product-form-actions {
                        margin-top: 18px;
                    }

                    #pricing-markup #supplier-product-message {
                        margin-top: 14px;
                        overflow-wrap: anywhere;
                    }

                    /* =========================================
                    SUPPLIER OFFERS TABLE
                    ========================================= */

                    #pricing-markup .supplier-product-table-wrap {
                        width: 100%;
                        overflow-x: auto;
                    }

                    #pricing-markup .supplier-product-table {
                        width: 100%;
                        min-width: 760px;
                        border-collapse: collapse;
                    }

                    #pricing-markup .supplier-product-table th {
                        background: #f3f5f7;
                        font-weight: 700;
                        white-space: nowrap;
                    }

                    #pricing-markup .supplier-product-table td {
                        vertical-align: middle;
                    }

                    #pricing-markup .supplier-product-table input[data-price-input] {
                        box-sizing: border-box;
                        width: 125px;
                        max-width: 100%;
                        padding: 8px;
                        border: 1px solid #ccc;
                        border-radius: 5px;
                    }

                    #pricing-markup .supplier-offer-actions {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 7px;
                    }

                    #pricing-markup .supplier-offer-actions button {
                        margin: 0;
                        white-space: nowrap;
                    }

                    /* =========================================
                    RESPONSIVE LAYOUT
                    ========================================= */

                    @media (max-width: 900px) {
                        #pricing-markup .supplier-product-form-grid {
                            grid-template-columns: repeat(2, minmax(0, 1fr));
                        }
                    }

                    @media (max-width: 600px) {
                        #pricing-markup .supplier-product-form-grid {
                            grid-template-columns: 1fr;
                        }

                        #pricing-markup .controls-bar {
                            padding: 10px;
                        }

                        #pricing-markup .toggle-btn {
                            padding: 8px 12px;
                        }
                    }





                    /* Cashier Shift Review — Pricing & Markup style */
                    #shift-review .pricing-panel {
                        width: 100%;
                        background: #fff;
                        border: 1px solid #e5e7eb;
                        border-radius: 10px;
                        padding: 22px;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
                        box-sizing: border-box;
                    }

                    #shift-review .pricing-panel-header {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 16px;
                        flex-wrap: wrap;
                        margin-bottom: 18px;
                    }

                    #shift-review .pricing-panel-header h3 {
                        margin: 0 0 6px;
                        font-size: 20px;
                        font-weight: 600;
                        color: #222;
                    }

                    #shift-review .pricing-panel-header p {
                        margin: 0;
                        color: #6b7280;
                        font-size: 13px;
                    }

                    #shift-review .shift-review-message {
                        margin-bottom: 12px;
                        font-size: 13px;
                    }

                    #shift-review .shift-review-table-wrapper {
                        width: 100%;
                        overflow-x: auto;
                        border: 1px solid #e5e7eb;
                        border-radius: 8px;
                    }

                    #shift-review #shift-review-table {
                        width: 100%;
                        min-width: 1100px;
                        margin: 0;
                        border: none;
                        background: #fff;
                        font-size: 13px;
                    }

                    #shift-review #shift-review-table thead th {
                        background: #f5f6f8;
                        color: #374151;
                        font-weight: 600;
                        white-space: nowrap;
                        padding: 13px 12px;
                        vertical-align: middle;
                        border-bottom: 1px solid #e5e7eb;
                    }

                    #shift-review #shift-review-table tbody td {
                        padding: 12px;
                        vertical-align: middle;
                        white-space: nowrap;
                    }

                    #shift-review #shift-review-table tbody tr:hover {
                        background: #f8fafc;
                    }

                    #shift-review .shift-review-empty {
                        text-align: center;
                        padding: 24px !important;
                        color: #6b7280;
                    }

                    #shift-review .finalize-shift-btn {
                        padding: 6px 12px;
                        border-radius: 5px;
                        font-size: 12px;
                        font-weight: 500;
                    }

                    @media (max-width: 768px) {
                        #shift-review .pricing-panel {
                            padding: 14px;
                        }

                        #shift-review .pricing-panel-header {
                            align-items: flex-start;
                        }
                    }




                    /* Space between Shift Review panels */
                    #shift-review .shift-review-content {
                        display: flex;
                        flex-direction: column;
                        gap: 20px;
                        padding: 20px;
                        box-sizing: border-box;
                    }

                    /* Prevent panel margins from interfering with gap */
                    #shift-review .shift-review-content > .pricing-panel {
                        width: 100%;
                        margin: 0;
                        box-sizing: border-box;
                    }

                    /* Heading panel spacing */
                    #shift-review .shift-review-panel-heading h3 {
                        margin: 0 0 8px;
                    }

                    #shift-review .shift-review-panel-heading p {
                        margin: 0;
                        line-height: 1.5;
                    }

                    /* Space between status message and table */
                    #shift-review .shift-review-message {
                        margin-bottom: 14px;
                    }

                    /* Responsive spacing */
                    @media (max-width: 768px) {
                        #shift-review .shift-review-content {
                            padding: 12px;
                            gap: 14px;
                        }
                    }






                                    
                                        
                </style>
                <?php } ?>




                <div id="settings" class="content-section">
                    <link rel="stylesheet" href="reports/reports-page.css">
                    <div class="custom-header" style="background-image: url(topbarlogo.png);background-repeat: no-repeat;background-size: cover;">
                        
                        <div class="top-bar">
                            <div class="tab">Report</div>
                            <div class="user-controls">
                                <div>
                                    <?php echo $row['userName']; ?>
                                </div>
                                <div class="user-icon">👤</div>
                                <div class="header-icons">
                                    <span class="icon-wrapper" onclick="openNotifications()"><i class="fas fa-envelope"></i><span class="badge">0</span></span>
                                    <span class="icon-wrapper" onclick="openCalendarModal()">📅<span class="badge">2</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Time range selector -->
                    <div class="report-filter">
                        <label for="reportType">Report Type:</label>
                        <select id="reportType" onchange="updateReportLinks()">
                            <option value="daily" selected>Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div id="report-content" class="report-cards">
                        <div class="report-card">
                            <h3>Newly Added Products</h3>
                            <div class="export-buttons">
                                <a href="reports/export_newproducts_pdf.php?type=daily" class="export-btn pdf">PDF</a>
                            </div>
                        </div>

                        <div class="report-card">
                            <h3>Inventory Summary</h3>
                            <div class="export-buttons">
                                <a href="reports/export_inventory_pdf.php?type=daily" class="export-btn pdf">PDF</a>
                            </div>
                        </div>

                        <div class="report-card">
                            <h3>Sales Report</h3>
                            <div class="export-buttons">
                                <a href="reports/export_sales_pdf.php?type=daily" class="export-btn pdf">PDF</a>
                            </div>
                        </div>

                        <div class="report-card">
                            <h3>Returns</h3>
                            <div class="export-buttons">
                                <a href="reports/export_returns_pdf.php?type=daily" class="export-btn pdf">PDF</a>
                            </div>
                        </div>

                        <div class="report-card">
                            <h3>Restock Report</h3>
                            <div class="export-buttons">
                                <a href="reports/export_restock_pdf.php?type=daily" class="export-btn pdf">PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                function updateReportLinks() {
                    const type = document.getElementById("reportType").value;
                    const links = document.querySelectorAll(".export-btn");

                    links.forEach(link => {
                        const newHref = link.href.replace(/type=(daily|weekly|monthly)/, `type=${type}`);
                        link.href = newHref;
                    });
                }

                document.addEventListener("DOMContentLoaded", function() {
                    updateReportLinks();
                    document.getElementById("reportType").addEventListener("change", updateReportLinks);
                });
                </script>
            </div>



    <!-- MODALS -->
     
    <link rel="stylesheet" href="assets/notifs.css">
    <div id="notificationModal" class="modal">
        <div class="notif-modal-content">
            <span class="close-btn" onclick="closeNotifications()">&times;</span>
            <h2>Notifications</h2>
            <button id="clearAllBtn" onclick="clearAllNotifications()">Clear All</button>
            <ul class="notification-list" id="notifList">
            <li>Loading notifications...</li>
            </ul>
        </div>
        <span id="notifBadge">0</span>
    </div>

    <div id="calendarModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <div class="calendar-header"><button onclick="prevMonth()">&#9664;</button><h2 id="monthYear"></h2><button onclick="nextMonth()">&#9654;</button></div>
                <button class="close-button" onclick="closeCalendarModal()">&times;</button>
            </div>
            <div class="calendar-grid-container">
                <div class="calendar-grid">
                    <div class="calendar-weekday">Sun</div><div class="calendar-weekday">Mon</div><div class="calendar-weekday">Tue</div><div class="calendar-weekday">Wed</div><div class="calendar-weekday">Thu</div><div class="calendar-weekday">Fri</div><div class="calendar-weekday">Sat</div>
                </div>
                <div id="calendar-body" class="calendar-grid"></div>
            </div>
        </div>
    </div>
    
    <div id="addScheduleModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header"><h2>Add Schedule for <span id="scheduleDate"></span></h2><button class="close-button" onclick="closeAddScheduleModal()">&times;</button></div>
            <form id="addScheduleForm" onsubmit="event.preventDefault(); saveSchedule();">
                <div class="modal-body">
                    <input type="hidden" id="eventDate">
                    <div class="form-group"><label for="eventSupplierId">Supplier ID</label><input type="number" id="eventSupplierId" placeholder="e.g., 1002" required></div>
                    <div class="form-group"><label for="eventType">Event Type</label><select id="eventType" required><option value="incoming">Incoming Delivery</option><option value="outgoing">Outgoing Shipment</option></select></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeAddScheduleModal()">Cancel</button><button type="submit" class="btn-save">Add Event</button></div>
            </form>
        </div>
    </div>

 

    <!-- =========================================================
     AI RECOMMENDATION / ORDER REVIEW MODAL
     ========================================================= -->

    <div id="aiRecommendationModal" style="display: none;">

        <div class="ai-order-modal-content">

            <!-- HEADER -->
            <div class="ai-order-header">
                <h2>AI RECOMMENDATION</h2>

                <button
                    type="button"
                    class="ai-order-close"
                    id="aiOrderCloseBtn">
                    &times;
                </button>
            </div>


            <!-- SUPPLIER SECTIONS WILL BE INSERTED HERE -->
            <div id="aiSupplierSections">

                <!-- JavaScript will create:
                    Supplier 1
                    Supplier 2
                    Supplier 3
                    etc.
                -->

            </div>


            <!-- FOOTER -->
            <div class="ai-order-footer">

                <div class="ai-order-grand-total">
                    <strong>Grand Total:</strong>
                    <span  style="color: green;" id="aiOrderGrandTotal">
                        ₱0.00
                    </span>
                </div>

                <div class="ai-order-buttons">

                    <button
                        type="button"
                        id="aiOrderCancelBtn"
                        class="ai-order-cancel-btn">
                        Cancel
                    </button>

                    <button
                        type="button"
                        id="aiOrderConfirmBtn"
                        class="ai-order-confirm-btn">
                        Confirm Order
                    </button>

                </div>

            </div>

        </div>

    </div>

    <!-- AI ORDER CONFIRMATION MODAL -->
    <div id="aiOrderConfirmationModal" style="display: none;">

        <div class="ai-order-modal-content">

            <!-- HEADER -->
            <div class="ai-order-header">
                <h2>CONFIRM AI ORDER</h2>

                <button
                    type="button"
                    class="ai-order-close"
                    id="aiConfirmationCloseBtn">
                    &times;
                </button>
            </div>

            <!-- ORDER REVIEW -->
            <div id="aiOrderConfirmationContent">

                <!-- JavaScript will insert the order summary here -->

            </div>

            <!-- FOOTER -->
            <div class="ai-order-footer">

                <div class="ai-order-buttons">

                    <button
                        type="button"
                        id="aiConfirmationCancelBtn"
                        class="ai-order-cancel-btn">
                        Back
                    </button>

                    <button
                        type="button"
                        id="aiConfirmationConfirmBtn"
                        class="ai-order-confirm-btn">
                        Confirm & Place Order
                    </button>

                </div>

            </div>

        </div>

    </div>    

    <!-- Link to the external JavaScript file -->
    <!-- <script src="homepagescript.js"></script> -->

    <script src="homepagescript.js"></script>
    <script src="ai_recommendation.js"></script>
    <script src="add_list_restock.js"></script>
    <script src="add_list_to_order.js"></script>
    <!-- <script src="get_product.js" defer></script>
    <script src="get_supplier.js" defer></script> -->
    <script src="assets/add_user.js" defer></script>
    <script src="assets/pricing_markup.js" defer></script>
    <script src="assets/supplier_product_management.js" defer></script>
    <script src="archive_restore.js" defer></script>

</body>
</html>