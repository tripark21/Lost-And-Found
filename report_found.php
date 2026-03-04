<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Found Item - Campus Lost & Found</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo"> Campus Lost & Found</h1>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="lost_items.php">Lost Items</a></li>
                <li><a href="found_items.php">Found Items</a></li>
                <li><a href="report_lost.php">Report Lost</a></li>
                <li><a href="report_found.php" class="active">Report Found</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Report Found Item</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <form method="POST" action="handlers/process_report_firebase.php" class="report-form">
                <input type="hidden" name="item_type" value="found">

                <div class="form-group">
                    <label for="item_name">Item Name *</label>
                    <input type="text" id="item_name" name="item_name" required placeholder="e.g., Red Wallet, Samsung Phone">
                </div>

                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select a category</option>
                        <option value="Bags & Backpacks">Bags & Backpacks</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Jewelry">Jewelry</option>
                        <option value="Keys">Keys</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Documents">Documents</option>
                        <option value="Wallets">Wallets</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="5" required placeholder="Describe the item in detail..."></textarea>
                </div>

                <div class="form-group">
                    <label for="date_reported">Date Found *</label>
                    <input type="date" id="date_reported" name="date_reported" required>
                </div>

                <div class="form-group">
                    <label for="location">Location Found *</label>
                    <input type="text" id="location" name="location" required placeholder="e.g., Near Library, Lost & Found Office">
                </div>

                <div class="form-group">
                    <label for="contact_name">Your Name *</label>
                    <input type="text" id="contact_name" name="contact_name" required placeholder="Full name">
                </div>

                <div class="form-group">
                    <label for="contact_email">Email *</label>
                    <input type="email" id="contact_email" name="contact_email" required placeholder="your.email@college.edu">
                </div>

                <div class="form-group">
                    <label for="contact_phone">Phone Number</label>
                    <input type="tel" id="contact_phone" name="contact_phone" placeholder="Optional">
                </div>

                <button type="submit" class="btn btn-primary">Report Found Item</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 College Lost & Found System. Help us keep our campus community connected.</p>
    </footer>
</body>
</html>
