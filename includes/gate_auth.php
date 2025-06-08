<?php
include("config.php");

if (!$is_logged_in){
    header("Location: /project-web-teori/public/auth/login.php");
    exit;
}