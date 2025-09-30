<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
  header('Location: /procurement_system/login.php');
  exit;
}

$employeeId = (int)($_SESSION['user_id'] ?? 0);
if ($employeeId <= 0) {
  header('Location: /procurement_system/login.php');
  exit;
}

$message = '';

function column_exists(PDO $pdo, string $table, string $col): bool {
  $sql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1";
  $st = $pdo->prepare($sql);
  $st->execute([$table, $col]);
  return (bool)$st->fetchColumn();
}

function th_status(string $s): string {
  $map = [
    // สถานะของ PR
    'Pending'             => 'รอดำเนินการ',
    'ApprovedByDeptHead'  => 'อนุมัติโดยหัวหน้าแผนก',
    'RejectedByDeptHead'  => 'ไม่อนุมัติโดยหัวหน้าแผนก',
    'OpenForBid'          => 'เปิดประกาศให้เสนอราคา',
    'Ordered'             => 'ออกใบสั่งซื้อแล้ว',

    'Approved'            => 'อนุมัติ',
    'PendingApproval'     => 'รออนุมัติ',
    'Rejected'            => 'ไม่อนุมัติ',
    'Cancelled'           => 'ยกเลิก',
  ];
  return $map[$s] ?? $s; 
}
$products = $pdo->query('SELECT id, name FROM products ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

/* ================= create request + items ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_request'])) {
  $reason = trim($_POST['reason'] ?? '');
  $pids   = $_POST['product_id'] ?? [];
  $qtys   = $_POST['qty'] ?? [];

  if ($reason === '') {
    $message = 'กรุณาระบุเหตุผลการขอซื้อ';
  } else {
    $lines = [];
    $n = max(count($pids), count($qtys));
    for ($i = 0; $i < $n; $i++) {
      $pid = (int)($pids[$i] ?? 0);
      $q   = (float)($qtys[$i] ?? 0);
      if ($pid > 0 && $q > 0) $lines[] = [$pid, $q];
    }

    if (empty($lines)) {
      $message = 'กรุณาเลือกสินค้าและจำนวนอย่างน้อย 1 รายการ';
    } else {
      $priTable = 'purchase_request_items';
      $reqCol   = column_exists($pdo, $priTable, 'request_id') ? 'request_id'
        : (column_exists($pdo, $priTable, 'purchase_request_id') ? 'purchase_request_id' : null);
      $qtyCol   = column_exists($pdo, $priTable, 'quantity') ? 'quantity'
        : (column_exists($pdo, $priTable, 'qty') ? 'qty' : null);
      if ($reqCol === null || $qtyCol === null) {
        die('purchase_request_items: ไม่พบคอลัมน์ request_id/purchase_request_id หรือ quantity/qty');
      }

      try {
        $pdo->beginTransaction();

        // head
        $stmt = $pdo->prepare('
          INSERT INTO purchase_requests (employee_id, reason, status, created_at)
          VALUES (?, ?, "Pending", NOW())
        ');
        $stmt->execute([$employeeId, $reason]);
        $reqId = (int)$pdo->lastInsertId();

        // items
        $insItem = $pdo->prepare("
          INSERT INTO {$priTable} (`{$reqCol}`, product_id, `{$qtyCol}`)
          VALUES (?, ?, ?)
        ");
        foreach ($lines as [$pid, $q]) {
          $insItem->execute([$reqId, $pid, $q]);
        }

        $pdo->commit();
        $message = 'สร้างใบขอซื้อเรียบร้อยแล้ว (#' . $reqId . ')';
      } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
      }
    }
  }
}


$sql = 'SELECT id, reason, status, created_at
        FROM purchase_requests
        WHERE employee_id = ? AND status <> "RejectedByDeptHead"
        ORDER BY id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute([$employeeId]);
$myRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$itemsByReq = [];
if (!empty($myRequests)) {
  $priTable = 'purchase_request_items';
  $reqCol   = column_exists($pdo, $priTable, 'request_id') ? 'request_id'
    : (column_exists($pdo, $priTable, 'purchase_request_id') ? 'purchase_request_id' : null);
  $qtyCol   = column_exists($pdo, $priTable, 'quantity') ? 'quantity'
    : (column_exists($pdo, $priTable, 'qty') ? 'qty' : null);
  if ($reqCol === null || $qtyCol === null) {
    die('purchase_request_items: ไม่พบคอลัมน์ request_id/purchase_request_id หรือ quantity/qty');
  }

  $ids = array_map('intval', array_column($myRequests, 'id'));
  $ids = array_values(array_filter($ids, fn($v) => $v > 0));
  if (!empty($ids)) {
    $ph = implode(',', array_fill(0, count($ids), '?'));
    $sqlItems = "
      SELECT pri.`$reqCol` AS request_id, pri.product_id, pri.`$qtyCol` AS quantity,
             p.name AS product_name
      FROM {$priTable} pri
      JOIN products p ON p.id = pri.product_id
      WHERE pri.`$reqCol` IN ($ph)
      ORDER BY p.name
    ";
    $stI = $pdo->prepare($sqlItems);
    $stI->execute($ids);
    while ($row = $stI->fetch(PDO::FETCH_ASSOC)) {
      $rid = (int)$row['request_id'];
      $itemsByReq[$rid][] = $row;
    }
  }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
  <div class="row g-0">

    <!-- Sidebar (desktop) -->
    <aside class="col-lg-2 d-none d-lg-block sidebar">
      <div class="sidebar-title">เมนูพนักงาน</div>
      <nav class="nav flex-column">
        <a class="nav-link <?= nav_active('/employee/index.php') ?>" href="/procurement_system/employee/index.php">
          <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/employee/purchase_requests') ?>" href="/procurement_system/employee/purchase_requests.php">
          <i class="bi bi-file-text me-2"></i> จัดการขอซื้อสินค้า
        </a>
        <a class="nav-link <?= nav_active('/employee/low_stock') ?>" href="/procurement_system/employee/low_stock.php">
          <i class="bi bi-box-seam me-2"></i> สินค้าใกล้หมด
        </a>
      </nav>
    </aside>

    <!-- Offcanvas (mobile) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">
          <i class="bi bi-person-badge-fill me-1"></i> เมนูพนักงาน
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body offcanvas-nav">
        <a class="nav-link <?= nav_active('/employee/index.php') ?>" href="/procurement_system/employee/index.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-speedometer2 me-2"></i> แดชบอร์ด
        </a>
        <a class="nav-link <?= nav_active('/employee/purchase_requests') ?>" href="/procurement_system/employee/purchase_requests.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-file-text me-2"></i> จัดการขอซื้อสินค้า
        </a>
        <a class="nav-link <?= nav_active('/employee/low_stock') ?>" href="/procurement_system/employee/low_stock.php" data-bs-dismiss="offcanvas">
          <i class="bi bi-box-seam me-2"></i> สินค้าใกล้หมด
        </a>
      </div>
    </div>

    <!-- Main -->
    <main class="col-lg-10 app-content">
      <h2 class="mb-3">จัดการขอซื้อสินค้า</h2>

      <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <!-- ฟอร์มสร้างใบขอซื้อ -->
      <div class="card mb-4">
        <div class="card-header bg-light"><strong>สร้างใบขอซื้อใหม่</strong></div>
        <div class="card-body">
          <form method="post" id="prForm" autocomplete="off">
            <div class="mb-3">
              <label class="form-label" for="reason">เหตุผลการขอซื้อ</label>
              <input type="text" class="form-control" id="reason" name="reason" placeholder="ระบุเหตุผล" required>
            </div>

            <div id="lineItems">
              <div class="row g-2 align-items-end pr-line">
                <div class="col-md-6">
                  <label class="form-label">สินค้า</label>
                  <select class="form-select" name="product_id[]">
                    <option value="">-- เลือกสินค้า --</option>
                    <?php foreach ($products as $p): ?>
                      <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">จำนวน</label>
                  <input type="number" step="0.01" min="0" class="form-control" name="qty[]" placeholder="0">
                </div>
                <div class="col-md-3">
                  <button type="button" class="btn btn-outline-secondary mt-4" onclick="removeLine(this)">ลบรายการ</button>
                </div>
              </div>
            </div>

            <div class="mt-3">
              <button type="button" class="btn btn-outline-primary" onclick="addLine()">+ เพิ่มรายการ</button>
              <button type="submit" name="create_request" class="btn btn-primary">สร้างใบขอซื้อ</button>
            </div>
          </form>
        </div>
      </div>

      <!-- ใบขอซื้อของฉัน -->
      <h5>ใบขอซื้อของฉัน</h5>
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th>เลขที่</th>
              <th>เหตุผล</th>
              <th>สถานะ</th>
              <th>วันที่สร้าง</th>
              <th style="width:130px">จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($myRequests as $r): ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= htmlspecialchars($r['reason'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars(th_status($r['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <button type="button"
                          class="btn btn-sm btn-outline-primary"
                          data-bs-toggle="modal"
                          data-bs-target="#prDetailModal"
                          data-req="<?= (int)$r['id'] ?>">
                    ดูรายละเอียด
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($myRequests)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted">ยังไม่มีใบขอซื้อ</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Modal รายละเอียด -->
      <div class="modal fade" id="prDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="prDetailTitle">รายละเอียดใบขอซื้อ</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="table-responsive">
                <table class="table table-sm mb-0">
                  <thead class="table-light">
                    <tr>
                      <th style="width:140px">รหัสสินค้า (PK)</th>
                      <th>ชื่อสินค้า</th>
                      <th class="text-end" style="width:120px">จำนวน</th>
                    </tr>
                  </thead>
                  <tbody id="prDetailBody"></tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
          </div>
        </div>
      </div>

      <script>
        const PR_ITEMS = <?= json_encode($itemsByReq ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

        function escapeHtml(s) {
          return String(s).replace(/[&<>"']/g, m => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
          }[m]));
        }

        const prModal = document.getElementById('prDetailModal');
        prModal.addEventListener('show.bs.modal', function(ev) {
          const btn = ev.relatedTarget;
          const rid = btn.getAttribute('data-req');
          const title = prModal.querySelector('#prDetailTitle');
          const body = prModal.querySelector('#prDetailBody');

          title.textContent = 'รายละเอียดใบขอซื้อ #' + rid;
          body.innerHTML = '';

          const rows = PR_ITEMS[rid] || [];
          if (!rows.length) {
            body.innerHTML = '<tr><td colspan="3" class="text-center text-muted">ยังไม่มีรายการสินค้า</td></tr>';
            return;
          }
          let html = '';
          rows.forEach(r => {
            html += `
              <tr>
                <td>${escapeHtml(r.product_id)}</td>
                <td>${escapeHtml(r.product_name)}</td>
                <td class="text-end">${Number(r.quantity).toLocaleString()}</td>
              </tr>`;
          });
          body.innerHTML = html;
        });

        function addLine() {
          const wrap = document.getElementById('lineItems');
          const first = wrap.querySelector('.pr-line');
          const clone = first.cloneNode(true);
          clone.querySelectorAll('select,input').forEach(el => el.value = '');
          wrap.appendChild(clone);
        }

        function removeLine(btn) {
          const wrap = document.getElementById('lineItems');
          const line = btn.closest('.pr-line');
          if (wrap.querySelectorAll('.pr-line').length > 1) line.remove();
        }
      </script>
    </main>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
