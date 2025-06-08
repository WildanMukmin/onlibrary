
<?php
include("config.php");

if ($role !== "user" && $is_logged_in) {
    header("Location: /project-web-teori/views/dashboard.php");
    exit;
}

if ($role !== "user"&& !$is_logged_in){
    header("Location: /project-web-teori/public/auth/login.php");
    exit;
}