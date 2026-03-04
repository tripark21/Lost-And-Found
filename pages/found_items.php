<?php
session_start();
include '../config/config.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

$where = "WHERE item_type='found' AND status='unresolved'";

if ($search) {
    $where .= " AND (item_name LIKE '%$search%' OR description LIKE '%$search%')";
}

if ($category) {
    $where .= " AND category='$category'";
}

$result = $conn->query("SELECT * FROM items $where ORDER BY date_reported DESC");
$items = $result->fetch_all(MYSQLI_ASSOC);

$categories = $conn->query("SELECT DISTINCT category FROM items WHERE item_type='found' ORDER BY category");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Found Items - Campus Lost & Found</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">🎓 Campus Lost & Found</h1>
            <ul class="nav-menu">
                <li><a href="../index.php">Home</a></li>
                <li><a href="lost_items.php">Lost Items</a></li>
                <li><a href="found_items.php" class="active">Found Items</a></li>
                <li><a href="report_lost.php">Report Lost</a></li>
                <li><a href="report_found.php">Report Found</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2>Found Items</h2>
        
        <div class="search-section">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search found items..." value="<?php echo $search; ?>">
                
                <select name="category">
                    <option value="">All Categories</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['category']; ?>" <?php echo ($category == $cat['category']) ? 'selected' : ''; ?>>
                            <?php echo $cat['category']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <button type="submit" class="btn btn-search">Search</button>
                <a href="found_items.php" class="btn btn-secondary">Clear</a>
            </form>
        </div>

        <div class="items-grid">
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $item): ?>
                    <div class="item-card">
                        <div class="item-header">
                            <span class="item-type found">FOUND</span>
                            <span class="category-badge"><?php echo $item['category']; ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                        <p class="item-date">Found: <?php echo date('M d, Y', strtotime($item['date_reported'])); ?></p>
                        <p class="item-location">📍 <?php echo htmlspecialchars($item['location']); ?></p>
                        <p class="item-description"><?php echo substr(htmlspecialchars($item['description']), 0, 100) . '...'; ?></p>
                        <div class="item-contact">
                            <p>Contact: <?php echo htmlspecialchars($item['contact_name']); ?></p>
                            <p>Email: <?php echo htmlspecialchars($item['contact_email']); ?></p>
                        </div>
                        <a href="view_item.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-items">
                    <p>No found items at the moment. Check back soon!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 College Lost & Found System. Help us keep our campus community connected.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
