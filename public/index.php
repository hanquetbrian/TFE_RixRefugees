<?php
$url = (isset($_GET["q"])?$_GET["q"]:"/");

switch ($url) {
    case "/":
        session_start();
        if(isset($_SESSION['fb_access_token'])) {

            $title = "RixRefugiees";
            include "../include/lodging.php";
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
