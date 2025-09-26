<?php
// procurement_system/admin/departments.php

// ---- Bootstrap (ไม่มี output) ----
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

// role check ก่อน output ใด ๆ
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
  header('Location: /procurement_system/login.php');
  exit;
}

$message = '';
$editing = null;

/* =============== Helpers =============== */
function get_role_id(PDO $pdo, string $roleName): ?int
{
  try {
    $s = $pdo->prepare('SELECT id FROM roles WHERE name=? LIMIT 1');
    $s->execute([$roleName]);
    $rid = $s->fetchColumn();
    return $rid ? (int)$rid : null;
  } catch (Throwable $e) {
    return null;
  }
}

/** sync role_id ของพนักงานในแผนกให้ตรง base_role/head_role_name */
function sync_department_roles(PDO $pdo, int $deptId): void
{
  $s = $pdo->prepare('
        SELECT d.head_id, dr.name AS base_role, dr.head_role_name
        FROM departments d
        JOIN department_roles dr ON dr.id = d.base_role_id
        WHERE d.id=?');
  $s->execute([$deptId]);
  $cfg = $s->fetch(PDO::FETCH_ASSOC);
  if (!$cfg) return;

  $base      = $cfg['base_role'] ?? 'Employee';
  $headName  = $cfg['head_role_name'] ?? 'DeptHead';
  $headEmpId = (int)($cfg['head_id'] ?? 0);

  $baseId = get_role_id($pdo, $base);
  $headId = get_role_id($pdo, $headName);

  if ($baseId) {
    $pdo->prepare('UPDATE employees SET role_id=? WHERE dept_id=?')->execute([$baseId, $deptId]);
  }
  if ($headEmpId && $headId) {
    $pdo->prepare('UPDATE employees SET role_id=? WHERE id=?')->execute([$headId, $headEmpId]);
  }
}

/* =============== Actions (ทำก่อนแสดงผล) =============== */
// ลบแผนก
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $pdo->prepare('DELETE FROM departments WHERE id=?')->execute([$id]);
  header('Location: /procurement_system/admin/departments.php'); // redirect ได้จริงเพราะยังไม่ output
  exit;
}

// โหลดเพื่อแก้ไข
if (isset($_GET['edit'])) {
  $id = (int)$_GET['edit'];
  $st = $pdo->prepare('SELECT * FROM departments WHERE id=?');
  $st->execute([$id]);
  $editing = $st->fetch(PDO::FETCH_ASSOC);
  if (!$editing) $message = 'ไม่พบแผนกที่ต้องการแก้ไข';
}

/* ---- เพิ่มชนิดแผนกใหม่จากโมดัล ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_dept_role'])) {
  $name = trim($_POST['new_role_name'] ?? '');
  $head = $_POST['new_head_role'] ?? 'DeptHead';
  if ($name === '') {
    $message = 'กรุณากรอกชื่อชนิดแผนก';
  } else {
    try {
      $ins = $pdo->prepare('INSERT INTO department_roles (name, head_role_name) VALUES (?, ?)');
      $ins->execute([$name, $head]);
      $message = 'เพิ่มชนิดแผนกสำเร็จ: ' . htmlspecialchars($name);
    } catch (Throwable $e) {
      $message = 'เพิ่มชนิดแผนกไม่สำเร็จ (อาจมีชื่อซ้ำ)';
    }
  }
}

/* ---- เพิ่ม/อัปเดตแผนก ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['create_dept_role'])) {
  if (isset($_POST['update_id'])) {
    // UPDATE
    $id          = (int)$_POST['update_id'];
    $name        = trim($_POST['name'] ?? '');
    $base_role_id = $_POST['base_role_id'] !== '' ? (int)$_POST['base_role_id'] : null;
    $head_id     = $_POST['head_id'] !== '' ? (int)$_POST['head_id'] : null;

    if ($name === '' || !$base_role_id) {
      $message = 'กรุณากรอกชื่อแผนกและเลือกชนิดแผนก';
    } else {
      if ($head_id) {
        $chk = $pdo->prepare('SELECT 1 FROM employees WHERE id=? AND dept_id=?');
        $chk->execute([$head_id, $id]);
        if (!$chk->fetchColumn()) $message = 'หัวหน้าที่เลือกไม่ได้อยู่ในแผนกนี้';
      }
      if ($message === '') {
        $r = $pdo->prepare('SELECT name FROM department_roles WHERE id=?');
        $r->execute([$base_role_id]);
        $roleName = $r->fetchColumn() ?: 'Employee';

        $up = $pdo->prepare('UPDATE departments SET name=?, base_role=?, base_role_id=?, head_id=? WHERE id=?');
        $up->execute([$name, $roleName, $base_role_id, $head_id, $id]);

        sync_department_roles($pdo, $id);
        $message = 'อัปเดตแผนกเรียบร้อย';
        $editing = null;
      }
    }
  } else {
    // INSERT
    $name         = trim($_POST['name'] ?? '');
    $base_role_id = $_POST['base_role_id'] !== '' ? (int)$_POST['base_role_id'] : null;

    if ($name === '' || !$base_role_id) {
      $message = 'กรุณากรอกชื่อแผนกและเลือกชนิดแผนก';
    } else {
      $r = $pdo->prepare('SELECT name FROM department_roles WHERE id=?');
      $r->execute([$base_role_id]);
      $roleName = $r->fetchColumn() ?: 'Employee';

      $ins = $pdo->prepare('INSERT INTO departments (name, base_role, base_role_id, head_id) VALUES (?, ?, ?, NULL)');
      $ins->execute([$name, $roleName, $base_role_id]);
      $message = 'เพิ่มแผนกเรียบร้อย (กำหนดหัวหน้าได้ภายหลังเมื่อมีพนักงานในแผนก)';
    }
  }
}

/* =============== Data =============== */
$sql = 'SELECT d.id, d.name, d.base_role, e.name AS head_name
        FROM departments d
        LEFT JOIN employees e ON e.id = d.head_id
        ORDER BY d.id';
$departments = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$employeesInDept = [];
if ($editing) {
  $q = $pdo->prepare('SELECT id, name FROM employees WHERE dept_id=? ORDER BY name');
  $q->execute([(int)$editing['id']]);
  $employeesInDept = $q->fetchAll(PDO::FETCH_ASSOC);
}
$deptRoleOptions = $pdo->query('SELECT id, name, head_role_name FROM department_roles ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

// ---- เริ่มแสดงผล (Topbar) ----
require_once __DIR__ . '/../includes/header.php';
?>

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
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">จัดการแผนก</h2>

      <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <?php if ($editing): ?>
        <!-- ฟอร์มแก้ไข -->
        <div class="card mb-4 border-primary">
          <div class="card-header bg-primary text-white"><strong>แก้ไขแผนก #<?= (int)$editing['id'] ?></strong></div>
          <div class="card-body">
            <form method="post">
              <input type="hidden" name="update_id" value="<?= (int)$editing['id'] ?>">
              <div class="row g-3 align-items-end">
                <div class="col-md-4">
                  <label class="form-label">ชื่อแผนก</label>
                  <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($editing['name']) ?>" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">หัวหน้าแผนก</label>
                  <select class="form-select" name="head_id">
                    <option value="">-- เลือกหัวหน้า (เฉพาะพนักงานในแผนกนี้) --</option>
                    <?php foreach ($employeesInDept as $emp): ?>
                      <option value="<?= $emp['id'] ?>" <?= ((int)($editing['head_id'] ?? 0) === (int)$emp['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($emp['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <?php if (empty($employeesInDept)): ?>
                    <div class="form-text text-danger">ยังไม่มีพนักงานสังกัดแผนกนี้ กรุณาไปเพิ่มพนักงานให้แผนกก่อน</div>
                  <?php endif; ?>
                </div>
                <div class="col-md-3">
                  <label class="form-label">ชนิดแผนก (base_role)</label>
                  <div class="input-group">
                    <select class="form-select" name="base_role_id" required>
                      <?php foreach ($deptRoleOptions as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ((int)($editing['base_role_id'] ?? 0) === (int)$r['id'] ? 'selected' : '') ?>>
                          <?= htmlspecialchars($r['name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addDeptRoleModal">+</button>
                  </div>
                </div>
                <div class="col-md-1">
                  <a href="departments.php" class="btn btn-outline-secondary">ยกเลิก</a>
                  <button class="btn btn-success ms-1">บันทึก</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      <?php else: ?>
        <!-- ฟอร์มเพิ่ม -->
        <div class="card mb-4">
          <div class="card-header bg-light"><strong>เพิ่มแผนก</strong></div>
          <div class="card-body">
            <form method="post">
              <div class="row g-3 align-items-end">
                <div class="col-md-5">
                  <label class="form-label">ชื่อแผนก</label>
                  <input type="text" class="form-control" name="name" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">ชนิดแผนก (base_role)</label>
                  <div class="input-group">
                    <select class="form-select" name="base_role_id" required>
                      <option value="">-- เลือกชนิดแผนก --</option>
                      <?php foreach ($deptRoleOptions as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addDeptRoleModal">+</button>
                  </div>
                </div>
                <div class="col-md-3">
                  <button class="btn btn-primary">เพิ่มแผนก</button>
                </div>
              </div>
              <div class="form-text mt-2">
                เพิ่มแผนกเสร็จแล้ว ไปกำหนดพนักงานสังกัดแผนกก่อน จากนั้นกลับมา “แก้ไขแผนก” เพื่อเลือกหัวหน้า
              </div>
            </form>
          </div>
        </div>
      <?php endif; ?>

      <!-- ตารางแผนก -->
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>รหัส</th>
              <th>ชื่อแผนก</th>
              <th>หัวหน้าแผนก</th>
              <th>ชนิดแผนก (base_role)</th>
              <th style="width:160px">จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($departments as $d): ?>
              <tr>
                <td><?= $d['id'] ?></td>
                <td><?= htmlspecialchars($d['name']) ?></td>
                <td><?= htmlspecialchars($d['head_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($d['base_role']) ?></td>
                <td>
                  <a class="btn btn-sm btn-warning" href="?edit=<?= $d['id'] ?>">แก้ไข</a>
                  <a class="btn btn-sm btn-danger" href="?delete=<?= $d['id'] ?>" onclick="return confirm('ยืนยันการลบแผนกนี้?')">ลบ</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<!-- Modal: เพิ่มชนิดแผนก -->
<div class="modal fade" id="addDeptRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <input type="hidden" name="create_dept_role" value="1">
      <div class="modal-header">
        <h5 class="modal-title">เพิ่มชนิดแผนก</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">ชื่อชนิดแผนก</label>
          <input type="text" class="form-control" name="new_role_name" placeholder="เช่น Warehouse, IT, QA" required>
        </div>
        <div class="mb-2">
          <label class="form-label">บทบาทหัวหน้า (Head role)</label>
          <select class="form-select" name="new_head_role" required>
            <option value="DeptHead">DeptHead (หัวหน้าทั่วไป)</option>
            <option value="PurchasingHead">PurchasingHead (หัวหน้าจัดซื้อ)</option>
            <option value="Admin">Admin (หัวหน้า/แอดมินสูงสุด)</option>
          </select>
        </div>
        <div class="form-text">
          * ชนิดแผนกจะถูกใช้กำหนดสิทธิ์พื้นฐานของสมาชิกในแผนก และบทบาทของหัวหน้าตามที่เลือก
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">บันทึก</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>