<?php
// procurement_system/employee/index.php

/* ================== Bootstrap (อย่าให้มี output ก่อน redirect/header) ================== */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/* ❗ เดิม include header.php ก่อน check_role → ถ้า role ไม่ตรงจะ redirect หลังมี output
      ✅ แก้: ตรวจสิทธิ์ก่อนมี output เสมอ */
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
  header('Location: /procurement_system/login.php');
  exit;
}

/* ตอนนี้ค่อย include header (Topbar) */
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar (desktop) -->
    <!-- ❗ เดิมใช้ col-md-2 → ไปชนกับ layout หน้าอื่น
         ✅ ใช้ col-lg-2 ให้คู่กับ main col-lg-10 -->
    <aside class="col-lg-2 d-none d-lg-block sidebar">
      <div class="sidebar-title">เมนูพนักงาน</div>
      <nav class="nav flex-column">
        <a class="nav-link <?= nav_active('/employee/index.php') ?>" href="/procurement_system/employee/index.php">
          <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/employee/purchase_requests') ?>" href="/procurement_system/employee/purchase_requests.php">
          <i class="bi bi-file-text me-2"></i> จัดการขอซื้อสินค้า
        </a>
        <a class="nav-link <?= nav_active('/employee/low_stock') ?>" href="/procurement_system/employee/low_stock.php">
          <i class="bi bi-box-seam me-2"></i> สินค้าใกล้หมด
        </a>
      </nav>
    </aside>

    <!-- Offcanvas (mobile) – ใช้กับปุ่มแฮมเบอร์เกอร์ใน header -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
          <i class="bi bi-person-badge-fill me-1"></i> เมนูพนักงาน
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body offcanvas-nav">
        <a class="nav-link <?= nav_active('/employee/index.php') ?>" href="/procurement_system/employee/index.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/employee/purchase_requests') ?>" href="/procurement_system/employee/purchase_requests.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-file-text me-2"></i> จัดการขอซื้อสินค้า
        </a>
        <a class="nav-link <?= nav_active('/employee/low_stock') ?>" href="/procurement_system/employee/low_stock.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-box-seam me-2"></i> สินค้าใกล้หมด
        </a>
      </div>
    </div>


    <!-- Main -->
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">ยินดีต้อนรับ พนักงาน</h2>
      <p>เลือกเมนูด้านซ้ายเพื่อจัดการคำขอซื้อสินค้า หรือดูรายการสินค้าที่ต่ำกว่าคงคลังขั้นต่ำ</p>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>