<?php
require_once __DIR__ . '/init_session.php';
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json; charset=utf-8');

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
$note_id = isset($data['note_id']) ? intval($data['note_id']) : (isset($_POST['note_id']) ? intval($_POST['note_id']) : 0);

if ($note_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid note id']);
    exit;
}

$uid = intval($_SESSION['uid']);

$stmt = $conn->prepare('DELETE FROM Note WHERE note_id = ? AND uid = ?');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Prepare failed', 'error' => $conn->error]);
    exit;
}

$stmt->bind_param('ii', $note_id, $uid);
$ok = $stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($ok) {
    echo json_encode(['success' => true, 'affected' => $affected]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Delete failed']);
}

?>
