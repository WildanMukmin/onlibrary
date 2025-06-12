<?php
include '../../includes/db_connection.php';
session_start();

if (isset($_SESSION['user'])) {
    header("Location: ../../views/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $nomor = mysqli_real_escape_string($conn, trim($_POST['nomor']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));

    if (empty($nama) || empty($email) || empty($_POST['password']) || empty($nomor) || empty($alamat)) {
        $_SESSION['error'] = "Semua field wajib diisi.";
        header("Location: register.php");
        exit;
    }

    $checkQuery = "SELECT * FROM anggota WHERE email = '$email'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        $_SESSION['error'] = "Email sudah terdaftar.";
        header("Location: register.php");
        exit;
    }

    $insertQuery = "INSERT INTO anggota (nama, email, password, nomor, alamat) 
                    VALUES ('$nama', '$email', '$password', '$nomor', '$alamat')";

    if (mysqli_query($conn, $insertQuery)) {
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat menyimpan data.";
        header("Location: register.php");
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-green-100 to-emerald-100 min-h-screen flex items-center justify-center px-4">
  <div class="flex flex-col md:flex-row bg-white shadow-2xl rounded-3xl overflow-hidden max-w-4xl w-full">
    
    <div class="md:w-1/2 flex">
      <img src="../../assets/signup.jpg" alt="Sign up image" class="w-full h-full object-cover">
    </div>

    <div class="md:w-1/2 p-10">
      <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">OnLibrary</h2>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="register.php" class="space-y-4">
        <div>
          <label for="nama" class="block text-sm font-medium text-gray-700">Full Name</label>
          <input type="text" name="nama" id="nama" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-400 focus:outline-none">
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" name="email" id="email" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-400 focus:outline-none">
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input type="password" name="password" id="password" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-400 focus:outline-none">
        </div>

        <div>
          <label for="nomor" class="block text-sm font-medium text-gray-700">Phone Number</label>
          <input type="text" name="nomor" id="nomor" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-400 focus:outline-none">
        </div>

        <div>
          <label for="alamat" class="block text-sm font-medium text-gray-700">Address</label>
          <input type="text" name="alamat" id="alamat" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-400 focus:outline-none">
        </div>

        <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md shadow-md transition duration-300">
          Sign Up
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-600">
        Have an account?
        <a href="login.php" class="text-green-600 hover:underline font-medium">Log in</a>
      </p>
    </div>
  </div>
</body>
</html>
