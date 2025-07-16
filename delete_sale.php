<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid sale ID.");
}

include 'config/db.php';
$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM sales WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: sales_report.php");
exit;
