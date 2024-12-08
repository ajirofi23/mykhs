<?php
session_start();
include('db_connection.php'); // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form dan sanitasi input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); 

    // Query untuk mencari user berdasarkan username
    $stmt = $conn->prepare("SELECT username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Pengecekan password (MD5 atau password_hash)
            if (strlen($user['password']) == 32) { // Password MD5
                $isPasswordValid = (md5($password) === $user['password']);
            } else { // Password menggunakan password_hash
                $isPasswordValid = password_verify($password, $user['password']);
            }

            if ($isPasswordValid) {
                // Set session untuk user
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect berdasarkan role
                $redirectPaths = [
                    'mahasiswa' => 'mahasiswa/index.php',
                    'dosen' => 'dosen/index.php'
                ];

                header("Location: " . ($redirectPaths[$user['role']] ?? 'login.php'));
                exit();
            } else {
                echo "<script>alert('Password salah!');</script>";
            }
        } else {
            echo "<script>alert('Username tidak ditemukan!');</script>";
        }
    } else {
        // Debugging query error
        echo "Query gagal: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Akademik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://horizon.ac.id/wp-content/uploads/2023/05/1-1-scaled.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            backdrop-filter: brightness(0.9);
            overflow: hidden;
        }
        .login-container {
            backdrop-filter: blur(10px) brightness(1.1);
            background: rgba(255, 255, 255, 0.8);
            animation: fadeIn 1s ease-out;
        }
        header {
            background: linear-gradient(90deg, rgba(0, 172, 193, 1) 0%, rgba(128, 208, 199, 1) 100%);
            color: white;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: slideIn 1s ease-out;
        }
        header h1 {
            font-family: 'Arial', sans-serif;
            text-align: center;
            font-size: 2.5rem;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        .image-container {
            animation: slideInLeft 1s ease-out;
        }
        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <header>
        <h1>Sistem Akademik by MyKHS</h1>
    </header>
    <main class="flex justify-center items-center h-screen">
        <div class="grid grid-cols-2 gap-4 items-center w-3/4">
            <div class="image-container">
                <img src="https://horizon.ac.id/wp-content/uploads/2023/12/website1-scaled.jpg" alt="Academic Illustration" class="rounded-lg shadow-lg w-full">
            </div>
            <div class="login-container bg-white shadow-md rounded-lg p-6 max-w-sm w-full">
                <h2 class="text-2xl font-bold text-center mb-4">Login</h2>
                <form action="login.php" method="POST">
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" id="username" name="username" class="form-control mt-1" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password" class="form-control mt-1" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
