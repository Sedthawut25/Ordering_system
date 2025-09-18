<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_role(array $allowed_roles) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ระบบจัดซื้อจัดจ้าง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/procurement_system/assets/css/style.css">
</head>

<body>
    <!-- Top navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">ระบบจัดซื้อ</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-white">สวัสดี <?= htmlspecialchars($_SESSION['name'] ?? '') ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/procurement_system/logout.php">ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">