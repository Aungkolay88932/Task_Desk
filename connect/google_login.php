<?php
// 1. 加载环境变量 (必须在 getenv 之前)
$envFile = dirname(__DIR__) . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            putenv(trim($name) . "=" . trim($value));
        }
    }
} else {
    die("Error: .env file not found at $envFile");
}

// 2. 获取配置
$client_id = getenv('GOOGLE_CLIENT_ID');
$redirect_uri = "http://localhost/taskdesk/connect/google_callback.php";
$scope = "email profile";

// 3. 检查 ID 是否读取成功
if (!$client_id) {
    die("Error: GOOGLE_CLIENT_ID is empty. Please check your .env file.");
}

$params = [
    'response_type' => 'code',
    'client_id'     => $client_id,
    'redirect_uri'  => $redirect_uri,
    'scope'         => $scope,
    'access_type'   => 'offline',  // 获取 refresh_token 用于长效登录
    'prompt'        => 'consent'    // 强制显示授权界面
];

$url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query($params);

// 4. 跳转到 Google
header("Location: $url");
exit;