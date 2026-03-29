<?php
// Include database connection
require_once 'includes/db.php';

// Check if user is logged in AND request method is POST
if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// Get the current user's ID from session
$user_id = $_SESSION['user']['id'];

// Build the shipping address string from form inputs (trim removes extra spaces)
$shipping_address = trim($_POST['full_name']) . ', '
                  . trim($_POST['address'])   . ', '
                  . trim($_POST['city'])       . ' '
                  . trim($_POST['postal_code']);

// Get total order price and cast to float
$total = (float)$_POST['total'];

try {
    // Start transaction (so all queries succeed or fail together)
    $pdo->beginTransaction();

    // Select all items in the user's cart and join with books table to get prices
    $stmt = $pdo->prepare("
        SELECT c.*, b.price
        FROM cart c
        JOIN books b ON c.book_id = b.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();

    // If cart is empty, throw an error
    if (empty($cart_items)) {
        throw new Exception("Empty cart");
    }

    // Insert a new order into orders table
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total, shipping_address, status)
        VALUES (?, ?, ?, 'pending')
    ");
    $stmt->execute([$user_id, $total, $shipping_address]);

    // Get the ID of the newly created order
    $order_id = $pdo->lastInsertId();

    // Prepare statement for inserting items into order_items table
    $item_stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, book_id, quantity, format, price_each)
        VALUES (?, ?, ?, ?, ?)
    ");

    //prepare statement to reduce stock in books table
    $stock_stmt = $pdo->prepare("
        UPDATE books SET stock = stock - ? WHERE id = ?
    ");

    // Loop through each cart item
    foreach ($cart_items as $item) {
        // Insert item into order_items table
        $item_stmt->execute([
            $order_id,
            $item['book_id'],
            $item['quantity'],
            $item['format'],
            $item['price']
        ]);

        // Reduce stock for that book
        $stock_stmt->execute([$item['quantity'], $item['book_id']]);
    }

    // Remove all items from the user's cart after successful order
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

    // Commit transaction
    $pdo->commit();

    //Redirect to confirmation page with order ID
    header("Location: order_confirm.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    // undo all database changes if anything fails
    $pdo->rollBack();

    //Display error message
    die("Order failed: " . $e->getMessage());
}