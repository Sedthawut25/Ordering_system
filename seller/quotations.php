<?php
require_once __DIR__ . '/../includes/header.php';
check_role(['Seller']);
require_once __DIR__ . '/../db.php';

$seller_id = $_SESSION['seller_id'] ?? null;
if (!$seller_id || ($_SESSION['role'] ?? '') !== 'Seller') {
    header('Location: /procurement_system/login.php');
    exit;
}

// Creating new quote for a specific request
if (isset($_GET['create'])) {
    $requestId = (int)$_GET['create'];
    // Check if already quoted
    $exists = $pdo->prepare('SELECT id FROM quotations WHERE purchase_request_id = ? AND seller_id = ?');
    $exists->execute([$requestId, $sellerId]);
    if ($exists->fetch()) {
        header('Location: quotations.php');
        exit;
    }
    // Fetch request items
    $itemsStmt = $pdo->prepare('SELECT pri.product_id, pri.quantity, p.name FROM purchase_request_items pri JOIN products p ON pri.product_id = p.id WHERE pri.purchase_request_id = ?');
    $itemsStmt->execute([$requestId]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    // When form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Insert quotation record
        $qIns = $pdo->prepare('INSERT INTO quotations (purchase_request_id, seller_id, quote_date, status) VALUES (?, ?, NOW(), ?)');
        $qIns->execute([$requestId, $sellerId, 'Pending']);
        $quoteId = $pdo->lastInsertId();
        // Insert items
        foreach ($items as $it) {
            $price = (float)($_POST['price_' . $it['product_id']] ?? 0);
            $qty = $it['quantity'];
            $qi = $pdo->prepare('INSERT INTO quotation_items (quotation_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
            $qi->execute([$quoteId, $it['product_id'], $qty, $price]);
        }
        header('Location: quotations.php');
        exit;
    }
    ?>
    <!-- Sidebar -->
    <nav class="col-md-2 d-none d-md-block sidebar">
        <div class="position-sticky">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/index.php">แดชบอร์ด</a></li>
                <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/purchase_requests.php">ดูใบขอซื้อ</a></li>
                <li class="nav-item"><a class="nav-link active" href="/procurement_system/seller/quotations.php">จัดการใบเสนอราคา</a></li>
                <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/purchase_orders.php">ดูใบสั่งซื้อ</a></li>
            </ul>
        </div>
    </nav>
    <main class="col-md-10 ms-sm-auto px-md-4">
        <h2 class="mb-3">สร้างใบเสนอราคา</h2>
        <form method="post">
            <h5>ใบขอซื้อ #<?= $requestId ?></h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>สินค้า</th><th>จำนวนที่ขอ</th><th>ราคาต่อหน่วยของคุณ (บาท)</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td><?= htmlspecialchars($it['name']) ?></td>
                            <td><?= $it['quantity'] ?></td>
                            <td>
                                <input type="number" step="0.01" name="price_<?= $it['product_id'] ?>" class="form-control" required>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-success">ส่งใบเสนอราคา</button>
            <a href="quotations.php" class="btn btn-secondary">ยกเลิก</a>
        </form>
    </main>
    <?php require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Otherwise show list of quotations
$quotesStmt = $pdo->prepare('SELECT q.*, pr.id AS request_id, pr.request_date, s.company_name FROM quotations q JOIN purchase_requests pr ON q.purchase_request_id = pr.id JOIN sellers s ON q.seller_id = s.id WHERE q.seller_id = ? ORDER BY q.id DESC');
$quotesStmt->execute([$seller_id]);
$quotes = $quotesStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Sidebar -->
<nav class="col-md-2 d-none d-md-block sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/index.php">แดชบอร์ด</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/purchase_requests.php">ดูใบขอซื้อ</a></li>
            <li class="nav-item"><a class="nav-link active" href="/procurement_system/seller/quotations.php">จัดการใบเสนอราคา</a></li>
            <li class="nav-item"><a class="nav-link" href="/procurement_system/seller/purchase_orders.php">ดูใบสั่งซื้อ</a></li>
        </ul>
    </div>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4">
    <h2 class="mb-3">ใบเสนอราคาของฉัน</h2>
    <div class="accordion" id="quotesAccordion">
        <?php foreach ($quotes as $q): ?>
            <div class="accordion-item mb-2">
                <h2 class="accordion-header" id="headingQuote<?= $q['id'] ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQuote<?= $q['id'] ?>" aria-expanded="false" aria-controls="collapseQuote<?= $q['id'] ?>">
                        ใบเสนอราคา #<?= $q['id'] ?> | ใบขอซื้อ #<?= $q['request_id'] ?> | วันที่เสนอ <?= htmlspecialchars($q['quote_date']) ?> | สถานะ: <?= htmlspecialchars($q['status']) ?>
                    </button>
                </h2>
                <div id="collapseQuote<?= $q['id'] ?>" class="accordion-collapse collapse" aria-labelledby="headingQuote<?= $q['id'] ?>" data-bs-parent="#quotesAccordion">
                    <div class="accordion-body">
                        <?php
                        $itemsStmt = $pdo->prepare('SELECT qi.quantity, qi.price, p.name FROM quotation_items qi JOIN products p ON qi.product_id = p.id WHERE qi.quotation_id = ?');
                        $itemsStmt->execute([$q['id']]);
                        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <table class="table table-sm table-bordered mb-3">
                            <thead class="table-light"><tr><th>สินค้า</th><th>จำนวน</th><th>ราคาต่อหน่วย</th><th>ราคารวม</th></tr></thead>
                            <tbody>
                                <?php $total=0; foreach ($items as $it): ?>
                                    <?php $line = $it['quantity'] * $it['price']; $total += $line; ?>
                                    <tr><td><?= htmlspecialchars($it['name']) ?></td><td><?= $it['quantity'] ?></td><td><?= number_format($it['price'], 2) ?></td><td><?= number_format($line, 2) ?></td></tr>
                                <?php endforeach; ?>
                                <tr><td colspan="3" class="text-end"><strong>รวม</strong></td><td><strong><?= number_format($total, 2) ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($quotes)): ?>
            <p>ยังไม่มีใบเสนอราคา</p>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>