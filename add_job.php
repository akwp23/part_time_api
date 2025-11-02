<?php
require 'connect.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$email = $_POST['email'] ?? '';
$nama  = $_POST['nama_perusahaan'] ?? '';
$pekerjaan = $_POST['pekerjaan'] ?? '';
$hari_jam  = $_POST['hari_jam'] ?? '';
$alamat    = $_POST['alamat'] ?? '';
$persyaratan = $_POST['persyaratan'] ?? '';
$keterangan  = $_POST['keterangan'] ?? '';

if ($email==='' || $nama==='' || $pekerjaan==='') {
  echo json_encode(["success"=>false,"message"=>"Field wajib belum lengkap"]);
  exit;
}

/* cek status provider */
$st = $conn->prepare("SELECT COALESCE(status,'pending') AS status FROM provider_profiles WHERE email=? LIMIT 1");
$st->bind_param("s",$email);
$st->execute();
$sr = $st->get_result();
if (!$row = $sr->fetch_assoc()) {
  echo json_encode(["success"=>false,"message"=>"Profil provider tidak ditemukan"]);
  exit;
}
if (strtolower($row['status']) !== 'diterima') {
  echo json_encode(["success"=>false,"message"=>"Profil belum disetujui admin. Tidak bisa menambah lowongan."]);
  exit;
}

/* insert job */
$q = $conn->prepare("INSERT INTO provider_jobs (email, nama_perusahaan, pekerjaan, hari_jam, alamat, persyaratan, keterangan)
                     VALUES (?,?,?,?,?,?,?)");
$q->bind_param("sssssss", $email,$nama,$pekerjaan,$hari_jam,$alamat,$persyaratan,$keterangan);

if ($q->execute()) {
  echo json_encode(["success"=>true,"message"=>"Lowongan berhasil disimpan"]);
} else {
  echo json_encode(["success"=>false,"message"=>"Gagal menyimpan lowongan"]);
}
$conn->close();
