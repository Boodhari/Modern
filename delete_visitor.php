<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID.");
}

include 'config/db.php';

$id = intval($_GET['id']);

// Delete visitor
$stmt = $conn->prepare("DELETE FROM visitors WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: visitor_status.php");
exit;
