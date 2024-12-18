<?php
session_start();
include 'db.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['employee_name'])) {
    header("Location: login.php");
    exit();
}

// สร้าง Set ใหม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $set_name = $_POST['set_name'];
    $discount_percentage = $_POST['discount_percentage'];
    $market_price = $_POST['market_price']; // เพิ่มราคาตลาด
    $set_price = $_POST['set_price'];       // เพิ่มราคาของ Set

    // จัดการการอัปโหลดรูปภาพ
    $target_dir = "set/"; // โฟลเดอร์ปลายทางที่จัดเก็บรูป
    $file_name = basename($_FILES["set_image"]["name"]);
    $set_image = $target_dir . $file_name; // เส้นทางไฟล์

    // ตรวจสอบและย้ายไฟล์ที่อัปโหลด
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // สร้างโฟลเดอร์หากยังไม่มี
    }
    if (move_uploaded_file($_FILES["set_image"]["tmp_name"], $set_image)) {
        // เพิ่มข้อมูล Set ลงฐานข้อมูล
        $insert_set_query = "INSERT INTO sets (set_name, set_image, market_price, set_price, discount_percentage) 
                             VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_set_query);
        $stmt->bind_param("ssddi", $set_name, $set_image, $market_price, $set_price, $discount_percentage);
        $stmt->execute();
        $set_id = $stmt->insert_id;

        header("Location: add_item_to_set.php?set_id=$set_id");
        exit();
    } else {
        $error_message = "ไม่สามารถอัปโหลดรูปภาพได้!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้าง Set ใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>สร้าง Set ใหม่</h2>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="set_name" class="form-label">ชื่อ Set</label>
            <input type="text" name="set_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="set_image" class="form-label">รูปภาพ Set</label>
            <input type="file" name="set_image" class="form-control" accept="image/*" required>
        </div>
        <div class="mb-3">
            <label for="market_price" class="form-label">ราคาตลาด (บาท)</label>
            <input type="number" step="0.01" name="market_price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="discount_percentage" class="form-label">ส่วนลด (%)</label>
            <input type="number" name="discount_percentage" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="set_price" class="form-label">ราคาของ Set (บาท)</label>
            <input type="number" step="0.01" name="set_price" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">บันทึก</button>
        <a href="home.php" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
</body>
</html>
