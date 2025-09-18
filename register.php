<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// If a seller is already logged in, redirect to their dashboard
if (is_logged_in() && $_SESSION['role'] === 'Seller') {
    redirect_to_dashboard();
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_name = trim($_POST['contact_name'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if ($contact_name === '' || $company_name === '' || $email === '' || $password === '' || $confirm_password === '') {
        $error = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'รูปแบบอีเมลไม่ถูกต้อง';
    } elseif ($password !== $confirm_password) {
        $error = 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM sellers WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'อีเมลนี้ถูกใช้ไปแล้ว กรุณาใช้อีเมลอื่น';
        } else {
            // Insert new seller record
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare('INSERT INTO sellers (contact_name, company_name, address, phone, email, password, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $insert->execute([$contact_name, $company_name, $address, $phone, $email, $hash, 'Active']);
            $success = 'สมัครสมาชิกสำเร็จ! ท่านสามารถเข้าสู่ระบบได้แล้ว';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>สมัครสมาชิกผู้ขาย</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/procurement_system/assets/css/style.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">สมัครสมาชิกผู้ขาย</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>
                        <form method="post" autocomplete="off">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_name" class="form-label">ชื่อผู้ติดต่อ</label>
                                        <input type="text" class="form-control" id="contact_name" name="contact_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_name" class="form-label">ชื่อบริษัท/ร้านค้า</label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">ที่อยู่</label>
                                <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">เบอร์โทร</label>
                                        <input type="text" class="form-control" id="phone" name="phone" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">อีเมล</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">รหัสผ่าน</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">สมัครสมาชิก</button>
                            </div>
                        </form>
                        <hr>
                        <p class="text-center">เป็นสมาชิกอยู่แล้ว? <a href="/procurement_system/index.php">เข้าสู่ระบบที่นี่</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>