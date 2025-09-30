<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Seller']);
require_once __DIR__ . '/../db.php';  

$companyName = $_SESSION['company_name'] ?? null;

if (!$companyName && !empty($_SESSION['seller_id'])) {
  $stmt = $pdo->prepare("SELECT company_name FROM sellers WHERE id = ?");
  $stmt->execute([$_SESSION['seller_id']]);
  $companyName = $stmt->fetchColumn() ?: 'ผู้ขาย';
  $_SESSION['company_name'] = $companyName;
}
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

    <!-- Main content -->
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">ยินดีต้อนรับบริษัท <?= htmlspecialchars($companyName) ?></h2>
      <p>เลือกเมนูด้านซ้ายหรือเมนูบนมือถือเพื่อดูใบขอซื้อ จัดการใบเสนอราคา และตรวจสอบใบสั่งซื้อของท่าน</p>
    </main>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>