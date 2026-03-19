<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Online Bookstore</title>

        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>

    <div class="navbar">
        <div class="nav-left">
            <form action="books.php" method="GET">
                <input type="text" name="search" placeholder="Search books...">
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="nav-right">
            <a href="index.php">Home</a>
            <a href="books.php">Books</a>
            <a href="cart.php">Cart</a>
            <a href="login.php">Login</a>
        </div>
    </div>
</html>