<?php
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// get cart items with book details and the selected option
$stmt = $pdo->prepare("
    SELECT c.*, b.title, b.price 
    FROM cart c 
    JOIN books b ON c.book_id = b.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

$total = 0;
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
                        <th>Format (Option)</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><?php echo ucfirst($item['format']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <h3>Total Amount: $<?php echo number_format($total, 2); ?></h3>
            
            <form action="process_order.php" method="POST" class="payment-form">
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