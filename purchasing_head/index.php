<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['PurchasingHead']);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing_head/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing_head/approve_purchases.php">อนุมัติใบสั่งซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">ยินดีต้อนรับ หัวหน้าแผนกจัดซื้อ</h2>
    <p>เลือกเมนูด้านซ้ายเพื่อตรวจสอบและอนุมัติใบสั่งซื้อ</p>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>