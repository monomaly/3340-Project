<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MKJM Bookstore</title>

        <link rel="stylesheet" href="/style.css">
    </head>
    <body>
    <!-- Navigation -->
    <div class="navbar">
        <div class="nav-left">
            <form action="search.php" method="GET">
                <input type="text" name="search" placeholder="Search books...">
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="nav-right">
            <a href="/WWW/index.php">Home</a>
            <!-- Wiki pages -->
            <div class="dropdown">
                <button class="dropbtn">Wiki</button>
                <div class="dropdown-content">
                    <a href="/WikiPages/readguide.php">Read Guide</a>
                    <a href="/WikiPages/authors.php">Famous Authors</a>
                    <a href="/WikiPages/genres.php">Genres</a>
                    <a href="/WikiPages/bookrec.php">Book Recommendations</a>
                    <a href="/WikiPages/booktomovie.php">Book-to-Movie Adaptations</a>
                </div>
            </div>
            <a href="/cart.php">Cart</a>
            <div class="dropdown">
                <button class="dropbtn">
                    <?php 
                    if (isset($_SESSION['user'])) {
                        echo $_SESSION['user']['name']; 
                    } else {
                        echo "Account";
                    }
                    ?>
                </button>

                <div class="dropdown-content">
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="/account.php">Profile</a>
                        <a href="/orders.php">Orders</a>
                        <a href="/logout.php">Logout</a>
                    <?php else: ?>
                        <a href="/login.php">Login</a>
                        <a href="/signup.php">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="header-box">
        <h1>The MKJM Bookstore</h1>
    </div>
<div>
    <?php
    $cart_count = 0;
    if (isset($_SESSION['user'])) {
        $count_stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
        $count_stmt->execute([$_SESSION['user']['id']]);
        $cart_count = $count_stmt->fetchColumn() ?: 0;
    }
?>
</div>
<a href="/cart.php">Cart (<strong><?php echo $cart_count; ?></strong>)</a>
</html>