<?php
// procurement_system/employee/low_stock.php

/* ================= Bootstrap (อย่าให้มี output ก่อน redirect/header) ================= */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

/* ❗ เดิม include header.php ก่อน check_role → ถ้า role ไม่ใช่ Employee จะ redirect หลังมี output แล้วพัง
   ✅ แก้: เช็ค role ก่อน */
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
  header('Location: /procurement_system/login.php');
  exit;
}

/* ================= Data ================= */
$stmt = $pdo->query('
    SELECT p.id, p.name, p.quantity, p.min_stock
    FROM products p
    WHERE p.quantity < p.min_stock
    ORDER BY p.name
');
$lowStock = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ตอนนี้ค่อย include header.php (Topbar) */
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar (desktop) -->
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
    <!-- ❗ เดิมใช้ col-md-10 + ms-sm-auto → layout ไม่แมตช์กับ sidebar col-lg-2
         ✅ แก้: ใช้ col-lg-10 ให้คู่กับ col-lg-2 -->
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">รายการสินค้าที่ต่ำกว่าคงคลังขั้นต่ำ</h2>

      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>รหัสสินค้า</th>
              <th>ชื่อสินค้า</th>
              <th>จำนวนคงเหลือ</th>
              <th>ขั้นต่ำที่ควรมี</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lowStock as $product): ?>
              <tr>
                <td><?= (int)$product['id'] ?></td>
                <td><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int)$product['quantity'] ?></td>
                <td><?= (int)$product['min_stock'] ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($lowStock)): ?>
              <tr>
                <td colspan="4" class="text-center">ไม่มีสินค้าใกล้หมด</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>