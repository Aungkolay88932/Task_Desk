<?php
require_once __DIR__ . '/init_session.php';
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (empty($_SESSION['uid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$newName = null;
if ($data && isset($data['user_name'])) {
    $newName = trim($data['user_name']);
} elseif (isset($_POST['user_name'])) {
    $newName = trim($_POST['user_name']);
}

if (!$newName) {
    echo json_encode(['success' => false, 'message' => 'Name is required']);
    exit;
}

if (mb_strlen($newName) > 100) {
    echo json_encode(['success' => false, 'message' => 'Name too long']);
    exit;
}

$uid = intval($_SESSION['uid']);

$stmt = $conn->prepare('UPDATE user_info SET user_name = ? WHERE uid = ?');
if (!$stmt) {
    error_log('Prepare failed: ' . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}

$stmt->bind_param('si', $newName, $uid);
if ($stmt->execute()) {
    $_SESSION['user_name'] = $newName;
    echo json_encode(['success' => true, 'user_name' => $newName]);
} else {
    error_log('Execute failed: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}

$stmt->close();

?>
