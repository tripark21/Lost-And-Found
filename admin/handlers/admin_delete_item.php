<?php
session_start();
include __DIR__ . '/../../config/postgres_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin_panel.php");
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid item ID.';
    header("Location: ../admin_panel.php");
    exit;
}

try {
    // Get the media filename before deleting
    $stmt = $conn->prepare("SELECT media_filename FROM items WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $item = $stmt->fetch();

    if (!$item) {
        $_SESSION['error'] = 'Item not found.';
        header("Location: ../admin_panel.php");
        exit;
    }

    // Delete the database record
    $del = $conn->prepare("DELETE FROM items WHERE id = :id");
    $del->execute([':id' => $id]);

    // Delete the uploaded file if it exists
    if (!empty($item['media_filename'])) {
        deleteFile($item['media_filename']);
    }

    $_SESSION['success'] = "Item #$id has been deleted.";

} catch (PDOException $e) {
    error_log("Delete error: " . $e->getMessage());
    $_SESSION['error'] = 'Failed to delete item. Please try again.';
}

header("Location: ../admin_panel.php");
exit;

$conn = null;
?>