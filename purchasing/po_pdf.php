<?php
// procurement_system/purchasing/po_pdf.php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../db.php';

// allow only Purchasing
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Purchasing') {
  header('Location: /procurement_system/login.php');
  exit;
}

// ==== input ====
$poId = (int)($_GET['id'] ?? 0);
if ($poId <= 0) {
  http_response_code(400);
  exit('Invalid PO ID');
}

// ==== load head ====
$sqlHead = "
  SELECT
    po.id,
    po.order_date,
    po.status,
    q.id AS quotation_id,
    q.purchase_request_id,
    s.company_name,
    s.contact_name,
    s.address,
    s.phone,
    s.email
  FROM purchase_orders po
  JOIN quotations q ON q.id = po.quotation_id
  JOIN sellers s    ON s.id = q.seller_id
  WHERE po.id = ?
  LIMIT 1
";
$st = $pdo->prepare($sqlHead);
$st->execute([$poId]);
$head = $st->fetch(PDO::FETCH_ASSOC);
if (!$head) {
  http_response_code(404);
  exit('PO not found');
}

// ==== load items ====
$sqlItems = "
  SELECT qi.product_id, qi.quantity, qi.price, p.name AS product_name
  FROM quotation_items qi
  JOIN products p ON p.id = qi.product_id
  WHERE qi.quotation_id = ?
  ORDER BY p.name
";
$sti = $pdo->prepare($sqlItems);
$sti->execute([(int)$head['quotation_id']]);
$items = $sti->fetchAll(PDO::FETCH_ASSOC);

// total
$total = 0.0;
foreach ($items as $it) {
  $total += ((float)$it['quantity']) * ((float)$it['price']);
}

// ==== mPDF ====
require_once __DIR__ . '/../vendor/autoload.php';
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

$tmpDir  = __DIR__ . '/../tmp';
$fontDir = __DIR__ . '/../fonts';
if (!is_dir($tmpDir))  { @mkdir($tmpDir, 0777, true); }
if (!is_dir($fontDir)) { @mkdir($fontDir, 0777, true); }

// รวมโฟลเดอร์ฟอนต์เดิมของ mPDF + โฟลเดอร์ของเรา
$defaultConfig = (new ConfigVariables())->getDefaults();
$fontDirs      = $defaultConfig['fontDir'];
$defaultFontConfig = (new FontVariables())->getDefaults();
$fontData          = $defaultFontConfig['fontdata'];

// ตั้งค่าฟอนต์ไทย TH Sarabun New (ครบ 4 น้ำหนัก)
$fontData['thsarabunnew'] = [
  'R'  => 'THSarabunNew.ttf',
  'B'  => 'THSarabunNew-Bold.ttf',
  'I'  => 'THSarabunNew-Italic.ttf',
  'BI' => 'THSarabunNew-BoldItalic.ttf',
];

$mpdf = new Mpdf([
  'format'            => 'A4',
  'tempDir'           => $tmpDir,
  'fontDir'           => array_merge($fontDirs, [$fontDir]),
  'fontdata'          => $fontData,
  'default_font'      => 'thsarabunnew',
  'autoScriptToLang'  => true,
  'autoLangToFont'    => true,
  'useSubstitutions'  => false,   // บังคับใช้ฟอนต์ที่เรากำหนด
]);

$css = '
  body { font-size: 12pt; font-family: thsarabunnew, DejaVu Sans, sans-serif; }
  .title { font-weight: 700; font-size: 22pt; }
  .muted { color:#64748b; }
  .hr { border:0; border-top:1px solid #444; margin:12px 0; }

  .tbl { width:100%; border-collapse: collapse; }
  .tbl th, .tbl td { border:1px solid #333; padding:6px 8px; line-height: 1.25; }
  .tbl th { background:#ececec; font-weight:bold; text-align:center; }

  .right { text-align:right; }
  .center { text-align:center; }
';

$thaiDate = function (?string $ts): string {
  if (empty($ts)) return '';
  $t = strtotime($ts);
  $months = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
  $d = (int)date('j', $t);
  $m = $months[(int)date('n', $t)-1];
  $y = (int)date('Y', $t) + 543;
  $time = date('H:i:s', $t);
  return "$d $m $y $time น.";
};

$html = '
  <table style="width:100%">
    <tr>
      <td style="width:65%">
        <div class="title">ใบสั่งซื้อ (Purchase Order)</div>
        <div class="muted">
          รหัสใบสั่งซื้อ: '.(int)$head['id'].' | ใบขอซื้อ: '.(int)$head['purchase_request_id'].'
        </div>
        <div class="muted">
          วันที่สั่งซื้อ: '.$thaiDate($head['order_date']).'
          | สถานะ: '.htmlspecialchars($head['status'] ?? "", ENT_QUOTES, "UTF-8").'
        </div>
      </td>
      <td style="width:35%; vertical-align:top; text-align:right">
        <div><strong>ผู้ขาย</strong></div>
        <div><strong>'.htmlspecialchars($head['company_name'] ?? "", ENT_QUOTES, "UTF-8").'</strong></div>
        <div class="muted">'.nl2br(htmlspecialchars($head['address'] ?? "", ENT_QUOTES, "UTF-8")).'</div>
        <div><strong>โทร:</strong> '.htmlspecialchars($head['phone'] ?? "-", ENT_QUOTES, "UTF-8").'</div>
        <div><strong>อีเมล:</strong> '.htmlspecialchars($head['email'] ?? "-", ENT_QUOTES, "UTF-8").'</div>
      </td>
    </tr>
  </table>

  <hr class="hr"/>

  <table class="tbl">
    <thead>
      <tr>
        <th style="width:8%">#</th>
        <th>สินค้า</th>
        <th style="width:14%">จำนวน</th>
        <th style="width:18%">ราคาต่อหน่วย</th>
        <th style="width:20%">ราคารวม</th>
      </tr>
    </thead>
    <tbody>';

$i = 1;
foreach ($items as $it) {
  $qty   = (float)$it['quantity'];
  $price = (float)$it['price'];
  $line  = $qty * $price;
  $html .= '
      <tr>
        <td class="center">'.($i++).'</td>
        <td>'.htmlspecialchars($it['product_name'] ?? "", ENT_QUOTES, "UTF-8").'</td>
        <td class="right">'.number_format($qty, 2).'</td>
        <td class="right">'.number_format($price, 2).'</td>
        <td class="right">'.number_format($line, 2).'</td>
      </tr>';
}

$html .= '
      <tr>
        <td colspan="4" class="right"><strong>รวมทั้งสิ้น</strong></td>
        <td class="right"><strong>'.number_format($total, 2).'</strong></td>
      </tr>
    </tbody>
  </table>

  <br><br>

  <table style="width:100%">
    <tr>
      <td style="width:50%">
        <div class="muted">ผู้จัดทำ: '.htmlspecialchars($_SESSION['name'] ?? 'Purchasing', ENT_QUOTES, "UTF-8").'</div>
      </td>
      <td style="width:50%; text-align:right">
        <div class="muted">ลงชื่อ.............................................</div>
        <div class="muted">หัวหน้าแผนกจัดซื้อ</div>
      </td>
    </tr>
  </table>
';

$mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

$filename = 'PO-'.(int)$head['id'].'.pdf';
$mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
