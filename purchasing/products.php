<?php
// procurement_system/purchasing/products.php

/* ====== ห้ามมี output ก่อน redirect/header ====== */
// เดิม include header.php ก่อน check_role → อาจทำให้ redirect พัง
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Purchasing') {
  header('Location: /procurement_system/login.php');
  exit;
}

/* ====== Actions ====== */
// ลบสินค้า (ใช้ absolute path ตอน redirect)
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
  $stmt->execute([$id]);
  header('Location: /procurement_system/purchasing/products.php'); // ✅ absolute
  exit;
}

/* เพิ่มสินค้า */
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name      = trim($_POST['name'] ?? '');
  $typeId    = (int)($_POST['type_id'] ?? 0);
  $details   = trim($_POST['details'] ?? '');
  $quantity  = (int)($_POST['quantity'] ?? 0);
  $min_stock = (int)($_POST['min_stock'] ?? 0);
  $unit_price = (float)($_POST['unit_price'] ?? 0);

  if ($name && $typeId) {
    $stmt = $pdo->prepare('
            INSERT INTO products (name, product_type_id, details, quantity, min_stock, unit_price)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
    $stmt->execute([$name, $typeId, $details, $quantity, $min_stock, $unit_price]);
    $message = 'เพิ่มสินค้าสำเร็จ';
  } else {
    $message = 'กรุณากรอกชื่อสินค้าและเลือกประเภท';
  }
}

/* ====== Data ====== */
$types = $pdo->query('SELECT id, name FROM product_types ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

$products = $pdo->query('
    SELECT p.*, pt.name AS type_name
    FROM products p
    LEFT JOIN product_types pt ON pt.id = p.product_type_id
    ORDER BY p.id DESC
')->fetchAll(PDO::FETCH_ASSOC);

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
      <h2 class="mb-3">จัดการสินค้า</h2>

      <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <div class="card mb-4">
        <div class="card-header">เพิ่มสินค้าใหม่</div>
        <div class="card-body">
          <form method="post" autocomplete="off">
            <div class="row g-3">
              <div class="col-md-4">
                <label for="name" class="form-label">ชื่อสินค้า</label>
                <input type="text" class="form-control" id="name" name="name" required>
              </div>
              <div class="col-md-3">
                <label for="type_id" class="form-label">ประเภทสินค้า</label>
                <select class="form-select" id="type_id" name="type_id" required>
                  <option value="">-- เลือกประเภท --</option>
                  <?php foreach ($types as $t): ?>
                    <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-2">
                <label for="quantity" class="form-label">จำนวนเริ่มต้น</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="0" required>
              </div>
              <div class="col-md-2">
                <label for="min_stock" class="form-label">ขั้นต่ำคงเหลือ</label>
                <input type="number" class="form-control" id="min_stock" name="min_stock" min="0" value="0" required>
              </div>
              <div class="col-md-2">
                <label for="unit_price" class="form-label">ราคาต่อหน่วย (บาท)</label>
                <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" min="0" value="0" required>
              </div>
            </div>

            <div class="mt-3">
              <label for="details" class="form-label">รายละเอียด</label>
              <textarea class="form-control" id="details" name="details" rows="2"></textarea>
            </div>

            <button type="submit" class="btn btn-primary mt-3">เพิ่มสินค้า</button>
          </form>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:80px">รหัส</th>
              <th>ชื่อสินค้า</th>
              <th>ประเภท</th>
              <th class="text-end" style="width:120px">จำนวน</th>
              <th class="text-end" style="width:140px">ขั้นต่ำคงเหลือ</th>
              <th class="text-end" style="width:160px">ราคาต่อหน่วย</th>
              <th style="width:90px">ลบ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <tr>
                <td><?= (int)$product['id'] ?></td>
                <td><?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($product['type_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-end"><?= number_format((float)($product['quantity'] ?? 0), 2) ?></td>
                <td class="text-end"><?= number_format((float)($product['min_stock'] ?? 0), 2) ?></td>
                <td class="text-end"><?= number_format((float)($product['unit_price'] ?? 0), 2) ?></td>
                <td>
                  <a href="?delete=<?= (int)$product['id'] ?>"
                    class="btn btn-sm btn-danger"
                    onclick="return confirm('ยืนยันการลบสินค้านี้?')">ลบ</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
              <tr>
                <td colspan="7" class="text-center text-muted">ยังไม่มีสินค้า</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>