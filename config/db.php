<?php
$conn = new mysqli("sql12.freesqldatabase.com", "sql12789914", "QyEQRyamui", "sql12789914");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
