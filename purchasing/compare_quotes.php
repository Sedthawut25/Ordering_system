<?php
// procurement_system/purchasing/compare_quotes.php

/* ================= Helpers ================= */
if (!function_exists('fmt_thai_datetime_or_dash')) {
  function fmt_thai_datetime_or_dash(?string $dt): string {
    if (!$dt) return '-';
    $dt = trim($dt);
    if ($dt === '0000-00-00' || $dt === '0000-00-00 00:00:00') return '-';
    $ts = strtotime($dt);
    if ($ts === false) return '-';
    $months = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
    $d   = (int)date('j', $ts);
    $m   = (int)date('n', $ts);
    $yBE = (int)date('Y', $ts) + 543;
    $hm  = date('H:i', $ts);
    return sprintf('%d %s %d %s น.', $d, $months[$m-1], $yBE, $hm);
  }
}
if (!function_exists('is_bad_date')) {
  function is_bad_date(?string $s): bool {
    if (!$s) return true;
    $s = trim($s);
    return $s === '0000-00-00' || $s === '0000-00-00 00:00:00';
  }
}
const VAT_RATE = 0.07; // ล็อก VAT 7%

/* ================= Bootstrap (ห้ามมี output ก่อน redirect/header) ================= */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Purchasing') {
  header('Location: /procurement_system/login.php');
  exit;
}

/* ===== เลือกใบเสนอราคาเพื่อสร้างใบสั่งซื้อ ===== */
if (isset($_GET['selectQuote'])) {
  $quoteId = (int)$_GET['selectQuote'];

  $pdo->beginTransaction();
  try {
    // หา PR ของใบเสนอราคาที่เลือก
    $stmt = $pdo->prepare('SELECT purchase_request_id FROM quotations WHERE id = ?');
    $stmt->execute([$quoteId]);
    $purchaseRequestId = (int)$stmt->fetchColumn();

    if ($purchaseRequestId) {
      // สร้างใบสั่งซื้อ (รอหัวหน้าจัดซื้ออนุมัติ)
      $ins = $pdo->prepare('INSERT INTO purchase_orders (quotation_id, order_date, status) VALUES (?, NOW(), ?)');
      $ins->execute([$quoteId, 'PendingApproval']);

      // อัปเดตสถานะใบขอซื้อ
      $updReq = $pdo->prepare('UPDATE purchase_requests SET status = "Ordered" WHERE id = ?');
      $updReq->execute([$purchaseRequestId]);

      // ตรึงใบที่เลือก
      $updQuote = $pdo->prepare('UPDATE quotations SET status = "Selected" WHERE id = ?');
      $updQuote->execute([$quoteId]);

      // ✅ ยกเลิกใบอื่น ๆ ของ PR เดียวกัน (ถ้ายังไม่ถูกเลือก)
      $updOthers = $pdo->prepare('
        UPDATE quotations
        SET status = "Cancelled"
        WHERE purchase_request_id = ? AND id <> ? AND status <> "Selected"
      ');
      $updOthers->execute([$purchaseRequestId, $quoteId]);
    }

    $pdo->commit();
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die('เกิดข้อผิดพลาด: ' . $e->getMessage());
  }

  header('Location: /procurement_system/purchasing/compare_quotes.php');
  exit;
}

/* ===== ดึงใบขอซื้อที่เปิดประกาศ (พร้อมวันที่สำรองจากใบเสนอราคารายแรก) ===== */
$stmt = $pdo->prepare('
  SELECT
    pr.id,
    pr.reason,
    pr.request_date,
    MIN(q.quote_date) AS first_quote_date
  FROM purchase_requests pr
  LEFT JOIN quotations q
    ON q.purchase_request_id = pr.id
  WHERE pr.status = "OpenForBid"
  GROUP BY pr.id, pr.reason, pr.request_date
  ORDER BY pr.id DESC
');
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ================= ตอนนี้ค่อย include header (Topbar) ================= */
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
      <h2 class="mb-3">เปรียบเทียบใบเสนอราคา</h2>

      <?php if (empty($requests)): ?>
        <p class="text-muted">ยังไม่มีใบขอซื้อที่เปิดประกาศหรือยังไม่มีใบเสนอราคา</p>
      <?php endif; ?>

      <div class="accordion" id="compareAccordion">
        <?php foreach ($requests as $req): ?>
          <?php
            // เลือกวันที่: ใช้ request_date ถ้าโอเค ไม่งั้น fallback เป็น first_quote_date
            $rawDate = !is_bad_date($req['request_date'] ?? null)
                        ? $req['request_date']
                        : ($req['first_quote_date'] ?? null);
            $displayDate = fmt_thai_datetime_or_dash($rawDate);

            // ดึงใบเสนอราคาทั้งหมดของ PR นี้
            $qStmt = $pdo->prepare('
                SELECT q.*, s.company_name
                FROM quotations q
                JOIN sellers s ON q.seller_id = s.id
                WHERE q.purchase_request_id = ?
                ORDER BY q.id
            ');
            $qStmt->execute([(int)$req['id']]);
            $quotes = $qStmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$quotes) continue;
          ?>
          <div class="accordion-item mb-2">
            <h2 class="accordion-header" id="headingReq<?= (int)$req['id'] ?>">
              <button class="accordion-button collapsed" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapseReq<?= (int)$req['id'] ?>"
                aria-expanded="false"
                aria-controls="collapseReq<?= (int)$req['id'] ?>">
                ใบขอซื้อ #<?= (int)$req['id'] ?> | วันที่ <?= $displayDate ?>
              </button>
            </h2>

            <div id="collapseReq<?= (int)$req['id'] ?>" class="accordion-collapse collapse"
              aria-labelledby="headingReq<?= (int)$req['id'] ?>" data-bs-parent="#compareAccordion">
              <div class="accordion-body">
                <p><strong>เหตุผล:</strong> <?= htmlspecialchars($req['reason'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>

                <?php foreach ($quotes as $quote): ?>
                  <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <span>
                        ใบเสนอราคา #<?= (int)$quote['id'] ?>
                        | ผู้ขาย: <?= htmlspecialchars($quote['company_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        | สถานะ: <?= htmlspecialchars($quote['status'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                      </span>
                      <?php if (($quote['status'] ?? '') === 'Pending'): ?>
                        <a href="?selectQuote=<?= (int)$quote['id'] ?>"
                          class="btn btn-sm btn-success"
                          onclick="return confirm('ยืนยันเลือกใบเสนอราคานี้เพื่อสร้างใบสั่งซื้อ?')">
                          เลือกใบนี้
                        </a>
                      <?php endif; ?>
                    </div>

                    <div class="card-body">
                      <?php
                        $itemsStmt = $pdo->prepare('
                            SELECT qi.quantity, qi.price, p.name
                            FROM quotation_items qi
                            JOIN products p ON p.id = qi.product_id
                            WHERE qi.quotation_id = ?
                            ORDER BY p.name
                        ');
                        $itemsStmt->execute([(int)$quote['id']]);
                        $items  = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

                        $subtotal = 0.0;
                      ?>
                      <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                          <thead class="table-light">
                            <tr>
                              <th>สินค้า</th>
                              <th class="text-end" style="width:120px">จำนวน</th>
                              <th class="text-end" style="width:140px">ราคาต่อหน่วย</th>
                              <th class="text-end" style="width:160px">ราคารวม</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($items as $it):
                              $qty   = (float)($it['quantity'] ?? 0);
                              $price = (float)($it['price'] ?? 0);
                              $line  = $qty * $price;
                              $subtotal += $line;
                            ?>
                              <tr>
                                <td><?= htmlspecialchars($it['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-end"><?= number_format($qty, 2) ?></td>
                                <td class="text-end"><?= number_format($price, 2) ?></td>
                                <td class="text-end"><?= number_format($line, 2) ?></td>
                              </tr>
                            <?php endforeach; ?>

                            <?php
                              $vat   = $subtotal * VAT_RATE;
                              $grand = $subtotal + $vat;
                            ?>
                            <tr>
                              <td colspan="3" class="text-end"><strong>รวม (Subtotal)</strong></td>
                              <td class="text-end"><strong><?= number_format($subtotal, 2) ?></strong></td>
                            </tr>
                            <tr>
                              <td colspan="3" class="text-end">ภาษีมูลค่าเพิ่ม (VAT <?= (int)(VAT_RATE*100) ?>%)</td>
                              <td class="text-end"><?= number_format($vat, 2) ?></td>
                            </tr>
                            <tr>
                              <td colspan="3" class="text-end"><strong>รวมทั้งสิ้น (Grand Total)</strong></td>
                              <td class="text-end"><strong><?= number_format($grand, 2) ?></strong></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>

              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
