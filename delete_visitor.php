<?php
include 'config/db.php';

$id = $_GET['id'] ?? 0;

// First, delete related appointments
$stmt1 = $conn->prepare("DELETE FROM appointments WHERE visitor_id = ?");
$stmt1->bind_param("i", $id);
$stmt1->execute();

// Then delete the visitor
$stmt2 = $conn->prepare("DELETE FROM visitors WHERE id = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();

header("Location: visitor_status.php");
exit;
