<?php
require_once 'db_connect.php';


$username = $_POST['username'];
$email    = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

/* ① 检查 email */
$emailStmt = $conn->prepare(
    "SELECT uid FROM user_info WHERE email = ?"
);
$emailStmt->bind_param("s", $email);
$emailStmt->execute();
$emailStmt->store_result();

if ($emailStmt->num_rows > 0) {
    echo "<script>
            alert('Email already Exist');
            window.location.href='/Task_Desk/frontend/signup.html';
          </script>";
    $emailStmt->close();
    $conn->close();
    exit;
}
$emailStmt->close();

/* ② 检查 username */
$userStmt = $conn->prepare(
    "SELECT uid FROM user_info WHERE user_name = ?"
);
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userStmt->store_result();

if ($userStmt->num_rows > 0) {
    echo "<script>
            alert('Username already exists, please choose another one');
            window.location.href='/Task_Desk/frontend/signup.html';
          </script>";
    $userStmt->close();
    $conn->close();
    exit;
}
$userStmt->close();

/* ③ 插入新用户 */
$insertStmt = $conn->prepare(
    "INSERT INTO user_info (user_name, email, user_password, login_type)
     VALUES (?, ?, ?,?)"
);
$login_type="normal";
$insertStmt->bind_param("ssss", $username, $email, $password,$login_type);

if ($insertStmt->execute()) {
    
     echo "<script>
            alert('Register Sucessfully');
            window.location.href='/Task_Desk/frontend/login.html';
          </script>";
    $userStmt->close();

} else {
    echo "Something went wrong: " . $insertStmt->error;
}

$insertStmt->close();
$conn->close();
?>
