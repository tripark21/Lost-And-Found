<?php
session_start();
include __DIR__ . '/config/postgres_config.php';

try {
    $stmt = $conn->query("
        SELECT
            COUNT(CASE WHEN item_type='lost'  AND status='unresolved' THEN 1 END) AS lost_count,
            COUNT(CASE WHEN item_type='found' AND status='unresolved' THEN 1 END) AS found_count,
            COUNT(CASE WHEN status IN ('claimed','returned')          THEN 1 END) AS resolved_count
        FROM items
    ");
    $stats          = $stmt->fetch();
    $lost_count     = $stats['lost_count']     ?? 0;
    $found_count    = $stats['found_count']    ?? 0;
    $resolved_count = $stats['resolved_count'] ?? 0;
} catch (PDOException $e) {
    $lost_count = $found_count = $resolved_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Lost &amp; Found</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <h1 class="logo">🎓 Campus Lost &amp; Found</h1>
        <ul class="nav-menu">
            <li><a href="index_postgres.php" class="active">Home</a></li>
            <li><a href="pages/lost_items_postgres.php">Lost Items</a></li>
            <li><a href="pages/found_items_postgres.php">Found Items</a></li>
            <li><a href="report_lost_postgres.php">Report Lost</a></li>
            <li><a href="report_found_postgres.php">Report Found</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <section class="hero">
        <h2>Campus Lost &amp; Found</h2>
        <p>Helping our campus community reunite lost items with their owners</p>
    </section>

    <div class="cards-grid">
        <div class="card">
            <span class="card-icon">🔎</span>
            <h3>Browse Lost Items</h3>
            <p>Search through items reported as lost by campus members.</p>
            <a href="pages/lost_items_postgres.php" class="btn btn-primary">View Lost Items</a>
        </div>
        <div class="card">
            <span class="card-icon">📦</span>
            <h3>Browse Found Items</h3>
            <p>Check items that have been found and turned in by our community.</p>
            <a href="pages/found_items_postgres.php" class="btn btn-primary">View Found Items</a>
        </div>
        <div class="card">
            <span class="card-icon">📢</span>
            <h3>Report a Lost Item</h3>
            <p>Lost something? Report it here and upload a photo or video to help others identify it.</p>
            <a href="report_lost_postgres.php" class="btn btn-primary">Report Lost</a>
        </div>
        <div class="card">
            <span class="card-icon">💝</span>
            <h3>Report a Found Item</h3>
            <p>Found something? Help the owner by submitting a report with photos or video.</p>
            <a href="report_found_postgres.php" class="btn btn-primary">Report Found</a>
        </div>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h4><?php echo (int)$lost_count; ?></h4>
            <p>Active Lost Reports</p>
        </div>
        <div class="stat-box">
            <h4><?php echo (int)$found_count; ?></h4>
            <p>Found Items Waiting</p>
        </div>
        <div class="stat-box">
            <h4><?php echo (int)$resolved_count; ?></h4>
            <p>Items Reunited</p>
        </div>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Campus Lost &amp; Found &mdash; Helping keep our community connected.</p>
</footer>
</body>
</html>
<?php $conn = null; ?>