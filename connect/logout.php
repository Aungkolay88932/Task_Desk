<?php
require_once __DIR__ . '/init_session.php';
session_destroy();
header("Location: /taskdesk/frontend/login.html");
