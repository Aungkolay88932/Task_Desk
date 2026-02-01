<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/init_session.php';
require_once __DIR__ . '/db_connect.php';

$envFile = dirname(__DIR__) . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            // 同时设置到环境变量和超级全局变量中，确保万无一失
            putenv(trim($name) . "=" . trim($value));
            $_ENV[trim($name)] = trim($value);
        }
    }
} else {
    // 如果报错，说明路径真的写错了，或者权限不足
    die("无法找到 .env 文件，请检查路径: " . $envFile);
}

$client_id = getenv('GOOGLE_CLIENT_ID');

$client_secret = getenv('GOOGLE_CLIENT_SECRET');

$redirect_uri = "http://localhost/taskdesk/connect/google_callback.php";

// 如果没有 code，跳回登录
if (!isset($_GET['code'])) {
    header("Location: /taskdesk/connect/google_login.php");
    exit;
}

$code = $_GET['code'];

/* 1️⃣ 用 code 换 access token */
$token_url = "https://oauth2.googleapis.com/token";

$data = [
    'code' => $code,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code'
];

$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded",
        'content' => http_build_query($data)
    ]
];

$response = @file_get_contents($token_url, false, stream_context_create($options));
$token = json_decode($response, true);

if (!$token || !isset($token['access_token'])) {
    die("<h3>Can't get Google Access Token</h3><p>Please Check Your Internet or try again! <a href='google_login.php'>click</a></p>");
}

$access_token = $token['access_token'];
$refresh_token = isset($token['refresh_token']) ? $token['refresh_token'] : null;

/* 2️⃣ 用 token 获取用户信息，改成 cURL 并加容错 */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/oauth2/v2/userinfo?access_token=$access_token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$user_info_response = curl_exec($ch);

if(curl_errno($ch)){
    curl_close($ch);
    die("<h3>Can't Get Google User Information</h3><p>Internet fail：" . htmlspecialchars(curl_error($ch)) . "</p>");
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if($httpCode != 200){
    die("<h3>Can't Get Google User Information</h3><p>HTTP status: $httpCode</p>");
}

$user = json_decode($user_info_response, true);

if(!$user || empty($user['email']) || empty($user['name'])){
    die("<h3>Google return information not complete</h3><p>Cant login，pleas try again</p>");
}

$email = $user['email'];
$name = $user['name'];
$google_id = $user['id'];

/* 3️⃣ 查数据库是否已有用户（按 email） */
$stmt = $conn->prepare("SELECT uid FROM user_info WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // 新用户 → 注册（Google）
    $stmt = $conn->prepare(
        "INSERT INTO user_info (user_name, email, google_id, login_type, refresh_token)
         VALUES (?, ?, ?, 'google', ?)"
    );
    $stmt->bind_param("ssss", $name, $email, $google_id, $refresh_token);
    $stmt->execute();

    $user_id = $stmt->insert_id;
} else {
    // 老用户 → 直接登录
    $row = $result->fetch_assoc();
    $user_id = $row['uid'];

    if ($refresh_token) {
        $stmt = $conn->prepare("UPDATE user_info SET refresh_token=? WHERE uid=?");
        $stmt->bind_param("si", $refresh_token, $user_id);
        $stmt->execute();
    }
}

/* 4️⃣ 登录成功 → 写入 session（与 login.php 一致，只存 uid / user_name） */
$_SESSION['uid'] = $user_id;
$_SESSION['user_name'] = $name;

header("Location: /taskdesk/frontend/home.php");
exit;
