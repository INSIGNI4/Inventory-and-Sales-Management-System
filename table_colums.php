    <?php

    $GLOBALS['conn_pos'] = $conn_pos;

    $table_colums_mapping = [
        /// ======= INVENTORY DATABASE ======= ///

        'users' => [
            
            'id', 
            'userName', 
            'email', 
            'password', 
            'phone', 
            'image',
            'accountType', 
            'reset_token_hash', 
            'reset_token_expires_at',
            'account_activation_hash',
            'accountType',
            'is_active' 
        ],
        
        // 'forecast' => [
        //     'Forecast_ID', 'ForecastType', 'Product_ID', 'ForecastPeriod',
        //     'ForecastStart', 'ForecastEnd', 'ProjectedSales', 'ConfidenceLevel'
        // ],

        // 'product' => [
        //     'Product_ID', 'ProductName', 'Type', 'ReordingPoints', 'UnitsOrdered',
        //     'UnitSold', 'StorePrice', 'SupplierPrice', 'Image', 'Supplier_ID', 
        //     'Pack_Size', 
        //     'Barcode',
        //     'LocationS','LocationR'
        // ],







    'product' => [
        'Product_ID',
        'ProductName',
        'Type',
        'ReordingPoints',
        'UnitsOrdered',
        'UnitSold',
        'StorePrice',
        'Image',
        'Pack_Size',
        'Barcode',
        'LocationS',
        'LocationR',
        'IsArchived'
    ],


        'inventory' => [
            'Product_ID', 'LocationS','LocationR', 'Price', 'Inventory', 'UnitIN',
            'UnitOut', 'Status', 'ExpirationDate','Barcode','Supplier_ID'
        ],

        // 'newaddition' => [
        //     'Inventory_ID','Product_ID', 'Quantity', 'Date_Added',
        //     'Expiration_Date', 'Status', 'Supplier_ID','LocationS','LocationR'
        // ],
        // 'sales' => [
        //     'Order_ID', 'Transaction_ID', 'Product_ID','ProductName','Unit_Price', 'Quantity', 'TotalPrice',
        //     'Barcode',
        //     'SalesDate', 'id','BatchNum','userName'
        // ],
        // 'transactions' => [
        //     'Transaction_ID', 'Customer_ID', 'ReferenceNo', 'PurchaseType', 'PaymentMethod', 'ServiceType',
        //     'Transaction_Date','Total_Price'
        // ],

        // 'transactions' => [
        //         'Transaction_ID', 'ReferenceNo', 'PaymentMethod',
        //         'Transaction_Date','Total_Price'
        // ],

        'customers' => [
            'Customer_ID', 'CustomerName', 'Location', 'Email', 'PhoneNumber'
        ],
        'customersreturns' => [
            'CReturn_ID', 'ReferenceNo', 'Quantity', 'ReturnedDate', 'ReasonForReturn',
            'Product_ID' 
        ],
        // 'supplier' => [
        //     'Supplier_ID',
        //     'SupplierFName',
        //     'SupplierLName',
        //     'Location',
        //     'Email',
        //     'PhoneNumber',
        //     'OfferedProductsType'
            
        // ],
        //new
        'supplier' => [
            'Supplier_ID',
            'CompanyName',
            'ContactPerson',
            'Address',
            'City',
            'Email',
            'PhoneNumber',
            'ProductCategory',
            'IsArchived'
        ],

        'supplierreturns' => [
            'SReturns_ID', 'Supplier_ID', 'Product_ID', 'Quantity', 'ReturnedDate', 'Status', 'Reason'
        ],
        'pulledoutitems' => [
            'Pulled_ID', 'Product_ID', 'Supplier_ID', 'Quantity', 'Reason', 'PulledDate'
        ],

        // order restocking
        'restock' => [
            'Orestock_ID', 'ItemToOrder_ID','Type', 'Quantity', 'OrderDate', 'Product_ID',
            'Supplier_ID', 'Status', 'Image', 'DeliveryStatus','Date_Received'
            ,'TotalReceived','withIssue','ExpirationDate','OrderCost','PurchaseUnitCost'
        ],
        
        // 'restock' => [
        //     'Restock_ID', 'ItemToOrder_ID', 'Received_Quantity', 'Date_Received', 'ExpirationDate',
        //     'Receipt_Image', 'Status', 'WithIssue', 'Notes'
        // ],

        // 'item_to_order' => [
        //     'ItemToOrder_ID', 'ListToOrder_ID', 'Product_ID', 'Current_Stock', 'Predicted_Demand',
        //     'Forecast_Start_Date', 'Forecast_End_Date', 'Net_Demand', 'Pack_Size',
        //     'Recommended_Order_Quantity', 'Ordered_Quantity', 'Unit_Cost', 'Line_Total',
        //     'Received_Quantity', 'Item_Status', 'Notes'
        // ],

        // 'list_to_order' => [
        //     'ListToOrder_ID', 'Supplier_ID', 'Order_Date', 'Expected_Receive_Date', 'Order_Status',
        //     'Total_Amount', 'Created_By', 'Notes'
        // ],

        // 'inventory_batch' => [
        //     'Batch_ID', 'Product_ID', 'Restock_ID', 'Supplier_ID', 'BatchNum',
        //     'ExpirationDate', 'ReceivedDate', 'Quantity'
        // ],





        
        // /// ======= FORECASTING & ANALYTICS DATABASE ======= ///



        // 'daily_forecast' => [
        //     'DailyForecast_ID', 'ForecastType', 'ProductScope', 'ForecastPeriod',
        //     'ForecastStart', 'ForecastEnd', 'ProjectedSales', 'ConfidenceLevel', 'Account_ID'
        // ],
        // 'weekly_forecast' => [
        //     'WeeklyForecast_ID', 'ForecastType', 'ProductScope', 'ForecastPeriod',
        //     'ForecastStart', 'ForecastEnd', 'ProjectedSales', 'ConfidenceLevel', 'Account_ID'
        // ],
        // 'monthly_forecast' => [
        //     'MonthlyForecast_ID', 'ForecastType', 'ProductScope', 'ForecastPeriod',
        //     'ForecastStart', 'ForecastEnd', 'ProjectedSales', 'ConfidenceLevel', 'Account_ID'
        // ],
        
        // 'analytics' => [
        //     'AnalyticsID', 'Forecast_ID', 'Forecast_Type','Report_Date', 'ProductScope', 'PeriodType', 'SalesMetrics',
        //     'Inventory_ID', 'Account_ID'
        // ],
        
        
        // 'salesaggregration' => [
        //     'Aggregation_ID', 'PeriodType', 'PeriodStart', 'PeriodEnd', 'Product_ID', 'TotalSales', 'TotalQuantity'
        // ],





        // 'daily_sales' => [
        //     'DailySales_ID ', 'PeriodStart', 'PeriodEnd', 'Product_ID', 'TotalSales', 'TotalQuantity'
        // ],

        // 'weekly_sales' => [
        //     'WeeklySales_ID ', 'PeriodStart', 'PeriodEnd', 'Product_ID', 'TotalSales', 'TotalQuantity'
        // ],

        // 'monthly_sales' => [
        //     'MonthlySales_ID ', 'PeriodStart', 'PeriodEnd', 'Product_ID', 'TotalSales', 'TotalQuantity'
        // ],



        // 'daily_total_sales' => [
        //     'EntireDailySales_ID ', 'PeriodStart', 'PeriodEnd', 'TotalSales', 'TotalQuantity'
        // ],

        // 'weekly_total_sales' => [
        //     'EntireWeeklySales_ID ', 'PeriodStart', 'PeriodEnd', 'TotalSales', 'TotalQuantity'
        // ],

        // 'monthly_total_sales' => [
        //     'EntireMonthlySales_ID ', 'PeriodStart', 'PeriodEnd', 'TotalSales', 'TotalQuantity'
        // ],

        

    /// ======= POS DATABASE ======= ///

        'sales_item_test' => [
            'SalesItem_ID', 'Sales_ID', 'Product_ID', 'Quantity', 'Unit_Price', 'TotalPrice',
            'date_created', 'date_updated', 'BatchNum', 
        ],
        
        'sales_test' => [
            'Sales_ID', 'ReferenceNo', 'User_ID', 'total_amount', 'amount_tendered', 'change_amt',
            'date_created', 'date_updated',
        ],


    ];


