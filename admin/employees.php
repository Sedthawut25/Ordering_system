<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Admin']);
require_once __DIR__ . '/../db.php';

/* ---------- helper: คำนวณชื่อ role จากแผนก/หัวหน้า ---------- */
function compute_role_name(PDO $pdo, ?int $dept_id, int $employee_id): string
{
    if (!$dept_id) return 'Employee';
    $stmt = $pdo->prepare('SELECT base_role, head_id FROM departments WHERE id=?');
    $stmt->execute([$dept_id]);
    $d = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$d) return 'Employee';

    $base = $d['base_role'] ?? 'Employee';
    $isHead = ((int)$d['head_id'] === (int)$employee_id);

    if ($isHead) {
        if ($base === 'Purchasing') return 'PurchasingHead';
        if ($base === 'Admin')      return 'Admin';
        return 'DeptHead';
    }
    return $base;
}

/* ---------- ลบ ---------- */
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM employees WHERE id = ?')->execute([$delId]);
    header('Location: employees.php');
    exit;
}

$message = '';
$editing = null;

/* ---------- โหลดข้อมูลที่จะแก้ไข ---------- */
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM employees WHERE id = ?');
    $stmt->execute([$id]);
    $editing = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$editing) $message = 'ไม่พบพนักงานที่ต้องการแก้ไข';
}

/* ---------- เพิ่ม/แก้ไข ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_id'])) {
        // Update
        $id      = (int)$_POST['update_id'];
        $name    = trim($_POST['name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $dept_id = ($_POST['dept_id'] !== '' ? (int)$_POST['dept_id'] : null);

        if ($name && $email) {
            $q = $pdo->prepare('SELECT id FROM employees WHERE email=? AND id<>? LIMIT 1');
            $q->execute([$email, $id]);
            if ($q->fetch()) {
                $message = 'อีเมลนี้ถูกใช้โดยผู้ใช้อื่นแล้ว';
            } else {
                $pdo->prepare('UPDATE employees SET name=?, phone=?, email=?, dept_id=? WHERE id=?')
                    ->execute([$name, $phone, $email, $dept_id, $id]);
                // sync role_id ถ้าคุณยังใช้ตาราง roles
                try {
                    $roleName = compute_role_name($pdo, $dept_id, $id);
                    $rid = $pdo->prepare('SELECT id FROM roles WHERE name=?');
                    $rid->execute([$roleName]);
                    $role_id = $rid->fetchColumn();
                    if ($role_id) {
                        $pdo->prepare('UPDATE employees SET role_id=? WHERE id=?')->execute([$role_id, $id]);
                    }
                } catch (Throwable $e) { /* ไม่มี roles ก็ข้ามได้ */
                }
                $message = 'อัปเดตข้อมูลเรียบร้อย';
                $editing = null;
            }
        } else {
            $message = 'กรุณากรอกชื่อและอีเมล';
        }
    } else {
        // Create
        $name     = trim($_POST['name'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $dept_id  = ($_POST['dept_id'] !== '' ? (int)$_POST['dept_id'] : null);
        $password = $_POST['password'] ?? '';

        if ($name && $email && $password) {
            $stmt = $pdo->prepare('SELECT id FROM employees WHERE email=? LIMIT 1');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = 'อีเมลนี้ถูกใช้แล้ว';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare('INSERT INTO employees (name, phone, email, dept_id, password) VALUES (?, ?, ?, ?, ?)')
                    ->execute([$name, $phone, $email, $dept_id, $hashed]);
                $newId = (int)$pdo->lastInsertId();
                // sync role_id ถ้ามีตาราง roles
                try {
                    $roleName = compute_role_name($pdo, $dept_id, $newId);
                    $rid = $pdo->prepare('SELECT id FROM roles WHERE name=?');
                    $rid->execute([$roleName]);
                    $role_id = $rid->fetchColumn();
                    if ($role_id) {
                        $pdo->prepare('UPDATE employees SET role_id=? WHERE id=?')->execute([$role_id, $newId]);
                    }
                } catch (Throwable $e) {
                }
                $message = 'บันทึกพนักงานเรียบร้อย';
            }
        } else {
            $message = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        }
    }
}

/* ---------- Data ---------- */
$depts = $pdo->query('SELECT id, name FROM departments ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT e.*,
               d.name AS dept_name,
               CASE
                 WHEN d.base_role='Purchasing' AND d.head_id = e.id THEN 'PurchasingHead'
                 WHEN d.base_role='Admin'      AND d.head_id = e.id THEN 'Admin'
                 WHEN d.head_id = e.id                              THEN 'DeptHead'
                 ELSE d.base_role
               END AS effective_role
        FROM employees e
        LEFT JOIN departments d ON d.id = e.dept_id
        ORDER BY e.id";
$employees = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="index.php">แดชบอร์ด</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="employees.php">จัดการพนักงาน</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="product_types.php">จัดการประเภทสินค้า</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="departments.php">จัดการแผนก</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="payment_types.php">จัดการประเภทการจ่าย</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="members.php">ดูสมาชิกผู้ขาย</a>
            </li>
        </ul>
    </div>
</nav>


<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">จัดการพนักงาน</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- ฟอร์มเพิ่ม -->
    <div class="card mb-4">
        <div class="card-header bg-light"><strong>เพิ่มพนักงานใหม่</strong></div>
        <div class="card-body">
            <form method="post">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">ชื่อพนักงาน</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">เบอร์โทร</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">อีเมล</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">แผนก</label>
                        <select class="form-select" name="dept_id">
                            <option value="">-- เลือกแผนก --</option>
                            <?php foreach ($depts as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">รหัสผ่าน</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary">บันทึกพนักงาน</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ฟอร์มแก้ไข -->
    <?php if ($editing): ?>
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white"><strong>แก้ไขพนักงาน: #<?= (int)$editing['id'] ?></strong></div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="update_id" value="<?= (int)$editing['id'] ?>">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">ชื่อพนักงาน</label>
                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($editing['name']) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">เบอร์โทร</label>
                            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($editing['phone']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">อีเมล</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($editing['email']) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">แผนก</label>
                            <select class="form-select" name="dept_id">
                                <option value="">-- เลือกแผนก --</option>
                                <?php foreach ($depts as $dept): ?>
                                    <option value="<?= $dept['id'] ?>" <?= ((int)$editing['dept_id'] === (int)$dept['id'] ? 'selected' : '') ?>>
                                        <?= htmlspecialchars($dept['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="employees.php" class="btn btn-outline-secondary">ยกเลิก</a>
                        <button class="btn btn-success">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- ตาราง -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>รหัส</th>
                    <th>ชื่อพนักงาน</th>
                    <th>เบอร์โทร</th>
                    <th>อีเมล</th>
                    <th>แผนก</th>
                    <th>ตำแหน่ง (คำนวณอัตโนมัติ)</th>
                    <th style="width:160px">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?= $emp['id'] ?></td>
                        <td><?= htmlspecialchars($emp['name']) ?></td>
                        <td><?= htmlspecialchars($emp['phone']) ?></td>
                        <td><?= htmlspecialchars($emp['email']) ?></td>
                        <td><?= htmlspecialchars($emp['dept_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($emp['effective_role'] ?? '—') ?></td>
                        <td>
                            <a class="btn btn-sm btn-warning" href="?edit=<?= $emp['id'] ?>">แก้ไข</a>
                            <a class="btn btn-sm btn-danger" href="?delete=<?= $emp['id'] ?>" onclick="return confirm('ยืนยันการลบพนักงานคนนี้?')">ลบ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>