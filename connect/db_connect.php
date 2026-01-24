<?php
$servername="localhost";
$username="root";
$password="";
$dbname="Task_Desk";

$conn=new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
    die("fail connect:".$conn->connect_error);
}



?>
