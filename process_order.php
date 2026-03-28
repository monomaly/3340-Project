<?php
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$full_name = $_POST['full_name'];
$address = $_POST['address'] . ", " . $_POST['city'] . " " . $_POST['postal_code'];

try {
    // Start Transaction: Requirements #5 & #6 (Professional Documentation)
    $pdo->beginTransaction();

    // 1. Calculate Total from Cart
    $stmt = $pdo->prepare("SELECT c.book_id, c.quantity, b.price FROM cart c JOIN books b ON c.book_id = b.id WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();

    if (empty($cart_items)) {
        throw new Exception("Cart is empty");
    }

    $total = 0;
    foreach ($cart_items as $item) {
        $total += ($item['price'] * $item['quantity']);
    }
    $total_with_tax = $total * 1.13; // Adding 13% tax as per checkout.php logic

    // 2. Create the Order Record
    $order_stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, full_name, address) VALUES (?, ?, ?, ?)");
    $order_stmt->execute([$user_id, $total_with_tax, $full_name, $address]);
    $order_id = $pdo->lastInsertId();

    // 3. Move items to order_items (Requirement #2: Records History)
    $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $item_stmt->execute([$order_id, $item['book_id'], $item['quantity'], $item['price']]);
    }

    // 4. Clear the Cart
    $clear_stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear_stmt->execute([$user_id]);

    // Commit all changes
    $pdo->commit();

    // Redirect to a success page (or account.php to show history)
    header("Location: account.php?status=success&order_id=" . $order_id);

} catch (Exception $e) {
    // If anything fails, undo everything
    $pdo->rollBack();
    header("Location: checkout.php?error=failed");
}