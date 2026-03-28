<?php
require_once 'includes/db.php';
include 'includes/header.php';
 // Fake admin login for testing
$_SESSION['user'] = [
    'id'   => 1,
    'name' => 'Admin',
    'role' => 'admin'
];
// Redirect if not admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
 
$message = '';
 
// ── DELETE ────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM books WHERE id = :id")->execute(['id' => $del_id]);
    $message = 'Book deleted.';
}
 
// ── ADD / EDIT ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $author      = trim($_POST['author']);
    $price       = (float)$_POST['price'];
    $stock       = (int)$_POST['stock'];
    $rating      = (int)$_POST['rating'];
    $format      = $_POST['format'] === 'hardcover' ? 'hardcover' : 'paperback';
    $cover_image = trim($_POST['cover_image']);
    $edit_id     = (int)($_POST['edit_id'] ?? 0);
 
    if ($edit_id > 0) {
        // Update existing book
        $pdo->prepare("
            UPDATE books SET
                title        = :title,
                author       = :author,
                price        = :price,
                stock        = :stock,
                rating       = :rating,
                format       = :format,
                cover_image  = :cover_image
            WHERE id = :id
        ")->execute([
            'title'       => $title,
            'author'      => $author,
            'price'       => $price,
            'stock'       => $stock,
            'rating'      => $rating,
            'format'      => $format,
            'cover_image' => $cover_image,
            'id'          => $edit_id
        ]);
        $message = 'Book updated.';
    } else {
        // Insert new book
        $pdo->prepare("
            INSERT INTO books (title, author, price, stock, rating, format, cover_image)
            VALUES (:title, :author, :price, :stock, :rating, :format, :cover_image)
        ")->execute([
            'title'       => $title,
            'author'      => $author,
            'price'       => $price,
            'stock'       => $stock,
            'rating'      => $rating,
            'format'      => $format,
            'cover_image' => $cover_image
        ]);
        $message = 'Book added.';
    }
}
 
// ── GET BOOK FOR EDITING ──────────────────────────────────────
$edit_book = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->execute(['id' => (int)$_GET['edit']]);
    $edit_book = $stmt->fetch();
}
 
// ── GET ALL BOOKS ─────────────────────────────────────────────
$books = $pdo->query("SELECT * FROM books ORDER BY title ASC")->fetchAll();
?>
 <link rel="stylesheet" href="style.css"> 

<div class="admin-page">
    <h2>Manage Books</h2>
    <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
 
    <?php if ($message): ?>
        <p class="admin-message"><?php echo $message; ?></p>
    <?php endif; ?>
 
    <!-- ADD / EDIT FORM -->
    <div class="admin-form-box">
        <h3><?php echo $edit_book ? 'Edit Book' : 'Add New Book'; ?></h3>
        <form method="POST">
            <?php if ($edit_book): ?>
                <input type="hidden" name="edit_id" value="<?php echo $edit_book['id']; ?>">
            <?php endif; ?>
 
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" required
                    value="<?php echo htmlspecialchars($edit_book['title'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" required
                    value="<?php echo htmlspecialchars($edit_book['author'] ?? ''); ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" step="0.01" min="0" required
                        value="<?php echo $edit_book['price'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" min="0" required
                        value="<?php echo $edit_book['stock'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Rating (0-5)</label>
                    <input type="number" name="rating" min="0" max="5" required
                        value="<?php echo $edit_book['rating'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Format</label>
                    <select name="format">
                        <option value="paperback" <?php echo (!$edit_book || $edit_book['format'] === 'paperback') ? 'selected' : ''; ?>>Paperback</option>
                        <option value="hardcover" <?php echo ($edit_book && $edit_book['format'] === 'hardcover') ? 'selected' : ''; ?>>Hardcover</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Cover Image Filename</label>
                    <input type="text" name="cover_image" placeholder="e.g. dune.avif"
                        value="<?php echo htmlspecialchars($edit_book['cover_image'] ?? ''); ?>">
                </div>
            </div>
 
            <button type="submit" class="btn">
                <?php echo $edit_book ? 'Update Book' : 'Add Book'; ?>
            </button>
            <?php if ($edit_book): ?>
                <a href="books.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>
    </div>
 
    <!-- BOOKS TABLE -->
    <div class="admin-table-box">
        <h3>All Books (<?php echo count($books); ?>)</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Rating</th>
                    <th>Format</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                <tr>
                    <td><?php echo $book['id']; ?></td>
                    <td>
                        <?php if (!empty($book['cover_image'])): ?>
                            <img src="../images/book_images/<?php echo htmlspecialchars($book['cover_image']); ?>"
                                 style="width:40px; height:55px; object-fit:cover; border-radius:3px;">
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td>$<?php echo number_format($book['price'], 2); ?></td>
                    <td><?php echo $book['stock']; ?></td>
                    <td><?php echo $book['rating']; ?>/5</td>
                    <td><?php echo ucfirst($book['format']); ?></td>
                    <td>
                        <a href="books.php?edit=<?php echo $book['id']; ?>" class="btn btn-sm">Edit</a>
                        <a href="books.php?delete=<?php echo $book['id']; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this book?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>