<?php
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT c.*, b.title, b.price
    FROM cart c
    JOIN books b ON c.book_id = b.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

$subtotal  = 0;
$tax_rate  = 0.13;   // 13% HST (Ontario)
$shipping  = 5.99;   // flat shipping fee

foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$tax   = $subtotal * $tax_rate;
$total = $subtotal + $tax + $shipping;
?>

<div class="checkout-container">
    <h2>Confirm Your Order</h2>

    <?php if (empty($items)): ?>
        <p>Your cart is empty. <a href="index.php">Go shopping</a></p>
    <?php else: ?>
        <div class="order-summary">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Format</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><?php echo ucfirst($item['format']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right; padding:8px;">Subtotal</td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align:right; padding:8px;">Shipping</td>
                        <td>$<?php echo number_format($shipping, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align:right; padding:8px;">Tax (HST 13%)</td>
                        <td>$<?php echo number_format($tax, 2); ?></td>
                    </tr>
                    <tr style="font-weight:bold; font-size:16px;">
                        <td colspan="3" style="text-align:right; padding:8px;">Total</td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>

            <form action="process_order.php" method="POST" class="payment-form">
                <input type="hidden" name="total" value="<?php echo number_format($total, 2); ?>">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea name="address" required placeholder="123 University Ave"></textarea>
                </div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" required>
                </div>
                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" required>
                </div>
                <button type="submit" class="btn">Confirm and Pay</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>