<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$conn = new mysqli("localhost", "root", "", "part_time_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi gagal"]);
    exit;
}

$id = $_POST['id'] ?? '';

if (empty($id)) {
    echo json_encode(["success" => false, "message" => "ID kosong"]);
    exit;
}

$query = "SELECT * FROM applications WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();

    // ambil juga data profil pelamar (nama, alamat, jurusan, foto_ktm)
    $emailPelamar = $data['email_pelamar'] ?? '';
    $profile = null;
    if (!empty($emailPelamar)) {
        $q2 = $conn->prepare("SELECT nama_lengkap, alamat, jurusan, foto_ktm FROM profiles WHERE email = ?");
        $q2->bind_param("s", $emailPelamar);
        $q2->execute();
        $r2 = $q2->get_result();
        if ($r2->num_rows > 0) {
            $profile = $r2->fetch_assoc();

            // tambahkan base URL ke foto KTM jika ada
            if (!empty($profile['foto_ktm'])) {
                $profile['foto_ktm'] = "uploads/" . basename($profile['foto_ktm']);
            }
        }
    }

    // gabungkan data lamaran + profil
    $dataGabung = [
        "id" => $data['id'],
        "dokumen" => $data['dokumen'],
        "status" => $data['status'],
        "email_pelamar" => $data['email_pelamar'],
        "nama_pelamar" => $profile['nama_lengkap'] ?? $data['nama_pelamar'],
        "alamat" => $profile['alamat'] ?? $data['alamat'],
        "jurusan" => $profile['jurusan'] ?? $data['jurusan'],
        "foto_ktm" => $profile['foto_ktm'] ?? null,
    ];

    echo json_encode(["success" => true, "data" => $dataGabung]);
} else {
    echo json_encode(["success" => false, "message" => "Data tidak ditemukan"]);
}

$conn->close();
?>
