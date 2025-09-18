<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['DeptHead']);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/dept_head/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/dept_head/approve_requests.php">อนุมัติใบขอซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">ยินดีต้อนรับ หัวหน้าแผนก</h2>
    <p>โปรดเลือกเมนูด้านซ้ายเพื่ออนุมัติหรือปฏิเสธใบขอซื้อสินค้า</p>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>