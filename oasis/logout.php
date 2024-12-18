<?php
session_start();
session_unset(); // เคลียร์ค่า Session ทั้งหมด
session_destroy(); // ทำลาย Session
header("Location: login.php");
exit();
?>
