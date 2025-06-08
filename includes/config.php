<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
$is_logged_in = isset($user);
$user_id = $is_logged_in ? $user['id'] : null;
$username = $is_logged_in ? $user['nama'] : 'Guest';
$role = $is_logged_in ? strtolower($user['role']) : 'guest';
$base_url = "http://localhost/project-web-teori";
