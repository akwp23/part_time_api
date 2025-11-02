<?php
include 'connect.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$email   = $_POST['email'] ?? '';
$nama    = $_POST['nama_perusahaan'] ?? '';
$profil  = $_POST['profil_perusahaan'] ?? '';
$alamat  = $_POST['alamat'] ?? '';
$telepon = $_POST['no_telepon'] ?? '';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email tidak boleh kosong"]);
    exit;
}

// 📸 Upload foto jika ada
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
    if (!in_array($imageFileType, $allowed_types)) {
        echo json_encode([
            "success" => false,
            "message" => "Format foto tidak diizinkan (hanya JPG/PNG)"
        ]);
        exit;
    }

    if (move_uploaded_file($_FILES["foto_perusahaan"]["tmp_name"], $target_file)) {
        $fotoPath = $target_file;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Gagal mengunggah foto perusahaan"
        ]);
        exit;
    }
}

// 🔁 Update data profil perusahaan
if (!empty($fotoPath)) {
    $stmt = $conn->prepare("UPDATE provider_profiles 
                            SET nama_perusahaan=?, profil_perusahaan=?, alamat=?, no_telepon=?, foto_perusahaan=? 
                            WHERE email=?");
    $stmt->bind_param("ssssss", $nama, $profil, $alamat, $telepon, $fotoPath, $email);
} else {
    $stmt = $conn->prepare("UPDATE provider_profiles 
                            SET nama_perusahaan=?, profil_perusahaan=?, alamat=?, no_telepon=? 
                            WHERE email=?");
    $stmt->bind_param("sssss", $nama, $profil, $alamat, $telepon, $email);
}

if ($stmt->execute()) {

    // 📨 Simpan notifikasi otomatis ke tabel notifications_provider
    $notifStmt = $conn->prepare("INSERT INTO notifications_provider (email_provider, nama_perusahaan, status, tanggal_update)
                                 VALUES (?, ?, 'Profil diperbarui', NOW())");
    $notifStmt->bind_param("ss", $email, $nama);
    $notifStmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Profil perusahaan berhasil diperbarui dan notifikasi dikirim"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Gagal memperbarui profil perusahaan"
    ]);
}

$conn->close();
?>
