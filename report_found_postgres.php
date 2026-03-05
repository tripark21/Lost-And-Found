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
        <a href="index_postgres.php" class="logo"><div class="logo-mark">🎓</div><span>Campus Lost &amp; Found</span></a>
        <button class="nav-toggle" aria-label="Toggle menu"><span></span><span></span><span></span></button>
        <ul class="nav-menu">
            <li><a href="index_postgres.php">Home</a></li>
            <li><a href="pages/lost_items_postgres.php">Lost Items</a></li>
            <li><a href="pages/found_items_postgres.php">Found Items</a></li>
            <li><a href="report_lost_postgres.php">Report Lost</a></li>
            <li><a href="report_found_postgres.php" class="active">Report Found</a></li>
            <li class="nav-admin"><a href="admin/admin_panel.php">Admin</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <a href="index_postgres.php" class="back-link">← Back to Home</a>

    <div class="form-wrap">
        <div class="form-shell">
            <div class="form-top" style="background:linear-gradient(135deg,#062018,#053320);">
                <h2>Report a Found Item</h2>
                <p>Help reunite this item with its owner. Provide as much detail as possible.</p>
            </div>
            <div class="form-body">
                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-error"><span class="alert-icon">⚠️</span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form method="POST" action="handlers/process_report_postgres.php" class="report-form" enctype="multipart/form-data">
                    <input type="hidden" name="item_type" value="found">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Item Name *</label>
                            <input type="text" name="item_name" required placeholder="e.g., Black Wallet, Student ID">
                        </div>
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" required>
                                <option value="">Select category</option>
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
                    </div>

                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="description" rows="4" required placeholder="Describe what you found — color, brand, any features visible…"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date Found *</label>
                            <input type="date" name="date_reported" required max="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Location Found *</label>
                            <input type="text" name="location" required placeholder="e.g., Cafeteria, Near Gym">
                        </div>
                    </div>

                    <div class="form-sep"></div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Your Full Name *</label>
                            <input type="text" name="contact_name" required placeholder="Full name">
                        </div>
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="contact_email" required placeholder="you@college.edu">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Phone Number <span style="font-weight:400;opacity:.5;text-transform:none;letter-spacing:0">(optional)</span></label>
                        <input type="tel" name="contact_phone" placeholder="09xx-xxx-xxxx">
                    </div>

                    <div class="form-group">
                        <label>Photo or Video <span style="font-weight:400;opacity:.5;text-transform:none;letter-spacing:0">(optional)</span></label>
                        <input type="file" name="media" accept="image/jpeg,image/png,image/gif,video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm">
                        <div class="form-hint">📎 JPG, PNG, GIF · MP4, AVI, MOV, MKV, WebM · Max 50 MB</div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-accent btn-lg">Submit Found Report</button>
                        <a href="index_postgres.php" class="btn btn-ghost btn-lg">Cancel</a>
                    </div>
                    <p class="req-note">* Required fields</p>
                </form>
            </div>
        </div>
    </div>
</div>

<footer><p>&copy; <?php echo date('Y'); ?> Campus Lost &amp; Found</p></footer>
<script src="js/nav.js"></script>
</body>
</html>