<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Seller']);
require_once __DIR__ . '/../db.php';

$seller_id = $_SESSION['seller_id'] ?? null;
if (!$seller_id || ($_SESSION['role'] ?? '') !== 'Seller') {
    header('Location: /procurement_system/login.php');
    exit;
}

// Fetch purchase orders belonging to this seller
$stmt = $pdo->prepare('SELECT po.*, pr.id AS request_id, po.status FROM purchase_orders po JOIN quotations q ON po.quotation_id = q.id JOIN purchase_requests pr ON q.purchase_request_id = pr.id WHERE q.seller_id = ? ORDER BY po.id DESC');
$stmt->execute([$seller_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/purchase_requests.php">ดูใบขอซื้อ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/quotations.php">จัดการใบเสนอราคา</a></li>
            <li class="nav-item"><a class="nav-link active" href="/procurement_system/seller/purchase_orders.php">ดูใบสั่งซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">ใบสั่งซื้อของฉัน</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>รหัสใบสั่งซื้อ</th>
                    <th>ใบขอซื้อ</th>
                    <th>วันที่สั่งซื้อ</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= $order['request_id'] ?></td>
                        <td><?= htmlspecialchars($order['order_date']) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="4" class="text-center">ยังไม่มีใบสั่งซื้อ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>