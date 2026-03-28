CREATE DATABASE IF NOT EXISTS store;
USE store;

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(150) NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    stock INT DEFAULT 0,
    rating TINYINT DEFAULT 0 CHECK (rating BETWEEN 0 AND 5),
    cover_image VARCHAR(255)
);
INSERT INTO books (title, author, price, stock, rating, cover_image) VALUES
('Harry Potter and the Philosopher''s Stone', 'J.K. Rowling',         12.99, 50, 5, 'harry_potter.avif'),
('The Lord of the Rings',                     'J.R.R. Tolkien',       24.99, 30, 5, 'lord_of_the_rings.avif'),
('The Hobbit',                                'J.R.R. Tolkien',       14.99, 35, 5, 'hobbit.avif'),
('Percy Jackson and the Lightning Thief',     'Rick Riordan',         13.99, 40, 5, 'percy_jackson.avif'),
('Eragon',                                    'Christopher Paolini',  14.99, 28, 4, 'eragon.avif'),
('Sherlock Holmes: The Complete Novels',      'Arthur Conan Doyle',   17.99, 25, 5, 'sherlock_holmes.avif'),
('Murder on the Orient Express',              'Agatha Christie',      12.99, 32, 5, 'orient_express.avif'),
('Gone Girl',                                 'Gillian Flynn',        14.99, 30, 4, 'gone_girl.avif'),
('Pride and Prejudice',                       'Jane Austen',          10.99, 45, 5, 'pride_and_prejudice.avif'),
('Me Before You',                             'Jojo Moyes',           13.99, 38, 5, 'me_before_you.avif'),
('The Notebook',                              'Nicholas Sparks',      12.99, 35, 4, 'notebook.avif'),
('Dune',                                      'Frank Herbert',        17.99, 40, 5, 'dune.avif'),
('The Martian',                               'Andy Weir',            15.99, 33, 5, 'martian.avif'),
('Ender''s Game',                             'Orson Scott Card',     14.99, 36, 5, 'ender_game.avif'),
('IT',                                        'Stephen King',         19.99, 28, 4, 'it.avif'),
('The Shining',                               'Stephen King',         15.99, 30, 5, 'shining.avif'),
('Dracula',                                   'Bram Stoker',          10.99, 27, 4, 'dracula.avif'),
('The Hunger Games',                          'Suzanne Collins',      12.99, 50, 5, 'hunger_games.avif'),
('The Book Thief',                            'Markus Zusak',         14.99, 34, 5, 'book_thief.avif'),
('The Alchemist',                             'Paulo Coelho',         13.99, 42, 5, 'alchemist.avif'),
('The Chronicles of Narnia',                  'C.S. Lewis',           18.99, 38, 5, 'narnia.avif');

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    role ENUM('admin', 'user') DEFAULT 'user'
);

INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@bookstore.com', 'admin'),
('john', 'john@bookstore.com', 'user');

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    UNIQUE KEY unique_cart_item (user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);