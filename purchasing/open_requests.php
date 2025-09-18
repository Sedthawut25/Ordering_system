<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Purchasing']);
require_once __DIR__ . '/../db.php';

// If action to open for bid
if (isset($_GET['open'])) {
    $reqId = (int)$_GET['open'];
    $stmt = $pdo->prepare('UPDATE purchase_requests SET status = "OpenForBid" WHERE id = ?');
    $stmt->execute([$reqId]);
    header('Location: open_requests.php');
    exit;
}

// Fetch requests approved by dept head but not yet open for bid
$stmt = $pdo->prepare('SELECT pr.*, e.name AS employee_name FROM purchase_requests pr JOIN employees e ON pr.employee_id = e.id WHERE pr.status = "ApprovedByDeptHead" ORDER BY pr.id DESC');
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/products.php">จัดการสินค้า</a></li>
            <li class="nav-item"><a class="nav-link active" href="/procurement_system/purchasing/open_requests.php">ใบขอซื้อเปิดประกาศ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/compare_quotes.php">เปรียบเทียบใบเสนอราคา</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/purchase_orders.php">ใบสั่งซื้อ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/tax_reports.php">รายงานภาษีซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">ใบขอซื้อที่รอเปิดประกาศ</h2>
    <div class="accordion" id="requestsList">
        <?php foreach ($requests as $req): ?>
            <div class="accordion-item mb-2">
                <h2 class="accordion-header" id="heading<?= $req['id'] ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $req['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $req['id'] ?>">
                        ใบขอซื้อ #<?= $req['id'] ?> | พนักงาน: <?= htmlspecialchars($req['employee_name']) ?> | วันที่ <?= htmlspecialchars($req['request_date']) ?>
                    </button>
                </h2>
                <div id="collapse<?= $req['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $req['id'] ?>" data-bs-parent="#requestsList">
                    <div class="accordion-body">
                        <p><strong>เหตุผล:</strong> <?= htmlspecialchars($req['reason']) ?></p>
                        <?php
                        // Items
                        $itemsStmt = $pdo->prepare('SELECT pri.quantity, p.name FROM purchase_request_items pri JOIN products p ON pri.product_id = p.id WHERE pri.purchase_request_id = ?');
                        $itemsStmt->execute([$req['id']]);
                        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <table class="table table-sm table-bordered mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th>สินค้า</th>
                                    <th>จำนวน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $it): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($it['name']) ?></td>
                                        <td><?= $it['quantity'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <a href="?open=<?= $req['id'] ?>" class="btn btn-primary" onclick="return confirm('ยืนยันการเปิดประกาศใบขอซื้อนี้?')">เปิดประกาศให้ผู้ขายเสนอราคา</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($requests)): ?>
            <p>ไม่มีใบขอซื้อที่รอการเปิดประกาศ</p>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>