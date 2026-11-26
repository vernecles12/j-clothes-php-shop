<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    try {
        // Connect without selecting a database first, then create it if needed.
        $server = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $server->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        initialize_database($pdo);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        $safeMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        exit('<main style="font-family:Arial,sans-serif;max-width:760px;margin:60px auto;padding:24px;color:#20352b"><h1>J Clothes database connection failed</h1><p>Check that MySQL is running and that the database settings in <code>config/config.php</code> match your environment.</p><p><strong>Technical details:</strong> ' . $safeMessage . '</p><p>For XAMPP, start Apache and MySQL, then confirm that the <code>pdo_mysql</code> PHP extension is enabled.</p></main>');
    }
}

function initialize_database(PDO $pdo): void
{
    $columns = $pdo->query("SHOW COLUMNS FROM users LIKE 'login_count'")->fetchAll();
    if (!$columns) {
        $pdo->exec('ALTER TABLE users ADD COLUMN login_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER password_hash');
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(140) NOT NULL,
        category VARCHAR(80) NOT NULL,
        description TEXT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        rating DECIMAL(2,1) NOT NULL DEFAULT 4.8,
        image VARCHAR(190) NOT NULL,
        is_featured TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'Placed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_id INT UNSIGNED NOT NULL,
        product_id INT UNSIGNED NOT NULL,
        product_name VARCHAR(140) NOT NULL,
        quantity INT UNSIGNED NOT NULL,
        unit_price DECIMAL(10,2) NOT NULL,
        CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $products = [
        ['Blue Knit Sweater', 'Everyday essentials', 'A soft blue crewneck sweater with ribbed cuffs and a relaxed everyday fit.', 899.00, 4.9, 'linen-shirt.jpg', 1, 'Everyday Linen Shirt'],
        ['Floral Sleeve Kimono Jacket', 'Modern classics', 'A navy kimono-style jacket with detailed floral sleeve accents.', 549.00, 4.8, 'sage-tee.jpg', 1, 'Sage Oversized Tee'],
        ['Graphic Pocket Shirt', 'Modern classics', 'A lightweight white short-sleeve shirt with contrast portrait pocket artwork.', 1199.00, 4.9, 'trousers.jpg', 1, 'Earth Tone Trousers'],
        ['Embroidered Woven Shirt', 'Everyday essentials', 'A blue woven shirt featuring textured panels and embroidered trim.', 999.00, 4.7, 'knit-top.jpg', 0, 'Forest Knit Top'],
        ['Ombre T-Shirt and Shorts Set', 'Everyday essentials', 'A coordinated yellow-to-navy ombre tee and shorts set with a casual relaxed shape.', 399.00, 4.8, 'tote.jpg', 0, 'Weekend Canvas Tote'],
        ['Classic White Short-Sleeve Shirt', 'Modern classics', 'A crisp white short-sleeve shirt with a clean button-front finish.', 1699.00, 5.0, 'utility-jacket.jpg', 0, 'Relaxed Utility Jacket'],
        ['Pink Rabbit Hoodie', 'Everyday essentials', 'A cozy pink rabbit-print hoodie with a soft long-sleeve silhouette.', 799.00, 4.8, 'white-shirt.jpg', 1, null],
        ['Oversized Denim Jacket', 'Outerwear', 'A structured oversized denim jacket with a washed blue finish.', 649.00, 4.9, 'pink-hoodie.jpg', 0, null],
        ['Beige Leather Tote Bag', 'Accessories', 'A roomy beige tote with dark leather-look handles and everyday carry space.', 1499.00, 4.9, 'denim-jacket.jpg', 0, null],
    ];

    $insert = $pdo->prepare('INSERT INTO products (name, category, description, price, rating, image, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $update = $pdo->prepare('UPDATE products SET name = ?, category = ?, description = ?, price = ?, rating = ?, image = ?, is_featured = ? WHERE id = ?');
    foreach ($products as $product) {
        $id = null;
        if ($product[7] !== null) {
            $findByName = $pdo->prepare('SELECT id FROM products WHERE name = ?');
            $findByName->execute([$product[7]]);
            $id = $findByName->fetchColumn();
        }
        if (!$id) {
            $findByImage = $pdo->prepare('SELECT id FROM products WHERE image = ?');
            $findByImage->execute([$product[5]]);
            $id = $findByImage->fetchColumn();
        }
        if ($id) {
            $update->execute([$product[0], $product[1], $product[2], $product[3], $product[4], $product[5], $product[6], (int) $id]);
        } else {
            $insert->execute(array_slice($product, 0, 7));
        }
    }
}
