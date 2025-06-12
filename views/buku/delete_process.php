<?php

include_once '../../functions/buku.php';
require_once '../../includes/gate_auth.php';
$id = $_GET['id'];
deleteBook($id);
header("Location: list.php");

?>