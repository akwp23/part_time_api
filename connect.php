<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "part_time_db"; // pastikan sama untuk semua
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Koneksi database gagal"]));
}
?>
