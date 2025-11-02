<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$conn = new mysqli("localhost", "root", "", "part_time_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi database gagal"]);
    exit;
}

$email = $_POST['email'] ?? '';
$nama = $_POST['nama_lengkap'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$no_telepon = $_POST['no_telepon'] ?? '';
$kampus = $_POST['nama_kampus'] ?? '';
$jurusan = $_POST['jurusan'] ?? '';
$semester = $_POST['semester'] ?? '';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email tidak boleh kosong"]);
    exit;
}

$uploadDir = "uploads/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Upload foto profil
$foto = '';
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $newName = "foto_" . time() . "_" . rand(1000, 9999) . "." . $ext;
    move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $newName);
    $foto = $uploadDir . $newName;
}

// Upload foto KTM
$foto_ktm = '';
if (isset($_FILES['foto_ktm']) && $_FILES['foto_ktm']['error'] === UPLOAD_ERR_OK) {
    $ext2 = pathinfo($_FILES['foto_ktm']['name'], PATHINFO_EXTENSION);
    $newName2 = "ktm_" . time() . "_" . rand(1000, 9999) . "." . $ext2;
    move_uploaded_file($_FILES['foto_ktm']['tmp_name'], $uploadDir . $newName2);
    $foto_ktm = $uploadDir . $newName2;
}

$stmt = $conn->prepare("INSERT INTO profiles (email, nama_lengkap, alamat, no_telepon, nama_kampus, jurusan, semester, foto, foto_ktm)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $email, $nama, $alamat, $no_telepon, $kampus, $jurusan, $semester, $foto, $foto_ktm);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Profil berhasil disimpan"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan profil"]);
}

$conn->close();
?>
