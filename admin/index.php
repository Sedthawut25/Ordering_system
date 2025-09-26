<?php
require_once __DIR__ . '/../includes/header.php'; // header มีเฉพาะ Topbar
check_role(['Admin']);
?>

<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar (desktop) -->
    <aside class="col-lg-2 d-none d-lg-block sidebar">
      <div class="sidebar-title">เมนูจัดการ</div>
      <nav class="nav flex-column">
        <a class="nav-link <?= nav_active('/admin/index.php') ?>" href="/procurement_system/admin/index.php">
          <i class="bi bi-speedometer2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/admin/employees') ?>" href="/procurement_system/admin/employees.php">
          <i class="bi bi-people"></i> จัดการพนักงาน
        </a>
        <a class="nav-link <?= nav_active('/admin/product_types') ?>" href="/procurement_system/admin/product_types.php">
          <i class="bi bi-tags"></i> จัดการประเภทสินค้า
        </a>
        <a class="nav-link <?= nav_active('/admin/departments') ?>" href="/procurement_system/admin/departments.php">
          <i class="bi bi-diagram-3"></i> จัดการแผนก
        </a>
        <a class="nav-link <?= nav_active('/admin/payment_types') ?>" href="/procurement_system/admin/payment_types.php">
          <i class="bi bi-cash-coin"></i> จัดการประเภทการจ่าย
        </a>
        <a class="nav-link <?= nav_active('/admin/members') ?>" href="/procurement_system/admin/members.php">
          <i class="bi bi-truck"></i> ดูสมาชิกผู้ขาย
        </a>
      </nav>
    </aside>

    <!-- Offcanvas (mobile sidebar) ให้ปุ่มแฮมเบอร์เกอร์ใน header เรียกใช้ -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
          <i class="bi bi-bag-check-fill me-1"></i> เมนูระบบ
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body offcanvas-nav">
        <a class="nav-link <?= nav_active('/admin/index.php') ?>" href="/procurement_system/admin/index.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-speedometer2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/admin/employees') ?>" href="/procurement_system/admin/employees.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-people"></i> จัดการพนักงาน
        </a>
        <a class="nav-link <?= nav_active('/admin/product_types') ?>" href="/procurement_system/admin/product_types.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-tags"></i> จัดการประเภทสินค้า
        </a>
        <a class="nav-link <?= nav_active('/admin/departments') ?>" href="/procurement_system/admin/departments.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-diagram-3"></i> จัดการแผนก
        </a>
        <a class="nav-link <?= nav_active('/admin/payment_types') ?>" href="/procurement_system/admin/payment_types.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-cash-coin"></i> จัดการประเภทการจ่าย
        </a>
        <a class="nav-link <?= nav_active('/admin/members') ?>" href="/procurement_system/admin/members.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-truck"></i> ดูสมาชิกผู้ขาย
        </a>
      </div>
    </div>

    <!-- Main content (ต้องอยู่ใน row เดียวกัน) -->
    <main class="col-lg-10 app-content">
      <h1 class="mb-2">ยินดีต้อนรับ ผู้ดูแลระบบ</h1>
      <p>เลือกเมนูด้านซ้ายเพื่อจัดการข้อมูลระบบ</p>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>