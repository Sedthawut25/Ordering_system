<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['PurchasingHead']);
require_once __DIR__ . '/../db.php';

// Approve order
if (isset($_GET['approve'])) {
    $orderId = (int)$_GET['approve'];
    // Update order status
    $stmt = $pdo->prepare('UPDATE purchase_orders SET status = "Approved" WHERE id = ?');
    $stmt->execute([$orderId]);
    // Update stock quantity for each product in quotation
    // Get quotation id
    $stmtQ = $pdo->prepare('SELECT quotation_id FROM purchase_orders WHERE id = ?');
    $stmtQ->execute([$orderId]);
    $quoteId = $stmtQ->fetchColumn();
    if ($quoteId) {
        // Get items and update stock
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

// Reject order
if (isset($_GET['reject'])) {
    $orderId = (int)$_GET['reject'];
    $stmt = $pdo->prepare('UPDATE purchase_orders SET status = "Rejected" WHERE id = ?');
    $stmt->execute([$orderId]);
    header('Location: approve_purchases.php');
    exit;
}

// Fetch orders pending approval
$stmt = $pdo->query('SELECT po.*, q.purchase_request_id, s.company_name FROM purchase_orders po JOIN quotations q ON po.quotation_id = q.id JOIN sellers s ON q.seller_id = s.id WHERE po.status = "PendingApproval" ORDER BY po.id');
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing_head/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link active" href="/procurement_system/purchasing_head/approve_purchases.php">อนุมัติใบสั่งซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">อนุมัติใบสั่งซื้อ</h2>
    <div class="accordion" id="ordersAccordion">
        <?php foreach ($orders as $o): ?>
            <div class="accordion-item mb-2">
                <h2 class="accordion-header" id="headingOrder<?= $o['id'] ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrder<?= $o['id'] ?>" aria-expanded="false" aria-controls="collapseOrder<?= $o['id'] ?>">
                        ใบสั่งซื้อ #<?= $o['id'] ?> | ผู้ขาย: <?= htmlspecialchars($o['company_name']) ?> | วันที่ <?= htmlspecialchars($o['order_date']) ?>
                    </button>
                </h2>
                <div id="collapseOrder<?= $o['id'] ?>" class="accordion-collapse collapse" aria-labelledby="headingOrder<?= $o['id'] ?>" data-bs-parent="#ordersAccordion">
                    <div class="accordion-body">
                        <!-- Show quotation items -->
                        <?php
                        $itemsStmt = $pdo->prepare('SELECT qi.quantity, qi.price, p.name FROM quotation_items qi JOIN quotations q ON qi.quotation_id = q.id JOIN products p ON qi.product_id = p.id WHERE q.id = ?');
                        $itemsStmt->execute([$o['quotation_id']]);
                        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <table class="table table-sm table-bordered mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th>สินค้า</th>
                                    <th>จำนวน</th>
                                    <th>ราคาต่อหน่วย</th>
                                    <th>ราคารวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = 0;
                                foreach ($items as $it): ?>
                                    <?php $line = $it['quantity'] * $it['price'];
                                    $total += $line; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($it['name']) ?></td>
                                        <td><?= $it['quantity'] ?></td>
                                        <td><?= number_format($it['price'], 2) ?></td>
                                        <td><?= number_format($line, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>รวม</strong></td>
                                    <td><strong><?= number_format($total, 2) ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <a href="?approve=<?= $o['id'] ?>" class="btn btn-success" onclick="return confirm('ยืนยันการอนุมัติใบสั่งซื้อนี้?')">อนุมัติ</a>
                        <a href="?reject=<?= $o['id'] ?>" class="btn btn-danger" onclick="return confirm('ยืนยันการปฏิเสธใบสั่งซื้อนี้?')">ไม่อนุมัติ</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
            <p>ไม่มีใบสั่งซื้อที่รอการอนุมัติ</p>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>