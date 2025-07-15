<?php
include 'config/db.php';
include('includes/header.php');
$success = false;

// Fetch visitors for dropdown
$visitors = $conn->query("SELECT id, full_name, purpose FROM visitors ORDER BY visit_date DESC");

// Fetch distinct services from history_taking
$services = $conn->query("SELECT DISTINCT services FROM history_taking ORDER BY date_taken DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitor_id = $_POST['visitor_id'];
    $service = $_POST['service'];
    $amount = $_POST['amount_paid'];
    $payment_type = $_POST['payment_type'];
    // Get visitor name
    $stmt = $conn->prepare("SELECT full_name FROM visitors WHERE id = ?");
    $stmt->bind_param("i", $visitor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $visitor = $result->fetch_assoc();
    $patient_name = $visitor['full_name'];
    

    // Insert voucher
    $insert = $conn->prepare("INSERT INTO vouchers (visitor_id, patient_name, service, amount_paid ,payment_type) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("issds", $visitor_id, $patient_name, $service, $amount,$payment_type);
    $insert->execute();
    $voucher_id = $insert->insert_id;
    $success = true;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Generate Payment Voucher</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h2 class="mb-4">🧾 Generate Voucher</h2>

  <?php if ($success): ?>
    <div class="alert alert-success">
      Voucher created! <a href="print_voucher.php?id=<?= $voucher_id ?>" class="btn btn-sm btn-outline-primary">🖨️ Print Voucher</a>
    </div>
  <?php endif; ?>

  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label>Select Visitor</label>
      <select name="visitor_id" class="form-select" required>
        <option value="">-- Choose Patient --</option>
        <?php while ($v = $visitors->fetch_assoc()): ?>
          <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['full_name']) ?> - <?= htmlspecialchars($v['purpose']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>

   <div class="col-md-6">
  <label>Service Description</label>
  <input list="services" name="service" class="form-control" required placeholder="Choose or type service">
  <datalist id="services">
    <?php while ($s = $services->fetch_assoc()): ?>
      <option value="<?= htmlspecialchars($s['services']) ?>">
    <?php endwhile; ?>
  </datalist>
</div>

    <div class="col-md-4">
      <label>Amount Paid (USD)</label>
      <input type="number" step="0.01" name="amount_paid" class="form-control" required>
    </div>
    <div class="mb-3">
  <label for="payment_type" class="form-label">Payment Type</label>
  <select name="payment_type" id="payment_type" class="form-select" required>
    <option value="Zaad-SLSH">Zaad - SLSH</option>
    <option value="Zaad-USD">Zaad - USD</option>
    <option value="Edahab-SLSH">Edahab - SLSH</option>
    <option value="Edahab-USD">Edahab - USD</option>
    <option value="Cash-SLSH" selected>Cash - SLSH</option>
    <option value="Cash-USD">Cash - USD</option>
  </select>
</div>

    <div class="col-12">
      <button type="submit" class="btn btn-success">💾 Save Voucher</button>
      <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</body>
</html>
