<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'config/db.php';

// Check if ID is provided
if (!isset($_GET['id']) || intval($_GET['id']) < 1) {
    die("Invalid product ID.");
}

$id = intval($_GET['id']);
$success = false;
$error = "";

// Fetch product data
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// Update product if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    if ($name && $price >= 0 && $quantity >= 0) {
        $update = $conn->prepare("UPDATE products SET name = ?, price = ?, quantity = ? WHERE id = ?");
        $update->bind_param("sdii", $name, $price, $quantity, $id);
        if ($update->execute()) {
            $success = true;
            $product['name'] = $name;
            $product['price'] = $price;
            $product['quantity'] = $quantity;
        } else {
            $error = "Failed to update product.";
        }
    } else {
        $error = "Please enter valid values.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Product - Pharmacy POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 600px;">
  <h2>Edit Product</h2>

  <?php if ($success): ?>
    <div class="alert alert-success">✅ Product updated successfully.</div>
  <?php elseif (!empty($error)): ?>
    <div class="alert alert-danger">❌ <?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label>Product Name</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>

    <div class="mb-3">
      <label>Price ($)</label>
      <input type="number" name="price" class="form-control" step="0.01" value="<?= $product['price'] ?>" required>
    </div>

    <div class="mb-3">
      <label>Quantity</label>
      <input type="number" name="quantity" class="form-control" value="<?= $product['quantity'] ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">💾 Update Product</button>
    <a href="products.php" class="btn btn-secondary">⬅️ Cancel</a>
  </form>
</div>
</body>
</html>
