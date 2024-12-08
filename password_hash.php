<?php
include('db_connection.php'); // Koneksi ke database

// Data user yang akan diupdate dengan hash password
$users = [
    ["username" => "admin1", "password" => "admin123"],
    ["username" => "Aldi Muhamad Riski", "password" => "aldi123"],
    ["username" => "Cut Ainal Mardhiah", "password" => "cut123"],
    ["username" => "Daryana", "password" => "daryana123"],
    ["username" => "dosen1", "password" => "dosen123"],
    ["username" => "Fajar Nur Farrijal", "password" => "fajar123"],
    ["username" => "Nanda Ramdania", "password" => "nanda123"],
    ["username" => "Rofi Febrian Aji", "password" => "rofi123"],
    ["username" => "Salsa Lismaya", "password" => "salsa123"],
    ["username" => "Sintia Nurdestriana", "password" => "sintia123"],
    ["username" => "Tania Kurniasih Febrianti", "password" => "tania123"],
    ["username" => "Dosen1", "password" => "2"],
    ["username" => "Tresa Agustian", "password" => "tresa123"],
    ["username" => "Taufik Hidayatullah", "password" => "taufik123"],
    ["username" => "Wahyudi", "password" => "wahyudi123"]
];

// Loop untuk memperbarui password dengan hash
foreach ($users as $user) {
    $username = $user['username'];
    $plain_password = $user['password'];

    // Hash password menggunakan password_hash()
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

    // Query untuk update password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $hashed_password, $username);

    if ($stmt->execute()) {
        echo "Password untuk '$username' berhasil di-hash dan disimpan.<br>";
    } else {
        echo "Gagal mengupdate password untuk '$username': " . $stmt->error . "<br>";
    }
}

$conn->close();
?>
