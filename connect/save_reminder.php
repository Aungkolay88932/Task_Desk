<?php
require_once __DIR__ . '/check_auth.php';
require_once __DIR__ . '/db_connect.php';

// Expect POST: title, description, date, remind [, reminder_id for edit ]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_SESSION['uid'];
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['date'] ?? '';
    $remind = $_POST['remind'] ?? '';
    $reminder_id = isset($_POST['reminder_id']) ? intval($_POST['reminder_id']) : 0;

    $notify_datetime = null;
    if ($date && $remind) {
        $notify_datetime = $date . ' ' . $remind . ':00';
    }

    if ($reminder_id > 0) {
        // Update existing reminder (only if it belongs to this user)
        $upd_with_desc = "UPDATE reminder SET reminder_title = ?, reminder_description = ?, reminder_time = ?, is_done = 0 WHERE reminder_id = ? AND uid = ?";
        $stmt = $conn->prepare($upd_with_desc);
        $updated = false;
        if ($stmt) {
            $stmt->bind_param('sssii', $title, $description, $notify_datetime, $reminder_id, $uid);
            $updated = $stmt->execute();
            $stmt->close();
        }
        if (!$updated) {
            $upd = "UPDATE reminder SET reminder_title = ?, reminder_time = ?, is_done = 0 WHERE reminder_id = ? AND uid = ?";
            $stmt2 = $conn->prepare($upd);
            if ($stmt2) {
                $stmt2->bind_param('ssii', $title, $notify_datetime, $reminder_id, $uid);
                $stmt2->execute();
                $stmt2->close();
            }
        }
    } else {
        // Insert new reminder
        $sql_with_desc = "INSERT INTO reminder (uid, reminder_title, reminder_description, reminder_time, is_done) VALUES (?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($sql_with_desc);
        $saved = false;
        if ($stmt) {
            $stmt->bind_param('isss', $uid, $title, $description, $notify_datetime);
            $saved = $stmt->execute();
            $stmt->close();
        }
        if (!$saved) {
            $sql = "INSERT INTO reminder (uid, reminder_title, reminder_time, is_done) VALUES (?, ?, ?, 0)";
            $stmt2 = $conn->prepare($sql);
            if ($stmt2) {
                $stmt2->bind_param('iss', $uid, $title, $notify_datetime);
                $stmt2->execute();
                $stmt2->close();
            }
        }
    }

    header('Location: /taskdesk/frontend/remainder.php');
    exit;
}

header('Location: /taskdesk/frontend/remainder.php');
exit;
