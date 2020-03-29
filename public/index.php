<?php
$url = (isset($_GET["q"])?$_GET["q"]:"/");
session_start();

if(isset($_GET['user']) && $_GET['user'] = 'guest') {
    $_SESSION['fb_access_token'] = "";
    $_SESSION['fb_name'] = "Prénom Nom";
    $_SESSION['fb_profile_pic'] = "";
    $_SESSION['fb_id'] = "0";
}

switch ($url) {
    case "/":
        if (isset($_SESSION['fb_access_token'])) {

            $title = "RixRefugee";
            include "../include/lodging.php";
        } elseif (isset($_SESSION['ERROR']['FB'])) {
            include "../error/access_denied.html";
            session_unset();
        } else {
            include "../php_function/loginFacebook.php";
        }
        break;
    case "/info_lodging":
        $title = "RixRefugee info";
        include "../include/info_lodging.php";
        break;
    case "/survey":
        include "../include/survey.php";
        break;
    case "/add_survey":
        $title = "RixRefugee add survey";
        include "../include/add_survey.php";
        break;
    case "/fb-callback":
        include "../php_function/fb-callback.php";
        break;
    default:
        include "../error/404.html";
}
