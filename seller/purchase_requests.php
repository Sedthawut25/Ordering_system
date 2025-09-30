<?php

function fmt_thai_datetime_or_dash(?string $dt): string
{
  if (!$dt) return '-';
  $dt = trim($dt);
  if ($dt === '0000-00-00' || $dt === '0000-00-00 00:00:00') return '-';

  $ts = strtotime($dt);
  if ($ts === false) return htmlspecialchars($dt, ENT_QUOTES, 'UTF-8'); // กันเคสแปลก

  $months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
  $d   = (int)date('j', $ts);
  $m   = (int)date('n', $ts);  // 1-12
  $yBE = (int)date('Y', $ts) + 543;
  $hm  = date('H:i', $ts);
  return sprintf('%d %s %d %s น.', $d, $months[$m - 1], $yBE, $hm);
}

require_once __DIR__ . '/../includes/header.php';
check_role(['Seller']);
require_once __DIR__ . '/../db.php';

$seller_id = $_SESSION['seller_id'] ?? null;
if (!$seller_id || ($_SESSION['role'] ?? '') !== 'Seller') {
  header('Location: /procurement_system/login.php');
  exit;
}

$stmt = $pdo->prepare('
    SELECT pr.*, e.name AS employee_name
    FROM purchase_requests pr
    JOIN employees e ON pr.employee_id = e.id
    WHERE pr.status = "OpenForBid"
    ORDER BY pr.id DESC
');
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid">
  <div class="row g-0">

    
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

    <!-- Offcanvas (mobile) -->
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
      <h2 class="mb-3">ใบขอซื้อที่เปิดประกาศ</h2>

      <div class="accordion" id="sellerRequestsAccordion">
        <?php foreach ($requests as $req): ?>
          <?php
        
          $rawDate = $req['request_date'] ?? null;
          if (!$rawDate || $rawDate === '0000-00-00' || $rawDate === '0000-00-00 00:00:00') {
            $rawDate = $req['created_at'] ?? ($req['updated_at'] ?? null);
          }
          $displayDate = fmt_thai_datetime_or_dash($rawDate);

      
          $check = $pdo->prepare('SELECT id FROM quotations WHERE purchase_request_id = ? AND seller_id = ?');
          $check->execute([$req['id'], $seller_id]);
          $hasQuote = $check->fetchColumn();
          ?>
          <div class="accordion-item mb-2">
            <h2 class="accordion-header" id="heading<?= (int)$req['id'] ?>">
              <button class="accordion-button collapsed" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapse<?= (int)$req['id'] ?>"
                aria-expanded="false"
                aria-controls="collapse<?= (int)$req['id'] ?>">
                ใบขอซื้อ #<?= (int)$req['id'] ?> |
                พนักงาน: <?= htmlspecialchars($req['employee_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> |
                วันที่ <?= $displayDate ?>
              </button>
            </h2>
            <div id="collapse<?= (int)$req['id'] ?>"
              class="accordion-collapse collapse"
              aria-labelledby="heading<?= (int)$req['id'] ?>"
              data-bs-parent="#sellerRequestsAccordion">
              <div class="accordion-body">
                <p><strong>เหตุผล:</strong> <?= htmlspecialchars($req['reason'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <?php
                $itemsStmt = $pdo->prepare('
                    SELECT pri.quantity, p.name
                    FROM purchase_request_items pri
                    JOIN products p ON pri.product_id = p.id
                    WHERE pri.purchase_request_id = ?
                ');
                $itemsStmt->execute([$req['id']]);
                $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <table class="table table-sm table-bordered mb-3">
                  <thead class="table-light">
                    <tr>
                      <th>สินค้า</th>
                      <th style="width:140px" class="text-end">จำนวน</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($items as $it): ?>
                      <tr>
                        <td><?= htmlspecialchars($it['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-end"><?= number_format((float)($it['quantity'] ?? 0), 2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>

                <?php if ($hasQuote): ?>
                  <span class="badge bg-secondary">ท่านได้เสนอราคาแล้ว</span>
                <?php else: ?>
                  <a href="/procurement_system/seller/quotations.php?create=<?= (int)$req['id'] ?>"
                    class="btn btn-primary">
                    เสนอราคา
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

        <?php if (empty($requests)): ?>
          <p class="text-muted">ยังไม่มีใบขอซื้อที่เปิดประกาศ</p>
        <?php endif; ?>
      </div>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>