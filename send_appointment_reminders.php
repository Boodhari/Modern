<?php
include 'config/db.php';
include 'send_whatsapp.php';

$tomorrow = date('Y-m-d', strtotime('+1 day'));

$stmt = $conn->prepare("SELECT patient_name, phone, appointment_date FROM appointments WHERE appointment_date = ?");
$stmt->bind_param("s", $tomorrow);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $phone = preg_replace('/[^0-9]/', '', $row['phone']); // clean phone number
    if (strlen($phone) < 9) continue; // skip invalid numbers

    if (strpos($phone, "252") !== 0) {
        $phone = "252" . ltrim($phone, "0"); // add country code if missing
    }

    $message = "🦷 Reminder: Dear " . $row['patient_name'] . ", you have a dental appointment tomorrow on " . $row['appointment_date'] . ". - Smart Dental Pharmacy";

    sendWhatsApp($phone, $message);
}
?>
