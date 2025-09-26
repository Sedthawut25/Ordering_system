<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Seller']);
require_once __DIR__ . '/../db.php';

$seller_id = $_SESSION['seller_id'] ?? null;
if (!$seller_id || ($_SESSION['role'] ?? '') !== 'Seller') {
  header('Location: /procurement_system/login.php');
  exit;
}

// Fetch purchase orders belonging to this seller
$stmt = $pdo->prepare('
    SELECT po.*, pr.id AS request_id, po.status
    FROM purchase_orders po
    JOIN quotations q ON po.quotation_id = q.id
    JOIN purchase_requests pr ON q.purchase_request_id = pr.id
    WHERE q.seller_id = ?
    ORDER BY po.id DESC
');
$stmt->execute([$seller_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar -->
    <aside class="col-lg-2 d-none d-lg-block sidebar">
      <div class="sidebar-title">เมนูผู้ขาย</div>
      <nav class="nav flex-column">
        <a class="nav-link <?= nav_active('/seller/index.php') ?>" href="/procurement_system/seller/index.php">
          <i class="bi bi-speedometer2"></i>แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/seller/purchase_requests') ?>" href="/procurement_system/seller/purchase_requests.php">
          <i class="bi bi-file-earmark-text"></i>ดูใบขอซื้อ
        </a>
        <a class="nav-link <?= nav_active('/seller/quotations') ?>" href="/procurement_system/seller/quotations.php">
          <i class="bi bi-cash-coin"></i>จัดการใบเสนอราคา
        </a>
        <a class="nav-link <?= nav_active('/seller/purchase_orders') ?>" href="/procurement_system/seller/purchase_orders.php">
          <i class="bi bi-bag-check"></i>ดูใบสั่งซื้อ
        </a>
      </nav>
    </aside>

    <!-- Offcanvas (mobile sidebar) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
          <i class="bi bi-shop me-1"></i> เมนูผู้ขาย
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body offcanvas-nav">
        <a class="nav-link <?= nav_active('/seller/index.php') ?>" href="/procurement_system/seller/index.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-speedometer2"></i>แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/seller/purchase_requests') ?>" href="/procurement_system/seller/purchase_requests.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-file-earmark-text"></i>ดูใบขอซื้อ
        </a>
        <a class="nav-link <?= nav_active('/seller/quotations') ?>" href="/procurement_system/seller/quotations.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-cash-coin"></i>จัดการใบเสนอราคา
        </a>
        <a class="nav-link <?= nav_active('/seller/purchase_orders') ?>" href="/procurement_system/seller/purchase_orders.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-bag-check"></i>ดูใบสั่งซื้อ
        </a>
      </div>
    </div>

    <!-- Main content -->
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">ใบสั่งซื้อของฉัน</h2>
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th>รหัสใบสั่งซื้อ</th>
              <th>ใบขอซื้อ</th>
              <th>วันที่สั่งซื้อ</th>
              <th>สถานะ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td><?= $order['id'] ?></td>
                <td><?= $order['request_id'] ?></td>
                <td><?= htmlspecialchars($order['order_date']) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
              <tr>
                <td colspan="4" class="text-center text-muted">ยังไม่มีใบสั่งซื้อ</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>