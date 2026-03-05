<?php
session_start();
include __DIR__ . '/config/postgres_config.php';

try {
    $s = $conn->query("
        SELECT
            COUNT(CASE WHEN item_type='lost'  AND status='unresolved' THEN 1 END) AS lost,
            COUNT(CASE WHEN item_type='found' AND status='unresolved' THEN 1 END) AS found,
            COUNT(CASE WHEN status IN ('claimed','returned')          THEN 1 END) AS resolved
        FROM items
    ")->fetch();
} catch (PDOException $e) {
    $s = ['lost' => 0, 'found' => 0, 'resolved' => 0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Campus Lost & Found — reuniting lost items with their owners.">
    <title>Campus Lost &amp; Found</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="index_postgres.php" class="logo">
            <div class="logo-mark">🎓</div>
            <span>Campus Lost &amp; Found</span>
        </a>
        <button class="nav-toggle" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
        <ul class="nav-menu">
            <li><a href="index_postgres.php" class="active">Home</a></li>
            <li><a href="pages/lost_items_postgres.php">Lost Items</a></li>
            <li><a href="pages/found_items_postgres.php">Found Items</a></li>
            <li><a href="report_lost_postgres.php">Report Lost</a></li>
            <li><a href="report_found_postgres.php">Report Found</a></li>
            <li class="nav-admin"><a href="admin/admin_panel.php">Admin</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><span class="alert-icon">✅</span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error"><span class="alert-icon">⚠️</span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <section class="hero">
        <div class="hero-eyebrow">🎓 Campus Community</div>
        <h1>Lost something?<br><em>We'll help you find it.</em></h1>
        <p>Our campus lost &amp; found system connects students and staff — helping reunite lost items with their rightful owners.</p>
        <div class="hero-cta">
            <a href="pages/lost_items_postgres.php" class="btn btn-accent btn-lg">Browse Lost Items</a>
            <a href="report_lost_postgres.php" class="btn btn-surface btn-lg">Report an Item</a>
        </div>
    </section>

    <div class="stats-row">
        <div class="stat-tile">
            <div class="stat-tile-icon lost">🔍</div>
            <div>
                <div class="stat-tile-num"><?php echo (int)$s['lost']; ?></div>
                <div class="stat-tile-label">Active Lost Reports</div>
            </div>
        </div>
        <div class="stat-tile">
            <div class="stat-tile-icon found">📦</div>
            <div>
                <div class="stat-tile-num"><?php echo (int)$s['found']; ?></div>
                <div class="stat-tile-label">Items Found &amp; Waiting</div>
            </div>
        </div>
        <div class="stat-tile">
            <div class="stat-tile-icon resolved">🤝</div>
            <div>
                <div class="stat-tile-num"><?php echo (int)$s['resolved']; ?></div>
                <div class="stat-tile-label">Items Reunited</div>
            </div>
        </div>
    </div>

    <div class="cards-grid">
        <div class="action-card">
            <div class="action-card-icon red">🔍</div>
            <h3>Browse Lost Items</h3>
            <p>Search items reported as lost. Filter by category, location, or date.</p>
            <a href="pages/lost_items_postgres.php" class="card-link">View Lost Items →</a>
        </div>
        <div class="action-card">
            <div class="action-card-icon green">📦</div>
            <h3>Browse Found Items</h3>
            <p>Check items found and turned in. Maybe yours is already here waiting.</p>
            <a href="pages/found_items_postgres.php" class="card-link">View Found Items →</a>
        </div>
        <div class="action-card">
            <div class="action-card-icon blue">📢</div>
            <h3>Report Lost Item</h3>
            <p>Lost something? File a report with a photo or video to help others identify it.</p>
            <a href="report_lost_postgres.php" class="card-link">Report Lost →</a>
        </div>
        <div class="action-card">
            <div class="action-card-icon violet">💝</div>
            <h3>Report Found Item</h3>
            <p>Found something on campus? Help the owner by submitting a report.</p>
            <a href="report_found_postgres.php" class="card-link">Report Found →</a>
        </div>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Campus Lost &amp; Found — Built for our campus community.</p>
</footer>
<script src="js/nav.js"></script>
</body>
</html>
<?php $conn = null; ?>