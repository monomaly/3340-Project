<?php
require_once 'includes/db.php';
include 'includes/header.php';


$search = trim($_GET['search'] ?? '');
$books = [];

if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM books 
        WHERE title LIKE :title 
           OR author LIKE :author
        ORDER BY title ASC
    ");
    $stmt->execute([
        'title'  => '%' . $search . '%',
        'author' => '%' . $search . '%'
    ]);
    $books = $stmt->fetchAll();
}
?>

<div class="search-page">
    <link rel="stylesheet" href="style.css"> 

    <h2>
        <?php if ($search !== ''): ?>
            Search results for: <em><?php echo htmlspecialchars($search); ?></em>
        <?php else: ?>
            All Books
        <?php endif; ?>
    </h2>

    <?php if (empty($books)): ?>
        <p>No books found for "<?php echo htmlspecialchars($search); ?>". Try a different search.</p>
    <?php else: ?>
        <p><?php echo count($books); ?> book(s) found</p>
        <div class="book-grid">
            <?php foreach ($books as $book): ?>
                <div class="book-card">
                     <img 
        src="images/book_images/<?php echo $book['cover_image']; ?>"
        alt="<?php echo htmlspecialchars($book['title']); ?>"
        style="width:100%; height:200px; object-fit:cover;"
    >
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="author">by <?php echo htmlspecialchars($book['author']); ?></p>
                    <p class="price">$<?php echo number_format($book['price'], 2); ?></p>
                    <p class="rating">Rating: <?php echo $book['rating']; ?>/5</p>
                    <p class="stock">
                        <?php echo $book['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                    </p>
                    <a href="/book.php?id=<?php echo $book['id']; ?>" class="btn">View Book</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>