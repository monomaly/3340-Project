<?php
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user'])) {
    echo "<div class='wiki-page'><p>Please <a href='login.php'>login</a> to view your cart.</p></div>";
    include 'includes/footer.php';
    exit();
}

$user_id = $_SESSION['user']['id'];

// Handle Item Removal
if (isset($_GET['remove'])) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([(int)$_GET['remove'], $user_id]);
    header("Location: cart.php");
    exit();
}

// Fetch Cart items with Book details including format
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, b.title, b.price, c.quantity, c.format, b.cover_image
    FROM cart c
    JOIN books b ON c.book_id = b.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();
?>

<div class="wiki-page">
    <div class="header-box"><h1>Your Shopping Cart</h1></div>
    <div class="account-section">
        <?php if (empty($cart_items)): ?>
            <p style="text-align:center;">Your cart is empty. <a href="search.php">Go shopping!</a></p>
        <?php else: ?>
            <table style="width:100%; border-collapse: collapse;">
                <tr style="background:#f4f4f4;">
                    <th style="padding:10px;">Book</th>
                    <th>Format</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                <?php
                $grand_total = 0;
                foreach ($cart_items as $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $grand_total += $subtotal;
                ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px; display:flex; align-items:center; gap:10px;">
                        <img src="images/book_images/<?php echo htmlspecialchars($item['cover_image']); ?>" width="40">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </td>
                    <td><?php echo ucfirst($item['format']); ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                    <td><a href="cart.php?remove=<?php echo $item['cart_id']; ?>" style="color:red;">Remove</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <div style="text-align:right; margin-top:20px;">
                <h3>Total: $<?php echo number_format($grand_total, 2); ?></h3>
                <a href="checkout.php" class="account-btn">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>