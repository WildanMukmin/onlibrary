<?php
include("config.php");

if (!$is_logged_in){
    header("Location: /onlibrary/public/auth/login.php");
    exit;
}