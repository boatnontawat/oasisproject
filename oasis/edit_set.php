<?php
session_start();
include 'db.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['employee_name'])) {
    header("Location: login.php");
    exit();
}

$set_id = $_GET['id'] ?? 0;

// ดึงข้อมูล Set ปัจจุบัน
$set_query = "SELECT * FROM sets WHERE set_id = ?";
$stmt = $conn->prepare($set_query);
$stmt->bind_param("i", $set_id);
$stmt->execute();
$set_result = $stmt->get_result();
$set = $set_result->fetch_assoc();

if (!$set) {
    echo "Set ไม่พบ!";
    exit();
}

// ฟังก์ชันคำนวณราคารวมของ Items ใน Set
function calculate_set_price($conn, $set_id) {
    $query = "
        SELECT SUM(items.item_price) as total_price 
        FROM set_items 
        JOIN items ON set_items.item_id = items.item_id 
        WHERE set_items.set_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $set_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_price'] ?? 0;
}

// คำนวณราคารวมจาก Items ใน Set
$total_item_price = calculate_set_price($conn, $set_id);

// อัปเดตข้อมูล Set
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_set'])) {
    $set_name = $_POST['set_name'];
    $market_price = $_POST['market_price'];
    $discount_percentage = $_POST['discount_percentage'];
    $set_price = $_POST['set_price']; // ราคาที่ผู้ใช้กรอกเอง

    // อัปโหลดรูปภาพใหม่
    $target_dir = "set/";
    if (!empty($_FILES['set_image']['name'])) {
        $file_name = basename($_FILES['set_image']['name']);
        $set_image = $target_dir . $file_name;
        move_uploaded_file($_FILES["set_image"]["tmp_name"], $set_image);
    } else {
        $set_image = $set['set_image']; // ใช้รูปเดิม
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $update_query = "UPDATE sets SET set_name = ?, set_image = ?, market_price = ?, set_price = ?, discount_percentage = ? WHERE set_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssddii", $set_name, $set_image, $market_price, $set_price, $discount_percentage, $set_id);
    $stmt->execute();

    header("Location: edit_set.php?id=$set_id");
    exit();
}

// ดึง Items ใน Set
$set_items_query = "
    SELECT set_items.item_id, items.item_name, items.item_price, items.item_image
    FROM set_items
    JOIN items ON set_items.item_id = items.item_id
    WHERE set_items.set_id = ?";
$stmt = $conn->prepare($set_items_query);
$stmt->bind_param("i", $set_id);
$stmt->execute();
$set_items_result = $stmt->get_result();

// ดึงรายการ Items ทั้งหมด
$all_items_query = "SELECT * FROM items";
$all_items_result = mysqli_query($conn, $all_items_query);

// เพิ่ม Item เข้า Set
if (isset($_POST['add_item'])) {
    $item_id = $_POST['item_id'];
    $insert_item_query = "INSERT INTO set_items (set_id, item_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_item_query);
    $stmt->bind_param("ii", $set_id, $item_id);
    $stmt->execute();

    header("Location: edit_set.php?id=$set_id");
    exit();
}

// ลบ Item จาก Set
if (isset($_GET['delete_item_id'])) {
    $item_id = $_GET['delete_item_id'];
    $delete_item_query = "DELETE FROM set_items WHERE set_id = ? AND item_id = ?";
    $stmt = $conn->prepare($delete_item_query);
    $stmt->bind_param("ii", $set_id, $item_id);
    $stmt->execute();

    header("Location: edit_set.php?id=$set_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไข Set</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>แก้ไข Set</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="set_name" class="form-label">ชื่อ Set</label>
            <input type="text" name="set_name" class="form-control" value="<?php echo htmlspecialchars($set['set_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="set_image" class="form-label">รูปภาพ Set</label>
            <input type="file" name="set_image" class="form-control">
            <?php if (!empty($set['set_image'])): ?>
                <img src="<?php echo $set['set_image']; ?>" width="150" class="mt-2">
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="market_price" class="form-label">ราคาตลาด (บาท)</label>
            <input type="number" step="0.01" name="market_price" class="form-control" value="<?php echo $set['market_price']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="set_price" class="form-label">ราคาของ Set (คำนวณได้: <?php echo number_format($total_item_price, 2); ?> บาท)</label>
            <input type="number" step="0.01" name="set_price" class="form-control" value="<?php echo $set['set_price']; ?>">
        </div>
        <div class="mb-3">
            <label for="discount_percentage" class="form-label">ส่วนลด (%)</label>
            <input type="number" name="discount_percentage" class="form-control" value="<?php echo $set['discount_percentage']; ?>" required>
        </div>
        <button type="submit" name="update_set" class="btn btn-primary">บันทึกการแก้ไข</button>
        <a href="home.php" class="btn btn-secondary">กลับ</a>
    </form>

    <hr>

    <h4>เพิ่ม Item เข้า Set</h4>
    <form method="POST">
        <select name="item_id" class="form-select mb-3">
            <?php while ($item = mysqli_fetch_assoc($all_items_result)): ?>
                <option value="<?php echo $item['item_id']; ?>">
                    <?php echo htmlspecialchars($item['item_name']); ?> - <?php echo number_format($item['item_price'], 2); ?> บาท
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="add_item" class="btn btn-success">เพิ่ม Item</button>
    </form>

    <h4 class="mt-4">รายการ Items ใน Set</h4>
    <ul class="list-group">
        <?php while ($item = $set_items_result->fetch_assoc()): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?php echo htmlspecialchars($item['item_name']); ?></strong><br>
                    <img src="<?php echo htmlspecialchars($item['item_image']); ?>" width="50">
                    <?php echo number_format($item['item_price'], 2); ?> บาท
                </div>
                <a href="?id=<?php echo $set_id; ?>&delete_item_id=<?php echo $item['item_id']; ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('ยืนยันการลบ Item นี้ออกจาก Set?');">ลบ</a>
            </li>
        <?php endwhile; ?>
    </ul>
</div>
</body>
</html>
