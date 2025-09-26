<?php
// procurement_system/admin/members.php

/* ==================== Bootstrap (ห้ามมี output ก่อน header/redirect) ==================== */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

/* ❗ เดิมคุณ include header.php ก่อน check_role → ถ้า role ไม่ถูกต้องจะ redirect หลังมี output แล้ว ทำให้ header ส่งไม่ได้
   แก้: เช็คสิทธิ์ก่อนมี output เสมอ */
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
  header('Location: /procurement_system/login.php'); // ใช้ absolute path กันเพี้ยน
  exit;
}

/* ==================== Actions (ทำก่อนแสดงผลเสมอ) ==================== */
/* ❗ เดิม redirect ไป 'members.php' แบบ relative ยังพอได้ แต่แนะนำ absolute path ให้ชัวร์ โดยเฉพาะเวลามี base path อื่น */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $stmt = $pdo->prepare('UPDATE sellers SET status = "Inactive" WHERE id = ?');
  $stmt->execute([$id]);
  header('Location: /procurement_system/admin/members.php'); // ✅ absolute path
  exit;
}

/* ==================== Data ==================== */
$sellers = $pdo->query('SELECT * FROM sellers ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

/* ตอนนี้ค่อยดึง Topbar (มี output) */
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ❗ เดิมไม่มี container/row ครอบ sidebar + main → layout เพี้ยน/คอลัมน์ไม่ทำงาน
     ✅ เปิดเองในหน้านี้ เพราะ header ของคุณมีแค่ Topbar -->
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

    <!-- Offcanvas (mobile) – ปุ่มแฮมเบอร์เกอร์ใน header จะเปิดอันนี้ -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
          <i class="bi bi-bag-check-fill me-1"></i> เมนูระบบ
        </h5>
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

    <!-- Main content -->
    <!-- ❗ เดิมใช้ col-md-10 + ms-sm-auto (จาก template อื่น) ปนกับ col-lg-2 ของ sidebar → บาง breakpoint จัดคอลัมน์เพี้ยน
         ✅ ใช้ col-lg-10 ให้แมตช์กับ sidebar col-lg-2 -->
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">สมาชิกผู้ขาย</h2>

      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>รหัส</th>
              <th>ชื่อผู้ติดต่อ</th>
              <th>ชื่อบริษัท</th>
              <th>อีเมล</th>
              <th>เบอร์โทร</th>
              <th>สถานะ</th>
              <th>ปิดใช้งาน</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sellers as $seller): ?>
              <tr>
                <td><?= (int)$seller['id'] ?></td>
                <td><?= htmlspecialchars($seller['contact_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($seller['company_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($seller['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($seller['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <!-- ✅ ป้องกันช่องว่าง/NULL -->
                  <?= htmlspecialchars($seller['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td>
                  <?php if (($seller['status'] ?? 'Inactive') === 'Active'): ?>
                    <!-- ❗ เดิมลิงก์เป็น relative (โอเค) แต่ปุ่มใน offcanvas มือถือควรปิดเมนูก่อน → footer มีสคริปต์จัดการแล้ว -->
                    <a href="?delete=<?= (int)$seller['id'] ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('ยืนยันการปิดใช้งานผู้ขายคนนี้?')">
                      ปิดใช้งาน
                    </a>
                  <?php else: ?>
                    <span class="text-muted">—</span>
                  <?php endif; ?>
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