<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Fake admin login for testing
$_SESSION['user'] = [
    'id'       => 1,
    'username' => 'Admin',
    'role'     => 'admin'
];

// Redirect if not admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$message = '';

// Available templates
$templates = [
    'default' => [
        'name' => 'Default Theme',
        'description' => 'The original website theme with blue accents',
        'preview' => 'default-preview.jpg'
    ],
    'dark' => [
        'name' => 'Dark Theme',
        'description' => 'Modern dark theme for better readability',
        'preview' => 'dark-preview.jpg'
    ],
    'minimal' => [
        'name' => 'Minimal Theme',
        'description' => 'Clean and minimal design with subtle colors',
        'preview' => 'minimal-preview.jpg'
    ]
];

// Handle template switching
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['template'])) {
    $selected_template = $_POST['template'];

    if (array_key_exists($selected_template, $templates)) {
        // In a real application, you might save this to database or config file
        // For now, we'll just set it in session
        $_SESSION['site_template'] = $selected_template;
        $message = 'Template switched to: ' . $templates[$selected_template]['name'];
    } else {
        $message = 'Invalid template selected.';
    }
}

// Get current template (default to 'default' if not set)
$current_template = $_SESSION['site_template'] ?? 'default';
?>

<link rel="stylesheet" href="style.css">

<div class="admin-page">
    <h2>Template Manager</h2>
    <a href="admin.php" class="btn btn-secondary">Back to Dashboard</a>

    <?php if ($message): ?>
        <p class="admin-message"><?php echo $message; ?></p>
    <?php endif; ?>

    <div class="template-info">
        <h3>Current Template: <?php echo $templates[$current_template]['name']; ?></h3>
        <p><?php echo $templates[$current_template]['description']; ?></p>
    </div>

    <!-- Template Selection -->
    <div class="template-grid">
        <h3>Available Templates</h3>

        <?php foreach ($templates as $key => $template): ?>
            <div class="template-card <?php echo ($key === $current_template) ? 'active' : ''; ?>">
                <div class="template-header">
                    <h4><?php echo $template['name']; ?></h4>
                    <?php if ($key === $current_template): ?>
                        <span class="current-badge">Current</span>
                    <?php endif; ?>
                </div>

                <div class="template-description">
                    <p><?php echo $template['description']; ?></p>
                </div>

                <div class="template-preview">
                    <!-- Placeholder for template preview image -->
                    <div class="preview-placeholder">
                        <span>Preview</span>
                    </div>
                </div>

                <div class="template-actions">
                    <?php if ($key !== $current_template): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="template" value="<?php echo $key; ?>">
                            <button type="submit" class="btn">Switch to This Template</button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-disabled" disabled>Current Template</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Template Settings -->
    <div class="template-settings">
        <h3>Template Settings</h3>
        <p>Additional customization options will be available here in future updates.</p>
        <ul>
            <li>Color scheme customization</li>
            <li>Font selection</li>
            <li>Layout options</li>
            <li>Custom CSS injection</li>
        </ul>
    </div>
</div>

<style>
.template-info {
    background: #f0f8ff;
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
    border-left: 5px solid #007bff;
}

.template-grid {
    margin-top: 30px;
}

.template-grid h3 {
    margin-bottom: 20px;
}

.template-card {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    transition: border-color 0.3s;
}

.template-card.active {
    border-color: #007bff;
    background: #f8f9ff;
}

.template-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.template-header h4 {
    margin: 0;
    color: #333;
}

.current-badge {
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.template-description {
    margin-bottom: 15px;
}

.template-description p {
    margin: 0;
    color: #666;
}

.template-preview {
    margin-bottom: 15px;
}

.preview-placeholder {
    background: #f5f5f5;
    border: 1px dashed #ccc;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    color: #999;
    font-weight: bold;
}

.template-actions {
    text-align: center;
}

.btn-disabled {
    background: #ccc !important;
    cursor: not-allowed !important;
}

.template-settings {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    margin-top: 30px;
}

.template-settings ul {
    margin: 15px 0 0 20px;
}

.template-settings li {
    margin-bottom: 5px;
    color: #666;
}
</style>

<?php include 'includes/footer.php'; ?>