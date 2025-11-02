<?php
include 'connect.php';
$email = $_POST['email'] ?? '';
$stmt = $conn->prepare("SELECT * FROM provider_jobs WHERE email=? ORDER BY id DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$jobs = [];
while ($row = $result->fetch_assoc()) $jobs[] = $row;
echo json_encode(["success" => true, "jobs" => $jobs]);
?>
