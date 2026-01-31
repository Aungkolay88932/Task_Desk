<?php
require_once __DIR__ . '/init_session.php';
if (!isset($_SESSION['uid'])) {
    header("Location: /taskdesk/frontend/login.html");
    exit;
}
