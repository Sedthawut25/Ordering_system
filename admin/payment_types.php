<?php
// procurement_system/admin/payment_types.php

/* ================== Bootstrap (ห้ามมี output ก่อน redirect/header) ================== */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

/* ❗ เดิม include header.php ก่อน check_role → หากสิทธิ์ไม่ใช่ Admin แล้ว redirect หลังมี output จะ error
   ✅ แก้: ตรวจสิทธิ์ก่อนมี output เสมอ */
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
  header('Location: /procurement_system/login.php');
  exit;
}

/* ================== Actions (ทำก่อนแสดงผลเสมอ) ================== */
// ❗ เดิม redirect เป็น relative 'payment_types.php' ได้ แต่แนะนำ absolute path กันหลง base
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $stmt = $pdo->prepare('DELETE FROM payment_types WHERE id = ?');
  $stmt->execute([$id]);
  header('Location: /procurement_system/admin/payment_types.php'); // ✅ absolute
  exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  if ($name !== '') {
    $stmt = $pdo->prepare('INSERT INTO payment_types (name) VALUES (?)');
    $stmt->execute([$name]);
    $message = 'เพิ่มประเภทการจ่ายเรียบร้อย';
  } else {
    $message = 'กรุณากรอกชื่อประเภทการจ่าย';
  }
}

/* ================== Data ================== */
$types = $pdo->query('SELECT * FROM payment_types ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

/* ❗ เดิมไฟล์ include header.php ซ้ำ 2 ครั้ง (บรรทัดบนสุด และก่อน HTML) → ทำให้ HTML ซ้ำซ้อน/DOM เพี้ยน
   ✅ แก้: include header.php แค่ครั้งนี้ครั้งเดียว (หลังจากจัดการ action/data เสร็จ) */
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ❗ เดิมไม่มี container/row ครอบ sidebar + main และมี <div class="row g-0"></div> ที่ปิดทันที → layout เพี้ยน
     ✅ แก้: เปิด container-fluid + row g-0 ครอบ sidebar + main ให้ครบ -->
<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar (desktop) -->
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
    <!-- ❗ เดิมใช้ col-md-10 + ms-sm-auto ปะปนกับ sidebar col-lg-2 → บาง breakpoint เพี้ยน
         ✅ แก้: ใช้ col-lg-10 ให้คู่กับ sidebar col-lg-2 -->
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">จัดการประเภทการจ่าย</h2>

      <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <form method="post" class="mb-3 d-flex">
        <input type="text" name="name" class="form-control me-2" placeholder="ชื่อประเภทการจ่าย" required>
        <button type="submit" class="btn btn-primary">เพิ่ม</button>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>รหัส</th>
              <th>ชื่อประเภทการจ่าย</th>
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