<?php
session_start();
include 'db.php';

// ตรวจสอบการ login
if (!isset($_SESSION['n'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบการส่งค่า id
if (!isset($_GET['id'])) {
    echo "Error: ID is missing.";
    exit();
}

$item_id = intval($_GET['id']);

// ดึงข้อมูล item จากฐานข้อมูล
$query = "SELECT * FROM items WHERE item_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
} else {
    echo "Item not found.";
    exit();
}

// ตรวจสอบการส่งข้อมูลแก้ไข
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $item_price = floatval($_POST['item_price']);
    $item_image = $item['item_image']; // เก็บค่าเดิม

    // เช็คว่ามีการอัปโหลดรูปใหม่หรือไม่
    if (!empty($_FILES['item_image']['name'])) {
        $target_dir = "C:/xampp/htdocs/oasis/item/";
        $target_file = $target_dir . basename($_FILES['item_image']['name']);
        move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file);
        $item_image = $_FILES['item_image']['name']; // อัปเดตรูปใหม่
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $update_query = "UPDATE items SET item_name = ?, item_price = ?, item_image = ? WHERE item_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sdsi", $item_name, $item_price, $item_image, $item_id);
    $stmt->execute();

    // Redirect กลับไปที่หน้า all_item.php
    header("Location: all_item.php?success=Item updated successfully");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="header-left">
            <h3><?php echo htmlspecialchars($_SESSION['n']) . " - " . htmlspecialchars($_SESSION['hsn']); ?></h3>
        </div>
        <div class="header-center">
            <img src="logo.png" alt="Logo" class="logo">
        </div>
        <div class="header-right">
            <a href="logout.php" class="button">Logout</a>
        </div>
    </header>

    <div class="container">
        <h1>Edit Item</h1>
        <form method="POST" enctype="multipart/form-data">
            <label>Item Name:</label>
            <input type="text" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required><br>

            <label>Item Price:</label>
            <input type="number" step="0.01" name="item_price" value="<?php echo htmlspecialchars($item['item_price']); ?>" required><br>

            <label>Current Image:</label><br>
            <?php if (!empty($item['item_image'])): ?>
                <img src="C:/xampp/htdocs/oasis/item/<?php echo htmlspecialchars($item['item_image']); ?>" width="100" height="100"><br>
            <?php else: ?>
                No image uploaded.
            <?php endif; ?><br>

            <label>Upload New Image:</label>
            <input type="file" name="item_image"><br><br>

            <button type="submit">Update Item</button>
        </form>
    </div>
</body>
</html>
