<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สมัครสมาชิกผู้ขาย | ระบบจัดซื้อ</title>

    <!-- Font + Bootstrap + Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ไฟล์หลักของคุณ -->
    <link rel="stylesheet" href="/procurement_system/assets/css/style.css">
</head>

<body class="auth-bg d-flex align-items-center">
    <!-- ไอคอนพื้นหลังสไตล์เดียวกับหน้า login -->
    <div class="bg-floating">
        <i class="fi fi-1 bi-bag"></i>
        <i class="fi fi-2 bi-geo-alt"></i>
        <i class="fi fi-3 bi-cart2"></i>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="card auth-card">
                    <div class="card-header text-center">
                        <div class="brand-title">
                            <span class="badge-soft"><i class="bi bi-shop"></i> สมัครสมาชิกผู้ขาย</span>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger mb-4"><?= htmlspecialchars($error) ?></div>
                        <?php elseif (!empty($success)): ?>
                            <div class="alert alert-success mb-4"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <form method="post" autocomplete="off" class="g-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อผู้ติดต่อ</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" name="contact_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อบริษัท/ร้านค้า</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                                        <input type="text" class="form-control" name="company_name" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">ที่อยู่</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <textarea class="form-control" name="address" rows="2" required></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">เบอร์โทร</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" class="form-control" name="phone" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">อีเมล</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">รหัสผ่าน</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ยืนยันรหัสผ่าน</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                </div>

                                <div class="col-12 pt-2">
                                    <button type="submit" class="btn btn-brand w-100">
                                        <i class="bi bi-person-plus me-1"></i> สมัครสมาชิก
                                    </button>
                                </div>
                            </div>
                        </form>

                        <hr class="my-4">
                        <p class="text-center already mb-0">
                            เป็นสมาชิกอยู่แล้ว?
                            <a href="/procurement_system/index.php"><i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบที่นี่</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>