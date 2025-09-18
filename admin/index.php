<?php
require_once __DIR__ . '/../includes/header.php';
// Only allow admin role
check_role(['Admin']);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/admin/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/admin/employees.php">จัดการพนักงาน</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/admin/product_types.php">จัดการประเภทสินค้า</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/admin/departments.php">จัดการแผนก</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/admin/payment_types.php">จัดการประเภทการจ่าย</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/admin/members.php">ดูสมาชิกผู้ขาย</a></li>
        </ul>
    </div>
</nav>

<!-- Main content -->
<main class="col-md-10 ms-sm-auto px-md-4">
    <h1>ยินดีต้อนรับ ผู้ดูแลระบบ</h1>
    <p>เลือกเมนูด้านซ้ายเพื่อจัดการข้อมูลระบบ</p>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>