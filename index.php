
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
            and everything in between. In the modern age of digital media, we believe in the timeless joy of reading physical books. Our mission is to connect readers with their next great adventure, one page at a time.</p>
        <p>Whether you're a lifelong bookworm or just discovering the magic of reading, we have something for everyone. Our curated selection of books is handpicked to ensure quality and variety, so you can find the perfect book for any mood or occasion. Join us on this literary journey and discover your next favorite book today!</p>
        <p>Our team is passionate about books and dedicated to providing excellent customer service. We strive to create a welcoming and inclusive community for readers of all backgrounds. We believe that books have the power to inspire, educate, and bring people together, and we are committed to fostering a love of reading in our customers. Thank you for choosing us as your go-to online bookstore!</p>
        <p>We are proud to offer a wide selection of books at competitive prices, and we are always looking for ways to improve our service and provide the best possible experience for our customers. Whether you're looking for the latest bestseller or a hidden gem, we are here to help you find it. Thank you for being a part of our community, and happy reading!</p>
        <p>At our online bookstore, we are committed to sustainability and reducing our environmental impact.
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