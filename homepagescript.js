// --- Page and Modal Elements ---
// const loginPage = document.getElementById('login-page');
// const registerPage = document.getElementById('register-page');
const dashboardContainer = document.getElementById('dashboard-container');
const calendarModal = document.getElementById('calendarModal');
const addScheduleModal = document.getElementById('addScheduleModal');
const addScheduleForm = document.getElementById('addScheduleForm');
const productTooltip = document.getElementById('productTooltip');
const supplierTooltip = document.getElementById('supplierTooltip');

// --- Counters for dynamic rows ---
let inventoryRowCount = 30, inventoryIdCounter = 1030;
let productRowCount = 21, productIdCounter = 1021;
let supplierRowCount = 13, supplierIdCounter = 1013;
let customerRowCount = 13, customerIdCounter = 1013;
let sReturnsRowCount = 5, sReturnsIdCounter = 1005;
let cReturnsRowCount = 7, cReturnsIdCounter = 1007;
let orestockRowCount = 5, orestockIdCounter = 1005;
// const totalRows = <?= json_encode($totalRows) ?>; 
//     let transactionRowCount = totalRows;
//     let salesRowCount = totalRows;
let forecastRowCount = 10;
let analyticsRowCount = 10;
let stockAdjustmentRowCount = 7, pulledIdCounter = 1007;

// --- State Variables ---
let activeReturnsTable = 'customer';
// let activeRestockTable = 'listToOrder';
let activeRestockTable = 'itemToReceive';
let selectedListToOrderId = null;
let activeTransactionSalesView = 'transaction';
let activeForecastAnalyticsView = 'forecast';
let activeSalesAggregrationView = 'daily';

let currentDate = new Date(2025, 4, 1);
let calendarEvents = [
    { date: '2025-4-3', supplierId: '1003', type: 'incoming' },
    { date: '2025-4-3', supplierId: '1002', type: 'incoming' },
    { date: '2025-4-8', supplierId: '1001', type: 'outgoing' },
    { date: '2025-4-8', supplierId: '1003', type: 'outgoing' },
    { date: '2025-4-9', supplierId: '1002', type: 'outgoing' },
    { date: '2025-4-9', supplierId: '1001', type: 'outgoing' },
    { date: '2025-4-10', supplierId: '1002', type: 'outgoing' },
    { date: '2025-4-10', supplierId: '1003', type: 'outgoing' }
];

// --- DATA OBJECTS ---
const productDetailsData = {
    "1001": { 
        price: "PHP 7000", 
        quantity: "Out of Stock", 
        type: "Exhaust", 
        color: "Gray & White", 
        productName: "Yamaha XMAX(250 300) 2017-2024 Motorcycle Full Exhaust System Modify Front Mid Link Pipe Carbon Fiber Muffler DB Killer", 
        supplierId: "1001", 
        supplierName: "Juan Dela Cruz", 
        expiration: "None", 
        img: "https://i.imgur.com/gJ6Og3m.png" 
    },
    "1002": { price: "PHP 3500", quantity: "25 units", type: "Tires", color: "N/A", productName: "SAFEWAY TIRE (SF306) JAPAN STANDARD", supplierId: "1002", supplierName: "Peter Ang", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1003": { price: "PHP 1200", quantity: "15 units", type: "Brakes", color: "Black", productName: "For Kawasaki KLX 125/150/230/250/300", supplierId: "1002", supplierName: "Peter Ang", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1004": { price: "PHP 1400", quantity: "20 units", type: "Stand", color: "Alloy", productName: "JRP CNC Alloy Center Stand And Side Stand", supplierId: "1003", supplierName: "Marvin Solis", expiration: "2025-04-18", img: "https://i.imgur.com/placeholder.png" },
    "1005": { price: "PHP 3000", quantity: "10 units", type: "Forks", color: "Silver", productName: "Honda TMX 155 /EURO 150 Front Fork", supplierId: "1004", supplierName: "Queenie David", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1006": { price: "PHP 2800", quantity: "20 units", type: "Rims", color: "Black", productName: "28 HOLES Rims 1.60-19, 1.85-16, 1.60-21", supplierId: "1005", supplierName: "Anthony Han", expiration: "2025-04-18", img: "https://i.imgur.com/placeholder.png" },
    "1007": { price: "PHP 1700", quantity: "20 units", type: "Mirror", color: "N/A", productName: "TRC Racing CNC Stem Mount Adjustable", supplierId: "1006", supplierName: "Mike Bonn", expiration: "2025-04-18", img: "https://i.imgur.com/placeholder.png" },
    "1008": { price: "PHP 2800", quantity: "15 units", type: "Suspension", color: "Gold", productName: "TRC RACING AI-TECH SERIES SUSPENSION", supplierId: "1004", supplierName: "Queenie David", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1009": { price: "PHP 4800", quantity: "20 units", type: "Exhaust", color: "Red", productName: "JVT Pipe Nmax V2 / Aerox V2, M3, Click", supplierId: "1001", supplierName: "Juan Dela Cruz", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1010": { price: "PHP 4700", quantity: "5 units", type: "Tires", color: "Black", productName: "DUNLOP TIRE SIZE 13 & 14 TUBELESS MADE", supplierId: "1002", supplierName: "Peter Ang", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1011": { price: "PHP 1300", quantity: "20 units", type: "Brakes", color: "Silver", productName: "Honda ADV 160/150 Brake Lever Set", supplierId: "1003", supplierName: "Marvin Solis", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1012": { price: "PHP 1700", quantity: "20 units", type: "Stand", color: "Black", productName: "Motorcycle CNC Aluminum Adjustable", supplierId: "1003", supplierName: "Marvin Solis", expiration: "2025-09-18", img: "https://i.imgur.com/placeholder.png" },
    "1013": { price: "PHP 1400", quantity: "20 units", type: "Forks", color: "Black", productName: "Motorcycle Front Shock Absorber Set", supplierId: "1004", supplierName: "Queenie David", expiration: "2025-09-18", img: "https://i.imgur.com/placeholder.png" },
    "1014": { price: "PHP 2200", quantity: "15 units", type: "Rims", color: "Gold", productName: "RIM SET TMX155(RAYUS TAKASAGO)BY17", supplierId: "1005", supplierName: "Anthony Han", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1015": { price: "PHP 1900", quantity: "20 units", type: "Mirror", color: "Black", productName: "Street King Side Mirror V1-V3 CNC", supplierId: "1001", supplierName: "Juan Dela Cruz", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1016": { price: "PHP 2400", quantity: "15 units", type: "Suspension", color: "N/A", productName: "MOKOTO YAMAHA NMAX V2,V2.1 / AEROX", supplierId: "1006", supplierName: "Mike Bonn", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1017": { price: "PHP 1100", quantity: "20 units", type: "Forks", color: "N/A", productName: "FRONT FORK ASSY MEHOL FOR XRM125", supplierId: "1004", supplierName: "Queenie David", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1018": { price: "PHP 2000", quantity: "15 units", type: "Rims", color: "Black", productName: 'RED INDIAN RIM 17" 36 HOLES 2.50 3.00', supplierId: "1005", supplierName: "Anthony Han", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1019": { price: "PHP 1700", quantity: "10 units", type: "Mirror", color: "N/A", productName: "YAMAHA YTX 125 Classic Retro Style Black", supplierId: "1001", supplierName: "Juan Dela Cruz", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1020": { price: "PHP 1500", quantity: "10 units", type: "Suspension", color: "Black", productName: "YAMAHA YTX125 SHOCK ABSORBER", supplierId: "1006", supplierName: "Mike Bonn", expiration: "None", img: "https://i.imgur.com/placeholder.png" },
    "1021": { price: "PHP 1400", quantity: "N/A", type: "Box", color: "N/A", productName: "45L Motorcycle Top Box Motorcycle Trunk", supplierId: "1001", supplierName: "Juan Dela Cruz", expiration: "None", img: "https://i.imgur.com/placeholder.png" }
};

const supplierDetailsData = {
    "1001": { name: "Juan Dela Cruz", location: "QUEZON CITY, 1106 METRO MANILA", email: "Antersmotoparts@gmail.com", phone: "0915 517 7529", img: "https://i.imgur.com/KDs5L4x.png" },
    "1002": { name: "Peter Ang", location: "MAKATI, 1233 METRO MANILA", email: "Phbiketown@gmail.com", phone: "(02) 7261 7392", img: "https://i.imgur.com/placeholder.png" },
    "1003": { name: "Marvin Solis", location: "QUEZON CITY, 1113 METRO MANILA", email: "Masterbuloy@gmail.com", phone: "0908 663 7852", img: "https://i.imgur.com/placeholder.png" },
    "1004": { name: "Queenie David", location: "LAS PIÑAS, 1700 METRO MANILA", email: "Motoparts@gmail.com", phone: "(02) 8872 2240", img: "https://i.imgur.com/placeholder.png" },
    "1005": { name: "Anthony Han", location: "VALENZUELA, 1440 METRO MANILA", email: "HanMotostore@gmail.com", phone: "0908 663 7852", img: "https://i.imgur.com/placeholder.png" },
    "1006": { name: "Mike Bonn", location: "CALOOCAN, 1403 METRO MANILA", email: "BonnsPartsstore@gmail.com", phone: "0923 4545 232", img: "https://i.imgur.com/placeholder.png" },
    "1007": { name: "Jose Cruz", location: "QUEZON CITY, 1109 METRO MANILA", email: "JoseMotoparts2@gmail.com", phone: "0917 154 7019", img: "https://i.imgur.com/placeholder.png" },
    "1008": { name: "Karen Naval", location: "PARAÑAQUE, 1709 METRO MANILA", email: "MotorbikeStore@gmail.com", phone: "(02) 993 5454", img: "https://i.imgur.com/placeholder.png" },
    "1009": { name: "Warren Cruz", location: "CALOOCAN, 1420 METRO MANILA", email: "MotoStarCruzPH@gmail.com", phone: "0917 154 7019", img: "https://i.imgur.com/placeholder.png" },
    "1010": { name: "Kevin Sioson", location: "TAGUIG, 1633 METRO MANILA", email: "SiosonMotoParts@gmail.com", phone: "(02) 988 3324", img: "https://i.imgur.com/placeholder.png" },
    "1011": { name: "Marlon Torres", location: "LAS PIÑAS, 1740 METRO MANILA", email: "TMotorsPartsPH@gmail.com", phone: "0917 154 7019", img: "https://i.imgur.com/placeholder.png" },
    "1012": { name: "Christoper Reyes", location: "MAKATI, 1203 METRO MANILA", email: "RMotorBikeparts@gmail.com", phone: "0905 154 7018", img: "https://i.imgur.com/placeholder.png" },
    "1013": { name: "Dante Santos", location: "MARIKINA, 1800 METRO MANILA", email: "DanteMotoStore@gmail.com", phone: "(02) 900 8874", img: "https://i.imgur.com/placeholder.png" }
};

const locations = ["Shelf A - Row A1", "Shelf A - Row B1", "Shelf A - Row C1", "Shelf A - Row D1", "Shelf B - Row A2", "Shelf B - Row B2", "Shelf B - Row C2", "Shelf B - Row D2", "Shelf C - Row A3", "Shelf C - Row B3", "Shelf D - Row A1", "Shelf D - Row B1"];

// --- Page Navigation ---
// function showLoginPage() {
//     [loginPage, registerPage, dashboardContainer, calendarModal, addScheduleModal].forEach(el => el.style.display = 'none');
//     loginPage.style.display = 'flex';
// }
// function showRegisterPage() {
//     [loginPage, registerPage, dashboardContainer, calendarModal, addScheduleModal].forEach(el => el.style.display = 'none');
//     registerPage.style.display = 'flex';
// }
function showDashboard() {
    // [loginPage, registerPage, dashboardContainer, calendarModal, addScheduleModal].forEach(el => el.style.display = 'none');
    dashboardContainer.style.display = 'flex';
    showContentSection('home');
    
}
function showContentSection(sectionId) {
    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
    document.getElementById(sectionId)?.classList.add('active');
    
    document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
    document.querySelector(`.nav-link[onclick="showContentSection('${sectionId}')"]`)?.classList.add('active');

    const contentArea = document.querySelector('.content-area');
    const paddedSections = ['home', 'settings'];
    
    if (paddedSections.includes(sectionId)) {
        contentArea.style.padding = '25px';
    } else {
        contentArea.style.padding = '0';
    }

    if (sectionId === 'returns') {
        showReturnsTable('customer');
    }
    if (sectionId === 'transaction-sales') {
        showTransactionSalesView('transaction');
    }
    if (sectionId === 'forecast-analytics') {
        showForecastAnalyticsView('forecast');
    }

    if (sectionId === 'sales-aggregation') {
        showSalesAggregrationView('byProduct');
    }
    // if (sectionId === 'order-restock') {
    //     showRestockTable('listToOrder');
    // }

    if (sectionId === 'order-restock') {
        showRestockTable('itemToReceive');
    }  
    
    if (sectionId === 'real-stock-adjustments') {
    loadStockAdjustmentHistory();
}  

}

// --- Generic Add/Save/Cancel Row Functions ---
function addNewRow(config) {
    if (document.querySelector('.inline-edit-row')) return;
    config.rowCount++;
    if (config.idCounter) config.idCounter++;
    
    const tableBody = document.querySelector(config.tableSelector);
    const newRow = tableBody.insertRow(0);
    newRow.className = 'inline-edit-row';
    newRow.innerHTML = config.getHtml(config.rowCount, config.idCounter);
    
    document.getElementById(config.countElementId).textContent = String(config.rowCount).padStart(2, '0');
}

function saveRow(buttonEl, config) {
    const editRow = buttonEl.closest('tr');
    const tableBody = editRow.parentNode;
    
    const newRowData = config.getDataFromInputs(editRow);
    const staticRow = tableBody.insertRow(0);
    staticRow.innerHTML = config.getStaticHtml(newRowData);
    
    editRow.remove();
}

function cancelRow(buttonEl, config) {
    buttonEl.closest('tr').remove();
    config.rowCount--;
    if (config.idCounter) config.idCounter--;
    document.getElementById(config.countElementId).textContent = String(config.rowCount).padStart(2, '0');
}


function showReturnsTable(viewType) {
    activeReturnsTable = viewType;
    document.getElementById('supplier-returns-table-container').style.display = viewType === 'supplier' ? 'flex' : 'none';
    document.getElementById('customer-returns-table-container').style.display = viewType === 'customer' ? 'flex' : 'none';

    document.getElementById('customer-returns-btn').classList.toggle('active', viewType === 'customer');
    document.getElementById('supplier-returns-btn').classList.toggle('active', viewType === 'supplier');


    // document.getElementById('returns-count').textContent = String(viewType === 'supplier' ? sReturnsRowCount : cReturnsRowCount).padStart(2, '0');

    document.getElementById('cusreturns-add-btn').style.display = viewType === 'customer' ? 'inline-block' : 'none';
    document.getElementById('supreturns-add-btn').style.display = viewType === 'supplier' ? 'inline-block' : 'none';
}




function showRestockTable(viewType) {

    activeRestockTable = 'itemToReceive';

    // Only show the Receive/Restock table
    const receiveTable =
        document.getElementById('receive-restock-table-container');

    if (receiveTable) {
        receiveTable.style.display = 'flex';
    }

    // Keep the old tables hidden
    const listTable =
        document.getElementById('list-restock-table-container');

    if (listTable) {
        listTable.style.display = 'none';
    }

    const itemTable =
        document.getElementById('item-restock-table-container');

    if (itemTable) {
        itemTable.style.display = 'none';
    }

    // Only the Receive button exists now
    const receiveButton =
        document.getElementById('receive-restock-btn');

    if (receiveButton) {
        receiveButton.classList.add('active');
    }

    // Hide old buttons if they still exist somewhere
    const listButton =
        document.getElementById('list-restock-btn');

    if (listButton) {
        listButton.classList.remove('active');
    }

    const itemButton =
        document.getElementById('item-restock-btn');

    if (itemButton) {
        itemButton.classList.remove('active');
    }

    // Edit/Delete are not used for the new Version 1 receiving view
    const editButton =
        document.getElementById('edit-restock-btn');

    if (editButton) {
        editButton.style.display = 'none';
    }

    const deleteButton =
        document.getElementById('delete-restock-btn');

    if (deleteButton) {
        deleteButton.style.display = 'none';
    }

    // CREATE ORDER should remain available
    const addButton =
        document.getElementById('order-restock-add-btn');

    if (addButton) {
        addButton.style.display = 'inline-block';
    }

    // PLACE ORDER is Version 2 / List-to-Order functionality
    const placeOrderButton =
        document.getElementById('place-order-btn');

    if (placeOrderButton) {
        placeOrderButton.style.display = 'none';
    }

    // Update button is for receiving/restock
    const updateButton =
        document.getElementById('update-restock-status-btn');

    if (updateButton) {
        updateButton.style.display = 'inline-block';
    }

    // Count rows in the actual restock table
    const countElement =
        document.getElementById('products-count');

    if (countElement && receiveTable) {

        countElement.textContent =
            receiveTable.querySelectorAll('tbody tr').length;
    }
}



function showTransactionSalesView(viewType) {
    activeTransactionSalesView = viewType;
    document.getElementById('sales-view-container').style.display = viewType === 'sales' ? 'flex' : 'none';
    document.getElementById('transaction-view-container').style.display = viewType === 'transaction' ? 'flex' : 'none';

    document.getElementById('transaction-view-btn').classList.toggle('active', viewType === 'transaction');
    document.getElementById('sales-view-btn').classList.toggle('active', viewType === 'sales');

    // document.getElementById('transaction-sales-count').textContent = String(viewType === 'transaction' ? transactionRowCount : salesRowCount).padStart(2, '0');


    document.getElementById('transaction-add-btn').style.display = viewType === 'transaction' ? 'inline-block' : 'none';
    document.getElementById('sales-add-btn').style.display = viewType === 'sales' ? 'inline-block' : 'none';
}


// function updateRowCount() {
//     const rows = document.querySelectorAll('tbody tr'); // count visible rows
//     const visibleCount = Array.from(rows).filter(r => r.style.display !== 'none').length;

//     document.getElementById('transaction-sales-count').textContent = 
//         String(visibleCount).padStart(2, '0');
// }

// // Call it once on load
// updateRowCount();

let activeTableType = 'daily'

function showForecastAnalyticsView(viewType, tableType = activeTableType) {
    activeForecastAnalyticsView = viewType;
    activeTableType = tableType;

    document.getElementById('forecast-view-container').style.display = viewType === 'forecast' ? 'flex' : 'none';
    document.getElementById('analytics-view-container').style.display = viewType === 'analytics' ? 'flex' : 'none';

    document.getElementById('forecast-view-btn').classList.toggle('active', viewType === 'forecast');
    document.getElementById('analytics-view-btn').classList.toggle('active', viewType === 'analytics');

    document.getElementById('forecast-analytics-count').textContent = String(viewType === 'forecast' ? forecastRowCount : analyticsRowCount).padStart(2, '0');

    // document.getElementById('forecast-add-btn').style.display = viewType === 'forecast' ? 'inline-block' : 'none';
    // document.getElementById('analytics-add-btn').style.display = viewType === 'analytics' ? 'inline-block' : 'none';

    document.getElementById('forecast-toggle-buttons').style.display = viewType === 'forecast' ? 'block' : 'none';
    document.getElementById('analytics-toggle-buttons').style.display = viewType === 'analytics' ? 'block' : 'none';

    // FORECAST AND ANALYTICS VIEW TABLE
    document.getElementById('daily-forecast-view-container').style.display = (viewType === 'forecast' && tableType === 'daily')   ? 'block' : 'none';
    document.getElementById('weekly-forecast-view-container').style.display  = (viewType === 'forecast' && tableType === 'weekly')   ? 'block' : 'none';
    document.getElementById('monthly-forecast-view-container').style.display = (viewType === 'forecast' && tableType === 'monthly')   ? 'block' : 'none';

    document.getElementById('daily-forecast-view-btn').classList.toggle('active',   viewType === 'forecast' && tableType === 'daily');
    document.getElementById('weekly-forecast-view-btn').classList.toggle('active',  viewType === 'forecast' && tableType === 'weekly');
    document.getElementById('monthly-forecast-view-btn').classList.toggle('active', viewType === 'forecast' && tableType === 'monthly');

    document.getElementById('daily-analytics-view-container').style.display = (viewType === 'analytics' && tableType === 'daily')   ? 'block' : 'none';
    document.getElementById('weekly-analytics-view-container').style.display  = (viewType === 'analytics' && tableType === 'weekly')   ? 'block' : 'none';
    document.getElementById('monthly-analytics-view-container').style.display = (viewType === 'analytics' && tableType === 'monthly')   ? 'block' : 'none';

    document.getElementById('daily-analytics-view-btn').classList.toggle('active',   viewType === 'analytics' && tableType === 'daily');
    document.getElementById('weekly-analytics-view-btn').classList.toggle('active',  viewType === 'analytics' && tableType === 'weekly');
    document.getElementById('monthly-analytics-view-btn').classList.toggle('active', viewType === 'analytics' && tableType === 'monthly');


    // FORECAST AND ANALYTICS ADD BUTTONS
    document.getElementById('daily-forecast-add-btn').style.display   = (viewType === 'forecast' && tableType === 'daily')   ? 'inline-block' : 'none';
    document.getElementById('weekly-forecast-add-btn').style.display  = (viewType === 'forecast' && tableType === 'weekly')  ? 'inline-block' : 'none';
    document.getElementById('monthly-forecast-add-btn').style.display = (viewType === 'forecast' && tableType === 'monthly') ? 'inline-block' : 'none';

    document.getElementById('daily-analytics-add-btn').style.display   = (viewType === 'analytics' && tableType === 'daily')   ? 'inline-block' : 'none';
    document.getElementById('weekly-analytics-add-btn').style.display  = (viewType === 'analytics' && tableType === 'weekly')  ? 'inline-block' : 'none';
    document.getElementById('monthly-analytics-add-btn').style.display = (viewType === 'analytics' && tableType === 'monthly') ? 'inline-block' : 'none';

}

// function toggleSalesView(viewType) {
//     activetoggleSalesView = viewType;
//     const byProductBtn = document.getElementById('byproduct-sales-btn');
//     const entireBtn = document.getElementById('entire-sales-btn');

//     const productButtons = document.getElementById('product-toggle-buttons');
//     const entireButtons = document.getElementById('entire-toggle-buttons');

//     if (viewType === 'byProduct') {
//         byProductBtn.classList.add('active');
//         entireBtn.classList.remove('active');

//         productButtons.style.display = 'flex';
//         entireButtons.style.display = 'none';
//     } else if (viewType === 'entire') {
//         entireBtn.classList.add('active');
//         byProductBtn.classList.remove('active');

//         productButtons.style.display = 'none';
//         entireButtons.style.display = 'flex';
//     }
    
// }

let activeAggregationType = 'daily'

function showSalesAggregrationView(viewType, tableType = activeAggregationType) {
    activeSalesAggregrationView = viewType;
    activeAggregationType = tableType;
    
    // const byProductBtn = document.getElementById('byproduct-sales-btn');
    // const entireBtn = document.getElementById('entire-sales-btn');

    // const productButtons = document.getElementById('product-toggle-buttons');
    // const entireButtons = document.getElementById('entire-toggle-buttons');

    // if (viewType === 'byProduct') {
    //     byProductBtn.classList.add('active');
    //     entireBtn.classList.remove('active');

    //     productButtons.style.display = 'flex';
    //     entireButtons.style.display = 'none';
    // } else if (viewType.startsWith('entire')) {
    //     entireBtn.classList.add('active');
    //     byProductBtn.classList.remove('active');

    //     productButtons.style.display = 'none';
    //     entireButtons.style.display = 'flex';
    // }   

    // const byProductBtn = document.getElementById('byproduct-sales-btn');
    // const entireBtn = document.getElementById('entire-sales-btn');


    // 

    document.getElementById('byproduct-view-container').style.display = viewType === 'byProduct' ? 'flex' : 'none';
    document.getElementById('entire-view-container').style.display = viewType === 'entire' ? 'flex' : 'none';

    document.getElementById('byproduct-sales-btn').classList.toggle('active', viewType === 'byProduct');
    document.getElementById('entire-sales-btn').classList.toggle('active', viewType === 'entire');

    // document.getElementById('forecast-analytics-count').textContent = String(viewType === 'forecast' ? forecastRowCount : analyticsRowCount).padStart(2, '0');

    document.getElementById('product-toggle-buttons').style.display = viewType === 'byProduct' ? 'flex' : 'none';
    document.getElementById('entire-toggle-buttons').style.display = viewType === 'entire' ? 'flex' : 'none';

    document.getElementById('daily-product-btn').classList.toggle('active',   viewType === 'byProduct' && tableType === 'daily');
    document.getElementById('weekly-product-btn').classList.toggle('active',  viewType === 'byProduct' && tableType === 'weekly');
    document.getElementById('monthly-product-btn').classList.toggle('active', viewType === 'byProduct' && tableType === 'monthly');

    document.getElementById('daily-entire-btn').classList.toggle('active',   viewType === 'entire' && tableType === 'daily');
    document.getElementById('weekly-entire-btn').classList.toggle('active',  viewType === 'entire' && tableType === 'weekly');
    document.getElementById('monthly-entire-btn').classList.toggle('active', viewType === 'entire' && tableType === 'monthly');

    document.getElementById('daily-byProduct-view-container').style.display = (viewType === 'byProduct' && tableType === 'daily')   ? 'block' : 'none';
    document.getElementById('weekly-byProduct-view-container').style.display  = (viewType === 'byProduct' && tableType === 'weekly')   ? 'block' : 'none';
    document.getElementById('monthly-byProduct-view-container').style.display = (viewType === 'byProduct' && tableType === 'monthly')   ? 'block' : 'none';

    document.getElementById('daily-entire-view-container').style.display = (viewType === 'entire' && tableType === 'daily')   ? 'block' : 'none';
    document.getElementById('weekly-entire-view-container').style.display  = (viewType === 'entire' && tableType === 'weekly')   ? 'block' : 'none';
    document.getElementById('monthly-entire-view-container').style.display = (viewType === 'entire' && tableType === 'monthly')   ? 'block' : 'none';




}






// --- Calendar and Modal Functions ---

function openNotifications() {
  document.getElementById('notificationModal').style.display = 'block';
  loadNotifications();
}

function closeNotifications() {
  document.getElementById('notificationModal').style.display = 'none';
}

function loadNotifications() {
  fetch('notifications.php')
    .then(res => {
      if (!res.ok) throw new Error('Network response was not ok');
      return res.json();
    })
    .then(data => {
      const list = document.getElementById('notifList');
      const badge = document.getElementById('notifBadge');
      list.innerHTML = ''; // clear old notifs

      if (!data || data.length === 0) {
        list.innerHTML = '<li>No new notifications 🎉</li>';
        badge.textContent = '0';
      } else {
        data.forEach(n => {
          const li = document.createElement('li');
          li.classList.add(n.type);
          li.innerHTML = `
            <span class="notif-msg">${n.message}</span>
            <button class="mark-read-btn" title="Mark as read" onclick="markAsRead('${n.id}', this)">✅</button>
          `;
          list.appendChild(li);
        });
        badge.textContent = data.length;
      }
    })
    .catch(err => {
      console.error(err);
      document.getElementById('notifList').innerHTML = '<li>Error loading notifications.</li>';
      document.getElementById('notifBadge').textContent = '!';
    });
}

function markAsRead(id, btn) {
  fetch('mark_as_read.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id })
  })
  .then(res => res.json())
  .then(resp => {
    if (resp.success) {
      btn.parentElement.remove();
      updateBadgeCount();
    } else {
      alert('Failed to mark as read');
    }
  })
  .catch(console.error);
}

function clearAllNotifications() {
  if (!confirm("Are you sure you want to clear all notifications?")) return;

  fetch('clear_all_notifications.php', { method: 'POST' })
    .then(res => res.json())
    .then(resp => {
      if (resp.success) {
        document.getElementById('notifList').innerHTML = '<li>No new notifications 🎉</li>';
        document.getElementById('notifBadge').textContent = '0';
      } else {
        alert('Error clearing notifications: ' + (resp.error || 'Unknown'));
      }
    })
    .catch(console.error);
}

function updateBadgeCount() {
  const remaining = document.querySelectorAll('#notifList li').length;
  document.getElementById('notifBadge').textContent = remaining;
}


function updateBadgeCount() {
    const remaining = document.querySelectorAll('#notifList li').length;
    document.getElementById('notifBadge').textContent = remaining;
}



function openCalendarModal() { calendarModal.style.display = 'flex'; generateCalendar(); }
function closeCalendarModal() { calendarModal.style.display = 'none'; }
function prevMonth() { currentDate.setMonth(currentDate.getMonth() - 1); generateCalendar(); }
function nextMonth() { currentDate.setMonth(currentDate.getMonth() + 1); generateCalendar(); }

function generateCalendar() {
    const year = currentDate.getFullYear(); const month = currentDate.getMonth();
    document.getElementById('monthYear').textContent = `${["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][month]} ${year}`;
    const calendarBody = document.getElementById('calendar-body'); calendarBody.innerHTML = '';
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    for (let i = 0; i < firstDay; i++) { calendarBody.insertAdjacentHTML('beforeend', '<div class="calendar-day empty"></div>'); }
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${year}-${month}-${day}`;
        calendarBody.insertAdjacentHTML('beforeend', `<div class="calendar-day" data-date="${dateStr}" onclick="openAddScheduleModal('${dateStr}')"><div class="day-number">${day}</div><div class="calendar-events"></div></div>`);
    }
    renderEvents();
}

function renderEvents() {
    document.querySelectorAll('.calendar-event').forEach(e => e.remove());
    calendarEvents.forEach(event => {
        const dayCell = document.querySelector(`.calendar-day[data-date="${event.date}"] .calendar-events`);
        if (dayCell) {
            const icon = event.type === 'incoming' ? '🕒' : '🚚';
            dayCell.insertAdjacentHTML('beforeend', `<div class="calendar-event event-${event.type}">${icon} SID: ${event.supplierId}</div>`);
        }
    });
}

function openAddScheduleModal(dateStr) {
    addScheduleModal.style.display = 'flex';
    document.getElementById('eventDate').value = dateStr;
    const [year, month, day] = dateStr.split('-');
    document.getElementById('scheduleDate').textContent = `${parseInt(month) + 1}/${day}/${year}`;
}
function closeAddScheduleModal() { addScheduleModal.style.display = 'none'; addScheduleForm.reset(); }
function saveSchedule() {
    calendarEvents.push({ date: document.getElementById('eventDate').value, supplierId: document.getElementById('eventSupplierId').value, type: document.getElementById('eventType').value });
    renderEvents(); closeAddScheduleModal();
}

// --- Tooltip Functions ---
function showProductTooltip(productId) {
    const data = productDetailsData[productId]; if (!data) return;
    document.getElementById('tooltipImg').src = data.img;
    document.getElementById('tooltipPrice').textContent = data.price;
    const quantitySpan = document.getElementById('tooltipQuantity');
    quantitySpan.textContent = data.quantity;
    quantitySpan.className = data.quantity === 'Out of Stock' ? 'quantity-oos' : '';
    document.getElementById('tooltipType').textContent = data.type;
    document.getElementById('tooltipType').className = 'type-tag';
    document.getElementById('tooltipColor').textContent = data.color;
    document.getElementById('tooltipProductId').textContent = productId;
    document.getElementById('tooltipProductName').textContent = data.productName;
    document.getElementById('tooltipSupplierId').textContent = data.supplierId;
    document.getElementById('tooltipSupplierName').textContent = data.supplierName;
    const expirationSpan = document.getElementById('tooltipExpiration');
    expirationSpan.textContent = data.expiration;
    expirationSpan.className = data.expiration !== "None" ? 'expiration-red' : '';
    productTooltip.style.display = 'block';
}
function hideProductTooltip() { productTooltip.style.display = 'none'; }

function showSupplierTooltip(supplierId) {
    const data = supplierDetailsData[supplierId]; if (!data) return;
    document.getElementById('supplierTooltipImg').src = data.img;
    document.getElementById('supplierTooltipId').textContent = supplierId;
    document.getElementById('supplierTooltipName').textContent = data.name;
    document.getElementById('supplierTooltipLocation').textContent = data.location;
    document.getElementById('supplierTooltipEmail').textContent = data.email;
    document.getElementById('supplierTooltipPhone').textContent = data.phone;
    supplierTooltip.style.display = 'block';
}
function hideSupplierTooltip() { supplierTooltip.style.display = 'none'; }

// --- Event Listeners ---
document.addEventListener('DOMContentLoaded', () => {
    showDashboard();

});



const sidebar = document.querySelector('.sidebar');
const sidebarToggle = document.getElementById('sidebar-toggle');
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        // if (sidebar.classList.contains('collapsed')) {
        //     sidebarToggle.innerHTML = '»';
            

        // } else {
        //     sidebarToggle.innerHTML = '«';
        // }

        if (sidebar.classList.contains('collapsed')) {
            sidebarToggle.innerHTML = '»';
            // sidebarToggle.style.color = ' #430202ff';
            // sidebarToggle.style.background = 'transparent'; 
        } else {
            sidebarToggle.innerHTML = '«';
            // sidebarToggle.style.color = ' white';
            // sidebarToggle.style.background = ' #333'; // expanded state
        }

    });
}

    
    // Add button listeners
    // document.getElementById('na-add-btn').addEventListener('click', () => addNewRow(addConfig.newAdditions));
    // document.getElementById('products-add-btn').addEventListener('click', () => addNewRow(addConfig.products));
    // // Placeholder listeners for other add buttons
    // document.getElementById('suppliers-add-btn').addEventListener('click', () => alert('Add supplier functionality not fully implemented.'));
    // document.getElementById('customer-add-btn').addEventListener('click', () => alert('Add customer functionality not fully implemented.'));
    // document.getElementById('returns-add-btn').addEventListener('click', () => alert('Add return functionality not fully implemented.'));
    // document.getElementById('order-restock-add-btn').addEventListener('click', () => alert('Add order/restock functionality not fully implemented.'));
    // document.getElementById('transaction-sales-add-btn').addEventListener('click', () => alert('Add transaction/sales functionality not fully implemented.'));
    // document.getElementById('forecast-analytics-add-btn').addEventListener('click', () => alert('Add forecast/analytics functionality not fully implemented.'));
    // document.getElementById('stock-adjustments-add-btn').addEventListener('click', () => alert('Add stock adjustment functionality not fully implemented.'));

    // View toggle listeners
    document.getElementById('supplier-returns-btn').addEventListener('click', () => showReturnsTable('supplier'));
    document.getElementById('customer-returns-btn').addEventListener('click', () => showReturnsTable('customer'));
    document.getElementById('transaction-view-btn').addEventListener('click', () => showTransactionSalesView('transaction'));
    document.getElementById('sales-view-btn').addEventListener('click', () => showTransactionSalesView('sales'));


    // document.getElementById('item-restock-btn').addEventListener('click', () => showRestockTable('itemToOrder'));
    // document.getElementById('list-restock-btn').addEventListener('click', () => showRestockTable('listToOrder'));
    // document.getElementById('receive-restock-btn').addEventListener('click', () => showRestockTable('itemToReceive'));
    
    // document.getElementById('list-restock-btn').addEventListener('click', function () {
    //     showRestockTable('listToOrder');
    // });

    // document.getElementById('item-restock-btn').addEventListener('click', function () {
    //     showRestockTable('itemToOrder');
    // });

    // document.getElementById('receive-restock-btn').addEventListener('click', function () {
    //     showRestockTable('itemToReceive');
    // });



//BUTTONS FOR RESTOCK


    // document.getElementById('list-restock-btn').addEventListener('click', function () {
    //     showRestockTable('listToOrder');
    // });

    // document.getElementById('item-restock-btn').addEventListener('click', function () {
    //     showRestockTable('itemToOrder');
    // });

    // document.getElementById('receive-restock-btn').addEventListener('click', function () {
    //     showRestockTable('itemToReceive');
    // });



    const receiveRestockBtn =
        document.getElementById('receive-restock-btn');

    if (receiveRestockBtn) {

        receiveRestockBtn.addEventListener('click', function () {

            showRestockTable('itemToReceive');

        });

    }



    // SELECT LIST TO ORDER
    document.querySelectorAll('.list-order-row').forEach(row => {
        row.addEventListener('click', function () {
            document.querySelectorAll('.list-order-row').forEach(r => {
                r.classList.remove('selected');
            });

            this.classList.add('selected');

            selectedListToOrderId = this.dataset.id;

            console.log('Selected ListToOrder_ID:', selectedListToOrderId);
        });
    });



    const byproductSalesBtn =
        document.getElementById('byproduct-sales-btn');

    if (byproductSalesBtn) {
        byproductSalesBtn.addEventListener(
            'click',
            () => showSalesAggregrationView('byProduct', 'daily')
        );
    }


    const entireSalesBtn =
        document.getElementById('entire-sales-btn');

    if (entireSalesBtn) {
        entireSalesBtn.addEventListener(
            'click',
            () => showSalesAggregrationView('entire', 'daily')
        );
    }


    const dailyProductBtn =
        document.getElementById('daily-product-btn');

    if (dailyProductBtn) {
        dailyProductBtn.addEventListener(
            'click',
            () => showSalesAggregrationView('byProduct', 'daily')
        );
    }


    const weeklyProductBtn =
        document.getElementById('weekly-product-btn');

    if (weeklyProductBtn) {
        weeklyProductBtn.addEventListener(
            'click',
            () => showSalesAggregrationView('byProduct', 'weekly')
        );
    }


    const monthlyProductBtn =
        document.getElementById('monthly-product-btn');

    if (monthlyProductBtn) {
        monthlyProductBtn.addEventListener(
            'click',
            () => showSalesAggregrationView('byProduct', 'monthly')
        );
    }


    const dailyEntireBtn =
        document.getElementById('daily-entire-btn');

    if (dailyEntireBtn) {
        dailyEntireBtn.addEventListener(
            'click',
            () => showSalesAggregrationView('entire', 'daily')
        );
    }


    const weeklyEntireBtn =
        document.getElementById('weekly-entire-btn');

    if (weeklyEntireBtn) {
        weeklyEntireBtn.addEventListener(
            'click',
            () => showSalesAggregrationView('entire', 'weekly')
        );
    }


    const monthlyEntireBtn =
        document.getElementById('monthly-entire-btn');

    if (monthlyEntireBtn) {
        monthlyEntireBtn.addEventListener(
            'click',
            () => showSalesAggregrationView('entire', 'monthly')
        );
    }


    

    // Account page listeners for profile picture
    const uploadLink = document.getElementById('upload-link');
    const removeLink = document.getElementById('remove-link');
    const profileUpload = document.getElementById('profile-upload');
    const profilePicDisplay = document.querySelector('.profile-pic-display');

    if (uploadLink) {
        uploadLink.addEventListener('click', (e) => {
            e.preventDefault();
            profileUpload.click();
        });
    }

    if (removeLink) {
        removeLink.addEventListener('click', (e) => {
            e.preventDefault();
            profilePicDisplay.style.backgroundImage = 'none';
            profilePicDisplay.innerHTML = '<span>👤</span>';
        });
    }
    
    if (profileUpload) {
        profileUpload.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    profilePicDisplay.style.backgroundImage = `url('${event.target.result}')`;
                    profilePicDisplay.innerHTML = ''; // Remove the icon
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Sidebar Toggle Listener


    // const hamburger = document.querySelector(".sidetoggle-btn");

    // hamburger.addEventListener("click",function(){
    //     document.querySelector("#sidebar").classList.toggle("collapsed");
    // })

    // Tooltip and Modal listeners
    const contentArea = document.querySelector('.content-area');
    contentArea.addEventListener('mouseover', (e) => {
        if (e.target.classList.contains('product-id-cell')) showProductTooltip(e.target.textContent);
        if (e.target.classList.contains('supplier-id-cell')) showSupplierTooltip(e.target.textContent);
    });
    contentArea.addEventListener('mouseout', (e) => {
        if (e.target.classList.contains('product-id-cell')) hideProductTooltip();
        if (e.target.classList.contains('supplier-id-cell')) hideSupplierTooltip();
    });
    contentArea.addEventListener('mousemove', (e) => {
        if (productTooltip.style.display === 'block') {
            productTooltip.style.left = `${e.pageX + 15}px`;
            productTooltip.style.top = `${e.pageY + 15}px`;
        }
        if (supplierTooltip.style.display === 'block') {
            supplierTooltip.style.left = `${e.pageX + 15}px`;
            supplierTooltip.style.top = `${e.pageY + 15}px`;
        }
    });
    calendarModal.addEventListener('click', (e) => { if (e.target === calendarModal) closeCalendarModal(); });

    // Event delegation for save/cancel buttons in tables
    contentArea.addEventListener('click', function(e) {
        if (e.target.classList.contains('save-btn')) {
            const section = e.target.closest('.content-section').id;
            // A more robust solution would map section IDs to configs
            if (section === 'new-additions') {
                saveRow(e.target, addConfig.newAdditions);
            } else if (section === 'products') {
                // saveRow(e.target, addConfig.products); // When implemented
            }
        }
        if (e.target.classList.contains('cancel-btn')) {
            const section = e.target.closest('.content-section').id;
            if (section === 'new-additions') {
                cancelRow(e.target, addConfig.newAdditions);
            } else if (section === 'products') {
                // cancelRow(e.target, addConfig.products); // When implemented
            }
        }
    });






const nulltoggleBtn =
    document.getElementById('toggleNullBtn');

const expInput =
    document.getElementById('expiration_date');

const expInputClass =
    document.getElementById('edit-expirationdate');

const notnulltoggleBtn =
    document.getElementById('toggleNotNullBtn');


if (
    nulltoggleBtn &&
    notnulltoggleBtn &&
    expInput &&
    expInputClass
) {

    nulltoggleBtn.addEventListener('click', function () {

        nulltoggleBtn.style.display = 'none';

        notnulltoggleBtn.style.display = 'inline-block';

        notnulltoggleBtn.style.color = 'blue';

        expInput.disabled = true;

        expInputClass.disabled = true;

    });


    notnulltoggleBtn.addEventListener('click', function () {

        // expInput.type = 'date';

        notnulltoggleBtn.style.display = 'none';

        nulltoggleBtn.style.display = 'inline-block';

        nulltoggleBtn.style.color = 'red';

        expInput.disabled = false;

        expInputClass.disabled = false;

    });

}






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






// ===============================
// USERS / ACCOUNTS MODALS
// ===============================

document.addEventListener("DOMContentLoaded", function () {

    const addUserButton = document.getElementById("users-add-btn");
    const editUserButton = document.getElementById("edit-user-btn");

    const addUserModal = document.getElementById("addUserModal");
    const editUserModal = document.getElementById("editUserModal");

    const addUserCancelButton = document.getElementById("user-cancel-btn");
    const editUserCancelButton = document.getElementById("edit-user-cancel-btn");

    const selectUserButtons = document.querySelectorAll(".select-user-btn");

    const editUserId = document.getElementById("edit-user-id");
    const editUserUsername = document.getElementById("edit-user-username");
    const editUserEmail = document.getElementById("edit-user-email");
    const editUserPhone = document.getElementById("edit-user-phone");
    const editUserAccountType = document.getElementById("edit-user-account-type");

    let selectedUser = null;


    // OPEN ADD USER MODAL
    if (addUserButton) {
        addUserButton.addEventListener("click", function () {

            addUserModal.style.display = "flex";

        });
    }


    // SELECT USER FROM TABLE
    selectUserButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            selectedUser = {
                id: this.dataset.id,
                username: this.dataset.username,
                email: this.dataset.email,
                phone: this.dataset.phone,
                accountType: this.dataset.accountType
            };

            // Fill the Edit User form
            editUserId.value = selectedUser.id;
            editUserUsername.value = selectedUser.username;
            editUserEmail.value = selectedUser.email;
            editUserPhone.value = selectedUser.phone;
            const editUserAccountType = document.getElementById("edit-user-account-type");

            // Highlight selected row
            selectUserButtons.forEach(function (item) {
                item.closest("tr").classList.remove("selected-user-row");
            });

            this.closest("tr").classList.add("selected-user-row");

        });

    });


    // OPEN EDIT USER MODAL
    if (editUserButton) {
        editUserButton.addEventListener("click", function () {

            if (!selectedUser) {
                alert("Please select a user first.");
                return;
            }

            editUserModal.style.display = "flex";

        });
    }


    // CLOSE ADD USER MODAL
    if (addUserCancelButton) {
        addUserCancelButton.addEventListener("click", function () {

            addUserModal.style.display = "none";

        });
    }


    // CLOSE EDIT USER MODAL
    if (editUserCancelButton) {
        editUserCancelButton.addEventListener("click", function () {

            editUserModal.style.display = "none";

        });
    }


    // CLOSE MODAL WHEN CLICKING OUTSIDE THE MODAL CONTENT
    window.addEventListener("click", function (event) {

        if (event.target === addUserModal) {
            addUserModal.style.display = "none";
        }

        if (event.target === editUserModal) {
            editUserModal.style.display = "none";
        }

    });

});







$(function () {
    const shiftReviewEndpoint = 'POS_TEST/cashier_shift.php';

    function formatPeso(value) {
        return '₱' + Number(value || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

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

    function loadShiftReview() {
        const $tbody = $('#shift-review-tbody');
        const $message = $('#shift-review-message');

        $message.text('Loading shifts...');

        $.ajax({
            url: shiftReviewEndpoint,
            type: 'GET',
            data: { action: 'list_closed' },
            dataType: 'json',

            success: function (response) {
                if (!response.success) {
                    $message.text(response.message || 'Unable to load shifts.');
                    return;
                }

                const shifts = response.shifts || [];

                if (!shifts.length) {
                    $tbody.html(
                        '<tr><td colspan="11" style="text-align:center;">' +
                        'No closed shifts found.</td></tr>'
                    );
                    $message.text('');
                    return;
                }

                const rows = shifts.map(function (shift) {
                    const variance = Number(shift.Cash_Variance || 0);
                    const varianceColor =
                        variance < 0 ? '#c62828' :
                        variance > 0 ? '#2e7d32' : '#333';

                    let actionHtml = '<span>Finalized</span>';

                    if (shift.Status === 'Closed') {
                        actionHtml =
                            '<button type="button" class="na-btn na-btn-add ' +
                            'finalize-shift-btn" data-shift-id="' +
                            Number(shift.Shift_ID) + '">' +
                            'Finalize</button>';
                    }

                    return `
                        <tr>
                            <td>${Number(shift.Shift_ID)}</td>
                            <td>${escapeHtml(shift.CashierName || 'Unknown')}</td>
                            <td>${escapeHtml(shift.Opened_At || '')}</td>
                            <td>${escapeHtml(shift.Closed_At || '')}</td>
                            <td>${formatPeso(shift.Opening_Float)}</td>
                            <td>${formatPeso(shift.ShiftSales)}</td>
                            <td>${formatPeso(shift.Expected_Cash)}</td>
                            <td>${formatPeso(shift.Actual_Cash)}</td>
                            <td style="color:${varianceColor};font-weight:bold;">
                                ${variance > 0 ? '+' : ''}${formatPeso(variance)}
                            </td>
                            <td>${escapeHtml(shift.Status)}</td>
                            <td>${actionHtml}</td>
                        </tr>
                    `;
                });

                $tbody.html(rows.join(''));
                $message.text('Shift records loaded.');
            },

            error: function (xhr) {
                $message.text(
                    xhr.responseJSON?.message ||
                    'Could not load shifts. Check the endpoint path and PHP error log.'
                );
            }
        });
    }

    $('#refresh-shifts-btn').on('click', loadShiftReview);

    // Load records whenever the manager opens Shift Review.
    $(document).on('click', '.nav-link', function () {
        const onclickValue = $(this).attr('onclick') || '';

        if (onclickValue.includes("'shift-review'")) {
            loadShiftReview();
        }
    });

    $(document).on('click', '.finalize-shift-btn', function () {
        const shiftId = Number($(this).data('shift-id'));

        if (!Number.isInteger(shiftId) || shiftId <= 0) {
            return;
        }

        if (!window.confirm(
            'Finalize Shift #' + shiftId + '?\n\n' +
            'Only finalize after reviewing the cash count and variance.'
        )) {
            return;
        }

        const $button = $(this);
        $button.prop('disabled', true).text('Finalizing...');

        $.ajax({
            url: shiftReviewEndpoint,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'finalize',
                shift_id: shiftId
            },

            success: function (response) {
                if (!response.success) {
                    window.alert(response.message || 'Unable to finalize shift.');
                    loadShiftReview();
                    return;
                }

                window.alert(response.message);
                loadShiftReview();
            },

            error: function (xhr) {
                window.alert(
                    xhr.responseJSON?.message ||
                    'Unable to finalize shift. Check the PHP error log.'
                );
                loadShiftReview();
            },

            complete: function () {
                $button.prop('disabled', false).text('Finalize');
            }
        });
    });
});
