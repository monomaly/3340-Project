<?php
require_once 'includes/db.php';
// Ensure user is logged in before accessing account page
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$message = '';

// Handle form submission for updating user profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update username if provided
    if (!empty($_POST['username'])) {
        $_SESSION['user']['username'] = $_POST['username'];
    }
    // Update email if provided
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
    <!-- Display success or informational message (if any) -->
    <?php if ($message): ?>
        <div class="account-message" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- User Profile Information Section -->
    <div class="account-section">
        <h2>My Profile</h2>
        <p><strong>Username: </strong><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></p>
        <p><strong>Email: </strong><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
        <p><strong>Role: </strong><?php echo htmlspecialchars($user['role'] ?? 'user'); ?></p>
    </div>

    <!-- Edit Profile Section -->
    <div class="account-section">
        <h2>Edit Profile</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter new username">
            <input type="email" name="email" placeholder="Enter new email">
            <button type="submit">Update Profile</button>
        </form>
    </div>

    <!-- Account Actions Section -->
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
