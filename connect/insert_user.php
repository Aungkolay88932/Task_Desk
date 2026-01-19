<?php
require_once 'db_connect.php';

$username=$_POST['username'];
$email=$_POST['email'];
$password= password_hash($_POST['password'],PASSWORD_DEFAULT);


$stmt = $conn->prepare ("INSERT INTO user_info(user_name,email,user_password) VALUES(?,?,?)");
$stmt->bind_param("sss",$username,$email,$password);

if ($stmt->execute()){
    echo "Register successfully";


}else{
    echo "something went worng".$stmt->error;
}
$stmt->close();
$conn->close();

?>