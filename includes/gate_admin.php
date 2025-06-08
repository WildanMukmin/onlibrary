<?php
include("config.php");

if ($role !== "admin" && $is_logged_in) {
    header("Location: /project-web-teori/views/dashboard.php");
    exit;
}

if ($role !== "admin"&& !$is_logged_in){
    header("Location: /project-web-teori/public/auth/login.php");
    exit;
}