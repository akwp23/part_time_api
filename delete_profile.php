<?php
include 'connect.php';
$email = $_POST['email'] ?? '';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email tidak ditemukan"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM profiles WHERE email=?");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Profil berhasil dihapus"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menghapus profil"]);
}
$conn->close();
?>
