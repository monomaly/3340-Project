<?php 

 // Provide an interactive step-by-step guide for User Login and Logout procedures.


// Include database connection to ensure session handling is consistent across the site
require_once '../includes/db.php'; 

// Include the common header for navigation and CSS template support
include '../includes/header.php'; 
?>

<div class="wiki-page">
    
    <div class="header-box">
        <h2>User Guide: Login & Logout</h2>
        <p>Follow these steps to manage your account access safely.</p>
    </div>

    <section class="wiki-section">
        <div class="wiki-info">
            <h2>How to Log In</h2>
            <ol>
                <li>Navigate to the <a href="../login.php">Login Page</a> via the <strong>Account</strong> dropdown menu.</li>
                <li>Enter your registered <strong>username</strong>.</li>
                <li>Enter your secure <strong>password</strong>.</li>
                <li>Click the <strong>Login</strong> button.</li>
            </ol>
            
            <div class="admin-message" style="background: #e1f5fe; color: #01579b; border: 1px solid #b3e5fc;">
                <strong>💡 Tip:</strong> Password fields are case-sensitive. Ensure your Caps Lock is off before typing.
            </div>
        </div>
    </section>

    <section class="wiki-section">
        <div class="wiki-info">
            <h2>Troubleshooting Access</h2>
            <ul>
                <li><strong>Incorrect Credentials:</strong> Double-check that your username matches your registration details exactly.</li>
                <li><strong>Forgotten Password:</strong> If you cannot access your account, please contact a site administrator.</li>
                <li><strong>Admin Redirection:</strong> Administrators are automatically redirected to the Admin Dashboard upon login.</li>
            </ul>
        </div>
    </section>

    <section class="wiki-section">
        <div class="wiki-info">
            <h2>How to Log Out</h2>
            <p>To protect your personal data, especially on shared devices, follow these steps:</p>
            <ol>
                <li>Hover over the <strong>Account</strong> button in the top navigation bar.</li>
                <li>Select <strong>Logout</strong> from the dropdown menu options.</li>
                <li>You will be securely logged out and redirected to the home page.</li>
            </ol>
            
            <div class="admin-message" style="background: #fff3e0; color: #e65100; border: 1px solid #ffe0b2;">
                <strong>⚠️ Security Note:</strong> Always log out when using public computers to prevent unauthorized access to your cart and profile.
            </div>
        </div>
    </section>

    <div style="text-align: center; margin-top: 20px; padding-bottom: 40px;">
        <a href="../index.php" class="btn">Return to Home</a>
        <a href="../WikiPages/readguide.php" class="btn btn-secondary">Next Guide</a>
    </div>
</div>

<?php 
include '../includes/footer.php'; 
?>