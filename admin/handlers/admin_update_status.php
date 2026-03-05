<?php
session_start();
include __DIR__ . '/../../config/postgres_config.php';
include __DIR__ . '/../../helpers/email_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_panel.php'); exit;
}

$id         = (int)($_POST['id']     ?? 0);
$new_status = trim($_POST['status']  ?? '');
$allowed    = ['unresolved', 'claimed', 'returned'];

if ($id <= 0 || !in_array($new_status, $allowed)) {
    $_SESSION['error'] = '⚠️ Invalid request.';
    header('Location: ../admin_panel.php'); exit;
}

try {
    // Fetch item first (needed for email notification)
    $fetch = $conn->prepare("SELECT * FROM items WHERE id = :id");
    $fetch->execute([':id' => $id]);
    $item = $fetch->fetch();

    if (!$item) {
        $_SESSION['error'] = '⚠️ Item not found.';
        header('Location: ../admin_panel.php'); exit;
    }

    // Update status
    $stmt = $conn->prepare(
        "UPDATE items SET status = :status, updated_at = NOW() WHERE id = :id"
    );
    $stmt->execute([':status' => $new_status, ':id' => $id]);

    // Status labels for flash message
    $labels = [
        'claimed'    => '✅ Marked as Claimed',
        'returned'   => '🔄 Marked as Returned',
        'unresolved' => '↩️ Re-opened as Unresolved',
    ];
    $_SESSION['success'] = ($labels[$new_status] ?? 'Status updated') . " — {$item['item_name']}";

    // Send email notification (only for claimed/returned)
    if (in_array($new_status, ['claimed', 'returned'])) {
        $sent = sendStatusNotification($item, $new_status);
        if ($sent) {
            $_SESSION['success'] .= ' · 📧 Email notification sent.';
        }
    }

} catch (PDOException $e) {
    error_log('Status update error: ' . $e->getMessage());
    $_SESSION['error'] = '⚠️ Database error. Please try again.';
}

header('Location: ../admin_panel.php');
exit;