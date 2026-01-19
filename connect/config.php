<?php
session_start();

$host = "localhost";
$db   = "Task_Desk";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB Connection failed");
}
