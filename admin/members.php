<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Admin']);
require_once __DIR__ . '/../db.php';

// Optionally handle deletion (soft delete by status?)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('UPDATE sellers SET status = "Inactive" WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: members.php');
    exit;
}

// Fetch sellers
$sellers = $pdo->query('SELECT * FROM sellers ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="employees.php">จัดการพนักงาน</a></li>
            <li class="nav-item"><a class="nav-link" href="product_types.php">จัดการประเภทสินค้า</a></li>
            <li class="nav-item"><a class="nav-link" href="departments.php">จัดการแผนก</a></li>
            <li class="nav-item"><a class="nav-link" href="payment_types.php">จัดการประเภทการจ่าย</a></li>
            <li class="nav-item"><a class="nav-link active" href="members.php">ดูสมาชิกผู้ขาย</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">สมาชิกผู้ขาย</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>รหัส</th>
                    <th>ชื่อผู้ติดต่อ</th>
                    <th>ชื่อบริษัท</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทร</th>
                    <th>สถานะ</th>
                    <th>ปิดใช้งาน</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sellers as $seller): ?>
                    <tr>
                        <td><?= $seller['id'] ?></td>
                        <td><?= htmlspecialchars($seller['contact_name']) ?></td>
                        <td><?= htmlspecialchars($seller['company_name']) ?></td>
                        <td><?= htmlspecialchars($seller['email']) ?></td>
                        <td><?= htmlspecialchars($seller['phone']) ?></td>
                        <td><?= htmlspecialchars($seller['status']) ?></td>
                        <td>
                            <?php if ($seller['status'] === 'Active'): ?>
                                <a href="?delete=<?= $seller['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการปิดใช้งานผู้ขายคนนี้?')">ปิดใช้งาน</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>