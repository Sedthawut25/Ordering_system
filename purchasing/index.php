<?php
// procurement_system/purchasing/index.php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Purchasing') {
  header('Location: /procurement_system/login.php');
  exit;
}

/* ตอนนี้ค่อย include header (Topbar อย่างเดียว) */
require_once __DIR__ . '/../includes/header.php';
?>


<div class="container-fluid">
  <div class="row g-0">

    <aside class="col-lg-2 d-none d-lg-block sidebar">
      <div class="sidebar-title">เมนูจัดซื้อ</div>
      <nav class="nav flex-column">
        <a class="nav-link <?= nav_active('/purchasing/index.php') ?>" href="/procurement_system/purchasing/index.php">
          <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/purchasing/products') ?>" href="/procurement_system/purchasing/products.php">
          <i class="bi bi-box-seam me-2"></i> จัดการสินค้า
        </a>
        <a class="nav-link <?= nav_active('/purchasing/open_requests') ?>" href="/procurement_system/purchasing/open_requests.php">
          <i class="bi bi-megaphone me-2"></i> ใบขอซื้อเปิดประกาศ
        </a>
        <a class="nav-link <?= nav_active('/purchasing/compare_quotes') ?>" href="/procurement_system/purchasing/compare_quotes.php">
          <i class="bi bi-diagram-3 me-2"></i> เปรียบเทียบใบเสนอราคา
        </a>
        <a class="nav-link <?= nav_active('/purchasing/purchase_orders') ?>" href="/procurement_system/purchasing/purchase_orders.php">
          <i class="bi bi-receipt me-2"></i> ใบสั่งซื้อ
        </a>
        <a class="nav-link <?= nav_active('/purchasing/tax_reports') ?>" href="/procurement_system/purchasing/tax_reports.php">
          <i class="bi bi-file-earmark-text me-2"></i> รายงานภาษีซื้อ
        </a>
      </nav>
    </aside>

    <!-- Offcanvas (mobile) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
          <i class="bi bi-bag-check-fill me-1"></i> เมนูจัดซื้อ
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body offcanvas-nav">
        <a class="nav-link <?= nav_active('/purchasing/index.php') ?>" href="/procurement_system/purchasing/index.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/purchasing/products') ?>" href="/procurement_system/purchasing/products.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-box-seam me-2"></i> จัดการสินค้า
        </a>
        <a class="nav-link <?= nav_active('/purchasing/open_requests') ?>" href="/procurement_system/purchasing/open_requests.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-megaphone me-2"></i> ใบขอซื้อเปิดประกาศ
        </a>
        <a class="nav-link <?= nav_active('/purchasing/compare_quotes') ?>" href="/procurement_system/purchasing/compare_quotes.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-diagram-3 me-2"></i> เปรียบเทียบใบเสนอราคา
        </a>
        <a class="nav-link <?= nav_active('/purchasing/purchase_orders') ?>" href="/procurement_system/purchasing/purchase_orders.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-receipt me-2"></i> ใบสั่งซื้อ
        </a>
        <a class="nav-link <?= nav_active('/purchasing/tax_reports') ?>" href="/procurement_system/purchasing/tax_reports.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-file-earmark-text me-2"></i> รายงานภาษีซื้อ
        </a>
      </div>
    </div>


    <main class="col-lg-10 app-content">
      <h2 class="mb-3">ยินดีต้อนรับ พนักงานจัดซื้อ</h2>
      <p>เลือกเมนูด้านซ้ายเพื่อจัดการสินค้า ประกาศใบขอซื้อ เปรียบเทียบใบเสนอราคา สร้างใบสั่งซื้อ และรายงานภาษีซื้อ</p>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>