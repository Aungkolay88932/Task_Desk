<?php
require_once __DIR__ . '/init_session.php';
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['uid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}
//check uploaded file exists and no error
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['avatar'];

// Basic validations
$maxSize = 2 * 1024 * 1024; // 2MB
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File too large (max 2MB)']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowed = ['image/jpeg','image/png','image/gif','image/webp'];
if (!in_array($mime, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

$data = file_get_contents($file['tmp_name']);

$uid = intval($_SESSION['uid']);

// Update avatar using mysqli send_long_data for BLOB
$stmt = $conn->prepare("UPDATE user_info SET avatar = ?, avatar_mime = ? WHERE uid = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Prepare failed']);
    exit;
}

$null = NULL;
$stmt->bind_param('bsi', $null, $mime, $uid);
$stmt->send_long_data(0, $data);
$ok = $stmt->execute();
$err = $stmt->error;
$stmt->close();

if ($ok) {
    // Return URL to fetch the avatar and bust cache
    $url = '/taskdesk/connect/avatar.php?cb=' . time();
    echo json_encode(['success' => true, 'url' => $url]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB update failed', 'error' => $err]);
    exit;
}

?>
