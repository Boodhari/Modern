<?php
include 'config/db.php';
include('includes/header1.php');

$success = false;

// Fetch patient names from the visitors table
$visitors = $conn->query("SELECT id, full_name FROM visitors ORDER BY visit_date DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient = $_POST['patient_name'];
    $sex = $_POST['patient_sex'];
    $weight = $_POST['patient_weight'];
    $doctor = $_POST['doctor_name'];
    $medications = $_POST['medications'];

    $stmt = $conn->prepare("INSERT INTO prescriptions (patient_name, patient_sex, patient_weight, doctor_name, medications) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $patient, $sex, $weight, $doctor, $medications);
    $stmt->execute();
    $success = true;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Write Prescription</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">
  <h2 class="mb-4">📝 Write Prescription</h2>

  <?php if ($success): ?>
    <div class="alert alert-success">✅ Prescription saved successfully.</div>
  <?php endif; ?>

  <form method="POST" class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Patient Name</label>
      <select name="patient_name" class="form-select" required>
        <option value="">-- Select Patient --</option>
        <?php while ($row = $visitors->fetch_assoc()): ?>
          <option value="<?= htmlspecialchars($row['full_name']) ?>"><?= htmlspecialchars($row['full_name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="col-md-2">
      <label class="form-label">Sex</label>
      <select name="patient_sex" class="form-select" required>
        <option value="">Select</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
    </div>

    <div class="col-md-2">
      <label class="form-label">Weight</label>
      <input type="text" name="patient_weight" class="form-control" required>
    </div>

    <div class="col-md-4">
      <label class="form-label">Doctor Name</label>
      <input type="text" name="doctor_name" class="form-control" required>
    </div>

    <div class="col-12">
      <label class="form-label">Medications (include dosage & instructions)</label>
      <textarea name="medications" class="form-control" rows="4" required></textarea>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-primary">💾 Save Prescription</button>
      <a href="index.html" class="btn btn-secondary">⬅️ Home</a>
    </div>
  </form>
</body>
</html>
