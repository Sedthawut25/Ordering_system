<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Employee']);
require_once __DIR__ . '/../db.php';

// Fetch products below minimum stock
$stmt = $pdo->query('SELECT p.id, p.name, p.quantity, p.min_stock FROM products p WHERE p.quantity < p.min_stock ORDER BY p.name');
$lowStock = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/employee/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/employee/purchase_requests.php">จัดการขอซื้อสินค้า</a></li>
            <li class="nav-item"><a class="nav-link active" href="/procurement_system/employee/low_stock.php">สินค้าใกล้หมด</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">รายการสินค้าที่ต่ำกว่าคงคลังขั้นต่ำ</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>รหัสสินค้า</th>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวนคงเหลือ</th>
                    <th>ขั้นต่ำที่ควรมี</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lowStock as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= $product['quantity'] ?></td>
                        <td><?= $product['min_stock'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($lowStock)): ?>
                    <tr>
                        <td colspan="4" class="text-center">ไม่มีสินค้าใกล้หมด</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>