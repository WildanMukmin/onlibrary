<?php
require_once __DIR__ . '/../../functions/anggota.php';
require_once __DIR__ . '/../../includes/gate_admin.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $member_id = $_GET['id'];

    deleteMember($member_id);

    header("Location: list.php?status=deleted");
    exit;
} else {
    header("Location: list.php");
    exit;
}

?>