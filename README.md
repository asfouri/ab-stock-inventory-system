<div align="center">

# A&B Stock  
### POS & Inventory Management System

A professional PHP 8 web application for sales, inventory, suppliers, users, and commercial management.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PDO](https://img.shields.io/badge/Database-PDO-34495E?style=for-the-badge)
![MVC](https://img.shields.io/badge/Architecture-MVC-2E86DE?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-Completed-2ECC71?style=for-the-badge)

</div>

---

## Overview

**A&B Stock** is a complete **Point of Sale and Inventory Management System** developed using **PHP 8**, **MySQL**, **PDO**, **JavaScript**, and a structured **MVC architecture**.

The system helps stores manage products, categories, suppliers, users, sales, stock levels, and dashboard statistics through a clean and modern web interface.

The application supports three main user types:

- **Administrator**
- **Vendeur / Seller**
- **Fournisseur / Supplier**

Each role has a dedicated interface, specific permissions, and restricted access according to its responsibilities.

---

## Preview

<div align="center">

<img src="screenshots/dashboard-admin.png" width="95%" alt="Admin Dashboard Preview">

</div>

---

## Table of Contents

- [Key Features](#key-features)
- [User Types](#user-types)
- [Screenshots](#screenshots)
- [Technical Deep Dive](#technical-deep-dive)
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
| Authentication | Secure login and signup system |
| Role-Based Access | Separate access for admin, vendeur, and fournisseur |
| Product Management | Add, update, delete, filter, and search products |
| Category Management | Organize products into categories |
| Supplier Management | Manage suppliers and linked supplier accounts |
| User Management | Manage administrators, sellers, and suppliers |
| POS Interface | Add products to cart and validate sales |
| Sales History | Track sales, totals, VAT, status, and sale details |
| Dashboard Analytics | View revenue, stock value, sales count, and stock alerts |
| Stock Alerts | Detect products below their alert threshold |
| Light / Dark Mode | Modern theme switching system |
| Cookie Consent | Cookie confirmation before accessing the application |
| Security | CSRF protection, bcrypt hashing, PDO prepared statements, and role checks |

---

## User Types

### Administrator

The administrator has full access to the application.

**Main permissions:**

- Manage products
- Manage categories
- Manage suppliers
- Manage users
- Access the POS interface
- View complete sales history
- View dashboard statistics
- Manage application settings

### Vendeur / Seller

The vendeur is responsible for sales operations.

**Main permissions:**

- Access the POS interface
- Search products
- Add products to the cart
- Validate sales
- View personal sales history

### Fournisseur / Supplier

The fournisseur has limited access to stock information.

**Main permissions:**

- View supplied products
- Check stock quantities
- Access only products linked to their supplier account

---

# Screenshots

## Public & Authentication Screens

These screens are accessible before entering the role-based areas of the application.

<table>
  <tr>
    <td align="center">
      <strong>Cookie Consent</strong><br><br>
      <img src="screenshots/cookie-consent.png" width="100%" alt="Cookie Consent">
    </td>
  </tr>
</table>

<table>
  <tr>
    <td align="center" width="50%">
      <strong>Login - Light Mode</strong><br><br>
      <img src="screenshots/login-light.png" width="100%" alt="Login Light Mode">
    </td>
    <td align="center" width="50%">
      <strong>Login - Dark Mode</strong><br><br>
      <img src="screenshots/login-dark.png" width="100%" alt="Login Dark Mode">
    </td>
  </tr>
</table>

---

## Administrator Screens

The administrator interface provides complete control over the system, including stock, products, users, suppliers, sales, and statistics.

### Admin Dashboard

<table>
  <tr>
    <td align="center">
      <img src="screenshots/dashboard-admin.png" width="100%" alt="Admin Dashboard">
    </td>
  </tr>
</table>

### Product & Category Management

<table>
  <tr>
    <td align="center" width="50%">
      <strong>Product Management</strong><br><br>
      <img src="screenshots/products-admin.png" width="100%" alt="Product Management">
    </td>
    <td align="center" width="50%">
      <strong>Category Management</strong><br><br>
      <img src="screenshots/categories-admin.png" width="100%" alt="Category Management">
    </td>
  </tr>
</table>

### Supplier & User Management

<table>
  <tr>
    <td align="center" width="50%">
      <strong>Supplier Management</strong><br><br>
      <img src="screenshots/suppliers-admin.png" width="100%" alt="Supplier Management">
    </td>
    <td align="center" width="50%">
      <strong>User Management</strong><br><br>
      <img src="screenshots/users-admin.png" width="100%" alt="User Management">
    </td>
  </tr>
</table>

### Admin Sales Operations

<table>
  <tr>
    <td align="center" width="50%">
      <strong>Admin POS Interface</strong><br><br>
      <img src="screenshots/pos-interface-admin.png" width="100%" alt="Admin POS Interface">
    </td>
    <td align="center" width="50%">
      <strong>Admin Sales History</strong><br><br>
      <img src="screenshots/sales-history-admin.png" width="100%" alt="Admin Sales History">
    </td>
  </tr>
</table>

---

## Vendeur Screens

The vendeur interface is focused on sales operations and personal sales tracking.

<table>
  <tr>
    <td align="center">
      <strong>Vendeur POS Interface</strong><br><br>
      <img src="./screenshots/seller-pos-interface.png" width="100%" alt="Vendeur POS Interface">
    </td>
  </tr>
</table>

<table>
  <tr>
    <td align="center">
      <strong>Vendeur Sales History</strong><br><br>
      <img src="./screenshots/seller-sales-history.png" width="100%" alt="Vendeur Sales History">
    </td>
  </tr>
</table>

---

## Fournisseur Screens

The fournisseur interface gives access only to the products and stock information related to that supplier.

<table>
  <tr>
    <td align="center">
      <strong>Fournisseur Product View</strong><br><br>
      <img src="screenshots/supplier-products.png" width="100%" alt="Fournisseur Product View">
    </td>
  </tr>
</table>

---

# Technical Deep Dive

## Backend Architecture

The backend is built with **PHP 8** using an object-oriented approach.  
The application follows the **MVC pattern**, which separates business logic, user interface, and request handling.

| Layer | Responsibility |
|---|---|
| Model | Handles database access and business rules |
| View | Displays HTML/PHP user interfaces |
| Controller | Processes requests and connects models with views |
| Front Controller | `index.php` centralizes routing and page access |

This separation makes the project easier to maintain, debug, and extend.

---

## MVC Routing System

The application uses `index.php` as the main entry point.  
Pages are routed through URL parameters such as:

```text
index.php?page=produits
index.php?page=caisse
index.php?page=utilisateurs
index.php?page=fournisseurs
