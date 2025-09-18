<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Seller']);
require_once __DIR__ . '/../db.php';

$seller_id = $_SESSION['seller_id'] ?? null;
if (!$seller_id || ($_SESSION['role'] ?? '') !== 'Seller') {
    header('Location: /procurement_system/login.php');
    exit;
}

// Fetch open requests
$stmt = $pdo->prepare('SELECT pr.*, e.name AS employee_name FROM purchase_requests pr JOIN employees e ON pr.employee_id = e.id WHERE pr.status = "OpenForBid" ORDER BY pr.id DESC');
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link active" href="/procurement_system/seller/purchase_requests.php">ดูใบขอซื้อ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/quotations.php">จัดการใบเสนอราคา</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/purchase_orders.php">ดูใบสั่งซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">ใบขอซื้อที่เปิดประกาศ</h2>
    <div class="accordion" id="sellerRequestsAccordion">
        <?php foreach ($requests as $req): ?>
            <?php
            // Check if this seller has already quoted for this request
            $check = $pdo->prepare('SELECT id FROM quotations WHERE purchase_request_id = ? AND seller_id = ?');
            $check->execute([$req['id'], $seller_id]);
            $hasQuote = $check->fetchColumn();
            ?>
            <div class="accordion-item mb-2">
                <h2 class="accordion-header" id="heading<?= $req['id'] ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $req['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $req['id'] ?>">
                        ใบขอซื้อ #<?= $req['id'] ?> | พนักงาน: <?= htmlspecialchars($req['employee_name']) ?> | วันที่ <?= htmlspecialchars($req['request_date']) ?>
                    </button>
                </h2>
                <div id="collapse<?= $req['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $req['id'] ?>" data-bs-parent="#sellerRequestsAccordion">
                    <div class="accordion-body">
                        <p><strong>เหตุผล:</strong> <?= htmlspecialchars($req['reason']) ?></p>
                        <?php
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
                        <?php if ($hasQuote): ?>
                            <span class="badge bg-secondary">ท่านได้เสนอราคาแล้ว</span>
                        <?php else: ?>
                            <a href="/procurement_system/seller/quotations.php?create=<?= $req['id'] ?>" class="btn btn-primary">เสนอราคา</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($requests)): ?>
            <p>ยังไม่มีใบขอซื้อที่เปิดประกาศ</p>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>