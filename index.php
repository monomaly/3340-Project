
<link rel="stylesheet" href="style.css"> 

    <script src="/js/script.js"></script>

<?php
require_once 'includes/db.php';
include 'includes/header.php';
// Fetch 10 random books for the carousel
$stmt = $pdo->prepare("SELECT id, title, cover_image FROM books ORDER BY RAND() LIMIT 10");
$stmt->execute();
$featured_books = $stmt->fetchAll();
?>

    <!-- Featured Books Carousel -->
    <div class="book-carousel">
        <h2>Featured Books</h2>
        <div class="carousel-container">
            <div class="carousel-slide">
                <?php foreach ($featured_books as $book): ?>
                    <a href="book.php?id=<?= $book['id'] ?>" class="book-link">
                        <img src="images/book_images/<?= $book['cover_image'] ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                    </a>
                <?php endforeach; ?>
                <!-- Duplicate for seamless loop -->
                <?php foreach ($featured_books as $book): ?>
                    <a href="book.php?id=<?= $book['id'] ?>" class="book-link">
                        <img src="images/book_images/<?= $book['cover_image'] ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <!-- About Us Section -->
    <div class="about">
        <h2>About Us</h2>
        <p>We are an online bookstore dedicated to bringing
            you the best book series, from fantasy to mystery
            and everything in between.
        </p>
    </div>
    <!-- Find Us on the google maps -->
    <div class="map-container">
        <h2>Find Us</h2>
        <iframe 
         src="https://www.google.com/maps?q=Windsor,Ontario&output=embed"            
         width="100%"
            height="400"
            style="border:0"
            allowfullscreen=""
            loading="lazy">
        </iframe>
    </div>


<?php include 'includes/footer.php'; ?>