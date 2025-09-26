<?php
// procurement_system/purchasing/tax_reports.php

/* ====== ห้ามมี output ก่อน redirect/header ====== */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Purchasing') {
  header('Location: /procurement_system/login.php');
  exit;
}

/* ====== Actions ====== */
if (isset($_GET['create']) && (int)$_GET['create'] > 0) {
  $orderId = (int)$_GET['create'];
  $ins = $pdo->prepare('INSERT INTO purchase_tax_reports (purchase_order_id, created_at) VALUES (?, NOW())');
  $ins->execute([$orderId]);
  header('Location: tax_reports.php');
  exit;
}

/* ====== Data ====== */
$reports = $pdo->query('
  SELECT ptr.*, po.id AS order_id, s.company_name
  FROM purchase_tax_reports ptr
  JOIN purchase_orders po ON po.id = ptr.purchase_order_id
  JOIN quotations q       ON q.id = po.quotation_id
  JOIN sellers s          ON s.id = q.seller_id
  ORDER BY ptr.id DESC
')->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('
  SELECT po.id, s.company_name
  FROM purchase_orders po
  JOIN quotations q ON q.id = po.quotation_id
  JOIN sellers s    ON s.id = q.seller_id
  WHERE po.status = "Approved"
    AND po.id NOT IN (SELECT purchase_order_id FROM purchase_tax_reports)
');
$stmt->execute();
$availableOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ====== Header (Topbar) ====== */
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar (desktop) -->
    <aside class="col-lg-2 d-none d-lg-block sidebar">
      <div class="sidebar-title">เมนูจัดซื้อ</div>
      <nav class="nav flex-column">
        <a class="nav-link <?= nav_active('/purchasing/index.php') ?>" href="/procurement_system/purchasing/index.php">
          <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/purchasing/products') ?>" href="/procurement_system/purchasing/products.php">
          <i class="bi bi-box-seam me-2"></i> จัดการสินค้า
        </a>
        <a class="nav-link <?= nav_active('/purchasing/open_requests') ?>" href="/procurement_system/purchasing/open_requests.php">
          <i class="bi bi-megaphone me-2"></i> ใบขอซื้อเปิดประกาศ
        </a>
        <a class="nav-link <?= nav_active('/purchasing/compare_quotes') ?>" href="/procurement_system/purchasing/compare_quotes.php">
          <i class="bi bi-diagram-3 me-2"></i> เปรียบเทียบใบเสนอราคา
        </a>
        <a class="nav-link <?= nav_active('/purchasing/purchase_orders') ?>" href="/procurement_system/purchasing/purchase_orders.php">
          <i class="bi bi-receipt me-2"></i> ใบสั่งซื้อ
        </a>
        <a class="nav-link <?= nav_active('/purchasing/tax_reports') ?>" href="/procurement_system/purchasing/tax_reports.php">
          <i class="bi bi-file-earmark-text me-2"></i> รายงานภาษีซื้อ
        </a>
      </nav>
    </aside>

    <!-- Offcanvas (mobile) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
          <i class="bi bi-bag-check-fill me-1"></i> เมนูจัดซื้อ
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body offcanvas-nav">
        <a class="nav-link <?= nav_active('/purchasing/index.php') ?>" href="/procurement_system/purchasing/index.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/purchasing/products') ?>" href="/procurement_system/purchasing/products.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-box-seam me-2"></i> จัดการสินค้า
        </a>
        <a class="nav-link <?= nav_active('/purchasing/open_requests') ?>" href="/procurement_system/purchasing/open_requests.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-megaphone me-2"></i> ใบขอซื้อเปิดประกาศ
        </a>
        <a class="nav-link <?= nav_active('/purchasing/compare_quotes') ?>" href="/procurement_system/purchasing/compare_quotes.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-diagram-3 me-2"></i> เปรียบเทียบใบเสนอราคา
        </a>
        <a class="nav-link <?= nav_active('/purchasing/purchase_orders') ?>" href="/procurement_system/purchasing/purchase_orders.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-receipt me-2"></i> ใบสั่งซื้อ
        </a>
        <a class="nav-link <?= nav_active('/purchasing/tax_reports') ?>" href="/procurement_system/purchasing/tax_reports.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-file-earmark-text me-2"></i> รายงานภาษีซื้อ
        </a>
      </div>
    </div>


    <!-- Main -->
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">รายงานภาษีซื้อ</h2>

      <h5 class="mt-4">สร้างรายงานใหม่</h5>
      <?php if ($availableOrders): ?>
        <ul class="list-group mb-4">
          <?php foreach ($availableOrders as $order): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              ใบสั่งซื้อ #<?= (int)$order['id'] ?> |
              ผู้ขาย: <?= htmlspecialchars($order['company_name'], ENT_QUOTES, 'UTF-8') ?>
              <a href="?create=<?= (int)$order['id'] ?>" class="btn btn-sm btn-success"
                onclick="return confirm('ยืนยันสร้างรายงานภาษีสำหรับใบสั่งซื้อนี้?')">สร้างรายงาน</a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">ไม่มีใบสั่งซื้อที่รอสร้างรายงาน</p>
      <?php endif; ?>

      <h5 class="mt-4">รายงานที่สร้างแล้ว</h5>
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:120px">รหัสรายงาน</th>
              <th style="width:140px">ใบสั่งซื้อ</th>
              <th>ผู้ขาย</th>
              <th class="text-end" style="width:200px">วันที่สร้าง</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reports as $r): ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= (int)$r['order_id'] ?></td>
                <td><?= htmlspecialchars($r['company_name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-end"><?= htmlspecialchars($r['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($reports)): ?>
              <tr>
                <td colspan="4" class="text-center text-muted">ยังไม่มีรายงานภาษีซื้อ</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>