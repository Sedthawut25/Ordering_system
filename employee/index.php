<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Employee']);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/employee/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/employee/purchase_requests.php">จัดการขอซื้อสินค้า</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/employee/low_stock.php">สินค้าใกล้หมด</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">ยินดีต้อนรับ พนักงาน</h2>
    <p>เลือกเมนูด้านซ้ายเพื่อจัดการคำขอซื้อสินค้า หรือดูรายการสินค้าที่ต่ำกว่าคงคลังขั้นต่ำ</p>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>