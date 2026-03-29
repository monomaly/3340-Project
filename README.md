# 3340-Project
COMP 3340 Website group project
3340-Project

## Information

### startup
The site can be ran locally with the run_web script which works on mac and linux


### info 
Dynamic forms: 
1. Log in and out
2. calculate sum total on checkout
etc. their is others

IOS Requirement:
- responsiveness under "Ipad Mini"

Wiki
 - Wiki articles are spread throughout guide html pages for local use, wiki dropdown and online usage guides.

Live Url is :https://mkjm.myweb.cs.uwindsor.ca/index.php


# MKJM Bookstore — Project Structure

```
3340-Project/
│
├── index.php                  # Home page with carousel and about section
├── search.php                 # Search and browse all books
├── book.php                   # Individual book detail page
├── cart.php                   # Shopping cart
├── add_to_cart.php            # Handles adding items to cart
├── checkout.php               # Order summary with tax and shipping
├── process_order.php          # Handles order submission and saves to DB
├── order_confirm.php          # Order confirmation page
├── account.php                # User profile page
├── login.php                  # Login page
├── logout.php                 # Destroys session and redirects to login
├── signup.php                 # New user registration
├── admin.php                  # Admin dashboard with stats and guide videos
├── admin_orders.php           # View and update order statuses
├── manage_book.php            # Add / edit / delete books
├── manage_users.php           # View and disable user accounts
├── monitor.php                # Site status monitoring page
├── help.php                   # User help page
├── script.js                  # Main JavaScript file
├── style.css                  # Main stylesheet
├── README.md                  # This file
│
├── docs/
│   ├── admin-guide.html       # Admin documentation
│   └── user-guide.html        # End user documentation
│
├── includes/
│   ├── db.php                 # PDO database connection + session start
│   ├── header.php             # Navbar, search bar, session handling
│   └── footer.php             # Footer HTML
│
├── WikiPages/
│   ├── readguide.php          # Reading guide wiki page
│   ├── authors.php            # Famous authors wiki page
│   ├── genres.php             # Genres wiki page
│   ├── bookrec.php            # Book recommendations wiki page
│   ├── booktomovie.php        # Book-to-movie adaptations wiki page
│   ├── help.php               # Wiki help page
│   └── loutorin.php           # Logout redirect page
│
├── images/
│   └── book_images/           # Book cover images (.avif files)
│
├── videos/
│   ├── admin-guide1.webm      # Admin guide video 1 — managing books
│   ├── admin-guide2.webm      # Admin guide video 2 — orders and users
│   └── user-guide.webm        # End user guide video
│
├── shopping/                  # Shopping related files
│
└── sql/
    └── schema.sql             # Full database schema with seed data
```

## Database Tables

| Table | Description |
|-------|-------------|
| `books` | All book listings with title, author, price, stock, rating, cover image |
| `users` | Registered user accounts with roles (admin / user) |
| `cart` | Active cart items linked to users, includes format selection |
| `orders` | Completed orders with status, total, and shipping address |
| `order_items` | Individual books within each order, stores price at time of purchase |

## Default Login Credentials

| Username | Password | Role |
|----------|----------|------|
| admin | password | Admin |
| john | password | User |