<?php

require_once 'includes/db.php';
include 'includes/header.php';

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get order ID from URL
$order_id = (int)($_GET['order_id'] ?? 0);

// Get current logged-in user's id
$user_id  = $_SESSION['user']['id'];

// If order ID is invalid, redirect to homepage
if ($order_id === 0) {
    header("Location: index.php");
    exit();
}

// Fetch order from database that matches both order ID and user ID
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

// If no order is found, redirect
if (!$order) {
    header("Location: index.php");
    exit();
}

// Get all items in this order and join with books table for extra info
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
        <!-- Confirmation icon -->
        <div class="confirm-icon">✓</div>

        <!-- Confirmation message -->
        <h2>Order Confirmed!</h2>
        <p>
            Thank you for your order. Your order 
            <strong>#<?php echo $order_id; ?></strong> has been placed successfully.
        </p>

        <!-- order details-->
        <div class="confirm-details">-->
            <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>

            <!-- Display shipping address-->
            <p><strong>Shipping to:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>

            <!-- display order date -->
            <p><strong>Order date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
        </div>

        <!-- order iterms table -->
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
                <!-- Loop through each item in the order -->
                <?php foreach ($items as $item): ?>
                <tr>
                    <td style="display:flex; align-items:center; gap:10px;">
                        <!-- Book cover image -->
                        <img src="images/book_images/<?php echo htmlspecialchars($item['cover_image']); ?>"
                             style="width:36px; height:50px; object-fit:cover; border-radius:3px;">

                        <!-- Book title -->
                        <?php echo htmlspecialchars($item['title']); ?>
                    </td>

                    <!-- book format -->
                    <td><?php echo ucfirst($item['format']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price_each'], 2); ?></td>

                    <!-- Subtotal (price × quantity) -->
                    <td>$<?php echo number_format($item['price_each'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>

            <!-- order total-->
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right; padding:10px; font-weight:bold;">
                        Total Charged
                    </td>
                    <td style="font-weight:bold;">
                        $<?php echo number_format($order['total'], 2); ?>
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="confirm-actions">
            <!-- Go back to homepage -->
            <a href="index.php" class="btn">Continue Shopping</a>

            <!-- Go to user account page -->
            <a href="account.php" class="btn btn-secondary">View Account</a>
        </div>
    </div>
</div>

<style>
/* Center the confirmation box on the page */
.confirm-page { display:flex; justify-content:center; padding:40px 20px; }

/* Main container styling */
.confirm-box { max-width:700px; width:100%; background:#fff; border:1px solid #ddd; border-radius:12px; padding:40px; }
.confirm-icon { font-size:48px; color:green; text-align:center; margin-bottom:10px; }
.confirm-box h2 { text-align:center; margin:0 0 10px; }
.confirm-box > p { text-align:center; color:#555; margin-bottom:24px; }
.confirm-details { background:#f9f9f9; border:1px solid #eee; border-radius:8px; padding:16px; margin-bottom:24px; }
.confirm-details p { margin:4px 0; font-size:14px; }

/* Table styling */
.confirm-table { width:100%; border-collapse:collapse; font-size:14px; margin-bottom:24px; }
.confirm-table th, .confirm-table td { padding:10px 12px; border:1px solid #ddd; text-align:left; }/
.confirm-table th { background:#f5f5f5; }

/* Buttons container */
.confirm-actions { display:flex; gap:12px; justify-content:center; }

/* button style */
.btn { display:inline-block; padding:10px 24px; background:#333; color:white; border-radius:6px; text-decoration:none; font-size:14px; }
.btn-secondary { background:#888; }
</style>

<?php 
// Include footer (closing HTML, footer content)
include 'includes/footer.php'; 
?>