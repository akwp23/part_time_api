<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost","root","","part_time_db");
$email = $_POST['email'] ?? '';
if(empty($email)){ echo json_encode(["success"=>false]); exit; }

$res = $conn->query("SELECT * FROM messages WHERE email_pelamar='$email' ORDER BY tanggal DESC");
$data = [];
while($r = $res->fetch_assoc()){ $data[] = $r; }

echo json_encode(["success"=>true,"messages"=>$data]);
$conn->close();
?>
