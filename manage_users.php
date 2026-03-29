<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Fake admin login for testing
$_SESSION['user'] = [
    'id'       => 1,
    'username' => 'Admin',
    'role'     => 'admin'
];

// Redirect if not admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$message = '';

// ── DELETE ────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    // Prevent deleting yourself
    if ($del_id !== $_SESSION['user']['id']) {
        $pdo->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $del_id]);
        $message = 'User deleted.';
    } else {
        $message = 'Cannot delete your own account.';
    }
}

// ── ADD / EDIT ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $role     = $_POST['role'] === 'admin' ? 'admin' : 'user';
    $password = trim($_POST['password'] ?? '');
    $edit_id  = (int)($_POST['edit_id'] ?? 0);

    // Basic validation
    if (empty($username) || empty($email)) {
        $message = 'Username and email are required.';
    } else {
        if ($edit_id > 0) {
            // Update existing user
            $params = [
                'username' => $username,
                'email'    => $email,
                'role'     => $role,
                'id'       => $edit_id
            ];
            $query = "
                UPDATE users SET
                    username = :username,
                    email    = :email,
                    role     = :role
                WHERE id = :id
            ";

            // Only update password if provided
            if (!empty($password)) {
                $params['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
                $query = "
                    UPDATE users SET
                        username      = :username,
                        email         = :email,
                        role          = :role,
                        password_hash = :password_hash
                    WHERE id = :id
                ";
            }

            $pdo->prepare($query)->execute($params);
            $message = 'User updated.';
        } else {
            // Insert new user
            if (empty($password)) {
                $message = 'Password is required for new users.';
            } else {
                $pdo->prepare("
                    INSERT INTO users (username, email, password_hash, role)
                    VALUES (:username, :email, :password_hash, :role)
                ")->execute([
                    'username'      => $username,
                    'email'         => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'role'          => $role
                ]);
                $message = 'User added.';
            }
        }
    }
}

// ── GET USER FOR EDITING ──────────────────────────────────────
$edit_user = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = :id");
    $stmt->execute(['id' => (int)$_GET['edit']]);
    $edit_user = $stmt->fetch();
}

// ── GET ALL USERS ─────────────────────────────────────────────
$users = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id DESC")->fetchAll();
?>

<link rel="stylesheet" href="style.css">

<div class="admin-page">
    <h2>Manage Users</h2>
    <a href="admin.php" class="btn btn-secondary">Back to Dashboard</a>

    <?php if ($message): ?>
        <p class="admin-message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- ADD / EDIT FORM -->
    <div class="admin-form-box">
        <h3><?php echo $edit_user ? 'Edit User' : 'Add New User'; ?></h3>
        <form method="POST">
            <?php if ($edit_user): ?>
                <input type="hidden" name="edit_id" value="<?php echo $edit_user['id']; ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required
                        value="<?php echo htmlspecialchars($edit_user['username'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required
                        value="<?php echo htmlspecialchars($edit_user['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role">
                        <option value="user" <?php echo (!$edit_user || $edit_user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo ($edit_user && $edit_user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Password <?php echo $edit_user ? '(leave blank to keep current)' : ''; ?></label>
                <input type="password" name="password" <?php echo $edit_user ? '' : 'required'; ?>
                    placeholder="<?php echo $edit_user ? 'Leave blank to keep current password' : 'Enter password'; ?>">
            </div>

            <button type="submit" class="btn">
                <?php echo $edit_user ? 'Update User' : 'Add User'; ?>
            </button>
            <?php if ($edit_user): ?>
                <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- USERS TABLE -->
    <div class="admin-table-box">
        <h3>All Users (<?php echo count($users); ?>)</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                    <td>
                        <a href="manage_users.php?edit=<?php echo $user['id']; ?>" class="btn btn-sm">Edit</a>
                        <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                            <a href="manage_users.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this user?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
