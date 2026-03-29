<?php
require_once 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username'])) {
        $_SESSION['user']['username'] = $_POST['username'];
    }
    if (!empty($_POST['email'])) {
        $_SESSION['user']['email'] = $_POST['email'];
    }
    if (isset($_POST['template'])) {
        $template = $_POST['template'];
        if (in_array($template, ['default', 'dark', 'minimal'])) {
            $_SESSION['site_template'] = $template;
            $message = 'Theme changed successfully!';
        }
    }
    header('Location: account.php');
    exit();
}

$user = $_SESSION['user'];

include 'includes/header.php';
?>

<div class="account-container">
    <?php if ($message): ?>
        <div class="account-message" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

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
        <h2>Theme Preferences</h2>
        <p>Choose your preferred website theme:</p>
        <p><strong>Current theme:</strong> <?php echo ucfirst($_SESSION['site_template'] ?? 'default'); ?></p>
        <form method="POST" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <label style="display: flex; align-items: center; gap: 5px;">
                <input type="radio" name="template" value="default" <?php echo (($_SESSION['site_template'] ?? 'default') === 'default') ? 'checked' : ''; ?>>
                Default Theme
            </label>
            <label style="display: flex; align-items: center; gap: 5px;">
                <input type="radio" name="template" value="dark" <?php echo (($_SESSION['site_template'] ?? 'default') === 'dark') ? 'checked' : ''; ?>>
                Dark Theme
            </label>
            <label style="display: flex; align-items: center; gap: 5px;">
                <input type="radio" name="template" value="minimal" <?php echo (($_SESSION['site_template'] ?? 'default') === 'minimal') ? 'checked' : ''; ?>>
                Minimal Theme
            </label>
            <button type="submit" style="margin-left: 10px;">Apply Theme</button>
        </form>
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