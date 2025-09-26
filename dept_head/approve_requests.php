<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

if (empty($_SESSION['role']) || !in_array($_SESSION['role'], ['DeptHead', 'Admin'], true)) {
  header('Location: /procurement_system/login.php');
  exit;
}

$uid = (int)($_SESSION['user_id'] ?? 0);

/* ---------------- helpers ---------------- */
function column_exists(PDO $pdo, string $table, string $col): bool
{
  $sql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1";
  $st = $pdo->prepare($sql);
  $st->execute([$table, $col]);
  return (bool)$st->fetchColumn();
}

$priTable = 'purchase_request_items';
$reqCol   = column_exists($pdo, $priTable, 'request_id') ? 'request_id'
  : (column_exists($pdo, $priTable, 'purchase_request_id') ? 'purchase_request_id' : null);
$qtyCol   = column_exists($pdo, $priTable, 'quantity') ? 'quantity'
  : (column_exists($pdo, $priTable, 'qty') ? 'qty' : null);

if ($reqCol === null || $qtyCol === null) {

  die('purchase_request_items: ไม่พบคอลัมน์ request_id/purchase_request_id หรือ quantity/qty');
}


if (isset($_GET['approve'])) {
  $id = (int)$_GET['approve'];
  $pdo->prepare("UPDATE purchase_requests
                   SET status='ApprovedByDeptHead', approved_by_head_at=NOW()
                   WHERE id=?")->execute([$id]);
  header('Location: /procurement_system/dept_head/approve_requests.php');
  exit;
}

if (isset($_GET['reject'])) {
  $id = (int)$_GET['reject'];
  try {
    $pdo->beginTransaction();
    $pdo->prepare("DELETE FROM {$priTable} WHERE `{$reqCol}`=?")->execute([$id]);
    $pdo->prepare("DELETE FROM purchase_requests WHERE id=?")->execute([$id]);
    $pdo->commit();
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $pdo->prepare("UPDATE purchase_requests
                       SET status='RejectedByDeptHead', approved_by_head_at=NOW()
                       WHERE id=?")->execute([$id]);
  }
  header('Location: /procurement_system/dept_head/approve_requests.php'); // ✅ absolute
  exit;
}

$st = $pdo->prepare("SELECT id FROM departments WHERE head_id=? LIMIT 1");
$st->execute([$uid]);
$deptId = (int)($st->fetchColumn() ?: 0);
if (!$deptId) {
  $st = $pdo->prepare("SELECT dept_id FROM employees WHERE id=? LIMIT 1");
  $st->execute([$uid]);
  $deptId = (int)($st->fetchColumn() ?: 0);
}

$sql = "
  SELECT
    pr.id,
    pr.reason,
    pr.status,
    pr.created_at,
    e.name  AS emp_name,
    e.email AS emp_email,
    COALESCE(SUM(pri.`$qtyCol`),0) AS total_qty,
    COALESCE(
      GROUP_CONCAT(CONCAT(p.id,'(',p.name,') x', pri.`$qtyCol`) ORDER BY p.name SEPARATOR ', '),
      ''
    ) AS items_text
  FROM purchase_requests pr
  JOIN employees e ON e.id = pr.employee_id
  LEFT JOIN {$priTable} pri ON pri.`$reqCol` = pr.id
  LEFT JOIN products p ON p.id = pri.product_id
  WHERE pr.status = 'Pending' AND e.dept_id = ?
  GROUP BY pr.id
  ORDER BY pr.id DESC
";
$q = $pdo->prepare($sql);
$q->execute([$deptId]);
$requests = $q->fetchAll(PDO::FETCH_ASSOC);


require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar (desktop) -->
    <aside class="col-lg-2 d-none d-lg-block sidebar">
      <div class="sidebar-title">เมนูหัวหน้าแผนก</div>
      <nav class="nav flex-column">
        <a class="nav-link <?= nav_active('/dept_head/index.php') ?>" href="/procurement_system/dept_head/index.php">
          <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/dept_head/approve_requests') ?>" href="/procurement_system/dept_head/approve_requests.php">
          <i class="bi bi-check2-square me-2"></i> อนุมัติใบขอซื้อ
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
          <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/dept_head/approve_requests') ?>" href="/procurement_system/dept_head/approve_requests.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-check2-square me-2"></i> อนุมัติใบขอซื้อ
        </a>
      </div>
    </div>


    <main class="col-lg-10 app-content">
      <h2 class="mb-3">อนุมัติใบขอซื้อสินค้า</h2>

      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:80px">#</th>
              <th>พนักงาน</th>
              <th>เหตุผล</th>
              <th>สินค้า (PK/ชื่อ xจำนวน)</th>
              <th style="width:120px">สถานะ</th>
              <th style="width:180px">วันที่สร้าง</th>
              <th style="width:160px">จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($requests as $r): ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= htmlspecialchars($r['emp_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  (<?= htmlspecialchars($r['emp_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</td>
                <td><?= htmlspecialchars($r['reason'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td style="white-space:normal; word-break:break-word;">
                  <?= htmlspecialchars($r['items_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  <?php if ((float)($r['total_qty'] ?? 0) > 0): ?>
                    <div class="text-muted small">รวมจำนวน: <?= number_format((float)$r['total_qty'], 2) ?></div>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <a class="btn btn-sm btn-success"
                    href="?approve=<?= (int)$r['id'] ?>"
                    onclick="return confirm('อนุมัติใบขอซื้อ #<?= (int)$r['id'] ?> ?')">อนุมัติ</a>
                  <a class="btn btn-sm btn-danger"
                    href="?reject=<?= (int)$r['id'] ?>"
                    onclick="return confirm('ไม่อนุมัติและลบใบขอซื้อ #<?= (int)$r['id'] ?> ?')">ไม่อนุมัติ</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($requests)): ?>
              <tr>
                <td colspan="7" class="text-center text-muted">ไม่มีใบขอซื้อที่รออนุมัติ</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>