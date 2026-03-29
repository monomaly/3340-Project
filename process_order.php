
<?php
require_once 'includes/db.php';

if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$shipping_address = trim($_POST['full_name']) . ', '
                  . trim($_POST['address'])   . ', '
                  . trim($_POST['city'])       . ' '
                  . trim($_POST['postal_code']);
$total = (float)$_POST['total'];

try {
    $pdo->beginTransaction();

    // 1. Get cart items
    $stmt = $pdo->prepare("
        SELECT c.*, b.price
        FROM cart c
        JOIN books b ON c.book_id = b.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();

    if (empty($cart_items)) {
        throw new Exception("Empty cart");
    }

    // 2. Insert into orders
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total, shipping_address, status)
        VALUES (?, ?, ?, 'pending')
    ");
    $stmt->execute([$user_id, $total, $shipping_address]);
    $order_id = $pdo->lastInsertId();

    // 3. Insert order items and reduce stock
    $item_stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, book_id, quantity, format, price_each)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stock_stmt = $pdo->prepare("
        UPDATE books SET stock = stock - ? WHERE id = ?
    ");
    foreach ($cart_items as $item) {
        $item_stmt->execute([
            $order_id,
            $item['book_id'],
            $item['quantity'],
            $item['format'],
            $item['price']
        ]);
        $stock_stmt->execute([$item['quantity'], $item['book_id']]);
    }

    // 4. Clear cart
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

    $pdo->commit();
    header("Location: order_confirm.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Order failed: " . $e->getMessage());
}