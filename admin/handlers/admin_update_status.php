<?php
session_start();
include __DIR__ . '/../../config/postgres_config.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin_panel.php");
    exit;
}

$id     = isset($_POST['id'])     ? (int)$_POST['id']         : 0;
$status = isset($_POST['status']) ? trim($_POST['status'])     : '';

$allowed_statuses = ['unresolved', 'claimed', 'returned'];

if ($id <= 0 || !in_array($status, $allowed_statuses, true)) {
    $_SESSION['error'] = 'Invalid request.';
    header("Location: ../admin_panel.php");
    exit;
}

try {
    $stmt = $conn->prepare("
        UPDATE items
        SET status = :status, updated_at = NOW()
        WHERE id = :id
    ");
    $stmt->execute([':status' => $status, ':id' => $id]);

    $labels = [
        'claimed'    => 'marked as Claimed ✅',
        'returned'   => 'marked as Returned 🔄',
        'unresolved' => 're-opened as Unresolved ↩️',
    ];

    $_SESSION['success'] = "Item #$id successfully " . $labels[$status] . ".";

} catch (PDOException $e) {
    error_log("Status update error: " . $e->getMessage());
    $_SESSION['error'] = 'Failed to update status. Please try again.';
}

header("Location: ../admin_panel.php");
exit;

$conn = null;
?>