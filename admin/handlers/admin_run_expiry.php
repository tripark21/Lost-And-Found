<?php
session_start();
include __DIR__ . '/../../config/postgres_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_panel.php'); exit;
}

$days = max(7, min(365, (int)($_POST['expiry_days'] ?? 30)));

try {
    $stmt = $conn->prepare("
        UPDATE items
        SET status = 'archived', updated_at = NOW()
        WHERE status = 'unresolved'
          AND date_reported < CURRENT_DATE - INTERVAL '{$days} days'
    ");
    $stmt->execute();
    $count = $stmt->rowCount();

    if ($count > 0) {
        $_SESSION['success'] = "⏰ Archived {$count} item" . ($count !== 1 ? 's' : '') . " older than {$days} days.";
    } else {
        $_SESSION['success'] = "⏰ No unresolved items older than {$days} days found. Nothing archived.";
    }

} catch (PDOException $e) {
    error_log('Expiry error: ' . $e->getMessage());
    $_SESSION['error'] = '⚠️ Expiry process failed. Please try again.';
}

header('Location: ../admin_panel.php');
exit;