<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Found Item – Campus Lost &amp; Found</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <h1 class="logo">🎓 Campus Lost &amp; Found</h1>
        <ul class="nav-menu">
            <li><a href="index_postgres.php">Home</a></li>
            <li><a href="pages/lost_items_postgres.php">Lost Items</a></li>
            <li><a href="pages/found_items_postgres.php">Found Items</a></li>
            <li><a href="report_lost_postgres.php">Report Lost</a></li>
            <li><a href="report_found_postgres.php" class="active">Report Found</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="form-container">
        <h2>Report a Found Item</h2>
        <p class="form-subtitle">Help reunite this item with its owner by providing as much detail as possible.</p>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="handlers/process_report_postgres.php" class="report-form" enctype="multipart/form-data">
            <input type="hidden" name="item_type" value="found">

            <div class="form-group">
                <label for="item_name">Item Name *</label>
                <input type="text" id="item_name" name="item_name" required
                    placeholder="e.g., Black Wallet, Student ID Card">
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <option value="">Select a category</option>
                    <option value="Bags & Backpacks">Bags &amp; Backpacks</option>
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
                <textarea id="description" name="description" rows="5" required
                    placeholder="Describe what you found — color, brand, any notable features..."></textarea>
            </div>

            <div class="form-group">
                <label for="date_reported">Date Found *</label>
                <input type="date" id="date_reported" name="date_reported" required
                    max="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="location">Location Found *</label>
                <input type="text" id="location" name="location" required
                    placeholder="e.g., Cafeteria Table 5, Near the Gym Entrance">
            </div>

            <div class="form-group">
                <label for="contact_name">Your Full Name *</label>
                <input type="text" id="contact_name" name="contact_name" required placeholder="Full name">
            </div>

            <div class="form-group">
                <label for="contact_email">Email Address *</label>
                <input type="email" id="contact_email" name="contact_email" required placeholder="you@college.edu">
            </div>

            <div class="form-group">
                <label for="contact_phone">Phone Number <span style="font-weight:400;color:var(--text-muted)">(optional)</span></label>
                <input type="tel" id="contact_phone" name="contact_phone" placeholder="e.g., 09xx-xxx-xxxx">
            </div>

            <div class="form-group">
                <label for="media">Photo or Video <span style="font-weight:400;color:var(--text-muted)">(optional)</span></label>
                <input type="file" id="media" name="media"
                    accept="image/jpeg,image/png,image/gif,video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm">
                <div class="file-info">
                    📎 Accepted: JPG, PNG, GIF | MP4, AVI, MOV, MKV, WebM — Max 50 MB<br>
                    A photo helps the owner identify their item faster.
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Submit Found Report</button>
                <a href="index_postgres.php" class="btn btn-secondary">Cancel</a>
            </div>
            <p class="required-note">* Required fields</p>
        </form>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Campus Lost &amp; Found &mdash; Helping keep our community connected.</p>
</footer>
</body>
</html>