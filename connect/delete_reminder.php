<?php
require_once __DIR__ . '/check_auth.php';
require_once __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_SESSION['uid'];
    $id = intval($_POST['reminder_id'] ?? 0);
    if ($id > 0) {
        $sql = "DELETE FROM reminder WHERE reminder_id = ? AND uid = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ii', $id, $uid);
            $stmt->execute();
            $stmt->close();
        }
    }
}

header('Location: /taskdesk/frontend/remainder.php');
exit;
