<?php
// procurement_system/purchasing/compare_quotes.php

/* ================= Bootstrap (ห้ามมี output ก่อน redirect/header) ================= */
// ❗ เดิม include header.php ก่อน check_role → หากสิทธิ์ไม่ตรงจะ redirect หลังมี output แล้ว error
// ✅ แก้: ตรวจสิทธิ์/ทำงานฝั่ง server-side ให้เสร็จ ก่อน include header.php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Purchasing') {
  header('Location: /procurement_system/login.php');
  exit;
}

/* ================= Action: เลือกใบเสนอราคาเพื่อสร้างใบสั่งซื้อ ================= */
// ❗ เดิมทำ 3 คำสั่ง (INSERT/UPDATE/UPDATE) แยกกัน หากคำสั่งใด fail ข้อมูลจะค้าง
// ✅ แก้: ครอบด้วย Transaction ให้สำเร็จ/ล้มเหลวพร้อมกัน และใช้ absolute path ตอน redirect
if (isset($_GET['selectQuote'])) {
  $quoteId = (int)$_GET['selectQuote'];

  $pdo->beginTransaction();
  try {
    // ดึง PR ที่สัมพันธ์กับ quotation
    $stmt = $pdo->prepare('SELECT purchase_request_id FROM quotations WHERE id = ?');
    $stmt->execute([$quoteId]);
    $purchaseRequestId = (int)$stmt->fetchColumn();

    if ($purchaseRequestId) {
      // สร้างใบสั่งซื้อ
      $ins = $pdo->prepare('INSERT INTO purchase_orders (quotation_id, order_date, status) VALUES (?, NOW(), ?)');
      $ins->execute([$quoteId, 'PendingApproval']);

      // อัปเดตสถานะใบขอซื้อ
      $updReq = $pdo->prepare('UPDATE purchase_requests SET status = "Ordered" WHERE id = ?');
      $updReq->execute([$purchaseRequestId]);

      // ทำเครื่องหมายใบเสนอราคาว่าถูกเลือก
      $updQuote = $pdo->prepare('UPDATE quotations SET status = "Selected" WHERE id = ?');
      $updQuote->execute([$quoteId]);

      // ทางเลือก: unselect ใบอื่นของ PR เดียวกัน (ถ้าต้องการให้เลือกได้เพียงใบเดียว)
      // $pdo->prepare('UPDATE quotations SET status="Unselected" WHERE purchase_request_id=? AND id<>?')
      //     ->execute([$purchaseRequestId, $quoteId]);
    }

    $pdo->commit();
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die('เกิดข้อผิดพลาด: ' . $e->getMessage());
  }

  header('Location: /procurement_system/purchasing/compare_quotes.php'); // ✅ absolute path
  exit;
}

/* ================= Data: โหลด PR ที่เปิดให้เปรียบเทียบราคา ================= */
// ⚠️ ตรวจสอบ workflow ของคุณให้แน่ใจว่า status ที่ต้องแสดงบนหน้านี้คือ "OpenForBid"
// หากใช้สถานะอื่นตอนมีใบเสนอราคาแล้ว เช่น "Quoted"/"WaitingForCompare" ให้ปรับตรงนี้
$stmt = $pdo->prepare('
    SELECT pr.id, pr.reason, pr.request_date
    FROM purchase_requests pr
    WHERE pr.status = "OpenForBid"
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
          // ดึงใบเสนอราคาของ PR นี้
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
                ใบขอซื้อ #<?= (int)$req['id'] ?> | วันที่ <?= htmlspecialchars($req['request_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
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
                      $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                      $total = 0;
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
                              $qty = (float)($it['quantity'] ?? 0);
                              $price = (float)($it['price'] ?? 0);
                              $lineTotal = $qty * $price;
                              $total += $lineTotal;
                            ?>
                              <tr>
                                <td><?= htmlspecialchars($it['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-end"><?= number_format($qty, 2) ?></td>
                                <td class="text-end"><?= number_format($price, 2) ?></td>
                                <td class="text-end"><?= number_format($lineTotal, 2) ?></td>
                              </tr>
                            <?php endforeach; ?>
                            <tr>
                              <td colspan="3" class="text-end"><strong>รวมทั้งสิ้น</strong></td>
                              <td class="text-end"><strong><?= number_format($total, 2) ?></strong></td>
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