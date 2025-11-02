<?php
include 'connect.php';

$id = $_POST['id'] ?? '';
$email = $_POST['email'] ?? '';
$nama = $_POST['nama_perusahaan'] ?? '';
$pekerjaan = $_POST['pekerjaan'] ?? '';
$hari_jam = $_POST['hari_jam'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$persyaratan = $_POST['persyaratan'] ?? '';
$keterangan =$_POST['keterangan'] ?? '';

$stmt = $conn->prepare("UPDATE provider_jobs SET nama_perusahaan=?, pekerjaan=?, hari_jam=?, alamat=?, persyaratan=?, keterangan=? WHERE id=? AND email=?");
$stmt->bind_param("ssssssss", $nama, $pekerjaan, $hari_jam, $alamat, $id, $email, $persyaratan, $keterangan);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Lowongan berhasil diperbarui"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal memperbarui lowongan"]);
}
$conn->close();
?>
