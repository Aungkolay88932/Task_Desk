<?php
$host     = "localhost";
$username = "root";
$password = "";
$dbname   = "Task_Desk";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
