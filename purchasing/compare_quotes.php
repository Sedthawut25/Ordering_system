<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Purchasing']);
require_once __DIR__ . '/../db.php';

// Handle selection of quotation to create purchase order
if (isset($_GET['selectQuote'])) {
    $quoteId = (int)$_GET['selectQuote'];
    // Fetch quotation and associated request
    $stmt = $pdo->prepare('SELECT purchase_request_id FROM quotations WHERE id = ?');
    $stmt->execute([$quoteId]);
    $purchaseRequestId = (int)$stmt->fetchColumn();
    if ($purchaseRequestId) {
        // Insert purchase order
        $ins = $pdo->prepare('INSERT INTO purchase_orders (quotation_id, order_date, status) VALUES (?, NOW(), ?)');
        $ins->execute([$quoteId, 'PendingApproval']);
        // Update request status
        $updReq = $pdo->prepare('UPDATE purchase_requests SET status = "Ordered" WHERE id = ?');
        $updReq->execute([$purchaseRequestId]);
        // Mark quotation as selected
        $updQuote = $pdo->prepare('UPDATE quotations SET status = "Selected" WHERE id = ?');
        $updQuote->execute([$quoteId]);
    }
    header('Location: compare_quotes.php');
    exit;
}

// Fetch requests that are open for bid and have quotes
$stmt = $pdo->prepare('SELECT pr.id, pr.reason, pr.request_date FROM purchase_requests pr WHERE pr.status = "OpenForBid"');
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/products.php">จัดการสินค้า</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/open_requests.php">ใบขอซื้อเปิดประกาศ</a></li>
            <li class="nav-item"><a class="nav-link active" href="/procurement_system/purchasing/compare_quotes.php">เปรียบเทียบใบเสนอราคา</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/purchase_orders.php">ใบสั่งซื้อ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/tax_reports.php">รายงานภาษีซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">เปรียบเทียบใบเสนอราคา</h2>
    <?php if (empty($requests)): ?>
        <p>ยังไม่มีใบขอซื้อที่เปิดประกาศหรือยังไม่มีใบเสนอราคา</p>
    <?php endif; ?>
    <div class="accordion" id="compareAccordion">
        <?php foreach ($requests as $req): ?>
            <?php
            // Fetch quotations for this request
            $qStmt = $pdo->prepare('SELECT q.*, s.company_name FROM quotations q JOIN sellers s ON q.seller_id = s.id WHERE q.purchase_request_id = ?');
            $qStmt->execute([$req['id']]);
            $quotes = $qStmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$quotes) continue;
            ?>
            <div class="accordion-item mb-2">
                <h2 class="accordion-header" id="headingReq<?= $req['id'] ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReq<?= $req['id'] ?>" aria-expanded="false" aria-controls="collapseReq<?= $req['id'] ?>">
                        ใบขอซื้อ #<?= $req['id'] ?> | วันที่ <?= htmlspecialchars($req['request_date']) ?>
                    </button>
                </h2>
                <div id="collapseReq<?= $req['id'] ?>" class="accordion-collapse collapse" aria-labelledby="headingReq<?= $req['id'] ?>" data-bs-parent="#compareAccordion">
                    <div class="accordion-body">
                        <p><strong>เหตุผล:</strong> <?= htmlspecialchars($req['reason']) ?></p>
                        <?php foreach ($quotes as $quote): ?>
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>ใบเสนอราคา #<?= $quote['id'] ?> | ผู้ขาย: <?= htmlspecialchars($quote['company_name']) ?> | สถานะ: <?= htmlspecialchars($quote['status']) ?></span>
                                    <?php if ($quote['status'] === 'Pending'): ?>
                                        <a href="?selectQuote=<?= $quote['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('ยืนยันเลือกใบเสนอราคานี้เพื่อสร้างใบสั่งซื้อ?')">เลือกใบนี้</a>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $itemsStmt = $pdo->prepare('SELECT qi.quantity, qi.price, p.name FROM quotation_items qi JOIN products p ON qi.product_id = p.id WHERE qi.quotation_id = ?');
                                    $itemsStmt->execute([$quote['id']]);
                                    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>สินค้า</th>
                                                <th>จำนวน</th>
                                                <th>ราคาต่อหน่วย</th>
                                                <th>ราคารวม</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total = 0; ?>
                                            <?php foreach ($items as $it): ?>
                                                <?php $lineTotal = $it['quantity'] * $it['price'];
                                                $total += $lineTotal; ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($it['name']) ?></td>
                                                    <td><?= $it['quantity'] ?></td>
                                                    <td><?= number_format($it['price'], 2) ?></td>
                                                    <td><?= number_format($lineTotal, 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>รวมทั้งสิ้น</strong></td>
                                                <td><strong><?= number_format($total, 2) ?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>