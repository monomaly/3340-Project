<?php
require_once 'includes/db.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header('Location: search.php');
    exit();
}

// Get book
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    echo "<p>Book not found.</p>";
    include 'includes/footer.php';
    exit();
}

// Handle add to cart
$cart_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }

    $user_id  = $_SESSION['user']['id'];
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    $format   = isset($_POST['format']) && $_POST['format'] === 'hardcover' ? 'hardcover' : 'paperback';

    $stmt = $pdo->prepare("
        INSERT INTO cart (user_id, book_id, quantity, format)
        VALUES (:user_id, :book_id, :quantity, :format)
        ON DUPLICATE KEY UPDATE quantity = quantity + :quantity2
    ");
    $stmt->execute([
        'user_id'   => $user_id,
        'book_id'   => $id,
        'quantity'  => $quantity,
        'quantity2' => $quantity,
        'format'    => $format
    ]);

    $cart_message = 'Added to cart!';
}
?>

<div class="book-page">

    <div class="book-detail">

        <!-- Cover Image -->
        <div class="book-cover">
            <?php if (!empty($book['cover_image'])): ?>
                <img
                    src="images/book_images/<?php echo htmlspecialchars($book['cover_image']); ?>"
                    alt="<?php echo htmlspecialchars($book['title']); ?>"
                >
            <?php else: ?>
                <div class="no-cover">No Image</div>
            <?php endif; ?>
        </div>

        <!-- Book Info -->
        <div class="book-info">
            <h1><?php echo htmlspecialchars($book['title']); ?></h1>
            <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
            <p class="book-rating">Rating: <?php echo $book['rating']; ?>/5</p>
            <p class="book-price">$<?php echo number_format($book['price'], 2); ?></p>
            <p class="book-stock">
                <?php if ($book['stock'] > 0): ?>
                    <span class="in-stock">In Stock (<?php echo $book['stock']; ?> left)</span>
                <?php else: ?>
                    <span class="out-of-stock">Out of Stock</span>
                <?php endif; ?>
            </p>

            <?php if ($cart_message): ?>
                <p class="cart-message"><?php echo $cart_message; ?></p>
            <?php endif; ?>

            <?php if ($book['stock'] > 0): ?>
                <form method="POST">
                    <div class="format-selector">
                        <label>Format:</label>
                        <select name="format">
                            <option value="paperback">Paperback</option>
                            <option value="hardcover">Hardcover</option>
                        </select>
                    </div>
                    <div class="quantity-selector">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $book['stock']; ?>">
                    </div>
                    <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
                </form>
            <?php endif; ?>

            <a href="search.php" class="btn btn-secondary">Back to Books</a>
        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>