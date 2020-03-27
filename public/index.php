<?php
$url = (isset($_GET["q"])?$_GET["q"]:"/");
session_start();

if(isset($_GET['user']) && $_GET['user'] = 'guess') {
    $_SESSION['fb_access_token'] = "";
    $_SESSION['fb_name'] = "Prénom Nom";
    $_SESSION['fb_profile_pic'] = "";
    $_SESSION['fb_id'] = "0";
}

switch ($url) {
    case "/":
        if (isset($_SESSION['fb_access_token'])) {

            $title = "RixRefugiees";
            include "../include/lodging.php";
        } elseif (isset($_SESSION['ERROR']['FB'])) {
            include "../error/access_denied.html";
            session_unset();
        } else {
            include "../php_function/loginFacebook.php";
        }
        break;

    case "/fb-callback":
        include "../php_function/fb-callback.php";
        break;
    default:
        include "../error/404.html";
}
