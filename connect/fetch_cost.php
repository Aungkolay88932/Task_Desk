<?php
require_once __DIR__ . '/init_session.php';
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['uid'])) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

$uid = intval($_SESSION['uid']);
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$date = isset($_GET['date']) ? $_GET['date'] : null;

$params = [$uid];
$where = 'WHERE uid = ?';

if ($type === 'day' && $date) {
    $where .= ' AND DATE(created_cost_time) = ?';
    $params[] = $date;
} elseif ($type === 'month' && $date) {
    // expect YYYY-MM or YYYY-MM-DD, match by month
    $ym = substr($date, 0, 7);
    $where .= ' AND DATE_FORMAT(created_cost_time, "%Y-%m") = ?';
    $params[] = $ym;
} elseif ($type === 'week') {
    $where .= ' AND created_cost_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
}

$sql = "SELECT cost_id, cost_name, price, created_cost_time as cost_date FROM cost $where ORDER BY created_cost_time DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => $conn->error]);
    exit;
}

// bind params dynamically
if (count($params) === 1) {
    $stmt->bind_param('i', $params[0]);
} elseif (count($params) === 2) {
    $stmt->bind_param('is', $params[0], $params[1]);
}

$stmt->execute();
$res = $stmt->get_result();
$out = [];
while ($row = $res->fetch_assoc()) {
    $out[] = [
        'cost_id' => (int)$row['cost_id'],
        'cost_name' => $row['cost_name'],
        'price' => floatval($row['price']),
        'cost_date' => $row['cost_date']
    ];
}

echo json_encode($out);

$stmt->close();

?>
