min orders · PHP
Copy

<?php
require_once 'includes/db.php';
 
// Auth check BEFORE any HTML output
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
 
// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($_POST['status'], $allowed)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $_POST['status'], 'id' => (int)$_POST['order_id']]);
    }
    header('Location: admin_orders.php');
    exit();
}
 
// Fetch all orders with username
$orders = $pdo->query("
    SELECT o.*, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
")->fetchAll();
 
include 'includes/header.php';
?>
 
<div class="admin-page">
    <h2>All Orders</h2>
    <a href="admin.php" class="btn btn-secondary">Back to Dashboard</a>
 
    <?php if (empty($orders)): ?>
        <p style="margin-top:20px;">No orders yet.</p>
    <?php else: ?>
        <table class="admin-table" style="margin-top:20px;">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <?php
                    // Get items for this order
                    $items_stmt = $pdo->prepare("
                        SELECT oi.quantity, oi.format, oi.price_each, b.title
                        FROM order_items oi
                        JOIN books b ON oi.book_id = b.id
                        WHERE oi.order_id = :order_id
                    ");
                    $items_stmt->execute(['order_id' => $order['id']]);
                    $items = $items_stmt->fetchAll();
                ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                    <td>$<?php echo number_format($order['total'], 2); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                    <td>
                        <?php foreach ($items as $item): ?>
                            <div style="font-size:13px;">
                                <?php echo htmlspecialchars($item['title']); ?>
                                (<?php echo ucfirst($item['format']); ?> x<?php echo $item['quantity']; ?>)
                                — $<?php echo number_format($item['price_each'], 2); ?> each
                            </div>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <form method="POST" style="display:flex; gap:6px; align-items:center;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status">
                                <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($s); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
 