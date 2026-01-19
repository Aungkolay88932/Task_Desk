<?php
session_start();

$client_id = getenv('GOOGLE_CLIENT_ID');
$redirect_uri = "http://localhost/Task_Desk/connect/google_callback.php";
$scope = "email profile";

$params = [
    'response_type' => 'code',
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'scope' => $scope,
    'access_type' => 'offline',  // ✅ 获取 refresh_token
    'prompt' => 'consent'        // ✅ 强制每次显示授权，确保拿到 refresh_token
];

$url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query($params);

header("Location: $url");
exit;
