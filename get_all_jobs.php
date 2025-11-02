<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$conn = new mysqli("localhost", "root", "", "part_time_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi database gagal"]);
    exit;
}

$email_user = $_POST['email'] ?? '';

$query = "
    SELECT 
        j.id,
        j.email AS email_provider,
        j.nama_perusahaan,
        j.pekerjaan,
        j.hari_jam,
        j.alamat,
        j.persyaratan,
        j.keterangan,
        j.created_at,
        j.status,                     -- ✅ tambahkan ini
        p.foto_perusahaan
    FROM provider_jobs j
    LEFT JOIN provider_profiles p ON j.email = p.email
    ORDER BY j.created_at DESC
";

$result = $conn->query($query);
$jobs = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $job_id = $row['id'];

        // Tambahkan URL foto lengkap
        if (!empty($row['foto_perusahaan'])) {
            if (strpos($row['foto_perusahaan'], 'uploads/') === false) {
                $row['foto_perusahaan'] = "uploads/" . $row['foto_perusahaan'];
            }
        }

        // Cegah null
        foreach ($row as $key => $value) {
            if (is_null($value)) {
                $row[$key] = '';
            }
        }

        // ✅ Cek apakah pelamar sudah melamar pekerjaan ini
        $is_applied = false;
        if (!empty($email_user)) {
            $check = $conn->prepare("SELECT 1 FROM applications WHERE email_pelamar=? AND job_id=? LIMIT 1");
            $check->bind_param("si", $email_user, $job_id);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $is_applied = true;
            }
            $check->close();
        }

        $row['is_applied'] = $is_applied;
        $jobs[] = $row;
    }

    echo json_encode(["success" => true, "jobs" => $jobs]);
} else {
    echo json_encode(["success" => false, "message" => "Tidak ada lowongan"]);
}

$conn->close();
?>
