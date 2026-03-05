<?php
session_start();
include __DIR__ . '/../../config/postgres_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_panel.php'); exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = '⚠️ Invalid request.';
    header('Location: ../admin_panel.php'); exit;
}

try {
    $fetch = $conn->prepare("SELECT item_name, media_filename FROM items WHERE id = :id");
    $fetch->execute([':id' => $id]);
    $item = $fetch->fetch();

    if (!$item) {
        $_SESSION['error'] = '⚠️ Item not found.';
        header('Location: ../admin_panel.php'); exit;
    }

    // Delete record
    $del = $conn->prepare("DELETE FROM items WHERE id = :id");
    $del->execute([':id' => $id]);

    // Remove uploaded file
    if (!empty($item['media_filename'])) {
        $file_path = __DIR__ . '/../../uploads/' . basename($item['media_filename']);
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
    }

    $_SESSION['success'] = "🗑️ Deleted: {$item['item_name']}";

} catch (PDOException $e) {
    error_log('Delete error: ' . $e->getMessage());
    $_SESSION['error'] = '⚠️ Could not delete item. Please try again.';
}

header('Location: ../admin_panel.php');
exit;