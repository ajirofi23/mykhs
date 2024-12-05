<?php
session_start();


// Periksa apakah pengguna sudah login dan memiliki role 'dosen'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'dosen') {
    header('Location: login.php');
    exit();
}

$dosen_uname = $_SESSION['username']; // Ini harus cocok dengan 'nid' di tabel 'dosen' okee bang
include('../db_connection.php');

// Redirect if the user is not logged in or not a dosen
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'dosen') {
    header('Location: login.php');
    exit();
}

// Fetch the dosen's profile information
$dosen_uname = $_SESSION['username'];
// give it a shot  anjaaayy wkwkwkwkwk 
$query = "SELECT * FROM dosen WHERE username = ?"; // ini salah why r u comparing nid with username? isn't this one should be a username isn't it? yeaa banggg 
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $dosen_uname);
$stmt->execute();
$dosen_result = $stmt->get_result();
$dosen = $dosen_result->fetch_assoc(); // variable dosen ada isinya kah? coba cek bang
// var_dump($dosen); // coba run
//masih sama bang nama tid
// var dump ngga ada nampilin apapun? nama tidak tersedia bang NULL BANG
// kejap ya  NULL BANGG

// // Check if the query was successful
// if ($dosen_result->num_rows > 0) {
//     $dosen = $dosen_result->fetch_assoc();
//     echo "<pre>";
//     print_r($dosen);  // Debug output of the dosen's data
//     echo "</pre>";
// } else {
//     echo "Data dosen tidak ditemukan!";
//     exit();
// }

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
    // $nilai_akhir = $_POST['nilai_akhir']; // Nilai Akhir


    // Periksa apakah mata kuliah dosen ada
    $mata_kuliah = isset($dosen['matakuliah']) ? $dosen['matakuliah'] : 'Tidak Diketahui';

    // Query untuk insert data nilai ke dalam tabel 'nilai'
    $insert_query = "INSERT INTO nilai (npm, nid, mata_kuliah, semester, nilai_harian, nilai_uts, nilai_uas) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Siapkan statement dan bind parameter
    $stmt = $conn->prepare($insert_query);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);  // Jika prepare gagal
    }
    
    // Pastikan parameter yang dibind sesuai dengan tipe data
    if (!$stmt->bind_param("sssiiii", $npm, $dosen['nid'], $mata_kuliah, $semester, $nilai_harian, $nilai_uts, $nilai_uas)) {
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
    <h2 class="mb-4">Selamat datang, Dosen: <?php echo isset($dosen['nama']) ? $dosen['nama'] : 'Nama tidak tersedia'; ?></h2>

        
        <form action="index.php" method="POST">
            <!-- Select Semester -->
            <div class="mb-3">
                <label for="semester" class="form-label">Pilih Semester</label>
                <select class="form-select" id="semester" name="semester" required>
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                    <option value="3">Semester 3</option>
                    <option value="4">Semester 4</option>
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
                <input type="number" id="nilai_harian" name="nilai_harian" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nilai_uts" class="form-label">Nilai UTS</label>
                <input type="number" id="nilai_uts" name="nilai_uts" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nilai_uas" class="form-label">Nilai UAS</label>
                <input type="number" id="nilai_uas" name="nilai_uas" class="form-control" required>
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


    </script>
</body>
</html>
