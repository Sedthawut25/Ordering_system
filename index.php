<?php
// /procurement_system/login.php
session_start();
require_once __DIR__ . '/db.php';

// ฟังก์ชัน redirect
function redirect_by_role(string $role): void
{
  switch ($role) {
    case 'Admin':
      header('Location: /procurement_system/admin/index.php');
      break;
    case 'DeptHead':
      header('Location: /procurement_system/dept_head/approve_requests.php');
      break;
    case 'PurchasingHead':
      header('Location: /procurement_system/purchasing_head/index.php');
      break;
    case 'Purchasing':
      header('Location: /procurement_system/purchasing/index.php');
      break;
    case 'Seller': // ✅ ผู้ขาย
      header('Location: /procurement_system/seller/index.php');
      break;
    default: // Employee
      header('Location: /procurement_system/employee/index.php');
      break;
  }
  exit;
}

// ถ้า login ค้างอยู่แล้ว
if (!empty($_SESSION['role']) && basename($_SERVER['PHP_SELF']) === 'login.php') {
  redirect_by_role($_SESSION['role']);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($email === '' || $password === '') {
    $error = 'กรุณากรอกอีเมลและรหัสผ่าน';
  } else {
    // ✅ 1) ตรวจสอบ employees ก่อน
    $sql = "
            SELECT
                e.id, e.name, e.email, e.password, e.dept_id,
                d.base_role, d.head_id
            FROM employees e
            LEFT JOIN departments d ON d.id = e.dept_id
            WHERE e.email = ?
            LIMIT 1
        ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($u && password_verify($password, $u['password'])) {
      // คำนวณ role
      $role = 'Employee';
      $base = $u['base_role'] ?? null;
      $isHead = !empty($u['head_id']) && (int)$u['head_id'] === (int)$u['id'];

      if ($base === 'Admin') {
        $role = 'Admin';
      } elseif ($isHead && $base === 'Purchasing') {
        $role = 'PurchasingHead';
      } elseif ($base === 'Purchasing') {
        $role = 'Purchasing';
      } elseif ($isHead) {
        $role = 'DeptHead';
      }

      // เซ็ต session
      $_SESSION['user_id'] = (int)$u['id'];
      $_SESSION['name']    = $u['name'];
      $_SESSION['email']   = $u['email'];
      $_SESSION['dept_id'] = $u['dept_id'];
      $_SESSION['role']    = $role;

      redirect_by_role($role);
    } else {
      // ✅ 2) ถ้าไม่เจอใน employees → ไปเช็ค sellers
      $sql2 = "SELECT id, contact_name, company_name, email, password, status 
                     FROM sellers WHERE email = ? LIMIT 1";
      $stmt2 = $pdo->prepare($sql2);
      $stmt2->execute([$email]);
      $s = $stmt2->fetch(PDO::FETCH_ASSOC);

      if ($s && password_verify($password, $s['password'])) {
        if ($s['status'] !== 'Active') {
          $error = 'บัญชีผู้ขายยังไม่ได้รับอนุมัติ';
        } else {
          // เซ็ต session สำหรับ seller
          $_SESSION['seller_id']   = (int)$s['id'];
          $_SESSION['name']        = $s['contact_name'];
          $_SESSION['email']       = $s['email'];
          $_SESSION['company']     = $s['company_name'];
          $_SESSION['role']        = 'Seller';

          redirect_by_role('Seller'); // ✅ ไป seller/index.php
        }
      } else {
        $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
      }
    }
  }
}
?>
<!doctype html>
<html lang="th">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>เข้าสู่ระบบ | ระบบจัดซื้อ</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom Styles -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-light auth-bg">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow-sm auth-card">
          <div class="card-body p-4">

            <!-- หัวข้อระบบ -->
            <div class="text-center mb-4">
              <div class="brand-title">
                <i class="bi bi-shop me-2 text-primary"></i> ร้านที่ซื้อทุกอย่าง
              </div>
            </div>

            <!-- หัวข้อเข้าสู่ระบบ -->
            <h4 class="mb-3 text-center">เข้าสู่ระบบ</h4>

            <?php if (!empty($error)): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="off">
              <div class="mb-3">
                <label class="form-label"><i class="bi bi-envelope me-1"></i> อีเมล</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label"><i class="bi bi-lock me-1"></i> รหัสผ่าน</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ
              </button>
            </form>

            <div class="text-center mt-3">
              <a href="/procurement_system/register.php" class="small">
                <i class="bi bi-person-plus"></i> สมัครสมาชิกผู้ขาย
              </a>
            </div>
          </div>
        </div>
        <p class="text-center text-muted small mt-3 mb-0">
          © <?= date('Y') ?> Procurement System
        </p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>