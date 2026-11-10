# An Inventory and Sales Management System with Product Demand Prediction with Analytics

A web-based Inventory and Sales Management System developed as an undergraduate capstone project for Global Reciprocal Colleges. The system integrates inventory management, supplier management, point of sale (POS), sales analytics, and machine learning-based product demand prediction to help managers and cashiers manage business operations and make informed restocking decisions.

The project uses MVM Daegu Supermarket Corp. – España Branch as its primary business reference and was developed using the Rapid Application Development (RAD) methodology.

---

## Group Members & Roles

- **Chris Enrique A. Escote** – Project Leader / Co-Developer
- **Carlo Jay B. Gomez** – Full Stack Developer
- **Bryan Lenard B. Dela Cruz** – QA Tester / Co-Developer / Documentation Specialist
- **Maritoni Q. Gallardo** – Quality Assurance / Documentation Specialist
- **Aubrey Bucu** – Quality Assurance / Documentation Specialist
- **Daryl C. Lanugan** – Documentation Specialist

---

## Tech Stack

| Category | Technologies |
|---|---|
| Frontend | HTML, CSS, JavaScript |
| Backend | PHP |
| Database | MySQL |
| Machine Learning | Python, XGBoost |
| Local Development Environment | XAMPP |
| Version Control | Git, GitHub |
| Database Administration | phpMyAdmin |

---

## System Features

### 1. Inventory Management
- Product creation, editing, and management
- Product categorization
- Inventory monitoring and stock tracking
- Automatic stock updates from sales and restocking activities
- Supplier management
- Restocking request, delivery, and receiving workflows

### 2. Point of Sale (POS)
- Sales transaction processing
- Barcode scanning
- Automatic inventory updates after sales
- Configurable tax settings and calculations
- Cashier checkout and payment processing
- Printable sales receipts

### 3. Supplier and Pricing Management
- Supplier-specific product purchase prices
- Product, category, and general markup configuration
- Suggested selling price calculations
- Supplier-product relationship management

### 4. Sales Analytics and Reporting
- Sales dashboard
- Sales performance monitoring
- Daily, weekly, and monthly reports
- Inventory and sales data analysis

### 5. Product Demand Prediction
- Machine learning-based product demand forecasting
- XGBoost prediction model
- Historical sales data analysis
- Restocking recommendations based on predicted demand and current inventory

### 6. User Access Management

The system provides three account types with different access permissions:

| Account Type | Responsibilities |
|---|---|
| Super Admin | User account management and administrative functions |
| Manager | Product and inventory management, supplier management, reports, pricing, and demand prediction |
| Cashier | POS operations and sales processing |

---

## System Requirements & Prerequisites

Before installing the project, ensure that the following are available on your computer:

- **XAMPP** – Apache and MySQL
- **PHP** – Version compatible with your installed XAMPP release
- **Python** – Required for the machine learning module
- **Git** – For cloning the repository
- **Web Browser** – Google Chrome or another modern browser
- **phpMyAdmin** – Included with XAMPP

---

## Installation & Setup Instructions

### 1. Clone the Repository

Open Git Bash or a terminal and navigate to your XAMPP `htdocs` directory.

```bash
cd /h/xampp/htdocs
git clone https://github.com/INSIGNI4/Inventory-and-Sales-Management-System.git
```

The cloned project directory should be:

```text
Login/
```

If you are using Windows Command Prompt, navigate to your XAMPP installation's `htdocs` directory before running the clone command.

### 2. Start XAMPP

1. Open the XAMPP Control Panel.
2. Start **Apache**.
3. Start **MySQL**.

Both services should be running before accessing the application.

---

## Database Setup & Configuration

The application uses two separate MySQL databases:

| Database | Purpose |
|---|---|
| `login` | Inventory, products, suppliers, user accounts, and related system data |
| `point_of_sale` | POS sales records, sales items, tax settings, and historical sales data |

### 1. Create the Databases

1. Open phpMyAdmin:

   `http://localhost/phpmyadmin`

2. Create the following databases:

   - `login`
   - `point_of_sale`

### 2. Import the Database Files

The repository contains SQL backup files inside the `database` directory.

| SQL File | Database to Import Into |
|---|---|
| `database/login.sql` | `login` |
| `database/point_of_sale.sql` | `point_of_sale` |

For each database:

1. Select the database in phpMyAdmin.
2. Open the **Import** tab.
3. Choose the corresponding SQL file.
4. Click **Import** or **Go**.
5. Wait for the import process to finish.

### 3. Configure the PHP Database Connection

The repository includes an example PHP configuration file.

1. In the project root directory, locate:

   `config.example.php`

2. Make a copy of the file.
3. Rename the copied file to:

   `config.local.php`

4. Open `config.local.php` and update the MySQL credentials according to your local installation.

Example:

```php
<?php

return [
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'UPDATE_PASSWORD_HERE',
    'inventory_database' => 'login',
    'pos_database' => 'point_of_sale'
];
```

Replace `UPDATE_PASSWORD_HERE` with your local MySQL password.

### 4. Configure the Python Database Connection

The machine learning module has its own database configuration file.

1. Navigate to:

   `ml_prediction-main/ml/`

2. Locate:

   `config_local.example.py`

3. Make a copy of the file.
4. Rename the copied file to:

   `config_local.py`

5. Open the copied file and update your local MySQL credentials.

Example:

```python
DB_CONFIG = {
    "host": "127.0.0.1",
    "port": 3306,
    "user": "root",
    "password": "UPDATE_PASSWORD_HERE",
    "database": "point_of_sale"
}
```

Replace `UPDATE_PASSWORD_HERE` with the same MySQL password used in the PHP configuration.

**Note:** If your local MySQL root account does not have a password, use an empty string:

```python
"password": ""
```

Apply the same setting in the PHP configuration file.

**Important:** The actual configuration files (`config.local.php` and `config_local.py`) are excluded from Git through `.gitignore`. Do not upload personal database credentials to the public repository.

---

## Running the Application

After completing the installation and database configuration:

1. Ensure Apache and MySQL are running in XAMPP.
2. Open your web browser.
3. Navigate to:

   `http://localhost/Login/`

4. Log in using one of the demonstration accounts below.

---

## Default Login Credentials

The following demonstration accounts are provided for testing the application.

| Account Type | Email | Password |
|---|---|---|
| Super Admin | `admin@demo.com` | `AdminDemo2026!` |
| Manager | `manager@demo.com` | `ManagerDemo2026!` |
| Cashier | `cashier@demo.com` | `CashierDemo2026!` |

**Important:** These are application login credentials. They are different from your local MySQL database credentials.

---

## Machine Learning Module

The product demand prediction module is located in:

```text
ml_prediction-main/
└── ml/
```

The module uses historical sales records from the `point_of_sale` database to generate product demand predictions and support inventory restocking recommendations.

The prediction functionality requires Python and the necessary machine learning dependencies.

---

## Database Structure Overview

### `login` Database

Contains the main inventory and system-related data, including:

- User accounts and access roles
- Product information
- Supplier information
- Inventory records
- Restocking records
- Supplier-specific product pricing
- Markup and pricing settings

### `point_of_sale` Database

Contains POS and sales-related data, including:

- `sales_test` – Sales transaction records
- `sales_item_test` – Individual items associated with sales
- Tax settings
- Historical sales data used by the demand prediction module

---

## System Development & Evaluation

The system was developed using the Rapid Application Development (RAD) methodology.

The research included:
- 25 unit and integration test cases
- Response-time measurements
- Evaluation by 14 respondents, consisting of 9 end users and 5 IT experts
- Software quality assessment using ISO/IEC 25010 as the evaluation reference

The system obtained an overall mean score of **3.44**, interpreted as **Highly Acceptable**.

---

## Important Notes

- Import both SQL database files before running the application.
- Configure both local database connection files using your own MySQL credentials.
- The demonstration accounts are intended for testing the application.
- Keep your actual local configuration files and database credentials private.
- The machine learning module requires Python and its required dependencies.
- The SQL files provide the database structure and demonstration records for a fresh installation.

---

## Project Information

**Institution:** Global Reciprocal Colleges

**Degree Program:** Bachelor of Science in Information Technology

**Project Year:** 2026

**Development Methodology:** Rapid Application Development (RAD)

**Primary Business Reference:** MVM Daegu Supermarket Corp. – España Branch


## License

This project was developed as an academic capstone project for Global Reciprocal Colleges and is intended for academic and educational purposes.