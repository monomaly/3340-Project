<?php
require_once 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username'])) {
        $_SESSION['user']['username'] = $_POST['username'];
    }
    if (!empty($_POST['email'])) {
        $_SESSION['user']['email'] = $_POST['email'];
    }
    header('Location: account.php');
    exit();
}

$user = $_SESSION['user'];

include 'includes/header.php';
?>

<div class="account-container">
    <div class="account-section">
        <h2>My Profile</h2>
        <p><strong>Username: </strong><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></p>
        <p><strong>Email: </strong><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
        <p><strong>Role: </strong><?php echo htmlspecialchars($user['role'] ?? 'user'); ?></p>
    </div>

    <div class="account-section">
        <h2>Edit Profile</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter new username">
            <input type="email" name="email" placeholder="Enter new email">
            <button type="submit">Update Profile</button>
        </form>
    </div>

    <div class="account-section">
        <h2>Order History</h2>
        <p>No orders yet.</p>
    </div>

    <div class="account-section">
        <h2>Account Actions</h2>
        <?php if ($user['role'] === 'admin'): ?>
            <a href="admin.php" class="account-btn">Admin Dashboard</a>
        <?php endif; ?>
        <a href="cart.php" class="account-btn">View Cart</a>
        <a href="logout.php" class="account-btn logout">Logout</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>