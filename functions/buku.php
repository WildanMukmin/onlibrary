<?php
require_once __DIR__ . '/../includes/db_connection.php';

function getBooks() {
    global $conn;
    $sql = "SELECT * FROM buku";
    $result = $conn->query($sql);
    return $result;
}

function getTotalBookStock() {
    global $conn;
    $sql = "SELECT COUNT(stok) as total_stock FROM buku";
    $result = $conn->query($sql);
    if ($result) {
        return $result->fetch_assoc()['total_stock'] ?? 0;
    }
    return 0;
}

function getBookById($id) {
    global $conn;
    $sql = "SELECT * FROM buku WHERE id = $id";
    $result = $conn->query($sql);
    return $result->fetch_assoc(); 
}

function addBook($judul, $penulis, $penerbit, $tahun_terbit, $isbn, $kategori, $deskripsi, $stok, $image) {
    global $conn;
    $image_path = '';
    if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../public/images/buku/';
        $cleanTitle = preg_replace('/[^a-zA-Z0-9-_]/', '_', strtolower($judul));
        $fileName = $isbn . '_' . time() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
        $targetFile = $uploadDir . $fileName;
        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            $image_path = '/project-web-teori/public/images/buku/' . $fileName;
        }
    }
    $sql = "
        INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, isbn, kategori, deskripsi, stok, image_path)
        VALUES ('$judul', '$penulis', '$penerbit', '$tahun_terbit', '$isbn', '$kategori', '$deskripsi', $stok, '$image_path')
    ";
    $result = $conn->query($sql);
    return $result;
}

function updateBook($id, $judul, $penulis, $penerbit, $tahun_terbit, $isbn, $kategori, $deskripsi, $stok, $image) {
    global $conn;
    
    $oldBook = getBookById($id);
    $image_path = $oldBook['image_path']; 
    
    if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
        
        if (!empty($oldBook['image_path'])) {
            $oldImagePath = $_SERVER['DOCUMENT_ROOT'] . parse_url($oldBook['image_path'], PHP_URL_PATH);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        
        $uploadDir = '../../public/images/buku/';
        $cleanTitle = preg_replace('/[^a-zA-Z0-9-_]/', '_', strtolower($judul));
        $fileName = $isbn . '_' . time() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            $image_path = '/project-web-teori/public/images/buku/' . $fileName;
        }
    }else{
                $cleanTitle = preg_replace('/[^a-zA-Z0-9-_]/', '_', strtolower($judul));
        $fileName = $isbn . '_' . time() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
        $image_path = $oldBook['image_path'];
    }

    $sql = "
        UPDATE buku 
        SET judul = '$judul',
            penulis = '$penulis',
            penerbit = '$penerbit',
            tahun_terbit = '$tahun_terbit',
            isbn = '$isbn',
            kategori = '$kategori',
            deskripsi = '$deskripsi',
            stok = $stok,
            image_path = '$image_path'
        WHERE id = $id
    ";
    
    return $conn->query($sql);
}


function deleteBook($id) {
    global $conn;

    $book = getBookById($id);
    if ($book && !empty($book['image_path'])) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . parse_url($book['image_path'], PHP_URL_PATH);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $sql = "DELETE FROM buku WHERE id = $id";
    return $conn->query($sql);
}
?>
