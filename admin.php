<?php
require_once 'includes/db.php';

// Fake admin login for testing
$_SESSION['user'] = [
    'id'       => 1,
    'username' => 'Admin',
    'role'     => 'admin'
];

// Auth check BEFORE any HTML output
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Stats
$total_books  = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$total_users  = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_carts  = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM cart")->fetchColumn();
$low_stock    = $pdo->query("SELECT COUNT(*) FROM books WHERE stock < 5")->fetchColumn();
$recent_books = $pdo->query("SELECT * FROM books ORDER BY id DESC LIMIT 5")->fetchAll();

include 'includes/header.php';
?>

<div class="admin-dashboard">

    <h2>Admin Dashboard</h2>
    <p class="admin-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></p>

    <!-- Stat Cards -->
    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-number"><?php echo $total_books; ?></span>
            <span class="stat-label">Total Books</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo $total_users; ?></span>
            <span class="stat-label">Registered Users</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo $total_carts; ?></span>
            <span class="stat-label">Active Carts</span>
        </div>
        <div class="stat-card <?php echo $low_stock > 0 ? 'stat-warning' : ''; ?>">
            <span class="stat-number"><?php echo $low_stock; ?></span>
            <span class="stat-label">Low Stock Books</span>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="admin-links">
        <h3>Manage</h3>
        <div class="admin-link-grid">
            <a href="manage_book.php" class="admin-link-card">
                <span>Manage Books</span>
            </a>
            <a href="manage_users.php" class="admin-link-card">
                <span>Manage Users</span>
            </a>
            <a href="admin_orders.php" class="admin-link-card">
                <span>View Orders</span>
            </a>
            <a href="monitor.php" class="admin-link-card">
                <span>Site Monitor</span>
            </a>
        </div>
    </div>

    <!-- Recent Books -->
    <div class="admin-recent">
        <h3>Recently Added Books</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_books as $book): ?>
                <tr>
                    <td><?php echo $book['id']; ?></td>
                    <td>
                        <?php if (!empty($book['cover_image'])): ?>
                            <img src="images/book_images/<?php echo htmlspecialchars($book['cover_image']); ?>"
                                 style="width:36px; height:50px; object-fit:cover; border-radius:3px;">
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td>$<?php echo number_format($book['price'], 2); ?></td>
                    <td class="<?php echo $book['stock'] < 5 ? 'low-stock' : ''; ?>">
                        <?php echo $book['stock']; ?>
                    </td>
                    <td>
                        <a href="manage_book.php?edit=<?php echo $book['id']; ?>" class="btn btn-sm">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include 'includes/footer.php'; ?>