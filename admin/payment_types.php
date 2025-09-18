<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Admin']);
require_once __DIR__ . '/../db.php';

// Delete payment type
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM payment_types WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: payment_types.php');
    exit;
}

// Insert new type
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name) {
        $stmt = $pdo->prepare('INSERT INTO payment_types (name) VALUES (?)');
        $stmt->execute([$name]);
        $message = 'เพิ่มประเภทการจ่ายเรียบร้อย';
    } else {
        $message = 'กรุณากรอกชื่อประเภทการจ่าย';
    }
}

// Fetch payment types
$types = $pdo->query('SELECT * FROM payment_types ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="employees.php">จัดการพนักงาน</a></li>
            <li class="nav-item"><a class="nav-link" href="product_types.php">จัดการประเภทสินค้า</a></li>
            <li class="nav-item"><a class="nav-link" href="departments.php">จัดการแผนก</a></li>
            <li class="nav-item"><a class="nav-link active" href="payment_types.php">จัดการประเภทการจ่าย</a></li>
            <li class="nav-item"><a class="nav-link" href="members.php">ดูสมาชิกผู้ขาย</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">จัดการประเภทการจ่าย</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post" class="mb-3 d-flex">
        <input type="text" name="name" class="form-control me-2" placeholder="ชื่อประเภทการจ่าย" required>
        <button type="submit" class="btn btn-primary">เพิ่ม</button>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>รหัส</th>
                    <th>ชื่อประเภทการจ่าย</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($types as $type): ?>
                    <tr>
                        <td><?= $type['id'] ?></td>
                        <td><?= htmlspecialchars($type['name']) ?></td>
                        <td><a href="?delete=<?= $type['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบประเภทนี้?')">ลบ</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>