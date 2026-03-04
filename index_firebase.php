<?php
session_start();
include 'firebase_config.php';

// Get statistics
$allItems = getAllItems();
$lostCount = count(array_filter($allItems, function($item) { 
    return $item['item_type'] === 'lost' && $item['status'] === 'unresolved'; 
}));
$foundCount = count(array_filter($allItems, function($item) { 
    return $item['item_type'] === 'found' && $item['status'] === 'unresolved'; 
}));
$claimedCount = count(array_filter($allItems, function($item) { 
    return $item['status'] === 'claimed'; 
}));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Lost & Found System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo"> Campus Lost & Found</h1>
            <ul class="nav-menu">
                <li><a href="index_firebase.php" class="active">Home</a></li>
                <li><a href="lost_items_firebase.php">Lost Items</a></li>
                <li><a href="found_items_firebase.php">Found Items</a></li>
                <li><a href="report_lost.php">Report Lost</a></li>
                <li><a href="report_found.php">Report Found</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <section class="hero">
            <h2>Welcome to Campus Lost & Found</h2>
            <p>Help us reunite lost items with their owners</p>
        </section>

        <section class="main-content">
            <div class="cards-grid">
                <div class="card">
                    <h3>Browse Lost Items</h3>
                    <p>Search through lost items reported by campus members</p>
                    <a href="lost_items_firebase.php" class="btn btn-primary">View Lost Items</a>
                </div>

                <div class="card">
                    <div class="card-icon">🔍</div>
                    <h3>Browse Found Items</h3>
                    <p>Check items that have been found on campus</p>
                    <a href="found_items_firebase.php" class="btn btn-primary">View Found Items</a>
                </div>

                <div class="card">
                    <div class="card-icon">📝</div>
                    <h3>Report Lost Item</h3>
                    <p>Report an item you've lost on campus</p>
                    <a href="report_lost.php" class="btn btn-primary">Report Lost</a>
                </div>

                <div class="card">
                    <div class="card-icon">✓</div>
                    <h3>Report Found Item</h3>
                    <p>Report an item you've found on campus</p>
                    <a href="report_found.php" class="btn btn-primary">Report Found</a>
                </div>
            </div>
        </section>

        <section class="stats">
            <div class="stat-box">
                <h4><?php echo $lostCount; ?></h4>
                <p>Lost Items</p>
            </div>
            <div class="stat-box">
                <h4><?php echo $foundCount; ?></h4>
                <p>Found Items</p>
            </div>
            <div class="stat-box">
                <h4><?php echo $claimedCount; ?></h4>
                <p>Resolved</p>
            </div>
        </section>
    </div>

    <footer>
        <p>&copy; 2026 College Lost & Found System. Help us keep our campus community connected.</p>
    </footer>
</body>
</html>
