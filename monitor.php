<?php
require_once 'includes/db.php';
include 'includes/header.php';

//fake admin login for testing
$_SESSION['user'] = [
    'id'       => 1,
    'username' => 'Admin',
    'role'     => 'admin'
];

// redirect if not admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// initialize status array
$status = [
    'overall' => 'unknown',
    'services' => []
];

//check database connection
function checkDatabase() {
    global $pdo;
    try {
        $pdo->query("SELECT 1");
        return ['status' => 'online', 'message' => 'Database connection successful'];
    } catch (Exception $e) {
        return ['status' => 'offline', 'message' => 'Database connection failed: ' . $e->getMessage()];
    }
}

//check database table
function checkDatabaseTables() {
    global $pdo;
    $tables = ['books', 'users', 'cart'];
    $missing = [];

    try {
        foreach ($tables as $table) {
            $result = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($result->rowCount() === 0) {
                $missing[] = $table;
            }
        }

        if (empty($missing)) {
            return ['status' => 'online', 'message' => 'All required tables exist'];
        } else {
            return ['status' => 'warning', 'message' => 'Missing tables: ' . implode(', ', $missing)];
        }
    } catch (Exception $e) {
        return ['status' => 'offline', 'message' => 'Table check failed: ' . $e->getMessage()];
    }
}

//check file system access
function checkFileSystem() {
    $paths = [
        'includes/',
        'images/',
        'images/book_images/',
        'style.css',
        'index.php'
    ];

    $inaccessible = [];
    foreach ($paths as $path) {
        if (!file_exists($path)) {
            $inaccessible[] = $path;
        }
    }

    if (empty($inaccessible)) {
        // Test write access
        $testFile = 'includes/test_write.tmp';
        if (@file_put_contents($testFile, 'test') !== false) {
            unlink($testFile);
            return ['status' => 'online', 'message' => 'File system accessible and writable'];
        } else {
            return ['status' => 'warning', 'message' => 'File system readable but not writable'];
        }
    } else {
        return ['status' => 'offline', 'message' => 'Missing files/directories: ' . implode(', ', $inaccessible)];
    }
}

//check key pages
function checkKeyPages() {
    $pages = [
        'index.php' => 'Home Page',
        'search.php' => 'Search Page',
        'book.php' => 'Book Details Page',
        'login.php' => 'Login Page'
    ];

    $inaccessible = [];
    foreach ($pages as $file => $name) {
        if (!file_exists($file)) {
            $inaccessible[] = $name;
        }
    }

    if (empty($inaccessible)) {
        return ['status' => 'online', 'message' => 'All key pages accessible'];
    } else {
        return ['status' => 'offline', 'message' => 'Missing pages: ' . implode(', ', $inaccessible)];
    }
}

//check session functionality
function checkSessions() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return ['status' => 'online', 'message' => 'Session system working'];
    } else {
        return ['status' => 'offline', 'message' => 'Session system not active'];
    }
}

//check image access
function checkImages() {
    $testImages = [
        'images/book_images/harry_potter.avif',
        'images/book_images/dune.avif'
    ];

    $missing = [];
    foreach ($testImages as $image) {
        if (!file_exists($image)) {
            $missing[] = basename($image);
        }
    }

    if (empty($missing)) {
        return ['status' => 'online', 'message' => 'Sample images accessible'];
    } else {
        return ['status' => 'warning', 'message' => 'Missing images: ' . implode(', ', $missing)];
    }
}

//check database data
function checkDatabaseData() {
    global $pdo;
    try {
        $bookCount = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
        $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

        if ($bookCount > 0 && $userCount > 0) {
            return ['status' => 'online', 'message' => "Database populated: {$bookCount} books, {$userCount} users"];
        } elseif ($bookCount > 0) {
            return ['status' => 'warning', 'message' => "Books found ({$bookCount}) but no users"];
        } elseif ($userCount > 0) {
            return ['status' => 'warning', 'message' => "Users found ({$userCount}) but no books"];
        } else {
            return ['status' => 'warning', 'message' => 'Database is empty'];
        }
    } catch (Exception $e) {
        return ['status' => 'offline', 'message' => 'Data check failed: ' . $e->getMessage()];
    }
}

// Run all checks
$status['services'] = [
    'Database Connection' => checkDatabase(),
    'Database Tables' => checkDatabaseTables(),
    'Database Data' => checkDatabaseData(),
    'File System' => checkFileSystem(),
    'Key Pages' => checkKeyPages(),
    'Session System' => checkSessions(),
    'Image Access' => checkImages()
];

// Determine overall status
$hasOffline = false;
$hasWarning = false;

foreach ($status['services'] as $service) {
    if ($service['status'] === 'offline') {
        $hasOffline = true;
    } elseif ($service['status'] === 'warning') {
        $hasWarning = true;
    }
}

if ($hasOffline) {
    $status['overall'] = 'offline';
} elseif ($hasWarning) {
    $status['overall'] = 'warning';
} else {
    $status['overall'] = 'online';
}

// Status colors
$statusColors = [
    'online' => '#27ae60',
    'warning' => '#f39c12',
    'offline' => '#e74c3c',
    'unknown' => '#95a5a6'
];

$statusText = [
    'online' => 'ONLINE',
    'warning' => 'WARNING',
    'offline' => 'OFFLINE',
    'unknown' => 'UNKNOWN'
];
?>

<link rel="stylesheet" href="style.css">

<div class="admin-page">
    <h2>Site Monitor</h2>
    <a href="admin.php" class="btn btn-secondary">Back to Dashboard</a>

    <!-- Overall Status -->
    <div class="status-overview" style="text-align: center; margin: 20px 0; padding: 20px; border-radius: 10px; background: <?php echo $statusColors[$status['overall']]; ?>; color: white;">
        <h3 style="margin: 0;">Overall Status: <?php echo $statusText[$status['overall']]; ?></h3>
        <p style="margin: 10px 0 0 0;">
            <?php
            if ($status['overall'] === 'online') {
                echo 'All systems operational';
            } elseif ($status['overall'] === 'warning') {
                echo 'Some services have warnings';
            } else {
                echo 'Critical systems offline';
            }
            ?>
        </p>
    </div>

    <!-- Service Status Grid -->
    <div class="status-grid">
        <?php foreach ($status['services'] as $serviceName => $service): ?>
            <div class="status-card" style="border-left: 5px solid <?php echo $statusColors[$service['status']]; ?>; margin-bottom: 15px; padding: 15px; background: #f9f9f9; border-radius: 5px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <h4 style="margin: 0;"><?php echo $serviceName; ?></h4>
                    <span style="background: <?php echo $statusColors[$service['status']]; ?>; color: white; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold;">
                        <?php echo $statusText[$service['status']]; ?>
                    </span>
                </div>
                <p style="margin: 0; color: #666;"><?php echo $service['message']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- System Information -->
    <div class="system-info" style="margin-top: 30px; padding: 20px; background: #f5f5f5; border-radius: 10px;">
        <h3>System Information</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div>
                <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?>
            </div>
            <div>
                <strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
            </div>
            <div>
                <strong>Database:</strong> MySQL
            </div>
            <div>
                <strong>Last Checked:</strong> <?php echo date('Y-m-d H:i:s'); ?>
            </div>
        </div>
    </div>

    <!-- Refresh Button -->
    <div style="text-align: center; margin-top: 20px;">
        <button onclick="location.reload()" class="btn">Refresh Status</button>
    </div>
</div>

<style>
.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

@media (max-width: 768px) {
    .status-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
