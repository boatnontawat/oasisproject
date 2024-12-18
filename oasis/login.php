<?php
session_start();
include 'db.php';

// ตรวจสอบการเข้าสู่ระบบ
if (isset($_SESSION['employee_name'])) {
    header("Location: home.php");
    exit();
}

// การเข้าสู่ระบบ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_name = $_POST['employee_name'];

    // ตรวจสอบข้อมูลผู้ใช้
    $query = "SELECT hospital_name FROM users WHERE employee_name = '$employee_name'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // บันทึกข้อมูล Session
        $_SESSION['employee_name'] = $employee_name;
        $_SESSION['hospital_name'] = $row['hospital_name'];

        // บันทึก log การเข้าสู่ระบบ
        $log_query = "INSERT INTO login_logs (employee_name) VALUES ('$employee_name')";
        mysqli_query($conn, $log_query); // เก็บข้อมูลการเข้าสู่ระบบ

        // Redirect ไปที่ home.php
        header("Location: home.php");
        exit();
    } else {
        echo "<p class='text-danger'>ไม่พบข้อมูลผู้ใช้</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <form method="POST" class="shadow p-4 rounded bg-light">
                <h2 class="text-center mb-4">เข้าสู่ระบบ</h2>

                <div class="mb-3">
                    <label for="employee_name" class="form-label">ชื่อพนักงาน:</label>
                    <input type="text" name="employee_name" id="employee_name" class="form-control" placeholder="กรอกชื่อพนักงาน" required>
                </div>

                <button type="submit" class="btn btn-success w-100">เข้าสู่ระบบ</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
