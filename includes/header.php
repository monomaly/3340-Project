<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MKJM Bookstore</title>

        <link rel="stylesheet" href="/WWW/style.css">
    </head>
    <body>
    <!-- Navigation -->
    <div class="navbar">
        <div class="nav-left">
            <form action="books.php" method="GET">
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
                    <a href="/WWW/WikiPages/readguide.php">Read Guide</a>
                    <a href="/WWW/WikiPages/authors.php">Famous Authors</a>
                    <a href="/WWW/WikiPages/genres.php">Genres</a>
                    <a href="/WWW/WikiPages/bookrec.php">Book Recommendations</a>
                    <a href="/WWW/WikiPages/booktomovie.php">Book-to-Movie Adaptations</a>
                </div>
            </div>
            <div class="dropdown">
                <a href="/WWW/cart.php">Cart</a>
                <button class="dropbtn">Account</button>
                <div class="dropdown-content">
                    <a href="/WWW/***">Login</a>
                    <a href="/WWW/***">Sign-Up</a>
                    <a href="/WWW/***">Login</a>
                </div>
            </div>
        </div>
    </div>

    <div class="header-box">
        <h1>The MKJM Bookstore</h1>
    </div>
</html>