<?php
// Include database connection
include('../db_connection.php');

// Ambil NPM dari parameter URL
$npm = isset($_GET['npm']) ? $_GET['npm'] : '';

// Query untuk mengambil data mahasiswa berdasarkan NPM
$query = "SELECT * FROM mahasiswa WHERE npm = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $npm);
$stmt->execute();
$result = $stmt->get_result();

// Jika mahasiswa ditemukan, kirim data mahasiswa dalam format JSON
if ($result->num_rows > 0) {
    $mahasiswa = $result->fetch_assoc();
    echo json_encode($mahasiswa);
} else {
    echo json_encode(['error' => 'Mahasiswa tidak ditemukan']);
}
?>
