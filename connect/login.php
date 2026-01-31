<?php
require_once 'init_session.php';
require_once 'db_connect.php';

$email    = $_POST['email'];
$password = $_POST['password'];

/* ① 用 email 查用户 */
$stmt = $conn->prepare(
    "SELECT uid, user_name, user_password FROM user_info WHERE email = ?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

/* ② 判断 email 是否存在 */
if ($result->num_rows === 0) {
   echo "<script>alert('Email not registered'); window.location.href='/taskdesk/frontend/login.html';</script>";
    exit;
}

$user = $result->fetch_assoc();

/* ③ 验证密码 */
if (!password_verify($password, $user['user_password'])) {
   echo "<script>alert('Incorrect password'); window.location.href='/taskdesk/frontend/login.html';</script>";
    exit;
}

/* ④ 登录成功 → 设置 session */
$_SESSION['uid']       = $user['uid'];
$_SESSION['user_name'] = $user['user_name'];

/* ⑤ 跳转 */
header("Location: /taskdesk/frontend/home.php");
exit;
