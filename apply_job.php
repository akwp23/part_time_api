<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);
ini_set('display_errors', 0);

$response = [
    "success" => false,
    "message" => "Terjadi kesalahan tidak diketahui",
    "debug" => []
];

try {
    $conn = new mysqli("localhost", "root", "", "part_time_db");
    if ($conn->connect_error) {
        $response["message"] = "Koneksi database gagal: " . $conn->connect_error;
        echo json_encode($response);
        exit;
    }

    // ✅ Ambil data dari Flutter
    $email_pelamar = $_POST['email_pelamar'] ?? '';
    $email_provider = $_POST['email_provider'] ?? '';
    $job_id = $_POST['job_id'] ?? '';
    $status = $_POST['status'] ?? 'menunggu';

    if (empty($email_pelamar) || empty($email_provider) || empty($job_id)) {
        $response["message"] = "Data lamaran tidak lengkap";
        $response["debug"] = $_POST;
        echo json_encode($response);
        exit;
    }

    // ✅ Ambil profil pelamar (cek error query)
   $sqlPelamar = "SELECT nama_lengkap AS nama, alamat, jurusan, foto_ktm FROM profiles WHERE email='$email_pelamar' LIMIT 1";

    $qPelamar = $conn->query($sqlPelamar);
    if (!$qPelamar) {
        $response["message"] = "Query profil gagal: " . $conn->error;
        $response["debug"] = ["sql" => $sqlPelamar];
        echo json_encode($response);
        exit;
    }
    $pelamar = $qPelamar->fetch_assoc() ?? [];

    $nama = $pelamar['nama'] ?? '';
    $alamat = $pelamar['alamat'] ?? '';
    $jurusan = $pelamar['jurusan'] ?? '';

    // ✅ Upload dokumen
    $dokumen = '';
    if (!empty($_FILES['dokumen']['name'])) {
        $dir = "uploads/dokumen/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $file = time() . "_" . basename($_FILES['dokumen']['name']);
        $target = $dir . $file;

        if (move_uploaded_file($_FILES['dokumen']['tmp_name'], $target)) {
            $dokumen = $target;
        } else {
            $response["message"] = "Gagal upload file ke folder server";
            echo json_encode($response);
            exit;
        }
    } else {
        $response["message"] = "Tidak ada file dikirim";
        echo json_encode($response);
        exit;
    }

    // ✅ Simpan lamaran
    $stmt = $conn->prepare("INSERT INTO applications (job_id, email_provider, email_pelamar, nama_pelamar, alamat, jurusan, dokumen, status, tanggal_kirim) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isssssss", $job_id, $email_provider, $email_pelamar, $nama, $alamat, $jurusan, $dokumen, $status);
    $ok = $stmt->execute();

    if ($ok) {
        $response["success"] = true;
        $response["message"] = "Lamaran berhasil dikirim!";
    } else {
        $response["message"] = "Gagal menyimpan lamaran: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} catch (Throwable $e) {
    $response["message"] = "Exception: " . $e->getMessage();
}

echo json_encode($response);
?>
