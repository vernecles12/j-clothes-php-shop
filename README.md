# J Clothes — PHP Clothing Shop

A responsive, minimalist clothing shop starter built with **PHP 8+, MySQL, PDO, HTML, CSS, and vanilla JavaScript**. The palette uses forest green, sage, cream, and warm earth tones.

## Features

- Responsive home page with Shop, Our Story, and Contact sections
- Product cards showing product image, description, price, and customer rating
- Add-to-bag, quantity update, item removal, and session-based shopping cart
- User registration and login with hashed passwords
- CSRF protection on forms, prepared SQL statements, and output escaping
- Demo checkout that saves orders and order items to MySQL (no real payment processing)
- Database tables and six starter products are created automatically
- Replaceable hero background image placeholder

## Requirements

- PHP 8.0 or newer
- MySQL 5.7+ or MariaDB 10.4+
- PHP extensions: `pdo`, `pdo_mysql`, `session`, and `mbstring`
- Apache / XAMPP / WAMP / MAMP, or PHP's local development server

## Run with XAMPP (recommended for beginners)

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Copy the `j-clothes-php-shop` folder into `C:\xampp\htdocs\`.
3. Open `config/config.php` and confirm your MySQL settings. The XAMPP defaults are usually:
   - Host: `127.0.0.1`
   - Database: `j_clothes`
   - Username: `root`
   - Password: empty
4. Visit `http://localhost/j-clothes-php-shop/` in your browser.
5. The app creates the `j_clothes` database, tables, and starter products automatically on first load. The MySQL account must have permission to create a database. Alternatively, create a database named `j_clothes` in phpMyAdmin first.

If you see a message about `could not find driver`, enable the `pdo_mysql` extension in your active PHP configuration (`php.ini`) and restart Apache.

## Optional: run using PHP's development server

From the project folder, after configuring a reachable MySQL server:

```bash
php -S localhost:8000
```

Then open `http://localhost:8000`.

## Replace the hero background

The hero uses this placeholder image:

`assets/images/hero-background-placeholder.svg`

To use your own image:

1. Copy your image into `assets/images/` (for example, `j-clothes-hero.jpg`).
2. Open `assets/css/style.css`.
3. Find `.hero-photo` and change its `background-image` to:

```css
background-image: url('../images/j-clothes-hero.jpg');
```

The green overlay is controlled by `.hero:before`; adjust its opacity or gradient if your new image needs more visibility.

## Replace product images

Product image files are in `assets/images/`. The starter products use SVG placeholder illustrations. You can replace them with your own product photos. Either keep the same filenames and formats or update the `image` values in the product records inside `config/database.php`.

## Important project files

```text
j-clothes-php-shop/
├── assets/
│   ├── css/style.css
│   ├── js/app.js
│   └── images/                 # hero and product image placeholders
├── auth/                       # register, login, logout
├── config/                     # app and database configuration
├── database/schema.sql
├── includes/                   # shared header, footer, helpers
├── add_to_cart.php
├── cart.php
├── checkout.php
└── index.php
```

## Notes before production

This is a learning/demo shop, not a production-ready commerce platform. Before going live, configure HTTPS, use a dedicated database user with limited permissions, set a strong database password, add real shipping/tax/payment integrations, add order-management/admin tools, and review privacy and consumer-protection requirements. Checkout currently records demo orders only and does not collect payment details.
