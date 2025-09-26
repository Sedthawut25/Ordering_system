<?php
$current = $_SERVER['REQUEST_URI'] ?? '';
function nav_active($needle)
{
  global $current;
  return (strpos($current, $needle) !== false) ? 'active' : '';
}

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function check_role(array $allowed_roles)
{
  if (empty($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header('Location: /procurement_system/login.php');
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <!-- แก้ viewport: เอา shrink-to-fit ออก และตั้ง initial-scale=1.0 ให้คงที่ -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ระบบจัดซื้อจัดจ้าง</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Main stylesheet -->
  <link rel="stylesheet" href="/procurement_system/assets/css/style.css">
</head>

<body>

  <!-- Top navigation bar -->
  <nav class="navbar navbar-expand-lg navbar-dark brandbar">
    <div class="container-fluid">
      <!-- Hamburger for mobile -->
      <button class="btn btn-outline-light d-lg-none me-2" type="button"
        data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
        aria-controls="offcanvasSidebar">
        <i class="bi bi-list"></i>
      </button>

      <a class="navbar-brand d-flex align-items-center gap-2" href="/procurement_system/admin/index.php">
        <i class="bi bi-bag-check-fill"></i> ระบบจัดซื้อ
      </a>

      <div class="d-flex ms-auto align-items-center gap-3">
        <span class="badge text-bg-dark border badge-role">
          <i class="bi bi-person-badge me-1"></i>
          <?= htmlspecialchars($_SESSION['role'] ?? 'User') ?>
        </span>
        <span class="text-white-50 d-none d-sm-inline">สวัสดี</span>
        <span class="text-white fw-semibold"><?= htmlspecialchars($_SESSION['name'] ?? 'ผู้ใช้') ?></span>
        <a class="btn btn-outline-light btn-sm" href="/procurement_system/logout.php">
          <i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ
        </a>
      </div>
    </div>
  </nav>