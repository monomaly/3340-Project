<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Fetch 10 random books for the carousel
$stmt = $pdo->prepare("SELECT id, title, cover_image FROM books ORDER BY RAND() LIMIT 10");
$stmt->execute();
$featured_books = $stmt->fetchAll();
?>

<link rel="stylesheet" href="style.css">
<script src="/js/script.js"></script>

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

<!-- Beginner Guide Video -->
<div class="guide-section">
    <h2>New Here? Watch Our Guide</h2>
    <p>Not sure where to start? This short video walks you through how to browse books, add them to your cart, and place an order.</p>
    <video controls width="100%" style="max-width:800px; border-radius:8px; display:block; margin:0 auto;">
        <source src="videos/user-guide.webm" type="video/webm">
        Your browser does not support the video tag.
    </video>
</div>

<!-- About Us Section -->
<div class="about">
    <h2>About Us</h2>
    <p>We are an online bookstore dedicated to connecting readers with their next great adventure. From fantasy and mystery to romance and science fiction, our handpicked selection has something for every mood and every reader.</p>
<p>We believe in the timeless joy of reading physical books. Whether you're a lifelong bookworm or just getting started, our team is here to help you find your next favourite from the latest bestseller to a hidden gem you never knew you needed.</p>
<p>We are committed to excellent customer service, competitive prices, and building a welcoming community for readers of all backgrounds. Thank you for being part of ours happy reading!</p>
</div>

<!-- Find Us on Google Maps -->
<div class="map-container">
    <h2>Find Us</h2>
    <iframe
        src="https://www.google.com/maps?q=Windsor,Ontario&output=embed"
        width="100%"
        height="400"
        style="border:0"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</div>

<?php include 'includes/footer.php'; ?>