<?php
require_once __DIR__ . '/../includes/db_connection.php';

function getMembers(){
    global $conn;
    $sql = "SELECT * FROM anggota";
    $result = $conn->query($sql);
    return $result;
}

function getMember($member_id) {
    global $conn;
    $sql = "SELECT * FROM anggota WHERE id = $member_id";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function getTotalMembers() {
    global $conn;
    $sql = "SELECT COUNT(id) as total FROM anggota";
    $result = $conn->query($sql);
    if ($result) {
        return $result->fetch_assoc()['total'];
    }
    return 0;
}

function addMember($nama, $email, $password, $nomor, $alamat) {
    global $conn;
    // Validasi data tidak kosong (opsional tambahan)
    if (empty($nama) || empty($email) || empty($_POST['password']) || empty($nomor) || empty($alamat)) {
        $_SESSION['error'] = "Semua field wajib diisi.";
        $_SESSION['error_time'] = time();
        header("Location: list.php");
        exit;
    }

    $checkQuery = "SELECT * FROM anggota WHERE email = '$email'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        $_SESSION['error'] = "Email sudah terdaftar.";
        $_SESSION['error_time'] = time();
        header("Location: list.php");
        exit;
    }

    $insertQuery = "INSERT INTO anggota (nama, email, password, nomor, alamat) 
                    VALUES ('$nama', '$email', '$password', '$nomor', '$alamat')";

    if (mysqli_query($conn, $insertQuery)) {
        $_SESSION['success'] = "Anggota berhasil ditambahkan.";
        $_SESSION['success_time'] = time();
        header("Location: list.php");
        exit;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat menyimpan data.";
        $_SESSION['error_time'] = time();
        header("Location: list.php");
        exit;
    }
}

function updateMember($id, $nama, $alamat, $nomor) {
    global $conn;
    $nama= mysqli_real_escape_string($conn, $nama);
    $alamat= mysqli_real_escape_string($conn, $alamat);
    $nomor= mysqli_real_escape_string($conn, $nomor);
    $id= (int)$id;

    $sql = "UPDATE anggota SET nama = '$nama', alamat = '$alamat', nomor = '$nomor' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function deleteMember($id) {
    global $conn;
    $sql = "DELETE FROM anggota WHERE id = $id";
        return $conn->query($sql);
}

?>