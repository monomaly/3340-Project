<?php
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT b.price, c.quantity FROM cart c JOIN books b ON c.book_id = b.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

$subtotal = 0;
foreach ($items as $item) { $subtotal += ($item['price'] * $item['quantity']); }

$tax = $subtotal * 0.13;
$shipping = ($subtotal > 50 || $subtotal == 0) ? 0 : 10;
$total = $subtotal + $tax + $shipping;
?>

<div class="wiki-page">
    <div class="header-box"><h1>Checkout Quote</h1></div>
    <div class="account-section" style="display:flex; gap:20px;">
        <div style="flex:2;">
            <form action="process_order.php" method="POST">
                <h3>Shipping Details</h3>
                <input type="text" name="full_name" placeholder="Full Name" required style="width:100%; margin-bottom:10px;">
                <input type="text" name="address" placeholder="123 Windsor St" required style="width:100%;">
                <button type="submit" class="account-btn" style="margin-top:20px;">Confirm & Pay</button>
            </form>
        </div>
        <div style="flex:1; background:#f9f9f9; padding:15px; border-radius:8px;">
            <h3>Summary</h3>
            <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
            <p>Tax (13%): $<?php echo number_format($tax, 2); ?></p>
            <p>Shipping: <?php echo $shipping == 0 ? "FREE" : "$10.00"; ?></p>
            <hr>
            <h4>Total: $<?php echo number_format($total, 2); ?></h4>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>