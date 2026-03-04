<?php
session_start();
include '../config/firebase_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_type = $_POST['item_type'];
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $date_reported = $_POST['date_reported'];
    $location = $_POST['location'];
    $contact_name = $_POST['contact_name'];
    $contact_email = $_POST['contact_email'];
    $contact_phone = isset($_POST['contact_phone']) ? $_POST['contact_phone'] : '';

    // Validation
    if (empty($item_name) || empty($category) || empty($description) || 
        empty($date_reported) || empty($location) || empty($contact_name) || empty($contact_email)) {
        $_SESSION['error'] = "All required fields must be filled!";
        header("Location: ../report_" . ($item_type == 'lost' ? 'lost' : 'found') . ".php");
        exit;
    }

    // Email validation
    if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address!";
        header("Location: ../report_" . ($item_type == 'lost' ? 'lost' : 'found') . ".php");
        exit;
    }

    // Date validation
    $date = DateTime::createFromFormat('Y-m-d', $date_reported);
    if (!$date || $date > new DateTime()) {
        $_SESSION['error'] = "Please enter a valid past date!";
        header("Location: ../report_" . ($item_type == 'lost' ? 'lost' : 'found') . ".php");
        exit;
    }

    // Prepare item data
    $itemData = [
        'item_type' => $item_type,
        'item_name' => $item_name,
        'description' => $description,
        'category' => $category,
        'date_reported' => $date_reported,
        'location' => $location,
        'contact_name' => $contact_name,
        'contact_email' => $contact_email,
        'contact_phone' => $contact_phone,
        'status' => 'unresolved',
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Add to Firebase
    if (addItem($itemData)) {
        $_SESSION['success'] = "Item reported successfully! Thank you for helping our campus community.";
        header("Location: ../index_firebase.php");
        exit;
    } else {
        $_SESSION['error'] = "Error reporting item. Please try again.";
        header("Location: ../report_" . ($item_type == 'lost' ? 'lost' : 'found') . ".php");
        exit;
    }
} else {
    header("Location: ../index_firebase.php");
    exit;
}
?>
