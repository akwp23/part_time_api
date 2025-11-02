<?php
include 'connect.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$email = $_POST['email'] ?? '';
$nama = $_POST['nama_perusahaan'] ?? '';
$profil = $_POST['profil_perusahaan'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$telepon = $_POST['no_telepon'] ?? '';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email tidak boleh kosong"]);
    exit;
}

// 📸 Proses upload foto (jika ada)
$fotoPath = '';
if (!empty($_FILES['foto_perusahaan']['name'])) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($_FILES["foto_perusahaan"]["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed_types = ['jpg', 'jpeg', 'png'];
    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($_FILES["foto_perusahaan"]["tmp_name"], $target_file)) {
            $fotoPath = $target_file;
        } else {
            echo json_encode(["success" => false, "message" => "Gagal mengunggah foto"]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "message" => "Format foto tidak diizinkan (hanya JPG/PNG)"]);
        exit;
    }
}

// 🔍 Cek apakah profil sudah ada
$stmt = $conn->prepare("SELECT email FROM provider_profiles WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // 🔁 Update jika sudah ada
    if (!empty($fotoPath)) {
        $stmt = $conn->prepare("UPDATE provider_profiles SET nama_perusahaan=?, profil_perusahaan=?, alamat=?, no_telepon=?, foto_perusahaan=? WHERE email=?");
        $stmt->bind_param("ssssss", $nama, $profil, $alamat, $telepon, $fotoPath, $email);
    } else {
        $stmt = $conn->prepare("UPDATE provider_profiles SET nama_perusahaan=?, profil_perusahaan=?, alamat=?, no_telepon=? WHERE email=?");
        $stmt->bind_param("sssss", $nama, $profil, $alamat, $telepon, $email);
    }
} else {
    // 🆕 Tambah baru
    $stmt = $conn->prepare("INSERT INTO provider_profiles (nama_perusahaan, profil_perusahaan, alamat, no_telepon, email, foto_perusahaan) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $nama, $profil, $alamat, $telepon, $email, $fotoPath);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Profil perusahaan berhasil disimpan"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan profil perusahaan"]);
}

$conn->close();
?>
