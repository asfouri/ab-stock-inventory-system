<div align="center">

# A&B Stock  
### POS & Inventory Management System

A professional PHP 8 web application for stock management, sales processing, suppliers, users, and commercial operations.

<br>

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PDO](https://img.shields.io/badge/Database-PDO-34495E?style=for-the-badge)
![MVC](https://img.shields.io/badge/Architecture-MVC-2E86DE?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-Completed-2ECC71?style=for-the-badge)

<br>

**Inventory Management · POS System · Role-Based Access · Dashboard Analytics · Secure PHP MVC**

</div>

---

## Project Overview

**A&B Stock** is a complete **Point of Sale and Inventory Management System** developed with **PHP 8**, **MySQL**, **PDO**, **JavaScript**, and a structured **MVC architecture**.

The application is designed to help a store manage its daily commercial operations, including products, categories, suppliers, users, sales, stock levels, and dashboard statistics.

The system provides a clean role-based experience for:

| User Type | Main Purpose |
|---|---|
| **Administrator** | Full system management |
| **Vendeur / Seller** | Sales and POS operations |
| **Fournisseur / Supplier** | Supplier-specific product and stock access |

---

## Preview

<div align="center">

<img src="screenshots/dashboard-admin.png" width="95%" alt="A&B Stock Dashboard Preview">

</div>

---

## Table of Contents

- [Key Features](#key-features)
- [User Types](#user-types)
- [How to Use the Project](#how-to-use-the-project)
- [Screenshots](#screenshots)
  - [Public and Authentication Screens](#public--authentication-screens)
  - [Administrator Screens](#administrator-screens)
  - [Vendeur Screens](#vendeur-screens)
  - [Fournisseur Screens](#fournisseur-screens)
- [Technical Section](#technical-section)
- [Technologies Used](#technologies-used)
- [Project Architecture](#project-architecture)
- [Main Modules](#main-modules)
- [Security Features](#security-features)
- [Database Design](#database-design)
- [Installation](#installation)
- [Project Highlights](#project-highlights)
- [Academic Context](#academic-context)
- [Author](#author)

---

## Key Features

| Feature | Description |
|---|---|
| **Authentication** | Secure login and signup system |
| **Role-Based Access** | Different permissions for admin, vendeur, and fournisseur |
| **Product Management** | Add, update, delete, filter, and search products |
| **Category Management** | Organize products by category |
| **Supplier Management** | Manage suppliers and linked supplier accounts |
| **User Management** | Manage administrators, sellers, and suppliers |
| **POS Interface** | Add products to cart and validate sales |
| **Sales History** | Track sales, totals, VAT, status, and sale details |
| **Dashboard Analytics** | Revenue, stock value, sales count, and stock alerts |
| **Stock Alerts** | Detect products below their alert threshold |
| **Light / Dark Mode** | Modern theme switching system |
| **Cookie Consent** | Cookie confirmation before accessing the application |
| **Security Layer** | CSRF protection, bcrypt hashing, PDO prepared statements, and role checks |

---

## User Types

### Administrator

The administrator has complete control over the platform.

**Main permissions:**

- Manage products
- Manage categories
- Manage suppliers
- Manage users
- Access the POS interface
- View complete sales history
- View dashboard statistics
- Manage application settings

---

### Vendeur / Seller

The vendeur is responsible for sales operations.

**Main permissions:**

- Access the POS interface
- Search available products
- Add products to the cart
- Validate sales
- View personal sales history

---

### Fournisseur / Supplier

The fournisseur has restricted access to product and stock data.

**Main permissions:**

- View supplied products
- Check stock quantities
- Access only products linked to their supplier account

---

# How to Use the Project

This section explains how the application is used after installation.

---

## 1. Accept Cookie Consent

When opening the application for the first time, the user must accept the required cookies.

Cookies are used for:

- PHP session authentication
- CSRF protection
- Interface theme preference

If the user refuses cookies, the application remains inaccessible.

---

## 2. Login to the System

Users log in using their email and password.

After authentication, the system redirects the user based on their role:

| Role | Redirected To |
|---|---|
| Admin | Admin dashboard |
| Vendeur | POS / sales interface |
| Fournisseur | Supplier product view |

---

## 3. Administrator Workflow

The administrator can manage the entire system.

### Admin can:

1. Open the dashboard to view business statistics.
2. Manage product categories.
3. Add, edit, or delete products.
4. Manage suppliers.
5. Create and manage users.
6. Access the POS interface.
7. View all sales history.
8. Monitor stock alerts.

### Typical admin workflow:

```text
Login → Dashboard → Manage Products → Manage Suppliers → Monitor Sales → Check Stock Alerts
