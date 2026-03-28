<?php
require_once 'includes/db.php';
session_start();

// FAKE LOGIN (for testing)
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'name' => 'Maria',
        'email' => 'maria@example.com'
    ];
}
$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['name'])) {
        $_SESSION['user']['name'] = $_POST['name'];
    }

    if (!empty($_POST['email'])) {
        $_SESSION['user']['email'] = $_POST['email'];
    }

    header("Location: account.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="style.css">

<div class="account-container">
    <div class="account-section">
        <h2>My Profile</h2>
        <p><strong>Name: </strong><?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></p>
        <p><strong>Email: </strong><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
    </div>

    <div class="account-section">
        <h2>Edit Profile</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Enter new name">
            <input type="email" name="email" placeholder="Enter new email">
            <button type="submit">Update Profile</button>
        </form>
    </div>

    <div class="account-section">
        <h2>Order History</h2>
    </div>

    <div class="account-section">
        <h2>Account Actions</h2>
        <a href="cart.php" class="account-btn">View Cart</a>
        <a href="logout.php" class="account-btn logout">Logout</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>