<?php
session_start();
include '../config/firebase_config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Get all found items
$allItems = getAllItems('found', 'unresolved');

// Filter items based on search and category
$items = [];
if ($allItems) {
    foreach ($allItems as $id => $item) {
        $matchesSearch = empty($search) || 
                        stripos($item['item_name'], $search) !== false || 
                        stripos($item['description'], $search) !== false;
        $matchesCategory = empty($category) || $item['category'] === $category;
        
        if ($matchesSearch && $matchesCategory) {
            $item['id'] = $id;
            $items[] = $item;
        }
    }
}

// Get unique categories
$categories = getCategories('found');
sort($categories);

$success_msg = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error_msg = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Found Items - Lost & Found System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>📋 Lost & Found System</h1>
            <ul class="nav-menu">
                <li><a href="../index_firebase.php">Home</a></li>
                <li><a href="lost_items_firebase.php">Lost Items</a></li>
                <li><a href="found_items_firebase.php" class="active">Found Items</a></li>
                <li><a href="../report_lost.php">Report Lost</a></li>
                <li><a href="../report_found.php">Report Found</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2>🔍 Found Items</h2>
        
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <div class="search-section">
            <form method="get" class="search-form">
                <input type="text" name="search" placeholder="Search by item name or description..." 
                       value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                
                <select name="category" class="category-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" 
                                <?php echo $category === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn-search">Search</button>
                <?php if ($search || $category): ?>
                    <a href="found_items_firebase.php" class="btn-secondary">Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (empty($items)): ?>
            <p class="no-items">No found items at the moment. Check back soon!</p>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach ($items as $item): ?>
                    <div class="card item-card">
                        <div class="card-header">
                            <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                            <span class="item-type found">Found</span>
                        </div>
                        <div class="card-body">
                            <p class="category-badge"><?php echo htmlspecialchars($item['category']); ?></p>
                            <p class="item-location">📍 <?php echo htmlspecialchars($item['location']); ?></p>
                            <p class="item-date">📅 <?php echo htmlspecialchars($item['date_reported']); ?></p>
                            <p class="item-description"><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?></p>
                        </div>
                        <div class="card-footer">
                            <a href="view_item_firebase.php?id=<?php echo urlencode($item['id']); ?>" class="btn-primary">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 Campus Lost & Found. Helping our community reunite with their belongings.</p>
    </footer>
</body>
</html>
