CREATE DATABASE IF NOT EXISTS bookstore;
USE bookstore;

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(150) NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    stock INT DEFAULT 0,
    rating TINYINT DEFAULT 0 CHECK (rating BETWEEN 0 AND 5)
);

INSERT INTO books (title, author, price, stock, rating) VALUES
('Harry Potter and the Philosopher''s Stone', 'J.K. Rowling',         12.99, 50, 5),
('The Lord of the Rings',                     'J.R.R. Tolkien',       24.99, 30, 5),
('The Hobbit',                                'J.R.R. Tolkien',       14.99, 35, 5),
('Percy Jackson and the Lightning Thief',     'Rick Riordan',         13.99, 40, 5),
('Eragon',                                    'Christopher Paolini',  14.99, 28, 4),
('Sherlock Holmes: The Complete Novels',      'Arthur Conan Doyle',   17.99, 25, 5),
('Murder on the Orient Express',              'Agatha Christie',      12.99, 32, 5),
('Gone Girl',                                 'Gillian Flynn',        14.99, 30, 4),
('Pride and Prejudice',                       'Jane Austen',          10.99, 45, 5),
('Me Before You',                             'Jojo Moyes',           13.99, 38, 5),
('The Notebook',                              'Nicholas Sparks',      12.99, 35, 4),
('Dune',                                      'Frank Herbert',        17.99, 40, 5),
('The Martian',                               'Andy Weir',            15.99, 33, 5),
('Ender''s Game',                             'Orson Scott Card',     14.99, 36, 5),
('IT',                                        'Stephen King',         19.99, 28, 4),
('The Shining',                               'Stephen King',         15.99, 30, 5),
('Dracula',                                   'Bram Stoker',          10.99, 27, 4),
('The Hunger Games',                          'Suzanne Collins',      12.99, 50, 5),
('The Book Thief',                            'Markus Zusak',         14.99, 34, 5),
('The Alchemist',                             'Paulo Coelho',         13.99, 42, 5),
('The Chronicles of Narnia',                  'C.S. Lewis',           18.99, 38, 5);