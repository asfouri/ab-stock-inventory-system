# A&B Stock — POS and Inventory Management System

A&B Stock is a PHP 8 web application designed for sales, stock, and commercial management.  
It provides a complete Point of Sale interface, inventory tracking, supplier management, user management, sales history, and dashboard analytics.

The project follows a structured MVC architecture and includes role-based access for administrators, sellers, and suppliers.

---

## Preview

![Dashboard Preview](screenshots/dashboard-admin.png)

---

## Features

- Secure authentication system
- Role-based access control
- Administrator, seller, and supplier interfaces
- Product management
- Category management
- Supplier management
- User management
- POS / cashier interface
- Cart and sales validation
- Automatic stock update after each sale
- Sales history
- Dashboard analytics
- Low-stock alerts
- Light and dark theme
- Cookie consent system
- CSRF protection
- Password hashing
- PDO prepared statements
- Soft delete for data integrity

---

## User Roles

### Administrator

The administrator has full access to the system.  
They can manage users, products, categories, suppliers, sales history, application settings, and dashboard statistics.

### Seller

The seller can access the POS interface, search products, add products to the cart, validate sales, and view their own sales history.

### Supplier

The supplier can view only the products and stock information related to the products they provide.

---

## Screenshots

### Cookie Consent

![Cookie Consent](screenshots/cookie-consent.png)

---

### Login Pages

#### Light Mode Login

![Light Login](screenshots/login-light.png)

#### Dark Mode Login

![Dark Login](screenshots/login-dark.png)

---

### Admin Interface

#### Admin Dashboard

![Admin Dashboard](screenshots/dashboard-admin.png)

#### Product Management

![Product Management](screenshots/products-admin.png)

#### Category Management

![Category Management](screenshots/categories-admin.png)

#### Supplier Management

![Supplier Management](screenshots/suppliers-admin.png)

#### User Management

![User Management](screenshots/users-admin.png)

#### POS Interface - Admin

![POS Interface Admin](screenshots/pos-interface-admin.png)

#### Sales History - Admin

![Sales History Admin](screenshots/sales-history-admin.png)

---

### Seller Interface

#### POS Interface - Seller

![POS Interface Seller](screenshots/pos-interface-seller.png)

#### Sales History - Seller

![Sales History Seller](screenshots/sales-history-seller.png)

---

### Supplier Interface

#### Supplier Product View

![Supplier Products](screenshots/supplier-products.png)

---

## Technologies Used

- PHP 8
- MySQL / MariaDB
- PDO
- HTML5
- CSS3
- JavaScript
- AJAX
- MVC Architecture
- XAMPP

---

## Project Architecture

The project follows the MVC architecture:

```text
ab-stock-inventory-system/
├── config/
├── Controleur/
├── Modele/
├── Vue/
├── public/
├── database/
├── screenshots/
└── index.php
