<?php
session_start();

require_once '../config.php';
require_once '../php_function/Auth.php';
require_once '../php_function/Page.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);

$url = (isset($_GET["q"]) ? $_GET["q"] : "/");

switch ($url) {
    case "/":
        if ($AUTH->isConnected()) {

            if($AUTH->isCoordinator()) {
                require_once '../php_function/db_connection.php';
                $page = new Page('include/lodging.php', "RixRefugee");
                $page->addParam("test", Page::PARAM_VALID_SESSION_ID, $dbh);
                include "../include/template.php";
            } else {
                include "../error/access_denied.html";
            }

        } else {
            $AUTH->connectToFacebook();
        }
        break;
    case "/info_lodging":
        $page = new Page('include/info_lodging.php', "RixRefugee info");
        include "../include/template.php";
        break;
    case "/survey":
        $page = new Page('include/survey.php', "RixRefugee Survey");
        $title = "RixRefugee Survey";
        include "../include/template.php";
        break;
    case "/add_survey":
        $page = new Page('include/add_survey.php', "RixRefugee add survey");
        $title = "";
        include "../include/template.php";
        break;
    case "/coordinator":
        $page = new Page('include/coordinator.php', "RixRefugee Coordinateur");
        $title = "";
        include "../include/template.php";
        break;
    case "/info_coordinator":
        $page = new Page('include/info_coordinator.php', "RixRefugee Coordinateur");
        include "../include/template.php";
        break;
    case "/validating_coordinator":
        $page = new Page('include/validating_coordinator.php', "RixRefugee Coordinateur en demande");
        include "../include/template.php";
        break;
    case "/volunteer":
        $page = new Page('include/volunteer.php', "RixRefugee Bénévoles");
        $title = "";
        include "../include/template.php";
        break;
    case "/info_volunteer":
        $page = new Page('include/info_volunteer.php', "Bénévole");
        include "../include/template.php";
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
    case "/404":
    default:
        include "../error/404.html";
}
