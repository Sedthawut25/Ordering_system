<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Seller']);
require_once __DIR__ . '/../db.php';

$seller_id = $_SESSION['seller_id'] ?? null;
if (!$seller_id || ($_SESSION['role'] ?? '') !== 'Seller') {
  header('Location: /procurement_system/login.php');
  exit;
}

if (isset($_GET['create'])) {
  $requestId = (int)$_GET['create'];

  // ตรวจว่าผู้ขายรายนี้เคยเสนอราคาสำหรับใบขอนี้แล้วหรือยัง
  $exists = $pdo->prepare('SELECT id FROM quotations WHERE purchase_request_id = ? AND seller_id = ?');
  $exists->execute([$requestId, $seller_id]);
  if ($exists->fetch()) {
    header('Location: quotations.php');
    exit;
  }

  // โหลดรายการสินค้าจากใบขอซื้อ
  $itemsStmt = $pdo->prepare('
        SELECT pri.product_id, pri.quantity, p.name
        FROM purchase_request_items pri
        JOIN products p ON pri.product_id = p.id
        WHERE pri.purchase_request_id = ?
    ');
  $itemsStmt->execute([$requestId]);
  $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

  // บันทึกฟอร์ม
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qIns = $pdo->prepare('
            INSERT INTO quotations (purchase_request_id, seller_id, quote_date, status)
            VALUES (?, ?, NOW(), ?)
        ');
    $qIns->execute([$requestId, $seller_id, 'Pending']);
    $quoteId = $pdo->lastInsertId();

    $qi = $pdo->prepare('
            INSERT INTO quotation_items (quotation_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ');
    foreach ($items as $it) {
      $price = (float)($_POST['price_' . $it['product_id']] ?? 0);
      $qty   = (int)$it['quantity'];
      $qi->execute([$quoteId, $it['product_id'], $qty, $price]);
    }

    header('Location: quotations.php');
    exit;
  }
?>

  <div class="container-fluid">
    <div class="row g-0">

      <!-- Sidebar (desktop) -->
      <aside class="col-lg-2 d-none d-lg-block sidebar">
        <div class="sidebar-title">เมนูผู้ขาย</div>
        <nav class="nav flex-column">
          <a class="nav-link <?= nav_active('/seller/index.php') ?>" href="/procurement_system/seller/index.php">
            <i class="bi bi-speedometer2"></i> แดชบอร์ด
          </a>
          <a class="nav-link <?= nav_active('/seller/purchase_requests') ?>" href="/procurement_system/seller/purchase_requests.php">
            <i class="bi bi-file-earmark-text"></i> ดูใบขอซื้อ
          </a>
          <a class="nav-link <?= nav_active('/seller/quotations') ?>" href="/procurement_system/seller/quotations.php">
            <i class="bi bi-cash-coin"></i> จัดการใบเสนอราคา
          </a>
          <a class="nav-link <?= nav_active('/seller/purchase_orders') ?>" href="/procurement_system/seller/purchase_orders.php">
            <i class="bi bi-bag-check"></i> ดูใบสั่งซื้อ
          </a>
        </nav>
      </aside>

      <!-- Offcanvas (mobile) ให้ปุ่มแฮมเบอร์เกอร์ใน header ใช้ได้ -->
      <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
            <i class="bi bi-bag-check-fill me-1"></i> เมนูระบบ
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body offcanvas-nav">
          <a class="nav-link <?= nav_active('/seller/index.php') ?>" href="/procurement_system/seller/index.php" data-bs-dismiss="offcanvas">
            <i class="bi bi-speedometer2"></i> แดชบอร์ด
          </a>
          <a class="nav-link <?= nav_active('/seller/purchase_requests') ?>" href="/procurement_system/seller/purchase_requests.php" data-bs-dismiss="offcanvas">
            <i class="bi bi-file-earmark-text"></i> ดูใบขอซื้อ
          </a>
          <a class="nav-link <?= nav_active('/seller/quotations') ?>" href="/procurement_system/seller/quotations.php" data-bs-dismiss="offcanvas">
            <i class="bi bi-cash-coin"></i> จัดการใบเสนอราคา
          </a>
          <a class="nav-link <?= nav_active('/seller/purchase_orders') ?>" href="/procurement_system/seller/purchase_orders.php" data-bs-dismiss="offcanvas">
            <i class="bi bi-bag-check"></i> ดูใบสั่งซื้อ
          </a>
        </div>
      </div>

      <!-- Main -->
      <main class="col-lg-10 app-content">
        <h2 class="mb-3">สร้างใบเสนอราคา</h2>
        <form method="post">
          <h5>ใบขอซื้อ #<?= $requestId ?></h5>
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>สินค้า</th>
                <th>จำนวนที่ขอ</th>
                <th>ราคาต่อหน่วยของคุณ (บาท)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): ?>
                <tr>
                  <td><?= htmlspecialchars($it['name']) ?></td>
                  <td><?= (int)$it['quantity'] ?></td>
                  <td>
                    <input type="number" step="0.01" name="price_<?= $it['product_id'] ?>" class="form-control" required>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <button type="submit" class="btn btn-success">ส่งใบเสนอราคา</button>
          <a href="quotations.php" class="btn btn-secondary">ยกเลิก</a>
        </form>
      </main>

    </div>
  </div>

<?php
  require_once __DIR__ . '/../includes/footer.php';
  exit;
}

$quotesStmt = $pdo->prepare('
    SELECT q.*, pr.id AS request_id, pr.request_date, s.company_name
    FROM quotations q
    JOIN purchase_requests pr ON q.purchase_request_id = pr.id
    JOIN sellers s ON q.seller_id = s.id
    WHERE q.seller_id = ?
    ORDER BY q.id DESC
');
$quotesStmt->execute([$seller_id]);
$quotes = $quotesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar (desktop) -->
    <aside class="col-lg-2 d-none d-lg-block sidebar">
      <div class="sidebar-title">เมนูผู้ขาย</div>
      <nav class="nav flex-column">
        <a class="nav-link <?= nav_active('/seller/index.php') ?>" href="/procurement_system/seller/index.php">
          <i class="bi bi-speedometer2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/seller/purchase_requests') ?>" href="/procurement_system/seller/purchase_requests.php">
          <i class="bi bi-file-earmark-text"></i> ดูใบขอซื้อ
        </a>
        <a class="nav-link <?= nav_active('/seller/quotations') ?>" href="/procurement_system/seller/quotations.php">
          <i class="bi bi-cash-coin"></i> จัดการใบเสนอราคา
        </a>
        <a class="nav-link <?= nav_active('/seller/purchase_orders') ?>" href="/procurement_system/seller/purchase_orders.php">
          <i class="bi bi-bag-check"></i> ดูใบสั่งซื้อ
        </a>
      </nav>
    </aside>

    <!-- Offcanvas (mobile) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
          <i class="bi bi-bag-check-fill me-1"></i> เมนูระบบ
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body offcanvas-nav">
        <a class="nav-link <?= nav_active('/seller/index.php') ?>" href="/procurement_system/seller/index.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-speedometer2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/seller/purchase_requests') ?>" href="/procurement_system/seller/purchase_requests.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-file-earmark-text"></i> ดูใบขอซื้อ
        </a>
        <a class="nav-link <?= nav_active('/seller/quotations') ?>" href="/procurement_system/seller/quotations.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-cash-coin"></i> จัดการใบเสนอราคา
        </a>
        <a class="nav-link <?= nav_active('/seller/purchase_orders') ?>" href="/procurement_system/seller/purchase_orders.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-bag-check"></i> ดูใบสั่งซื้อ
        </a>
      </div>
    </div>

    <!-- Main -->
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">ใบเสนอราคาของฉัน</h2>

      <div class="accordion" id="quotesAccordion">
        <?php foreach ($quotes as $q): ?>
          <div class="accordion-item mb-2">
            <h2 class="accordion-header" id="headingQuote<?= $q['id'] ?>">
              <button class="accordion-button collapsed" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapseQuote<?= $q['id'] ?>"
                aria-expanded="false"
                aria-controls="collapseQuote<?= $q['id'] ?>">
                ใบเสนอราคา #<?= $q['id'] ?> |
                ใบขอซื้อ #<?= $q['request_id'] ?> |
                วันที่เสนอ <?= htmlspecialchars($q['quote_date']) ?> |
                สถานะ: <?= htmlspecialchars($q['status']) ?>
              </button>
            </h2>
            <div id="collapseQuote<?= $q['id'] ?>" class="accordion-collapse collapse"
              aria-labelledby="headingQuote<?= $q['id'] ?>"
              data-bs-parent="#quotesAccordion">
              <div class="accordion-body">
                <?php
                $itemsStmt = $pdo->prepare('
                    SELECT qi.quantity, qi.price, p.name
                    FROM quotation_items qi
                    JOIN products p ON qi.product_id = p.id
                    WHERE qi.quotation_id = ?
                ');
                $itemsStmt->execute([$q['id']]);
                $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <table class="table table-sm table-bordered mb-3">
                  <thead class="table-light">
                    <tr>
                      <th>สินค้า</th>
                      <th>จำนวน</th>
                      <th>ราคาต่อหน่วย</th>
                      <th>ราคารวม</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $total = 0;
                    foreach ($items as $it): ?>
                      <?php $line = ((float)$it['quantity']) * ((float)$it['price']);
                      $total += $line; ?>
                      <tr>
                        <td><?= htmlspecialchars($it['name']) ?></td>
                        <td><?= (int)$it['quantity'] ?></td>
                        <td><?= number_format((float)$it['price'], 2) ?></td>
                        <td><?= number_format($line, 2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                    <tr>
                      <td colspan="3" class="text-end"><strong>รวม</strong></td>
                      <td><strong><?= number_format($total, 2) ?></strong></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

        <?php if (empty($quotes)): ?>
          <p class="text-muted">ยังไม่มีใบเสนอราคา</p>
        <?php endif; ?>
      </div>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>