<?php
include '../../includes/db_connection.php';
session_start();
if (isset($_SESSION['user'])) {
    header("Location: ../../views/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    $query = "SELECT * FROM anggota WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($pass, $user['password'])) {
            unset($user['password']); // Hapus password dari array user
            $_SESSION['user'] = $user;
            header("Location: ../../views/dashboard.php");
            exit;
        } else {
            $_SESSION['error'] = "Password salah.";
        }
    } else {
        $_SESSION['error'] = "Email tidak ditemukan.";
    }

    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-blue-100 to-indigo-100 min-h-screen flex items-center justify-center px-4">
  <div class="flex flex-col md:flex-row bg-white shadow-2xl rounded-3xl overflow-hidden max-w-4xl w-full">
    
    <div class="md:w-1/2 flex">
      <img src="../../assets/login.jpg" alt="Library" class="rounded-xl-left shadow-lg object-cover">
    </div>

    <div class="md:w-1/2 p-10">
      <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Welcome To OnLibrary</h2>

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

      <form method="POST" action="login.php" class="space-y-5">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="text" name="email" id="email" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input type="password" name="password" id="password" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow-md transition duration-300">
          Log in
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-600">
        Don't have an account?
        <a href="register.php" class="text-blue-600 hover:underline font-medium">Sign Up</a>
      </p>
    </div>
  </div>
</body>
</html>
