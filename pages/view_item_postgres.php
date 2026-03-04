<?php
session_start();
include __DIR__ . '/../config/postgres_config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: ../index_postgres.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM items WHERE id = :id");
$stmt->execute([':id' => $id]);
$item = $stmt->fetch();

if (!$item) {
    header("Location: ../index_postgres.php");
    exit;
}

$back_url   = ($item['item_type'] === 'lost') ? 'lost_items_postgres.php' : 'found_items_postgres.php';
$back_label = ($item['item_type'] === 'lost') ? 'Lost Items' : 'Found Items';
$status_class = 'status-' . strtolower($item['status']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['item_name']); ?> – Campus Lost &amp; Found</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <h1 class="logo">🎓 Campus Lost &amp; Found</h1>
        <ul class="nav-menu">
            <li><a href="../index_postgres.php">Home</a></li>
            <li><a href="lost_items_postgres.php">Lost Items</a></li>
            <li><a href="found_items_postgres.php">Found Items</a></li>
            <li><a href="../report_lost_postgres.php">Report Lost</a></li>
            <li><a href="../report_found_postgres.php">Report Found</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="back-link">
        <a href="<?php echo $back_url; ?>">← Back to <?php echo $back_label; ?></a>
    </div>

    <div class="item-detail">
        <div class="detail-header">
            <div>
                <h2><?php echo htmlspecialchars($item['item_name']); ?></h2>
                <div class="detail-badges">
                    <span class="item-type <?php echo strtolower($item['item_type']); ?>">
                        <?php echo strtoupper($item['item_type']); ?>
                    </span>
                    <span class="category-badge"><?php echo htmlspecialchars($item['category']); ?></span>
                    <span class="status-badge <?php echo $status_class; ?>">
                        <?php echo ucfirst(htmlspecialchars($item['status'])); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="detail-content">

            <?php if (!empty($item['media_filename'])): ?>
                <div class="detail-section">
                    <h3>Media</h3>
                    <div class="media-container">
                        <?php if ($item['media_type'] === 'image'): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($item['media_filename']); ?>"
                                alt="Item photo" class="media-display">
                        <?php elseif ($item['media_type'] === 'video'): ?>
                            <video controls class="media-display">
                                <source src="../uploads/<?php echo htmlspecialchars($item['media_filename']); ?>">
                                Your browser does not support the video tag.
                            </video>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="detail-section">
                <h3>Item Information</h3>
                <div class="detail-info">
                    <div class="info-item">
                        <strong>📅 Date <?php echo ucfirst($item['item_type']); ?></strong>
                        <span><?php echo date('F j, Y', strtotime($item['date_reported'])); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>📍 Location</strong>
                        <span><?php echo htmlspecialchars($item['location']); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>📂 Category</strong>
                        <span><?php echo htmlspecialchars($item['category']); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>📊 Status</strong>
                        <span><?php echo ucfirst(htmlspecialchars($item['status'])); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>🕒 Reported On</strong>
                        <span><?php echo date('F j, Y g:i A', strtotime($item['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h3>Description</h3>
                <div class="description-text">
                    <?php echo nl2br(htmlspecialchars($item['description'])); ?>
                </div>
            </div>

            <div class="detail-section">
                <h3>Contact Information</h3>
                <div class="contact-card">
                    <p><strong>👤 Name:</strong> <?php echo htmlspecialchars($item['contact_name']); ?></p>
                    <p><strong>✉️ Email:</strong>
                        <a href="mailto:<?php echo htmlspecialchars($item['contact_email']); ?>">
                            <?php echo htmlspecialchars($item['contact_email']); ?></a></p>
                    <?php if (!empty($item['contact_phone'])): ?>
                        <p><strong>☎️ Phone:</strong> <?php echo htmlspecialchars($item['contact_phone']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="contact-note">
                    💡 <strong>Tip:</strong> If this is your item or you have information about it, contact the reporter directly via email or phone above.
                </div>
            </div>

            <div class="detail-actions">
                <a href="<?php echo $back_url; ?>" class="btn btn-secondary">← Back to <?php echo $back_label; ?></a>
                <a href="mailto:<?php echo htmlspecialchars($item['contact_email']); ?>?subject=Re: <?php echo urlencode($item['item_name']); ?> - Campus Lost and Found"
                    class="btn btn-primary">📧 Contact Reporter</a>
            </div>

        </div>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Campus Lost &amp; Found &mdash; Helping keep our community connected.</p>
</footer>
</body>
</html>
<?php $conn = null; ?>