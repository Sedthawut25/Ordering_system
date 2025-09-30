<?php
// procurement_system/purchasing/open_requests.php

/* ---------- Helpers: ฟอร์แมตวันที่ไทย + เช็คค่าวันที่ไม่ดี ---------- */
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
    $hms = date('H:i:s', $ts);
    return sprintf('%d %s %d %s', $d, $months[$m-1], $yBE, $hms);
  }
}
if (!function_exists('is_bad_date')) {
  function is_bad_date(?string $s): bool {
    if (!$s) return true;
    $s = trim($s);
    return $s === '0000-00-00' || $s === '0000-00-00 00:00:00';
  }
}

/* ---------- Auth ก่อนมี output ---------- */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Purchasing') {
  header('Location: /procurement_system/login.php');
  exit;
}

/* ---------- เปิดประกาศ ---------- */
if (isset($_GET['open'])) {
  $reqId = (int)$_GET['open'];
  $stmt = $pdo->prepare('UPDATE purchase_requests SET status = "OpenForBid" WHERE id = ?');
  $stmt->execute([$reqId]);
  header('Location: /procurement_system/purchasing/open_requests.php');
  exit;
}

/* ---------- โหลดใบขอซื้อที่อนุมัติแล้ว
      วันที่จะแสดง: created_at > request_date > first_quote_date ---------- */
$sql = '
  SELECT
    pr.*,
    e.name AS employee_name,
    MIN(q.quote_date) AS first_quote_date
  FROM purchase_requests pr
  JOIN employees e ON e.id = pr.employee_id
  LEFT JOIN quotations q ON q.purchase_request_id = pr.id
  WHERE pr.status = "ApprovedByDeptHead"
  GROUP BY pr.id
  ORDER BY pr.id DESC
';
$stmt = $pdo->query($sql);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- include header ---------- */
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar desktop -->
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
      <h2 class="mb-3">ใบขอซื้อที่รอเปิดประกาศ</h2>

      <div class="accordion" id="requestsList">
        <?php foreach ($requests as $req): ?>
          <?php
            // ใช้วันที่จาก "จัดการขอซื้อ" (created_at) ก่อน
            // หากว่าง/ไม่ดี → ใช้ request_date → หากยังว่าง → ใช้วันที่ใบเสนอราคาแรก
            $raw = !is_bad_date($req['created_at'] ?? null) ? $req['created_at']
                 : (!is_bad_date($req['request_date'] ?? null) ? $req['request_date']
                 : ($req['first_quote_date'] ?? null));

            $displayDate = fmt_thai_datetime_or_dash($raw);
            if ($displayDate === '-') $displayDate = 'ยังไม่ระบุ';
          ?>
          <div class="accordion-item mb-2">
            <h2 class="accordion-header" id="heading<?= (int)$req['id'] ?>">
              <button class="accordion-button collapsed" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapse<?= (int)$req['id'] ?>">
                ใบขอซื้อ #<?= (int)$req['id'] ?> |
                พนักงาน: <?= htmlspecialchars($req['employee_name'], ENT_QUOTES, 'UTF-8') ?> |
                วันที่ <?= $displayDate ?>
              </button>
            </h2>

            <div id="collapse<?= (int)$req['id'] ?>" class="accordion-collapse collapse"
              data-bs-parent="#requestsList">
              <div class="accordion-body">
                <p><strong>เหตุผล:</strong> <?= htmlspecialchars($req['reason'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>

                <?php
                  $itemsStmt = $pdo->prepare('
                    SELECT pri.quantity, p.name
                    FROM purchase_request_items pri
                    JOIN products p ON p.id = pri.product_id
                    WHERE pri.purchase_request_id = ?
                    ORDER BY p.name
                  ');
                  $itemsStmt->execute([(int)$req['id']]);
                  $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <div class="table-responsive mb-3">
                  <table class="table table-sm table-bordered">
                    <thead class="table-light">
                      <tr>
                        <th>สินค้า</th>
                        <th class="text-end" style="width:120px">จำนวน</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $it): ?>
                        <tr>
                          <td><?= htmlspecialchars($it['name'], ENT_QUOTES, 'UTF-8') ?></td>
                          <td class="text-end"><?= number_format((float)$it['quantity'], 2) ?></td>
                        </tr>
                      <?php endforeach; ?>
                      <?php if (empty($items)): ?>
                        <tr>
                          <td colspan="2" class="text-center text-muted">ไม่มีรายการสินค้า</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

                <a href="?open=<?= (int)$req['id'] ?>" class="btn btn-primary"
                  onclick="return confirm('ยืนยันการเปิดประกาศใบขอซื้อนี้?')">
                  เปิดประกาศให้ผู้ขายเสนอราคา
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

        <?php if (empty($requests)): ?>
          <p class="text-muted">ไม่มีใบขอซื้อที่รอการเปิดประกาศ</p>
        <?php endif; ?>
      </div>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
