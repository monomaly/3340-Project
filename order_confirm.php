<?php
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$order_id = (int)($_GET['order_id'] ?? 0);
$user_id  = $_SESSION['user']['id'];

if ($order_id === 0) {
    header("Location: index.php");
    exit();
}

// Get order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit();
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, b.title, b.cover_image
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<div class="confirm-page">
    <div class="confirm-box">
        <div class="confirm-icon">✓</div>
        <h2>Order Confirmed!</h2>
        <p>Thank you for your order. Your order <strong>#<?php echo $order_id; ?></strong> has been placed successfully.</p>

        <div class="confirm-details">
            <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
            <p><strong>Shipping to:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
            <p><strong>Order date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
        </div>

        <h3>Items Ordered</h3>
        <table class="confirm-table">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Format</th>
                    <th>Qty</th>
                    <th>Price Each</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td style="display:flex; align-items:center; gap:10px;">
                        <img src="images/book_images/<?php echo htmlspecialchars($item['cover_image']); ?>"
                             style="width:36px; height:50px; object-fit:cover; border-radius:3px;">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </td>
                    <td><?php echo ucfirst($item['format']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price_each'], 2); ?></td>
                    <td>$<?php echo number_format($item['price_each'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right; padding:10px; font-weight:bold;">Total Charged</td>
                    <td style="font-weight:bold;">$<?php echo number_format($order['total'], 2); ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="confirm-actions">
            <a href="index.php" class="btn">Continue Shopping</a>
            <a href="account.php" class="btn btn-secondary">View Account</a>
        </div>
    </div>
</div>

<style>
.confirm-page { display:flex; justify-content:center; padding:40px 20px; }
.confirm-box { max-width:700px; width:100%; background:#fff; border:1px solid #ddd; border-radius:12px; padding:40px; }
.confirm-icon { font-size:48px; color:green; text-align:center; margin-bottom:10px; }
.confirm-box h2 { text-align:center; margin:0 0 10px; }
.confirm-box > p { text-align:center; color:#555; margin-bottom:24px; }
.confirm-details { background:#f9f9f9; border:1px solid #eee; border-radius:8px; padding:16px; margin-bottom:24px; }
.confirm-details p { margin:4px 0; font-size:14px; }
.confirm-table { width:100%; border-collapse:collapse; font-size:14px; margin-bottom:24px; }
.confirm-table th, .confirm-table td { padding:10px 12px; border:1px solid #ddd; text-align:left; }
.confirm-table th { background:#f5f5f5; }
.confirm-actions { display:flex; gap:12px; justify-content:center; }
.btn { display:inline-block; padding:10px 24px; background:#333; color:white; border-radius:6px; text-decoration:none; font-size:14px; }
.btn-secondary { background:#888; }
</style>

<?php include 'includes/footer.php'; ?>