<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Purchasing']);
require_once __DIR__ . '/../db.php';

// Fetch purchase orders with details
$stmt = $pdo->query('SELECT po.*, q.purchase_request_id, s.company_name, q.status AS quote_status FROM purchase_orders po JOIN quotations q ON po.quotation_id = q.id JOIN sellers s ON q.seller_id = s.id ORDER BY po.id DESC');
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/products.php">จัดการสินค้า</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/open_requests.php">ใบขอซื้อเปิดประกาศ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/compare_quotes.php">เปรียบเทียบใบเสนอราคา</a></li>
            <li class="nav-item"><a class="nav-link active" href="/procurement_system/purchasing/purchase_orders.php">ใบสั่งซื้อ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/tax_reports.php">รายงานภาษีซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">ใบสั่งซื้อ</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>รหัสใบสั่งซื้อ</th>
                    <th>รหัสใบขอซื้อ</th>
                    <th>ผู้ขาย</th>
                    <th>วันที่สั่งซื้อ</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><?= $o['id'] ?></td>
                        <td><?= $o['purchase_request_id'] ?></td>
                        <td><?= htmlspecialchars($o['company_name']) ?></td>
                        <td><?= htmlspecialchars($o['order_date']) ?></td>
                        <td><?= htmlspecialchars($o['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="5" class="text-center">ยังไม่มีใบสั่งซื้อ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>