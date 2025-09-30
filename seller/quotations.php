<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Seller']);
require_once __DIR__ . '/../db.php';

if (!function_exists('th_quote_status')) {
  function th_quote_status(?string $s): string {
    $s = trim((string)$s);
    switch ($s) {
      case 'Selected':  return 'ได้รับเลือก';
      case 'Cancelled': return 'ปฏิเสธ';
      case 'Pending':
      default:          return 'รอดำเนินการ';
    }
  }
}

$seller_id = $_SESSION['seller_id'] ?? null;
if (!$seller_id || ($_SESSION['role'] ?? '') !== 'Seller') {
  header('Location: /procurement_system/login.php');
  exit;
}

if (isset($_GET['create'])) {
  $requestId = (int)$_GET['create'];

  $exists = $pdo->prepare('SELECT id FROM quotations WHERE purchase_request_id = ? AND seller_id = ?');
  $exists->execute([$requestId, $seller_id]);
  if ($exists->fetch()) {
    header('Location: quotations.php');
    exit;
  }

  $itemsStmt = $pdo->prepare('
    SELECT pri.product_id, pri.quantity, p.name
    FROM purchase_request_items pri
    JOIN products p ON pri.product_id = p.id
    WHERE pri.purchase_request_id = ?
  ');
  $itemsStmt->execute([$requestId]);
  $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);


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
        <h2 class="mb-3">สร้างใบเสนอราคา</h2>

        <form method="post" id="quoteForm">
          <h5>ใบขอซื้อ #<?= $requestId ?></h5>

          <div class="table-responsive">
            <table class="table table-bordered align-middle" id="quoteTable">
              <thead class="table-light">
                <tr>
                  <th>สินค้า</th>
                  <th class="text-end" style="width:140px">จำนวนที่ขอ</th>
                  <th style="width:220px">ราคาต่อหน่วยของคุณ (บาท)</th>
                  <th class="text-end" style="width:160px">ราคารวม</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $it): ?>
                  <tr data-qty="<?= (float)$it['quantity'] ?>" data-row="item">
                    <td><?= htmlspecialchars($it['name']) ?></td>
                    <td class="text-end"><?= (int)$it['quantity'] ?></td>
                    <td>
                      <input type="number" step="0.01" min="0"
                        class="form-control price-input"
                        name="price_<?= $it['product_id'] ?>"
                        placeholder="0.00" required>
                    </td>
                    <td class="text-end fw-semibold">
                      <span class="line-total">0.00</span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>

              <tfoot>
                <tr>
                  <td colspan="3" class="text-end fw-semibold">รวม</td>
                  <td class="text-end fw-semibold"><span id="subTotal">0.00</span></td>
                </tr>
                <tr>
                  <td colspan="3" class="text-end fw-semibold">ภาษีมูลค่าเพิ่ม (VAT)</td>
                  <td class="text-end fw-semibold"><span id="vatAmount">0.00</span></td>
                </tr>
                <tr class="table-light">
                  <td colspan="3" class="text-end fw-bold">รวมสุทธิ</td>
                  <td class="text-end fw-bold"><span id="grandTotal">0.00</span></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">ส่งใบเสนอราคา</button>
            <a href="quotations.php" class="btn btn-secondary">ยกเลิก</a>
          </div>
        </form>
      </main>

    </div>
  </div>

  <script>
    (function() {
      const fmt = (n) => (isFinite(n) ? n.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) : '0.00');
      const VAT_RATE = 7; // 7%

      const recalc = () => {
        let sub = 0;
        document.querySelectorAll('#quoteTable tbody tr[data-row="item"]').forEach(tr => {
          const qty = parseFloat(tr.dataset.qty || '0');
          const price = parseFloat(tr.querySelector('.price-input').value || '0');
          const line = qty * price;
          sub += line;
          tr.querySelector('.line-total').textContent = fmt(line);
        });
        const vatAmt = sub * (VAT_RATE/100);
        const grand  = sub + vatAmt;
        document.getElementById('subTotal').textContent  = fmt(sub);
        document.getElementById('vatAmount').textContent = fmt(vatAmt);
        document.getElementById('grandTotal').textContent= fmt(grand);
      };
      document.querySelectorAll('.price-input').forEach(inp => {
        inp.addEventListener('input', recalc);
        inp.addEventListener('change', recalc);
      });
      recalc();
    })();
  </script>

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
            <h2 class="accordion-header" id="headingQuote<?= (int)$q['id'] ?>">
              <button class="accordion-button collapsed" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapseQuote<?= (int)$q['id'] ?>"
                aria-expanded="false"
                aria-controls="collapseQuote<?= (int)$q['id'] ?>">
                ใบเสนอราคา #<?= (int)$q['id'] ?> |
                ใบขอซื้อ #<?= (int)$q['request_id'] ?> |
                วันที่เสนอ <?= htmlspecialchars($q['quote_date'] ?? '', ENT_QUOTES, 'UTF-8') ?> |
                สถานะ: <?= htmlspecialchars(th_quote_status($q['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
              </button>
            </h2>
            <div id="collapseQuote<?= (int)$q['id'] ?>" class="accordion-collapse collapse"
              aria-labelledby="headingQuote<?= (int)$q['id'] ?>"
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

                  $subTotal = 0.0;
                  foreach ($items as $it) {
                    $subTotal += ((float)$it['quantity']) * ((float)$it['price']);
                  }
                  $VAT_RATE  = 0.07;
                  $vatAmount = $subTotal * $VAT_RATE;
                  $grand     = $subTotal + $vatAmount;
                ?>
                <table class="table table-sm table-bordered mb-3">
                  <thead class="table-light">
                    <tr>
                      <th>สินค้า</th>
                      <th class="text-end">จำนวน</th>
                      <th class="text-end">ราคาต่อหน่วย</th>
                      <th class="text-end">ราคารวม</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($items as $it):
                      $line = ((float)$it['quantity']) * ((float)$it['price']); ?>
                      <tr>
                        <td><?= htmlspecialchars($it['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-end"><?= number_format((float)($it['quantity'] ?? 0), 2) ?></td>
                        <td class="text-end"><?= number_format((float)($it['price'] ?? 0), 2) ?></td>
                        <td class="text-end"><?= number_format($line, 2) ?></td>
                      </tr>
                    <?php endforeach; ?>

                    <tr>
                      <td colspan="3" class="text-end"><strong>รวม</strong></td>
                      <td class="text-end"><strong><?= number_format($subTotal, 2) ?></strong></td>
                    </tr>
                    <tr>
                      <td colspan="3" class="text-end">ภาษีมูลค่าเพิ่ม (VAT)</td>
                      <td class="text-end"><?= number_format($vatAmount, 2) ?></td>
                    </tr>
                    <tr class="table-secondary">
                      <td colspan="3" class="text-end"><strong>รวมทั้งสิ้น</strong></td>
                      <td class="text-end"><strong><?= number_format($grand, 2) ?></strong></td>
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
