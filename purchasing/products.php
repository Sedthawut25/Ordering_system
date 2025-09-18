<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Purchasing']);
require_once __DIR__ . '/../db.php';

// Delete product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: products.php');
    exit;
}

// Insert new product
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $typeId = (int)($_POST['type_id'] ?? 0);
    $details = trim($_POST['details'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 0);
    $min_stock = (int)($_POST['min_stock'] ?? 0);
    $unit_price = (float)($_POST['unit_price'] ?? 0);
    if ($name && $typeId) {
        $stmt = $pdo->prepare('INSERT INTO products (name, product_type_id, details, quantity, min_stock, unit_price) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $typeId, $details, $quantity, $min_stock, $unit_price]);
        $message = 'เพิ่มสินค้าสำเร็จ';
    } else {
        $message = 'กรุณากรอกชื่อสินค้าและเลือกประเภท';
    }
}

// Fetch product types
$types = $pdo->query('SELECT id, name FROM product_types ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
// Fetch all products
$products = $pdo->query('SELECT p.*, pt.name AS type_name FROM products p LEFT JOIN product_types pt ON p.product_type_id = pt.id ORDER BY p.id')->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link active" href="/procurement_system/purchasing/products.php">จัดการสินค้า</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/open_requests.php">ใบขอซื้อเปิดประกาศ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/compare_quotes.php">เปรียบเทียบใบเสนอราคา</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/purchase_orders.php">ใบสั่งซื้อ</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/purchasing/tax_reports.php">รายงานภาษีซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">จัดการสินค้า</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div class="card mb-4">
        <div class="card-header">เพิ่มสินค้าใหม่</div>
        <div class="card-body">
            <form method="post">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">ชื่อสินค้า</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-3">
                        <label for="type_id" class="form-label">ประเภทสินค้า</label>
                        <select class="form-select" id="type_id" name="type_id" required>
                            <option value="">-- เลือกประเภท --</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="quantity" class="form-label">จำนวนเริ่มต้น</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="0" required>
                    </div>
                    <div class="col-md-2">
                        <label for="min_stock" class="form-label">ขั้นต่ำคงเหลือ</label>
                        <input type="number" class="form-control" id="min_stock" name="min_stock" min="0" value="0" required>
                    </div>
                    <div class="col-md-2">
                        <label for="unit_price" class="form-label">ราคาต่อหน่วย (บาท)</label>
                        <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" min="0" value="0" required>
                    </div>
                </div>
                <div class="mt-3">
                    <label for="details" class="form-label">รายละเอียด</label>
                    <textarea class="form-control" id="details" name="details" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3">เพิ่มสินค้า</button>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>รหัส</th>
                    <th>ชื่อสินค้า</th>
                    <th>ประเภท</th>
                    <th>จำนวน</th>
                    <th>ขั้นต่ำคงเหลือ</th>
                    <th>ราคาต่อหน่วย</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['type_name']) ?></td>
                        <td><?= $product['quantity'] ?></td>
                        <td><?= $product['min_stock'] ?></td>
                        <td><?= number_format($product['unit_price'], 2) ?></td>
                        <td><a href="?delete=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบสินค้านี้?')">ลบ</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>