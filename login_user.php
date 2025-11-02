<?php
// ============================
//  LOGIN USER API (FINAL FIX)
// ============================

// 🔹 Pastikan semua respon dalam format JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// 🔹 Matikan error HTML bawaan PHP agar Flutter tidak menerima HTML
ini_set('display_errors', 0);
error_reporting(0);

include "config.php";

// 🔹 Handle preflight (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 🔹 Ambil input dari Flutter
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// 🔹 Validasi input
if ($email === '' || $password === '') {
    echo json_encode([
        "success" => false,
        "message" => "Email dan password wajib diisi.",
        "role" => null,
        "email" => $email
    ]);
    exit;
}

// 🔹 Jika admin (bypass langsung)
if (strtolower($email) === 'ap3854324@gmail.com' && $password === 'admin123') {
    echo json_encode([
        "success" => true,
        "message" => "Login berhasil sebagai Admin.",
        "role" => "admin",
        "email" => $email
    ]);
    exit;
}

// 🔹 Cek data user dari tabel `users`
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Kesalahan query database: " . $conn->error
    ]);
    exit;
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// 🔹 Jika user ditemukan
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // ✅ Verifikasi password (hash)
    if (password_verify($password, $user['password'])) {
        echo json_encode([
            "success" => true,
            "message" => "Login berhasil.",
            "role" => $user['role'] ?? "jobseeker",
            "email" => $user['email']
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Password salah.",
            "role" => null,
            "email" => $email
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Email tidak ditemukan.",
        "role" => null,
        "email" => $email
    ]);
}

$stmt->close();
$conn->close();
?>
