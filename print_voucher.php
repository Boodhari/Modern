<?php
include 'config/db.php';
include('includes/header.php');

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM vouchers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$voucher = $result->fetch_assoc();

if (!$voucher) {
    die("Voucher not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Print Voucher</title>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .voucher {
      max-width: 400px;
      margin: 40px auto;
      padding: 20px;
      border: 2px dashed #000;
      background: #fff;
      font-size: 14px;
    }

    @media print {
      .no-print, .no-print * {
        display: none !important;
      }
      body {
        margin: 0;
        padding: 0;
        background: #fff !important;
      }
      .voucher {
        border: none;
        margin: 0 auto;
        width: 100%;
      }
    }
  </style>
</head>
<body>

<!-- Print content -->
<div class="voucher">
  <h5 class="text-center">Modern Dental Clinic</h5>
  <p class="text-center mb-1">Contact us: 063-4717156 / 063-7664666</p>
  <hr>
  <p><strong>Voucher ID:</strong> #<?= $voucher['id'] ?></p>
  <p><strong>Patient Name:</strong> <?= htmlspecialchars($voucher['patient_name']) ?></p>
  <p><strong>Service:</strong> <?= htmlspecialchars($voucher['service']) ?></p>
  <p><strong>Amount Paid:</strong>$ <?= number_format($voucher['amount_paid'], 2) ?> </p>
  <p><strong>Payment Type:</strong> <?= htmlspecialchars($voucher['payment_type']) ?></p>
  <p><strong>Date:</strong> <?= date('d M Y - H:i', strtotime($voucher['date_paid'])) ?></p>
  <hr>
  <p>Signature: ____________________________</p>
</div>

<!-- Print buttons -->
<div class="text-center no-print mt-4">
  <button onclick="window.print()" class="btn btn-primary">🖨️ Print</button>
  <a href="generate_voucher.php" class="btn btn-secondary">⬅️ Back</a>
</div>

</body>
</html>
