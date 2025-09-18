<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Purchasing']);
require_once __DIR__ . '/../db.php';

// Create tax report
if (isset($_GET['create']) && ($_GET['create']) > 0) {
    $orderId = (int)$_GET['create'];
    // Insert report
    $ins = $pdo->prepare('INSERT INTO purchase_tax_reports (purchase_order_id, created_at) VALUES (?, NOW())');
    $ins->execute([$orderId]);
    header('Location: tax_reports.php');
    exit;
}

// Fetch existing reports
$reports = $pdo->query('SELECT ptr.*, po.id AS order_id, s.company_name FROM purchase_tax_reports ptr JOIN purchase_orders po ON ptr.purchase_order_id = po.id JOIN quotations q ON po.quotation_id = q.id JOIN sellers s ON q.seller_id = s.id ORDER BY ptr.id DESC')->fetchAll(PDO::FETCH_ASSOC);
// Fetch purchase orders that are approved and not yet in reports
$stmt = $pdo->prepare('SELECT po.id, s.company_name FROM purchase_orders po JOIN quotations q ON po.quotation_id = q.id JOIN sellers s ON q.seller_id = s.id WHERE po.status = "Approved" AND po.id NOT IN (SELECT purchase_order_id FROM purchase_tax_reports)');
$stmt->execute();
$availableOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/products.php">จัดการสินค้า</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/open_requests.php">ใบขอซื้อเปิดประกาศ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/compare_quotes.php">เปรียบเทียบใบเสนอราคา</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/purchase_orders.php">ใบสั่งซื้อ</a></li>
            <li class="nav-item"><a class="nav-link active" href="/procurement_system/purchasing/tax_reports.php">รายงานภาษีซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">รายงานภาษีซื้อ</h2>
    <h5 class="mt-4">สร้างรายงานใหม่</h5>
    <?php if ($availableOrders): ?>
        <ul class="list-group mb-4">
            <?php foreach ($availableOrders as $order): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ใบสั่งซื้อ #<?= $order['id'] ?> | ผู้ขาย: <?= htmlspecialchars($order['company_name']) ?>
                    <a href="?create=<?= $order['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('ยืนยันสร้างรายงานภาษีสำหรับใบสั่งซื้อนี้?')">สร้างรายงาน</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>ไม่มีใบสั่งซื้อที่รอสร้างรายงาน</p>
    <?php endif; ?>
    <h5 class="mt-4">รายงานที่สร้างแล้ว</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>รหัสรายงาน</th>
                    <th>ใบสั่งซื้อ</th>
                    <th>ผู้ขาย</th>
                    <th>วันที่สร้าง</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= $r['order_id'] ?></td>
                        <td><?= htmlspecialchars($r['company_name']) ?></td>
                        <td><?= htmlspecialchars($r['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($reports)): ?>
                    <tr>
                        <td colspan="4" class="text-center">ยังไม่มีรายงานภาษีซื้อ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>