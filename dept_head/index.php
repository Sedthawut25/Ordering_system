<?php
// procurement_system/dept_head/index.php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'DeptHead') {
  header('Location: /procurement_system/login.php');
  exit;
}


require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
  <div class="row g-0">

    <aside class="col-lg-2 d-none d-lg-block sidebar">
      <div class="sidebar-title">เมนูหัวหน้าแผนก</div>
      <nav class="nav flex-column">
        <a class="nav-link <?= nav_active('/dept_head/index.php') ?>" href="/procurement_system/dept_head/index.php">
          <i class="bi bi-speedometer2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/dept_head/approve_requests') ?>" href="/procurement_system/dept_head/approve_requests.php">
          <i class="bi bi-check2-square"></i> อนุมัติใบขอซื้อ
        </a>
      </nav>
    </aside>

    <!-- Offcanvas (mobile) – ใช้กับปุ่มแฮมเบอร์เกอร์ใน header -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
          <i class="bi bi-diagram-3-fill me-1"></i> เมนูหัวหน้าแผนก
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body offcanvas-nav">
        <a class="nav-link <?= nav_active('/dept_head/index.php') ?>" href="/procurement_system/dept_head/index.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-speedometer2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/dept_head/approve_requests') ?>" href="/procurement_system/dept_head/approve_requests.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-check2-square"></i> อนุมัติใบขอซื้อ
        </a>
      </div>
    </div>


    <!-- Main -->
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">ยินดีต้อนรับ หัวหน้าแผนก</h2>
      <p>โปรดเลือกเมนูด้านซ้ายเพื่ออนุมัติหรือปฏิเสธใบขอซื้อสินค้า</p>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>