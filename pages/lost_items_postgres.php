<?php
session_start();
include __DIR__ . '/../config/postgres_config.php';

$search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$where  = "WHERE item_type = 'lost' AND status = 'unresolved'";
$params = [];

if ($search !== '') {
    $where .= " AND (item_name ILIKE :search OR description ILIKE :search OR location ILIKE :search)";
    $params[':search'] = '%' . $search . '%';
}
if ($category !== '') {
    $where .= " AND category = :category";
    $params[':category'] = $category;
}

$stmt = $conn->prepare("SELECT * FROM items $where ORDER BY date_reported DESC, created_at DESC");
$stmt->execute($params);
$items = $stmt->fetchAll();

$cat_stmt = $conn->prepare("SELECT DISTINCT category FROM items WHERE item_type = 'lost' ORDER BY category");
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Items – Campus Lost &amp; Found</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <h1 class="logo">🎓 Campus Lost &amp; Found</h1>
        <ul class="nav-menu">
            <li><a href="../index_postgres.php">Home</a></li>
            <li><a href="lost_items_postgres.php" class="active">Lost Items</a></li>
            <li><a href="found_items_postgres.php">Found Items</a></li>
            <li><a href="../report_lost_postgres.php">Report Lost</a></li>
            <li><a href="../report_found_postgres.php">Report Found</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2>Lost Items</h2>

    <div class="search-section">
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search by name, description, or location…"
                value="<?php echo htmlspecialchars($search); ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                        <?php echo ($category === $cat['category']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-search">Search</button>
            <a href="lost_items_postgres.php" class="btn btn-secondary">Clear</a>
        </form>
    </div>

    <div class="items-grid">
        <?php if (count($items) > 0): ?>
            <?php foreach ($items as $item): ?>
                <div class="item-card">
                    <div class="item-header">
                        <span class="item-type lost">LOST</span>
                        <span class="category-badge"><?php echo htmlspecialchars($item['category']); ?></span>
                    </div>

                    <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>

                    <?php if (!empty($item['media_filename'])): ?>
                        <?php if ($item['media_type'] === 'image'): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($item['media_filename']); ?>"
                                alt="Item photo" class="media-thumbnail">
                        <?php elseif ($item['media_type'] === 'video'): ?>
                            <video controls class="media-thumbnail">
                                <source src="../uploads/<?php echo htmlspecialchars($item['media_filename']); ?>">
                            </video>
                        <?php endif; ?>
                        <span class="media-icon">📎 Media Attached</span>
                    <?php endif; ?>

                    <div class="item-date">📅 <?php echo date('F j, Y', strtotime($item['date_reported'])); ?></div>
                    <div class="item-location">📍 <?php echo htmlspecialchars($item['location']); ?></div>
                    <p class="item-description">
                        <?php
                            $desc = htmlspecialchars($item['description']);
                            echo mb_strlen($desc) > 120 ? mb_substr($desc, 0, 120) . '…' : $desc;
                        ?>
                    </p>

                    <div class="item-contact">
                        <p><strong>Reported by:</strong> <?php echo htmlspecialchars($item['contact_name']); ?></p>
                        <p><strong>Email:</strong>
                            <a href="mailto:<?php echo htmlspecialchars($item['contact_email']); ?>">
                                <?php echo htmlspecialchars($item['contact_email']); ?></a></p>
                        <?php if (!empty($item['contact_phone'])): ?>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($item['contact_phone']); ?></p>
                        <?php endif; ?>
                    </div>

                    <a href="view_item_postgres.php?id=<?php echo (int)$item['id']; ?>"
                        class="btn btn-primary" style="margin-top:auto;">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-items">
                <p>🔍 No lost items found<?php echo ($search || $category) ? ' matching your search' : ''; ?>. Try adjusting your filters.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Campus Lost &amp; Found &mdash; Helping keep our community connected.</p>
</footer>
</body>
</html>
<?php $conn = null; ?>