<?php
include 'connect.php';
$email = $_POST['email'] ?? '';
$stmt = $conn->prepare("SELECT * FROM profiles WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$profiles = [];
while ($row = $result->fetch_assoc()) {
  $profiles[] = $row;
}
echo json_encode(["success" => true, "profiles" => $profiles]);
?>
