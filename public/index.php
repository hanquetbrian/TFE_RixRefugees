<?php
phpinfo();
die;
session_start();

require_once '../php_function/Auth.php';
require_once '../config.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);

$url = (isset($_GET["q"]) ? $_GET["q"] : "/");

if (isset($_GET['user']) && $_GET['user'] == 'guest') {
    $AUTH->connectWithGuest();
}

switch ($url) {
    case "/":
        if ($AUTH->isConnected()) {
            if($AUTH->isCoordinator()) {
                $title = "RixRefugee";
                include "../include/lodging.php";
            } else {
                include "../error/access_denied.html";
            }

        } else {
            $AUTH->connectToFacebook();
        }
        break;
    case "/info_lodging":
        $title = "RixRefugee info";
        include "../include/info_lodging.php";
        break;
    case "/survey":
        $title = "RixRefugee Survey";
        include "../include/survey.php";
        break;
    case "/add_survey":
        $title = "RixRefugee add survey";
        include "../include/add_survey.php";
        break;
    case "/coordinator":
        $title = "RixRefugee Coordinateur";
        include "../include/coordinator.php";
        break;
    case "/info_coordinator":
        include "../include/info_coordinator.php";
        break;
    case "/validating_coordinator":
        include "../include/validating_coordinator.php";
        break;
    case "/fb-callback":
        include "../php_function/fb-callback.php";
        break;

    case "/ask_access":
        include "../php_function/ask_access.php";
        break;
    case "/policy":
        include "../policy/privacy_policy.html";
        break;
    case "/terms":
        include "../policy/terms_conditions.html";
        break;
    default:
        include "../error/404.html";
}
