<?php
// ============================================================
// PostgreSQL Configuration – Campus Lost & Found
// ============================================================

define('DB_HOST', 'localhost');
define('DB_PORT', 5432);
define('DB_USER', 'postgres');
define('DB_PASS', '123');             // ← Your PostgreSQL password
define('DB_NAME', 'LAF_db');          // ← Your database name

// ============================================================
// File Upload Configuration
// ============================================================

define('UPLOAD_DIR',    __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50 MB

define('ALLOWED_EXTENSIONS', ['jpg','jpeg','png','gif','mp4','avi','mov','mkv','webm']);
define('ALLOWED_MIME_TYPES', [
    'image/jpeg','image/png','image/gif',
    'video/mp4','video/quicktime','video/x-msvideo',
    'video/x-matroska','video/webm'
]);

// ============================================================
// Database Connection
// ============================================================

try {
    $conn = new PDO(
        "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    // Set search_path so all queries use the 'lost' schema by default
    $conn->exec("SET search_path TO lost, public");

} catch (PDOException $e) {
    error_log("DB Connection failed: " . $e->getMessage());
    die("Unable to connect to the database. Please try again later.");
}

// ============================================================
// Helper Functions
// ============================================================

function uploadFile(array $file): array {
    if (empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file provided.'];
    }
    if (!is_dir(UPLOAD_DIR)) {
        if (!mkdir(UPLOAD_DIR, 0755, true)) {
            return ['success' => false, 'message' => 'Upload directory could not be created.'];
        }
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File exceeds the 50 MB size limit.'];
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
        return ['success' => false, 'message' => 'File type not allowed.'];
    }
    $finfo     = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime_type, ALLOWED_MIME_TYPES, true)) {
        return ['success' => false, 'message' => 'Invalid file format detected.'];
    }
    $filename = 'item_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $filepath = UPLOAD_DIR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Failed to save the uploaded file.'];
    }
    return ['success' => true, 'filename' => $filename, 'path' => $filepath];
}

function deleteFile(string $filename): bool {
    if (empty($filename)) return false;
    $filepath = UPLOAD_DIR . basename($filename);
    return file_exists($filepath) ? unlink($filepath) : false;
}

function getFileType(string $filename): string {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png','gif'], true))       return 'image';
    if (in_array($ext, ['mp4','avi','mov','mkv','webm'], true)) return 'video';
    return 'unknown';
}
?>