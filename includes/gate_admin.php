<?php
include("config.php");

if ($role !== "admin" && $is_logged_in) {
    header("Location: /onlibrary/views/dashboard.php");
    exit;
}

if ($role !== "admin"&& !$is_logged_in){
    header("Location: /onlibrary/public/auth/login.php");
    exit;
}