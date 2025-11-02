<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$conn = new mysqli("localhost", "root", "", "part_time_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi database gagal"]);
    exit;
}

$query = "
    SELECT 
        id,
        nama_perusahaan,
        profil_perusahaan,
        foto_perusahaan,
        alamat,
        no_telepon,
        email,
        status,
        tanggal_update
    FROM provider_profiles
    ORDER BY tanggal_update DESC
";

$result = $conn->query($query);

$providers = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Tambahkan prefix 'uploads/' jika belum ada
        if (!empty($row['foto_perusahaan']) && strpos($row['foto_perusahaan'], 'uploads/') === false) {
            $row['foto_perusahaan'] = "uploads/" . $row['foto_perusahaan'];
        }

        // Hindari nilai null di Flutter
        foreach ($row as $key => $value) {
            if (is_null($value)) $row[$key] = '';
        }

        $providers[] = $row;
    }

    echo json_encode(["success" => true, "providers" => $providers]);
} else {
    echo json_encode(["success" => false, "message" => "Tidak ada data penyedia"]);
}

$conn->close();
?>
