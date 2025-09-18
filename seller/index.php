<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Seller']);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/purchase_requests.php">ดูใบขอซื้อ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/quotations.php">จัดการใบเสนอราคา</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/purchase_orders.php">ดูใบสั่งซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">ยินดีต้อนรับ ผู้ขาย</h2>
    <p>เลือกเมนูด้านซ้ายเพื่อดูใบขอซื้อ จัดการใบเสนอราคา และตรวจสอบใบสั่งซื้อของท่าน</p>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>