<?php
// TinyMCE image upload endpoint.
// Returns {"location": "/uploads/filename.jpg"} on success.
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    exit;
}

require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_FILES['file'])) {
    echo json_encode(['error' => 'No file received.']);
    exit;
}

$upload_dir = dirname(__DIR__) . '/uploads/';
$filename = save_featured_image($_FILES['file'], $upload_dir);

if (!$filename) {
    http_response_code(400);
    echo json_encode(['error' => 'Upload failed. Accepted: JPEG, PNG, GIF, WebP under 5 MB.']);
    exit;
}

echo json_encode(['location' => '/uploads/' . $filename]);
