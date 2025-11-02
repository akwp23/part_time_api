<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$conn = new mysqli("localhost", "root", "", "part_time_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi database gagal"]);
    exit;
}

$email_provider = $_POST['email_provider'] ?? '';
if (empty($email_provider)) {
    echo json_encode(["success" => false, "message" => "Email provider kosong"]);
    exit;
}

$query = "
    SELECT 
        a.id,
        a.email_provider,
        a.email_pelamar,
        a.nama_pelamar,
        a.alamat,
        a.jurusan,
        a.dokumen,
        a.status,
        a.tanggal_kirim,
        p.foto_ktm
    FROM applications a
    LEFT JOIN profiles p ON a.email_pelamar = p.email
    WHERE a.email_provider = ?
    ORDER BY a.tanggal_kirim DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email_provider);
$stmt->execute();
$result = $stmt->get_result();

$applications = [];
while ($row = $result->fetch_assoc()) {
    foreach ($row as $key => $val) {
        if (is_null($val)) $row[$key] = '';
    }

    if (!empty($row['foto_ktm']) && strpos($row['foto_ktm'], 'uploads/') === false) {
        $row['foto_ktm'] = "uploads/" . $row['foto_ktm'];
    }

    $applications[] = $row;
}

echo json_encode(["success" => true, "applications" => $applications]);

$stmt->close();
$conn->close();
?>
