<?php
session_start();
include __DIR__ . '/../config/postgres_config.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index_postgres.php");
    exit;
}

// ── Inputs ────────────────────────────────────────────────────
$item_type     = trim($_POST['item_type']     ?? '');
$item_name     = trim($_POST['item_name']     ?? '');
$category      = trim($_POST['category']      ?? '');
$description   = trim($_POST['description']   ?? '');
$date_reported = trim($_POST['date_reported'] ?? '');
$location      = trim($_POST['location']      ?? '');
$contact_name  = trim($_POST['contact_name']  ?? '');
$contact_email = trim($_POST['contact_email'] ?? '');
$contact_phone = trim($_POST['contact_phone'] ?? '');

$redirect = ($item_type === 'lost')
    ? '../report_lost_postgres.php'
    : '../report_found_postgres.php';

// ── Validation ───────────────────────────────────────────────
$errors = [];

if (!in_array($item_type, ['lost','found'], true)) $errors[] = 'Invalid item type.';
if (empty($item_name))     $errors[] = 'Item name is required.';
if (empty($category))      $errors[] = 'Category is required.';
if (empty($description))   $errors[] = 'Description is required.';
if (empty($date_reported)) $errors[] = 'Date is required.';
if (empty($location))      $errors[] = 'Location is required.';
if (empty($contact_name))  $errors[] = 'Your name is required.';
if (empty($contact_email)) $errors[] = 'Email address is required.';

if (!empty($contact_email) && !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (!empty($date_reported)) {
    $date = DateTime::createFromFormat('Y-m-d', $date_reported);
    if (!$date || $date > new DateTime()) {
        $errors[] = 'Please enter a valid date that is not in the future.';
    }
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    header("Location: $redirect");
    exit;
}

// ── File Upload (optional) ───────────────────────────────────
$media_filename = null;
$media_type     = null;

if (isset($_FILES['media']) && $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['media']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'File upload error (code ' . $_FILES['media']['error'] . '). Please try again.';
        header("Location: $redirect");
        exit;
    }
    $upload = uploadFile($_FILES['media']);
    if (!$upload['success']) {
        $_SESSION['error'] = 'Upload failed: ' . $upload['message'];
        header("Location: $redirect");
        exit;
    }
    $media_filename = $upload['filename'];
    $media_type     = getFileType($media_filename);
}

// ── Insert into DB ───────────────────────────────────────────
try {
    $stmt = $conn->prepare("
        INSERT INTO items
            (item_type, item_name, description, category, date_reported,
             location, contact_name, contact_email, contact_phone,
             media_filename, media_type, status, created_at, updated_at)
        VALUES
            (:item_type, :item_name, :description, :category, :date_reported,
             :location, :contact_name, :contact_email, :contact_phone,
             :media_filename, :media_type, 'unresolved', NOW(), NOW())
    ");
    $stmt->execute([
        ':item_type'      => $item_type,
        ':item_name'      => $item_name,
        ':description'    => $description,
        ':category'       => $category,
        ':date_reported'  => $date_reported,
        ':location'       => $location,
        ':contact_name'   => $contact_name,
        ':contact_email'  => $contact_email,
        ':contact_phone'  => $contact_phone ?: null,
        ':media_filename' => $media_filename,
        ':media_type'     => $media_type,
    ]);

    $_SESSION['success'] = ucfirst($item_type) . ' item reported successfully! Thank you for helping our campus community.';
    header("Location: ../index_postgres.php");
    exit;

} catch (PDOException $e) {
    if ($media_filename) deleteFile($media_filename);
    error_log("DB insert error: " . $e->getMessage());
    $_SESSION['error'] = 'There was a problem saving your report. Please try again.';
    header("Location: $redirect");
    exit;
}

$conn = null;
?>