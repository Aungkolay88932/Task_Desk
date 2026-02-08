<?php
date_default_timezone_set('Asia/Yangon');
require_once __DIR__ . '/check_auth.php';
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

$uid = $_SESSION['uid'];
$current = date('Y-m-d H:i:s'); // current time (Asia/Yangon)

$query = "SELECT reminder_id, reminder_title FROM reminder WHERE uid = ? AND reminder_time <= ? AND is_done = 0";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode([]);
    exit;
}
$stmt->bind_param('is', $uid, $current);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}
$stmt->close();

// Mark as notified
if (!empty($rows)) {
    $ids = array_column($rows, 'reminder_id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    // Build types string for bind_param
    $types = str_repeat('i', count($ids));
    $sql = "UPDATE reminder SET is_done = 1 WHERE reminder_id IN ($placeholders)";
    $upd = $conn->prepare($sql);
    if ($upd) {
        // dynamic bind
        $params = [];
        foreach ($ids as $i => $val) {
            $params[] = &$ids[$i];
        }
        array_unshift($params, $types);
        call_user_func_array([$upd, 'bind_param'], $params);
        $upd->execute();
        $upd->close();
    }
}

echo json_encode($rows);
exit;
