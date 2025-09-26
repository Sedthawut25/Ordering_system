<?php
// procurement_system/admin/product_types.php

/* ================== Bootstrap (ห้ามมี output ก่อน redirect/header) ================== */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

/* ❗ เดิมคุณ include header.php ก่อน check_role → หาก role ไม่ถูกต้อง header redirect จะ fail
   ✅ แก้: ตรวจสิทธิ์ก่อนมี output */
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
  header('Location: /procurement_system/login.php');
  exit;
}

/* ================== Actions ================== */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $stmt = $pdo->prepare('DELETE FROM product_types WHERE id = ?');
  $stmt->execute([$id]);
  header('Location: /procurement_system/admin/product_types.php'); // ✅ absolute path
  exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  if ($name !== '') {
    $stmt = $pdo->prepare('INSERT INTO product_types (name) VALUES (?)');
    $stmt->execute([$name]);
    $message = 'เพิ่มประเภทสินค้าเรียบร้อย';
  } else {
    $message = 'กรุณากรอกชื่อประเภทสินค้า';
  }
}

/* ================== Data ================== */
$types = $pdo->query('SELECT * FROM product_types ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

/* ❗ เดิม include header.php ด้านบน → ทำให้มี output ก่อน redirect ได้
   ✅ แก้: include header.php หลังจาก action/data เสร็จ */
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar -->
    <aside class="col-lg-2 d-none d-lg-block sidebar">
      <div class="sidebar-title">เมนูจัดการ</div>
      <nav class="nav flex-column">
        <a class="nav-link <?= nav_active('/admin/index.php') ?>" href="/procurement_system/admin/index.php"><i class="bi bi-speedometer2"></i> แดชบอร์ด</a>
        <a class="nav-link <?= nav_active('/admin/employees') ?>" href="/procurement_system/admin/employees.php"><i class="bi bi-people"></i> จัดการพนักงาน</a>
        <a class="nav-link <?= nav_active('/admin/product_types') ?>" href="/procurement_system/admin/product_types.php"><i class="bi bi-tags"></i> จัดการประเภทสินค้า</a>
        <a class="nav-link <?= nav_active('/admin/departments') ?>" href="/procurement_system/admin/departments.php"><i class="bi bi-diagram-3"></i> จัดการแผนก</a>
        <a class="nav-link <?= nav_active('/admin/payment_types') ?>" href="/procurement_system/admin/payment_types.php"><i class="bi bi-cash-coin"></i> จัดการประเภทการจ่าย</a>
        <a class="nav-link <?= nav_active('/admin/members') ?>" href="/procurement_system/admin/members.php"><i class="bi bi-truck"></i> ดูสมาชิกผู้ขาย</a>
      </nav>
    </aside>

    <!-- Offcanvas (mobile) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel"><i class="bi bi-bag-check-fill me-1"></i> เมนูระบบ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body offcanvas-nav">
        <a class="nav-link <?= nav_active('/admin/index.php') ?>" href="/procurement_system/admin/index.php" data-bs-dismiss="offcanvas"><i class="bi bi-speedometer2"></i> แดชบอร์ด</a>
        <a class="nav-link <?= nav_active('/admin/employees') ?>" href="/procurement_system/admin/employees.php" data-bs-dismiss="offcanvas"><i class="bi bi-people"></i> จัดการพนักงาน</a>
        <a class="nav-link <?= nav_active('/admin/product_types') ?>" href="/procurement_system/admin/product_types.php" data-bs-dismiss="offcanvas"><i class="bi bi-tags"></i> จัดการประเภทสินค้า</a>
        <a class="nav-link <?= nav_active('/admin/departments') ?>" href="/procurement_system/admin/departments.php" data-bs-dismiss="offcanvas"><i class="bi bi-diagram-3"></i> จัดการแผนก</a>
        <a class="nav-link <?= nav_active('/admin/payment_types') ?>" href="/procurement_system/admin/payment_types.php" data-bs-dismiss="offcanvas"><i class="bi bi-cash-coin"></i> จัดการประเภทการจ่าย</a>
        <a class="nav-link <?= nav_active('/admin/members') ?>" href="/procurement_system/admin/members.php" data-bs-dismiss="offcanvas"><i class="bi bi-truck"></i> ดูสมาชิกผู้ขาย</a>
      </div>
    </div>

    <!-- Main -->
    <!-- ❗ เดิมใช้ col-md-10 + ms-sm-auto → layout เพี้ยนกับ sidebar col-lg-2
         ✅ แก้: ใช้ col-lg-10 ให้คู่กับ col-lg-2 -->
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">จัดการประเภทสินค้า</h2>

      <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <form method="post" class="mb-3 d-flex">
        <input type="text" name="name" class="form-control me-2" placeholder="ชื่อประเภทสินค้า" required>
        <button type="submit" class="btn btn-primary">เพิ่ม</button>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>รหัส</th>
              <th>ชื่อประเภทสินค้า</th>
              <th>ลบ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($types as $type): ?>
              <tr>
                <td><?= (int)$type['id'] ?></td>
                <td><?= htmlspecialchars($type['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <a href="?delete=<?= (int)$type['id'] ?>"
                    class="btn btn-sm btn-danger"
                    onclick="return confirm('ยืนยันการลบประเภทนี้?')">ลบ</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>