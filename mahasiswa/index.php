<?php
session_start();

// Periksa apakah pengguna sudah login dan memiliki role 'mahasiswa'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../login.php');
    exit();
}

include('../db_connection.php');

// Ambil data mahasiswa berdasarkan username
$username = $_SESSION['username'];
$query = "SELECT nama, npm, prodi, fakultas FROM mahasiswa WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$mahasiswa = $result->fetch_assoc();

if (!$mahasiswa) {
    echo "<p>Data mahasiswa tidak ditemukan.</p>";
    exit();
}

// Ambil data nilai berdasarkan npm mahasiswa
$npm = $mahasiswa['npm'];
$query_nilai = "SELECT mata_kuliah, semester, nilai_harian, nilai_uts, nilai_uas, nilai_akhir, grade FROM nilai WHERE npm = ?";
$stmt_nilai = $conn->prepare($query_nilai);
$stmt_nilai->bind_param("s", $npm);
$stmt_nilai->execute();
$result_nilai = $stmt_nilai->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1 class="text-center mt-3">Selamat Datang, <?= htmlspecialchars($mahasiswa['nama']); ?>!</h1>
        <p class="text-center">
            NPM: <?= htmlspecialchars($mahasiswa['npm']); ?> | 
            Prodi: <?= htmlspecialchars($mahasiswa['prodi']); ?> | 
            Fakultas: <?= htmlspecialchars($mahasiswa['fakultas']); ?>
        </p>
    </header>
    <main class="container mt-4">
        <h2 class="text-center">Data Nilai</h2>
        <?php if ($result_nilai->num_rows > 0): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Semester</th>
                        <th>Nilai Harian</th>
                        <th>Nilai UTS</th>
                        <th>Nilai UAS</th>
                        <th>Nilai Akhir</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($nilai = $result_nilai->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($nilai['mata_kuliah']); ?></td>
                        <td><?= htmlspecialchars($nilai['semester']); ?></td>
                        <td><?= htmlspecialchars($nilai['nilai_harian']); ?></td>
                        <td><?= htmlspecialchars($nilai['nilai_uts']); ?></td>
                        <td><?= htmlspecialchars($nilai['nilai_uas']); ?></td>
                        <td><?= htmlspecialchars($nilai['nilai_akhir'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($nilai['grade'] ?? '-'); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Belum ada data nilai yang tersedia.</p>
        <?php endif; ?>
    </main>
    <div class="text-center mt-3">
            <form action="../logout.php" method="post">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
    </div>
</body>
</html>
