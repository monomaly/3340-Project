<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Detect if running locally ---
$isLocal = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1');

if ($isLocal) {
    // Local credentials
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'store'); // your local DB
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // Remote university credentials
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'gunerk_store');
    define('DB_USER', 'gunerk_store');
    define('DB_PASS', 'zAk7fN3AG53VmxJX5s6v');
}

define('DB_CHARSET', 'utf8mb4');

// --- Connect to database ---
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}