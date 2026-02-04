<?php
require_once __DIR__ . '/init_session.php';
require_once __DIR__ . '/db_connect.php';

$uid = isset($_SESSION['uid']) ? intval($_SESSION['uid']) : null;

// If an explicit uid is passed (not recommended for production), use it
if (isset($_GET['uid'])) {
    $uid = intval($_GET['uid']);
}

// Serve default image if no uid
if (!$uid) {
    // default logo
    $default = __DIR__ . '/../frontend/image/default_image.png';
    if (file_exists($default)) {
        $def_mime = mime_content_type($default) ?: 'application/octet-stream';
        header('Content-Type: ' . $def_mime);
        readfile($default);
        exit;
    }
    http_response_code(404);
    exit;
}

$stmt = $conn->prepare("SELECT avatar, avatar_mime FROM user_info WHERE uid = ? LIMIT 1");
$stmt->bind_param('i', $uid);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    http_response_code(404);
    exit;
}

$stmt->bind_result($avatar, $mime);
$stmt->fetch();
$stmt->close();

if ($avatar !== null && strlen($avatar) > 0) {
    if (empty($mime)) $mime = 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . strlen($avatar));
    echo $avatar;
    exit;
} else {
    // fallback default
    $default = __DIR__ . '/../frontend/image/default_image.png';
    if (file_exists($default)) {
        $def_mime = mime_content_type($default) ?: 'application/octet-stream';
        header('Content-Type: ' . $def_mime);
        readfile($default);
        exit;
    }
}

http_response_code(204);
exit;

?>
