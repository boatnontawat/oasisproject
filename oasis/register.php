<?php
session_start();
include 'db.php';

// ตรวจสอบการเข้าสู่ระบบ
if (isset($_SESSION['employee_name'])) {
    header("Location: home.php");
    exit();
}

// การสมัครสมาชิก
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์ม
    $hospital_name = mysqli_real_escape_string($conn, $_POST['hospital_name']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $employee_name = mysqli_real_escape_string($conn, $_POST['employee_name']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    
    // การตรวจสอบอีเมล์
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        echo "<p class='text-danger'>กรุณากรอกอีเมล์ให้ถูกต้อง</p>";
    } else {
        // ตรวจสอบอีเมล์ที่มีอยู่แล้วในระบบ
        $check_email_query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email_query);
        if (mysqli_num_rows($result) > 0) {
            echo "<p class='text-danger'>อีเมล์นี้ถูกใช้งานแล้ว</p>";
        } else {
            // SQL Query สำหรับเพิ่มข้อมูลผู้ใช้ (ใช้คำสั่ง prepared statement เพื่อป้องกัน SQL Injection)
            $query = "INSERT INTO users (hospital_name, department, employee_name, phone_number, email) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssss", $hospital_name, $department, $employee_name, $phone_number, $email);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['employee_name'] = $employee_name;
                $_SESSION['hospital_name'] = $hospital_name;
                $_SESSION['department'] = $department; // เก็บแผนกใน session
                header("Location: home.php");
                exit();
            } else {
                echo "<p class='text-danger'>เกิดข้อผิดพลาดในการสมัครสมาชิก</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <form method="POST" class="shadow p-4 rounded bg-light">
                <h2 class="text-center mb-4">สมัครสมาชิก</h2>

                <div class="mb-3">
                    <label for="hospital_name" class="form-label">ชื่อโรงพยาบาล:</label>
                    <input type="text" name="hospital_name" id="hospital_name" class="form-control" placeholder="ชื่อโรงพยาบาล" required>
                </div>

                <!-- เพิ่มฟิลด์สำหรับแผนก -->
                <div class="mb-3">
                    <label for="department" class="form-label">แผนก:</label>
                    <input type="text" name="department" id="department" class="form-control" placeholder="แผนก" required>
                </div>
                
                <div class="mb-3">
                    <label for="employee_name" class="form-label">ชื่อพนักงาน:</label>
                    <input type="text" name="employee_name" id="employee_name" class="form-control" placeholder="ชื่อพนักงาน" required>
                </div>

                <div class="mb-3">
                    <label for="phone_number" class="form-label">เบอร์โทร:</label>
                    <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="เบอร์โทร" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">อีเมล์ (ถ้ามี):</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="อีเมล์ (ถ้ามี)">
                </div>

                

                <button type="submit" class="btn btn-success w-100">สมัครสมาชิก</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
