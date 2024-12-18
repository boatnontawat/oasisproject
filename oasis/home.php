<?php 
session_start();
include 'db.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['employee_name'])) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูล Set ทั้งหมดจากฐานข้อมูล
$sets_query = "SELECT * FROM sets";
$sets_result = mysqli_query($conn, $sets_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการ Set</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['employee_name']); ?> (<?php echo htmlspecialchars($_SESSION['hospital_name']); ?>)</h2>
    <div class="text-end mb-3">
        <a href="create_set.php" class="btn btn-success">เพิ่ม Set ใหม่</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <h3>รายการ Set</h3>
    <div class="row">
        <?php while ($set = mysqli_fetch_assoc($sets_result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="<?php echo htmlspecialchars($set['set_image']); ?>" class="card-img-top" alt="Set Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($set['set_name']); ?></h5>
                        <p class="card-text">ราคาตลาด: <?php echo number_format($set['market_price'], 2); ?> บาท</p>
                        <p class="card-text text-success">ราคาของ Set: <?php echo number_format($set['set_price'], 2); ?> บาท</p>
                        <p class="card-text text-danger">ส่วนลด: <?php echo (int)$set['discount_percentage']; ?>%</p>
                        <a href="edit_set.php?id=<?php echo (int)$set['set_id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                        <a href="delete_set.php?id=<?php echo (int)$set['set_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ Set นี้?')">ลบ</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
