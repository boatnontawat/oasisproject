<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "project_oasis";

// สร้างการเชื่อมต่อ
$conn = mysqli_connect($servername, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
