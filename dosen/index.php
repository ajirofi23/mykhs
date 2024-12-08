<?php
session_start();

// Periksa apakah pengguna sudah login dan memiliki role 'dosen'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'dosen') {
    header('Location: login.php');
    exit();
}

$dosen_uname = $_SESSION['username']; // Ini harus cocok dengan 'nid' di tabel 'dosen'
include('../db_connection.php');

// Fetch the dosen's profile information berdasarkan username
$query = "SELECT nama, nid, matakuliah FROM dosen WHERE username = ?"; 
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $dosen_uname);
$stmt->execute();
$dosen_result = $stmt->get_result();
$dosen = $dosen_result->fetch_assoc(); 

// Fetch mahasiswa (students) to populate the NPM dropdown
$mahasiswa_query = "SELECT npm, nama, prodi, jenis_kelamin FROM mahasiswa";
$mahasiswa_result = $conn->query($mahasiswa_query);

// Handle form submission for inputting grades
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $npm = $_POST['npm']; // NPM Mahasiswa
    $semester = $_POST['semester']; // Semester
    $nilai_harian = $_POST['nilai_harian']; // Nilai Harian
    $nilai_uts = $_POST['nilai_uts']; // Nilai UTS
    $nilai_uas = $_POST['nilai_uas']; // Nilai UAS

    // Periksa apakah mata kuliah dosen ada
    $mata_kuliah = isset($dosen['matakuliah']) ? $dosen['matakuliah'] : 'Tidak Diketahui';

    // Pastikan bahwa $dosen['nid'] adalah string, bukan integer
    $nid = (string) $dosen['nid']; // Mengonversi ke string jika diperlukan

    // Menghitung nilai akhir
    $nilai_akhir = ($nilai_harian * 0.4) + ($nilai_uts * 0.3) + ($nilai_uas * 0.3);

    // Menentukan grade berdasarkan nilai akhir
    if ($nilai_akhir >= 85) {
        $grade = "A";
    } elseif ($nilai_akhir >= 70) {
        $grade = "B";
    } elseif ($nilai_akhir >= 55) {
        $grade = "C";
    } else {
        $grade = "D";
    }

    // Query untuk insert data nilai ke dalam tabel 'nilai'
    $insert_query = "INSERT INTO nilai (npm, nid, mata_kuliah, semester, nilai_harian, nilai_uts, nilai_uas, nilai_akhir, grade) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Siapkan statement dan bind parameter
    $stmt = $conn->prepare($insert_query);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);  // Jika prepare gagal
    }

    // Bind parameter untuk nilai yang akan dimasukkan ke dalam query
    if (!$stmt->bind_param("sssiiiiis", $npm, $nid, $mata_kuliah, $semester, $nilai_harian, $nilai_uts, $nilai_uas, $nilai_akhir, $grade)) {
        die("Parameter binding failed: " . $stmt->error);  // Jika binding gagal
    }

    // Eksekusi query dan periksa apakah berhasil
    if ($stmt->execute()) {
        echo "<script>alert('Data nilai berhasil disimpan');</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: " . $stmt->error . "');</script>"; // Menampilkan pesan error eksekusi
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Nilai - Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-gradient-to-r from-teal-500 to-teal-300 text-white p-4">
        <h1 class="text-center">Input Nilai - Sistem Akademik</h1>
    </header>
    <main class="container my-5">
        <h2 class="mb-4">Selamat datang, Dosen: <?php echo isset($dosen['nama']) ? $dosen['nama'] : $dosen_uname; ?></h2>

        <form action="index.php" method="POST">
            <!-- Select Semester -->
            <div class="mb-3">
                <label for="semester" class="form-label">Pilih Semester</label>
                <select class="form-select" id="semester" name="semester" required>
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                    <option value="3">Semester 3</option>
                    <option value="4">Semester 4</option>
                    <option value="5">Semester 5</option>
                    <option value="6">Semester 6</option>
                    <option value="7">Semester 7</option>
                    <option value="8">Semester 8</option>
                </select>
            </div>

            <!-- Select NPM (Mahasiswa) -->
            <div class="mb-3">
                <label for="npm" class="form-label">Pilih NPM Mahasiswa</label>
                <select class="form-select" id="npm" name="npm" onchange="fetchStudentInfo()" required>
                    <option value="">-- Pilih NPM --</option>
                    <?php while ($mahasiswa = $mahasiswa_result->fetch_assoc()): ?>
                        <option value="<?php echo $mahasiswa['npm']; ?>">
                            <?php echo $mahasiswa['npm']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Student Info -->
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" id="nama" class="form-control" readonly>
            </div>
            <div class="mb-3">
                <label for="prodi" class="form-label">Prodi</label>
                <input type="text" id="prodi" class="form-control" readonly>
            </div>
            <div class="mb-3">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                <input type="text" id="jenis_kelamin" class="form-control" readonly>
            </div>

            <!-- Input Nilai -->
            <div class="mb-3">
                <label for="nilai_harian" class="form-label">Nilai Harian</label>
                <input type="number" id="nilai_harian" name="nilai_harian" class="form-control" oninput="calculateFinalGrade()" required>
            </div>
            <div class="mb-3">
                <label for="nilai_uts" class="form-label">Nilai UTS</label>
                <input type="number" id="nilai_uts" name="nilai_uts" class="form-control" oninput="calculateFinalGrade()" required>
            </div>
            <div class="mb-3">
                <label for="nilai_uas" class="form-label">Nilai UAS</label>
                <input type="number" id="nilai_uas" name="nilai_uas" class="form-control" oninput="calculateFinalGrade()" required>
            </div>
            <div class="mb-3">
                <label for="nilai_akhir" class="form-label">Nilai Akhir</label>
                <input type="number" id="nilai_akhir" name="nilai_akhir" class="form-control" readonly>
            </div>
            <div class="mb-3">
                <label for="grade" class="form-label">Grade</label>
                <input type="text" id="grade" name="grade" class="form-control" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Nilai</button>
        </form>
    </main>

    <script>
    // Fungsi untuk mengambil info mahasiswa saat memilih NPM
    function fetchStudentInfo() {
        const npm = document.getElementById('npm').value;
        
        // Cek jika NPM dipilih
        if (npm) {
            fetch('get_student_info.php?npm=' + npm)
                .then(response => response.json())  // Menunggu response dalam format JSON
                .then(data => {
                    // Jika ada data mahasiswa, isi input dengan data tersebut
                    if (data.error) {
                        alert(data.error); // Tampilkan pesan error jika mahasiswa tidak ditemukan
                    } else {
                        document.getElementById('nama').value = data.nama;
                        document.getElementById('prodi').value = data.prodi;
                        document.getElementById('jenis_kelamin').value = data.jenis_kelamin;
                    }
                })
                .catch(error => {
                    console.error('Terjadi kesalahan:', error);
                });
        }
    }

    // Fungsi untuk menghitung nilai akhir dan menentukan grade
    function calculateFinalGrade() {
        const nilaiHarian = parseFloat(document.getElementById('nilai_harian').value) || 0;
        const nilaiUTS = parseFloat(document.getElementById('nilai_uts').value) || 0;
        const nilaiUAS = parseFloat(document.getElementById('nilai_uas').value) || 0;

        // Hitung nilai akhir
        const nilaiAkhir = (nilaiHarian * 0.4) + (nilaiUTS * 0.3) + (nilaiUAS * 0.3);
        document.getElementById('nilai_akhir').value = nilaiAkhir.toFixed(2);

        // Tentukan grade berdasarkan nilai akhir
        let grade;
        if (nilaiAkhir >= 85) grade = "A";
        else if (nilaiAkhir >= 70) grade = "B";
        else if (nilaiAkhir >= 55) grade = "C";
        else grade = "D";

        // Tampilkan grade ke input
        document.getElementById('grade').value = grade;
    }
    </script>
     <div class="text-center mt-3">
            <form action="../logout.php" method="post">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
    </div>

</body>
</html>
