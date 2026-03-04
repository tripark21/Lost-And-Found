<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_type = $conn->real_escape_string($_POST['item_type']);
    $item_name = $conn->real_escape_string($_POST['item_name']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $date_reported = $conn->real_escape_string($_POST['date_reported']);
    $location = $conn->real_escape_string($_POST['location']);
    $contact_name = $conn->real_escape_string($_POST['contact_name']);
    $contact_email = $conn->real_escape_string($_POST['contact_email']);
    $contact_phone = isset($_POST['contact_phone']) ? $conn->real_escape_string($_POST['contact_phone']) : '';

    // Validation
    if (empty($item_name) || empty($category) || empty($description) || 
        empty($date_reported) || empty($location) || empty($contact_name) || empty($contact_email)) {
        $_SESSION['error'] = "All required fields must be filled!";
        header("Location: report_" . ($item_type == 'lost' ? 'lost' : 'found') . ".php");
        exit;
    }

    // Email validation
    if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address!";
        header("Location: report_" . ($item_type == 'lost' ? 'lost' : 'found') . ".php");
        exit;
    }

    // Date validation
    $date = DateTime::createFromFormat('Y-m-d', $date_reported);
    if (!$date || $date > new DateTime()) {
        $_SESSION['error'] = "Please enter a valid past date!";
        header("Location: report_" . ($item_type == 'lost' ? 'lost' : 'found') . ".php");
        exit;
    }

    // Insert into database
    $sql = "INSERT INTO items (item_type, item_name, description, category, date_reported, location, contact_name, contact_email, contact_phone, status)
            VALUES ('$item_type', '$item_name', '$description', '$category', '$date_reported', '$location', '$contact_name', '$contact_email', '$contact_phone', 'unresolved')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Item reported successfully! Thank you for helping our campus community.";
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['error'] = "Error reporting item: " . $conn->error;
        header("Location: report_" . ($item_type == 'lost' ? 'lost' : 'found') . ".php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}

$conn->close();
?>
