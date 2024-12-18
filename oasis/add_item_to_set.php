<?php
session_start();
include 'db.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['employee_name'])) {
    header("Location: login.php");
    exit();
}

// ดึง set_id จาก URL
$set_id = (int)$_GET['set_id'];

// ดึงรายการ Items ทั้งหมดจากฐานข้อมูล
$items_query = "SELECT * FROM items";
$items_result = mysqli_query($conn, $items_query);

// บันทึกรายการ Items เข้า Set พร้อมจำนวน
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_items = $_POST['selected_items']; // Array [item_id => quantity]

    // เพิ่ม Items ที่เลือกลงในตาราง set_items
    foreach ($selected_items as $item_id => $quantity) {
        for ($i = 0; $i < $quantity; $i++) {
            $insert_item_query = "INSERT INTO set_items (set_id, item_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_item_query);
            $stmt->bind_param("ii", $set_id, $item_id);
            $stmt->execute();
        }
    }

    // เปลี่ยนเส้นทางไปหน้า all_sets.php
    header("Location: all_sets.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่ม Item เข้า Set</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>เพิ่ม Item เข้า Set</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">เลือก Items</label>
            <div class="list-group" id="item-list">
                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <?php echo htmlspecialchars($item['item_name']) . " - " . number_format($item['item_price'], 2) . " บาท"; ?>
                        </div>
                        <button type="button" 
                                class="btn btn-sm btn-primary"
                                onclick="addItem(<?php echo $item['item_id']; ?>, '<?php echo htmlspecialchars($item['item_name']); ?>')">
                            เพิ่ม
                        </button>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- แสดงรายการ Items ที่เลือก -->
        <div class="mb-3">
            <h5>รายการ Items ที่เลือก:</h5>
            <ul id="selected-items-list" class="list-group"></ul>
        </div>

        <!-- ฟอร์มซ่อนสำหรับส่งข้อมูล -->
        <input type="hidden" name="selected_items" id="selected-items-input">

        <button type="submit" class="btn btn-success">บันทึก</button>
        <a href="all_sets.php" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>

<script>
    let selectedItems = {}; // เก็บรายการ items และจำนวนที่เลือก

    function addItem(itemId, itemName) {
        if (selectedItems[itemId]) {
            selectedItems[itemId]++; // เพิ่มจำนวน
        } else {
            selectedItems[itemId] = 1; // เพิ่มครั้งแรก
        }

        updateSelectedItemsList();
    }

    function updateSelectedItemsList() {
        const selectedItemsList = document.getElementById('selected-items-list');
        const selectedItemsInput = document.getElementById('selected-items-input');
        selectedItemsList.innerHTML = ''; // เคลียร์รายการก่อน

        for (const [itemId, quantity] of Object.entries(selectedItems)) {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.textContent = `Item ID: ${itemId} - จำนวน: ${quantity}`;
            
            // ปุ่มลบ
            const removeBtn = document.createElement('button');
            removeBtn.className = 'btn btn-sm btn-danger';
            removeBtn.textContent = 'ลบ';
            removeBtn.onclick = () => removeItem(itemId);

            li.appendChild(removeBtn);
            selectedItemsList.appendChild(li);
        }

        // แปลงข้อมูลเป็น JSON สำหรับส่งไปยัง PHP
        selectedItemsInput.value = JSON.stringify(selectedItems);
    }

    function removeItem(itemId) {
        if (selectedItems[itemId]) {
            delete selectedItems[itemId];
            updateSelectedItemsList();
        }
    }
</script>
</body>
</html>
