<?php
// procurement_system/purchasing_head/approve_purchases.php

/* ===== ตรวจสิทธิ์ก่อน output ===== */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'PurchasingHead') {
  header('Location: /procurement_system/login.php');
  exit;
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../db.php';


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
const VAT_RATE = 0.07;

if (isset($_GET['approve'])) {
  $orderId = (int)$_GET['approve'];
  $stmt = $pdo->prepare('UPDATE purchase_orders SET status = "Approved" WHERE id = ?');
  $stmt->execute([$orderId]);

  $stmtQ = $pdo->prepare('SELECT quotation_id FROM purchase_orders WHERE id = ?');
  $stmtQ->execute([$orderId]);
  $quoteId = $stmtQ->fetchColumn();
  if ($quoteId) {
    $itemsStmt = $pdo->prepare('SELECT product_id, quantity FROM quotation_items WHERE quotation_id = ?');
    $itemsStmt->execute([$quoteId]);
    while ($row = $itemsStmt->fetch(PDO::FETCH_ASSOC)) {
      $upd = $pdo->prepare('UPDATE products SET quantity = quantity + ? WHERE id = ?');
      $upd->execute([$row['quantity'], $row['product_id']]);
    }
  }
  header('Location: approve_purchases.php');
  exit;
}

if (isset($_GET['reject'])) {
  $orderId = (int)$_GET['reject'];
  $stmt = $pdo->prepare('UPDATE purchase_orders SET status = "Rejected" WHERE id = ?');
  $stmt->execute([$orderId]);
  header('Location: approve_purchases.php');
  exit;
}

$stmt = $pdo->query('
  SELECT po.*, q.purchase_request_id, s.company_name
  FROM purchase_orders po
  JOIN quotations q ON po.quotation_id = q.id
  JOIN sellers s ON q.seller_id = s.id
  WHERE po.status = "PendingApproval"
  ORDER BY po.id DESC
');
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar -->
    <div class="container-fluid">
      <div class="row g-0">

        <aside class="col-lg-2 d-none d-lg-block sidebar">
          <div class="sidebar-title">เมนูหัวหน้าจัดซื้อ</div>
          <nav class="nav flex-column">
            <a class="nav-link <?= nav_active('/purchasing_head/index.php') ?>" href="/procurement_system/purchasing_head/index.php">
              <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
            </a>
            <a class="nav-link <?= nav_active('/purchasing_head/approve_purchases') ?>" href="/procurement_system/purchasing_head/approve_purchases.php">
              <i class="bi bi-check2-square me-2"></i> อนุมัติใบสั่งซื้อ
            </a>
          </nav>
        </aside>

        <!-- Offcanvas (mobile) -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
              <i class="bi bi-bag-check-fill me-1"></i> เมนูหัวหน้าจัดซื้อ
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body offcanvas-nav">
            <a class="nav-link <?= nav_active('/purchasing_head/index.php') ?>" href="/procurement_system/purchasing_head/index.php" data-bs-dismiss="offcanvas">
              <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
            </a>
            <a class="nav-link <?= nav_active('/purchasing_head/approve_purchases') ?>" href="/procurement_system/purchasing_head/approve_purchases.php" data-bs-dismiss="offcanvas">
              <i class="bi bi-check2-square me-2"></i> อนุมัติใบสั่งซื้อ
            </a>
          </div>
        </div>

        <!-- Main -->
        <main class="col-lg-10 app-content">
          <h2 class="mb-3">อนุมัติใบสั่งซื้อ</h2>

          <div class="accordion" id="ordersAccordion">
            <?php foreach ($orders as $o): ?>
              <div class="accordion-item mb-2">
                <h2 class="accordion-header" id="headingOrder<?= (int)$o['id'] ?>">
                  <button class="accordion-button collapsed" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseOrder<?= (int)$o['id'] ?>"
                    aria-expanded="false"
                    aria-controls="collapseOrder<?= (int)$o['id'] ?>">
                    ใบสั่งซื้อ #<?= (int)$o['id'] ?> |
                    ผู้ขาย: <?= htmlspecialchars($o['company_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> |
                    วันที่ <?= fmt_thai_datetime_or_dash($o['order_date'] ?? null) ?>
                  </button>
                </h2>
                <div id="collapseOrder<?= (int)$o['id'] ?>" class="accordion-collapse collapse"
                  aria-labelledby="headingOrder<?= (int)$o['id'] ?>" data-bs-parent="#ordersAccordion">
                  <div class="accordion-body">
                    <?php
                      $itemsStmt = $pdo->prepare('
                        SELECT qi.quantity, qi.price, p.name
                        FROM quotation_items qi
                        JOIN products p ON qi.product_id = p.id
                        WHERE qi.quotation_id = ?
                      ');
                      $itemsStmt->execute([$o['quotation_id']]);
                      $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

                      $subtotal = 0.0;
                    ?>
                    <table class="table table-sm table-bordered mb-3">
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
                          <td colspan="3" class="text-end"><strong>รวม</strong></td>
                          <td class="text-end"><strong><?= number_format($subtotal, 2) ?></strong></td>
                        </tr>
                        <tr>
                          <td colspan="3" class="text-end">ภาษีมูลค่าเพิ่ม (<?= (int)(VAT_RATE * 100) ?>%)</td>
                          <td class="text-end"><?= number_format($vat, 2) ?></td>
                        </tr>
                        <tr>
                          <td colspan="3" class="text-end"><strong>รวมทั้งสิ้น</strong></td>
                          <td class="text-end"><strong><?= number_format($grand, 2) ?></strong></td>
                        </tr>
                      </tbody>
                    </table>

                    <a href="?approve=<?= (int)$o['id'] ?>" class="btn btn-success btn-sm"
                      onclick="return confirm('ยืนยันการอนุมัติใบสั่งซื้อนี้?')">อนุมัติ</a>
                    <a href="?reject=<?= (int)$o['id'] ?>" class="btn btn-danger btn-sm"
                      onclick="return confirm('ยืนยันการปฏิเสธใบสั่งซื้อนี้?')">ไม่อนุมัติ</a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>

            <?php if (empty($orders)): ?>
              <p class="text-muted text-center">ไม่มีใบสั่งซื้อที่รอการอนุมัติ</p>
            <?php endif; ?>
          </div>
        </main>

      </div>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
