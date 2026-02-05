
<?php
require_once __DIR__ . '/init_session.php';
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

if (!isset($_SESSION['uid'])) {
    http_response_code(401);
    echo 'Not logged in';
    exit;
}

$uid   = intval($_SESSION['uid']);
$name  = isset($_POST['name']) ? trim($_POST['name']) : '';
$price = isset($_POST['price']) ? trim($_POST['price']) : '';

$date  = isset($_POST['date']) ? trim($_POST['date']) : '';

if ($name === '' || $price === '') {
    http_response_code(400);
    echo 'Missing name or price';
    exit;
}

if (!is_numeric($price)) {
    http_response_code(400);
    echo 'Invalid price';
    exit;
}
$price = floatval($price);

// If date provided, normalize to YYYY-MM-DD; otherwise insert without cost_date
$cost_date = null;
if ($date !== '') {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $t = strtotime($date);
        if ($t === false) {
            http_response_code(400);
            echo 'Invalid date';
            exit;
        }
        $date = date('Y-m-d', $t);
    }
    $cost_date = $date;
}

if ($cost_date !== null) {
    $sql = "INSERT INTO cost (uid, cost_name, price, cost_date, created_cost_time) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) { http_response_code(500); echo 'DB prepare error: ' . $conn->error; exit; }
    $stmt->bind_param('isds', $uid, $name, $price, $cost_date);
} else {
    $sql = "INSERT INTO cost (uid, cost_name, price, created_cost_time) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) { http_response_code(500); echo 'DB prepare error: ' . $conn->error; exit; }
    $stmt->bind_param('isd', $uid, $name, $price);
}

$ok = $stmt->execute();
$err = $stmt->error;
$stmt->close();

if ($ok) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $err]);
}

?>
