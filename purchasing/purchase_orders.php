<?php
// procurement_system/purchasing/purchase_orders.php

/* ====== ห้ามมี output ก่อน redirect/header ====== */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Purchasing') {
  header('Location: /procurement_system/login.php');
  exit;
}

/* ====== Data: โหลด PO พร้อมรายละเอียด ====== */
$sql = '
  SELECT
    po.*,
    q.purchase_request_id,
    s.company_name,
    q.status AS quote_status
  FROM purchase_orders po
  JOIN quotations q ON q.id = po.quotation_id
  JOIN sellers s    ON s.id = q.seller_id
  ORDER BY po.id DESC
';
$orders = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

/* ====== จากนี้ค่อย include header (Topbar) ====== */
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
      <h2 class="mb-3">ใบสั่งซื้อ</h2>

      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:120px">รหัสใบสั่งซื้อ</th>
              <th style="width:140px">รหัสใบขอซื้อ</th>
              <th>ผู้ขาย</th>
              <th class="text-end" style="width:180px">วันที่สั่งซื้อ</th>
              <th style="width:160px">สถานะ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td><?= (int)$o['id'] ?></td>
                <td><?= (int)$o['purchase_request_id'] ?></td>
                <td><?= htmlspecialchars($o['company_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-end"><?= htmlspecialchars($o['order_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($o['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>

            <?php if (empty($orders)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted">ยังไม่มีใบสั่งซื้อ</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>