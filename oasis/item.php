<?php
session_start();
include 'db.php';

// ตรวจสอบว่า user login หรือไม่
if (!isset($_SESSION['employee_name'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    die("<p class='text-danger'>การเชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error() . "</p>");
}

// การจัดการฟอร์มการสร้าง item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $image = $_FILES['item_image'];

    // ตรวจสอบว่าอัปโหลดไฟล์หรือไม่
    if ($image['error'] === UPLOAD_ERR_OK) {
        $target_dir = "C:/xampp/htdocs/oasis/item/"; // โฟลเดอร์บนเซิร์ฟเวอร์
        $filename = basename($image["name"]);
        $target_file = $target_dir . $filename;

        // ตรวจสอบและย้ายไฟล์
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            // เก็บเส้นทางที่ใช้บนเว็บลงในฐานข้อมูล
            $web_path = "item/" . $filename;

            // บันทึกข้อมูลในฐานข้อมูล
            $query = "INSERT INTO items (item_name, item_image, item_price, created_by) 
                      VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssds", $item_name, $web_path, $item_price, $_SESSION['employee_name']);

            if ($stmt->execute()) {
                $_SESSION['message'] = "สร้าง Item สำเร็จ";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
                $_SESSION['message_type'] = "danger";
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "กรุณาอัปโหลดรูปภาพที่ถูกต้อง";
        $_SESSION['message_type'] = "danger";
    }

    // กลับไปหน้า all_items.php
    header("Location: all_items.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้าง Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="col-md-6 offset-md-3">
            <h2 class="text-center mb-4">สร้าง Item</h2>
            
            <!-- ฟอร์มสร้าง Item -->
            <form method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">
                <div class="mb-3">
                    <label for="item_name" class="form-label">ชื่อ Item:</label>
                    <input type="text" name="item_name" id="item_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="item_price" class="form-label">ราคา Item:</label>
                    <input type="number" name="item_price" id="item_price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="item_image" class="form-label">อัปโหลดรูปภาพ:</label>
                    <input type="file" name="item_image" id="item_image" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success w-100">สร้าง Item</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
