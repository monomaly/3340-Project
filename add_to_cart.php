<?php
// Documented Database Logic
require_once 'includes/db.php';
session_start();

// Private Area - User must be logged in to add to cart
if (!isset($_SESSION['user'])) {
    header("Location: login.php?error=must_login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $user_id = $_SESSION['user']['id'];
    $book_id = (int)$_POST['book_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Check if the book is already in the cart for this user
    $check_stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND book_id = ?");
    $check_stmt->execute([$user_id, $book_id]);
    $existing_item = $check_stmt->fetch();

    if ($existing_item) {
        // Update existing quantity
        $new_qty = $existing_item['quantity'] + $quantity;
        $update_stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $update_stmt->execute([$new_qty, $existing_item['id']]);
    } else {
        //SQL Insert for new cart record
        $insert_stmt = $pdo->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->execute([$user_id, $book_id, $quantity]);
    }

    // Redirect back to the cart or the previous page with a success message
    header("Location: cart.php?success=added");
    exit();
}