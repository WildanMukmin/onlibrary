<?php

include_once '../../includes/db_connection.php';
include_once '../../functions/peminjaman.php';
require_once '../../includes/gate_admin.php';
$id = $_GET['id'];
deleteTransactionById($id);
header("Location: list.php");

?>