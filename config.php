<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200); exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "part_time_db"; // ganti kalau nama DB kamu beda

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["success"=>false,"message"=>"DB connect failed: ".$conn->connect_error]);
  exit;
}

$conn->set_charset("utf8mb4");
