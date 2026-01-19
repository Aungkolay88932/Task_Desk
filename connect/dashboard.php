<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header("Location: /Task_Desk/frontend/login.html");
    exit;
}

// 检查 access_token 是否快过期（假设 1 小时）
if (!isset($_SESSION['access_token']) || (time() - $_SESSION['token_created'] > 3500)) {
    
    // 用 refresh_token 换新 access_token
    $stmt = $conn->prepare("SELECT refresh_token FROM user_info WHERE uid=?");
    $stmt->bind_param("i", $_SESSION['uid']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $refresh_token = $result['refresh_token'];

    if (!$refresh_token) {
        // 没有 refresh_token → 强制登出
        session_unset();
        session_destroy();
        header("Location: /Task_Desk/frontend/login.html?msg=login_expired");
        exit;
    }

    // 请求新的 access_token
    $data = [
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'refresh_token' => $refresh_token,
        'grant_type' => 'refresh_token'
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded",
            'content' => http_build_query($data)
        ]
    ];

    $response = file_get_contents("https://oauth2.googleapis.com/token", false, stream_context_create($options));
    $new_token = json_decode($response, true);

    if (!isset($new_token['access_token'])) {
        // 刷新失败 → 强制登出
        session_unset();
        session_destroy();
        header("Location: /Task_Desk/frontend/register.html?msg=login_expired");
        exit;
    }

    // 更新 session
    $_SESSION['access_token'] = $new_token['access_token'];
    $_SESSION['token_created'] = time();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - TaskDesk</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>

    <!-- 登出按钮 -->
    <form method="post" action="logout.php">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
