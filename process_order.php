<?php

require_once 'includes/db.php';

if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$full_name = $_POST['full_name'];
$address = $_POST['address'] . ", " . $_POST['city'] . " " . $_POST['postal_code'];

try {
    // Start Transaction to ensure data integrity
    $pdo->beginTransaction();

    // 1. Get current cart items to calculate final price and record options
    $stmt = $pdo->prepare("SELECT c.*, b.price FROM cart c JOIN books b ON c.book_id = b.id WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();

    if (empty($cart_items)) { throw new Exception("Empty cart"); }

    $total = 0;
    foreach ($cart_items as $item) {
        $total += ($item['price'] * $item['quantity']);
    }

    // 2. Insert into 'orders' table
    $order_stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, full_name, address) VALUES (?, ?, ?, ?)");
    $order_stmt->execute([$user_id, $total, $full_name, $address]);
    $order_id = $pdo->lastInsertId();

    // 3. Move items to 'order_items' 
    $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity, format, price_at_purchase) VALUES (?, ?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $item_stmt->execute([$order_id, $item['book_id'], $item['quantity'], $item['format'], $item['price']]);
    }

    // 4. Clear User Cart
    $clear_stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear_stmt->execute([$user_id]);

    $pdo->commit();
    header("Location: account.php?status=success");

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: checkout_confirm.php?error=process_failed");
}