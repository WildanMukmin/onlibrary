
<?php
include("config.php");

if ($role !== "user" && $is_logged_in) {
    header("Location: /onlibrary/views/dashboard.php");
    exit;
}

if ($role !== "user"&& !$is_logged_in){
    header("Location: /onlibrary/public/auth/login.php");
    exit;
}