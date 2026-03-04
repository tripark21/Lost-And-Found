<?php
session_start();
include '../config/firebase_config.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header("Location: ../index_firebase.php");
    exit;
}

// Get item details
$item = getItemById($id);

if (!$item) {
    $_SESSION['error'] = "Item not found!";
    header("Location: ../index_firebase.php");
    exit;
}

$item['id'] = $id;
$type = $item['item_type'];
$item_type_label = ucfirst($type);

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
    <title><?php echo htmlspecialchars($item['item_name']); ?> - Lost & Found System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>📋 Lost & Found System</h1>
            <ul class="nav-menu">
                <li><a href="../index_firebase.php">Home</a></li>
                <li><a href="lost_items_firebase.php">Lost Items</a></li>
                <li><a href="found_items_firebase.php">Found Items</a></li>
                <li><a href="../report_lost.php">Report Lost</a></li>
                <li><a href="../report_found.php">Report Found</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="back-link">
            <a href="<?php echo ($type === 'lost') ? 'lost_items_firebase.php' : 'found_items_firebase.php'; ?>">← Back to <?php echo $item_type_label; ?> Items</a>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <div class="item-detail">
            <div class="detail-header">
                <h1><?php echo htmlspecialchars($item['item_name']); ?></h1>
                <span class="item-type <?php echo htmlspecialchars($type); ?>"><?php echo $item_type_label; ?></span>
                <span class="status-badge"><?php echo ucfirst(htmlspecialchars($item['status'])); ?></span>
            </div>

            <div class="detail-content">
                <div class="detail-info">
                    <div class="info-group">
                        <label>Category:</label>
                        <p><?php echo htmlspecialchars($item['category']); ?></p>
                    </div>

                    <div class="info-group">
                        <label>Location:</label>
                        <p><?php echo htmlspecialchars($item['location']); ?></p>
                    </div>

                    <div class="info-group">
                        <label>Date Reported:</label>
                        <p><?php echo htmlspecialchars($item['date_reported']); ?></p>
                    </div>

                    <div class="info-group">
                        <label>Description:</label>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                    </div>

                    <div class="info-group">
                        <label>Status:</label>
                        <p><?php echo ucfirst(htmlspecialchars($item['status'])); ?></p>
                    </div>
                </div>

                <div class="contact-card">
                    <h3>📞 Contact Information</h3>
                    <div class="contact-info">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($item['contact_name']); ?></p>
                        <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($item['contact_email']); ?>"><?php echo htmlspecialchars($item['contact_email']); ?></a></p>
                        <?php if (!empty($item['contact_phone'])): ?>
                            <p><strong>Phone:</strong> <a href="tel:<?php echo htmlspecialchars($item['contact_phone']); ?>"><?php echo htmlspecialchars($item['contact_phone']); ?></a></p>
                        <?php endif; ?>
                    </div>
                    <p class="contact-note">
                        💡 <?php echo ($type === 'lost') ? 'Contact the person who found your item!' : 'Contact the person who lost this item!'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Campus Lost & Found. Helping our community reunite with their belongings.</p>
    </footer>
</body>
</html>
