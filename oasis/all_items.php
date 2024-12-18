<?php
session_start();
include 'db.php';

// ตรวจสอบการ login
if (!isset($_SESSION['employee_name'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    die("<p class='text-danger'>การเชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error() . "</p>");
}

// ฟังก์ชันลบ Item โดยใช้ Prepared Statement
if (isset($_GET['delete'])) {
    $item_id = filter_var($_GET['delete'], FILTER_VALIDATE_INT); // ตรวจสอบว่าเป็น Integer หรือไม่

    if ($item_id) {
        $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ?");
        $stmt->bind_param("i", $item_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "ลบ Item สำเร็จ";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "เกิดข้อผิดพลาดในการลบ Item";
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "ID ไม่ถูกต้อง";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: all_items.php");
    exit();
}

// ดึงข้อมูล Items ทั้งหมด
$query = "SELECT * FROM items";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("<p class='text-danger'>เกิดข้อผิดพลาดในการดึงข้อมูล: " . mysqli_error($conn) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการ Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">รายการ Items ทั้งหมด</h2>

        <!-- แสดงข้อความแจ้งเตือน -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?>">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <!-- ตารางแสดงข้อมูล -->
        <table class="table table-bordered table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>ชื่อ Item</th>
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
                            // ตรวจสอบรูปภาพ
                            $image_path = (!empty($row['item_image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/oasis/" . $row['item_image']))
                                ? "http://localhost/oasis/" . htmlspecialchars($row['item_image'])
                                : "https://via.placeholder.com/100";

                            $created_at = !empty($row['created_at']) ? date("d/m/Y H:i", strtotime($row['created_at'])) : "ไม่ระบุ";
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo number_format($row['item_price'], 2); ?> บาท</td>
                            <td><img src="<?php echo $image_path; ?>" alt="Item Image" width="100"></td>
                            <td><?php echo htmlspecialchars($row['created_by']); ?></td>
                            <td><?php echo $created_at; ?></td>
                            <td>
                                <a href="edit_item.php?id=<?php echo $row['item_id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                <a href="all_items.php?delete=<?php echo $row['item_id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('คุณต้องการลบ Item นี้หรือไม่?');">ลบ</a>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
