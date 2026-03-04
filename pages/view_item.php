<?php
session_start();
include '../config/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: ../index.php");
    exit;
}

$result = $conn->query("SELECT * FROM items WHERE id=$id");

if ($result->num_rows == 0) {
    header("Location: ../index.php");
    exit;
}

$item = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['item_name']); ?> - Campus Lost & Found</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">🎓 Campus Lost & Found</h1>
            <ul class="nav-menu">
                <li><a href="../index.php">Home</a></li>
                <li><a href="lost_items.php">Lost Items</a></li>
                <li><a href="found_items.php">Found Items</a></li>
                <li><a href="report_lost.php">Report Lost</a></li>
                <li><a href="report_found.php">Report Found</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="item-detail">
            <div class="detail-header">
                <h2><?php echo htmlspecialchars($item['item_name']); ?></h2>
                <span class="item-type <?php echo strtolower($item['item_type']); ?>">
                    <?php echo strtoupper($item['item_type']); ?>
                </span>
                <span class="category-badge"><?php echo $item['category']; ?></span>
            </div>

            <div class="detail-content">
                <div class="detail-section">
                    <h3>Item Information</h3>
                    <div class="detail-info">
                        <p>
                            <strong>Category:</strong>&nbsp;<span><?php echo $item['category']; ?></span>
                        </p>
                        <p>
                            <strong>Status:</strong>&nbsp;<span><?php echo ucfirst($item['status']); ?></span>
                        </p>
                        <p>
                            <strong>Date <?php echo ($item['item_type'] == 'lost') ? 'Lost' : 'Found'; ?>:</strong>&nbsp;
                            <span><?php echo date('M d, Y', strtotime($item['date_reported'])); ?></span>
                        </p>
                        <p>
                            <strong>Location:</strong>&nbsp;<span><?php echo htmlspecialchars($item['location']); ?></span>
                        </p>
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Description</h3>
                    <p class="description-text"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                </div>

                <div class="detail-section">
                    <h3>Contact Information</h3>
                    <div class="contact-card">
                        <p>
                            <strong>Name:</strong>&nbsp;<span><?php echo htmlspecialchars($item['contact_name']); ?></span>
                        </p>
                        <p>
                            <strong>Email:</strong>&nbsp;
                            <a href="mailto:<?php echo htmlspecialchars($item['contact_email']); ?>">
                                <?php echo htmlspecialchars($item['contact_email']); ?>
                            </a>
                        </p>
                        <?php if ($item['contact_phone']): ?>
                            <p>
                                <strong>Phone:</strong>&nbsp;
                                <a href="tel:<?php echo htmlspecialchars($item['contact_phone']); ?>">
                                    <?php echo htmlspecialchars($item['contact_phone']); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                    <p class="contact-note">
                        ℹ️ Contact this person if you have information about this item or if this is your item.
                    </p>
                </div>

                <div class="detail-actions">
                    <a href="<?php echo ($item['item_type'] == 'lost') ? 'lost_items.php' : 'found_items.php'; ?>" class="btn btn-secondary">
                        ← Back to Items
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 College Lost & Found System. Help us keep our campus community connected.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
