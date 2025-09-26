<?php
// procurement_system/purchasing_head/index.php


if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'PurchasingHead') {
  header('Location: /procurement_system/login.php');
  exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
  <div class="row g-0">

    <div class="container-fluid">
      <div class="row g-0">

        <aside class="col-lg-2 d-none d-lg-block sidebar">
          <div class="sidebar-title">เมนูหัวหน้าจัดซื้อ</div>
          <nav class="nav flex-column">
            <a class="nav-link <?= nav_active('/purchasing_head/index.php') ?>" href="/procurement_system/purchasing_head/index.php">
              <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
            </a>
            <a class="nav-link <?= nav_active('/purchasing_head/approve_purchases') ?>" href="/procurement_system/purchasing_head/approve_purchases.php">
              <i class="bi bi-check2-square me-2"></i> อนุมัติใบสั่งซื้อ
            </a>
          </nav>
        </aside>

        <!-- Offcanvas (mobile) -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
              <i class="bi bi-bag-check-fill me-1"></i> เมนูหัวหน้าจัดซื้อ
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body offcanvas-nav">
            <a class="nav-link <?= nav_active('/purchasing_head/index.php') ?>" href="/procurement_system/purchasing_head/index.php" data-bs-dismiss="offcanvas">
              <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
            </a>
            <a class="nav-link <?= nav_active('/purchasing_head/approve_purchases') ?>" href="/procurement_system/purchasing_head/approve_purchases.php" data-bs-dismiss="offcanvas">
              <i class="bi bi-check2-square me-2"></i> อนุมัติใบสั่งซื้อ
            </a>
          </div>
        </div>


        <!-- Main -->
        <main class="col-lg-10 app-content">
          <h2 class="mb-3">ยินดีต้อนรับ หัวหน้าแผนกจัดซื้อ</h2>
          <p>เลือกเมนูด้านซ้ายเพื่อตรวจสอบและอนุมัติใบสั่งซื้อ</p>
        </main>

      </div>
    </div>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>