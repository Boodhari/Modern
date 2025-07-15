<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'config/db.php';
include('includes/header.php');

// Today's Sales Total
$today = date('Y-m-d');
$sales_query = $conn->query("SELECT SUM(total) AS total_today FROM sales WHERE DATE(sale_date) = '$today'");
$sales_today = $sales_query->fetch_assoc()['total_today'] ?? 0;

// Total Stock
$stock_query = $conn->query("SELECT SUM(quantity) AS total_stock FROM products");
$total_stock = $stock_query->fetch_assoc()['total_stock'] ?? 0;

// Low Stock Count
$low_stock_query = $conn->query("SELECT COUNT(*) AS low_count FROM products WHERE quantity < 10");
$low_stock_count = $low_stock_query->fetch_assoc()['low_count'] ?? 0;
// Total Prescriptions
$prescriptions_query = $conn->query("SELECT COUNT(*) AS total_prescriptions FROM prescriptions");
$total_prescriptions = $prescriptions_query->fetch_assoc()['total_prescriptions'] ?? 0;
// Weekly Sales Data for Chart
$weekly_sales = [];
$labels = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $res = $conn->query("SELECT SUM(total) AS total FROM sales WHERE DATE(sale_date) = '$day'");
    $amount = $res->fetch_assoc()['total'] ?? 0;
    $labels[] = $day;
    $weekly_sales[] = $amount;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard - Pharmacy POS</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- ✅ Required for responsiveness -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container-fluid py-5"> <!-- ✅ fluid for full screen -->
  <h2 class="mb-4 text-center text-md-start">Welcome, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h2>

  <?php if ($low_stock_count > 0): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle-fill"></i>
      <strong>Attention!</strong> <?= $low_stock_count ?> product(s) are low in stock.
      <a href="products.php" class="alert-link">Check inventory</a>.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Summary Cards -->
  <div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="card shadow-sm text-white bg-primary">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-box-seam"></i> Total Stock</h5>
          <p class="card-text fs-4"><?= $total_stock ?> units</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="card shadow-sm text-white bg-success">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-cash-coin"></i> Today's Sales</h5>
          <p class="card-text fs-4">$<?= number_format($sales_today, 2) ?></p>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="card shadow-sm text-white bg-danger">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-exclamation-triangle"></i> Low Stock</h5>
          <p class="card-text fs-4"><?= $low_stock_count ?> item(s)</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="card shadow-sm text-white bg-info">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-clipboard-check"></i> Prescriptions</h5>
          <p class="card-text fs-4"><?= $total_prescriptions ?> issued</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Action Cards -->
  <div class="row g-3">
    <?php
    $features = [
      ["products.php", "bi-box", "Manage Products", "Add, update and manage inventory.", "primary"],
      ["sell.php", "bi-cart-check", "Sell Products", "Process sales and print receipts.", "success"],
      ["sales_report.php", "bi-graph-up-arrow", "Sales Report", "View and search daily sales.", "warning"],
      ["view_prescriptions.php", "bi-clipboard-data", "Prescriptions", "View and print prescriptions.", "secondary"],
      ["register_visitor.php", "bi-person-plus", "Register Visitor", "Register daily visitors to the pharmacy.", "info"],
      ["visitor_status.php", "bi-person-lines-fill", "Visitor Status", "Check and manage visitor status.", "dark"],
      ["view_visitors.php", "bi-people", "Visitor View", "View visitor records.", "dark"],
      ["view_history.php", "bi-clock-history", "History View", "View medical history.", "dark"],
      ["generate_voucher.php", "bi-file-earmark-plus", "Create Voucher", "Generate new payment voucher.", "dark"],
      ["view_vouchers.php", "bi-receipt", "Print Vouchers", "View and print vouchers.", "dark"]
    ];

    foreach ($features as $f):
    ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100 shadow-sm">
          <div class="card-body text-center">
            <i class="bi <?= $f[1] ?> display-4 text-<?= $f[4] ?> mb-3"></i>
            <h5 class="card-title"><?= $f[2] ?></h5>
            <p class="card-text"><?= $f[3] ?></p>
            <a href="<?= $f[0] ?>" class="btn btn-outline-<?= $f[4] ?> w-100">Go</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Chart -->


<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php include 'includes/footer.php'; ?>
</body>
</html>
