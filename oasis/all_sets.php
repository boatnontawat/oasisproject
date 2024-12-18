<?php
session_start();
include 'db.php';

// ตรวจสอบการ login
if (!isset($_SESSION['employee_name'])) {
    header("Location: login.php");
    exit();
}

// ฟังก์ชันลบ Set โดยใช้ Prepared Statement
if (isset($_GET['delete'])) {
    $set_id = filter_var($_GET['delete'], FILTER_VALIDATE_INT); // ตรวจสอบ ID
    if ($set_id) {
        $stmt = $conn->prepare("DELETE FROM sets WHERE set_id = ?");
        $stmt->bind_param("i", $set_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "ลบ Set สำเร็จ";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "เกิดข้อผิดพลาดในการลบ Set";
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "ID ไม่ถูกต้อง";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: all_sets.php");
    exit();
}

// ดึงข้อมูลทั้งหมดจากตาราง sets
$query = "SELECT * FROM sets";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการ Sets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">รายการ Sets ทั้งหมด</h2>

        <!-- แสดงข้อความแจ้งเตือน -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <!-- ตารางแสดงรายการ Sets -->
        <table class="table table-bordered table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>ชื่อ Set</th>
                    <th>ราคา</th>
                    <th>รูปภาพ</th>
                    <th>ผู้สร้าง</th>
                    <th>วันที่สร้าง</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php 
                            // ตรวจสอบ path รูปภาพ
                            $image_path = (!empty($row['set_image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/oasis/" . $row['set_image'])) 
                                          ? "http://localhost/oasis/" . htmlspecialchars($row['set_image']) 
                                          : "https://via.placeholder.com/100";

                            $created_by = $row['created_by'] ?: "ไม่ระบุ";
                            $created_at = $row['created_at'] ?: "ไม่ระบุ";
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['set_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['set_name']); ?></td>
                            <td><?php echo number_format($row['set_price'], 2); ?> บาท</td>
                            <td><img src="<?php echo $image_path; ?>" alt="Set Image" width="100"></td>
                            <td><?php echo htmlspecialchars($created_by); ?></td>
                            <td><?php echo htmlspecialchars($created_at); ?></td>
                            <td>
                                <a href="edit_set.php?id=<?php echo $row['set_id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                <a href="all_sets.php?delete=<?php echo $row['set_id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('คุณต้องการลบ Set นี้หรือไม่?');">ลบ</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">ยังไม่มีข้อมูล</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="text-center">
            <a href="create_set.php" class="btn btn-primary">สร้าง Set ใหม่</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
