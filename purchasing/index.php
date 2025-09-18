<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Purchasing']);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/products.php">จัดการสินค้า</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/open_requests.php">ใบขอซื้อเปิดประกาศ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/compare_quotes.php">เปรียบเทียบใบเสนอราคา</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/purchase_orders.php">ใบสั่งซื้อ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/tax_reports.php">รายงานภาษีซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">ยินดีต้อนรับ พนักงานจัดซื้อ</h2>
    <p>เลือกเมนูด้านซ้ายเพื่อจัดการสินค้า ประกาศใบขอซื้อ เปรียบเทียบใบเสนอราคา สร้างใบสั่งซื้อ และรายงานภาษีซื้อ</p>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>