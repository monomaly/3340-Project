<?php
// Include the database connection file
require_once 'includes/db.php';
// initialize status messages to display to the user
$error = '';
$success = '';

// Only run the logic if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// Sanitize and collect input
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($username === '' || $email === '' || $password === '' || $confirm === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Built-in PHP filter to ensure the email format is actually valid
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if username or email already taken
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            $error = 'Username or email is already taken.';
        } else {
            // Hash the password using BCRYPT
            $hash = password_hash($password, PASSWORD_BCRYPT);
            // Insert the new user into the database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :hash, 'user')");
            $stmt->execute(['username' => $username, 'email' => $email, 'hash' => $hash]);

            // Log them in immediately
            $_SESSION['user'] = [
                'id'       => $pdo->lastInsertId(),
                'username' => $username,
                'email'    => $email,
                'role'     => 'user'
            ];
            // Redirect to home page and stop further script execution
            header('Location: index.php');
            exit();
        }
    }
}
// Include the site header/navigation
include 'includes/header.php';
?>
<div class="login-page">
    <div class="login-box">
        <h2>Sign Up</h2>
        <?php if ($error): ?>
            <p class="login-error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-full">Create Account</button>
        </form>
        <p class="login-footer">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>