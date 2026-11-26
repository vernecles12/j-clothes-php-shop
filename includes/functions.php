<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function money(float|int|string $amount): string
{
    return CURRENCY . number_format((float) $amount, 2);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('Please sign in to continue.');
        header('Location: auth/login.php');
        exit;
    }
}

function set_flash(string $message, string $type = 'success', ?string $alertTitle = null, ?int $undoProductId = null): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type, 'alertTitle' => $alertTitle, 'undoProductId' => $undoProductId];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    $saved = $_SESSION['csrf_token'] ?? '';
    if (!is_string($sent) || !is_string($saved) || $saved === '' || !hash_equals($saved, $sent)) {
        http_response_code(419);
        exit('Your form session expired. Go back, refresh the page, and try again.');
    }
}

function cart_count(): int
{
    return array_sum(array_map('intval', $_SESSION['cart'] ?? []));
}

function cart_items(): array
{
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $ids = array_values(array_filter(array_map('intval', array_keys($cart))));
    if (!$ids) return [];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    $items = [];
    foreach ($products as $product) {
        $qty = max(1, min(20, (int) ($cart[$product['id']] ?? 1)));
        $product['quantity'] = $qty;
        $product['line_total'] = (float) $product['price'] * $qty;
        $items[] = $product;
    }
    return $items;
}

function cart_total(array $items): float
{
    return array_reduce($items, fn(float $sum, array $item): float => $sum + (float) $item['line_total'], 0.0);
}
