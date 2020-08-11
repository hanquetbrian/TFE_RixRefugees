<?php
session_start();

require_once '../config.php';
require_once '../php_function/db_connection.php';
require_once '../php_function/Auth.php';
require_once '../php_function/Page.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);
$url = (isset($_GET["q"]) ? $_GET["q"] : "/");

switch ($url) {
    case "/":
        if ($AUTH->isConnected()) {
            if($AUTH->isCoordinator()) {
                $page = new Page('include/lodging.php', "RixRefugee", $AUTH, Page::coordinator);
                $page->addScript('js/lodging.js');
                include "../include/template.php";
            } else {
                $AUTH->updatePrivateInfo($AUTH->getFbAccessToken(), $dbh);
                include "../error/access_denied.html";
            }

        } else {
            header('Location: /login');
        }
        break;
    case "/login":
        $page = new Page('include/login.php', "RixRefugee info");
        $page->addCSS('css/login.css');
        include "../include/template.php";
        break;
    case "/connect_facebook":
        $AUTH->connectToFacebook();
        break;
    case "/info_lodging":
        $page = new Page('include/info_lodging.php', "RixRefugee info", $AUTH, Page::coordinator);
        $page->addParam("lodging_session_id", Page::PARAM_VALID_SESSION_ID, $dbh);
        include "../include/template.php";
        break;
    case "/hosts":
        $page = new Page('include/hosts.php', "RixRefugee hébergeur", $AUTH, Page::coordinator);
        $page->addParam("lodging_session_id", Page::PARAM_VALID_SESSION_ID, $dbh);
        $page->addScript('js/hosts.js');
        include "../include/template.php";
        break;
    case "/list_sessions":
        $page = new Page('include/list_sessions.php', "RixRefugee historique", $AUTH, Page::coordinator);
        $page->addParam("lodging_id", Page::PARAM_VALID_LODGING_ID, $dbh);
        include "../include/template.php";
        break;
    case "/survey":
        $page = new Page('include/survey.php', "RixRefugee Survey", $AUTH, Page::volunteer);
        $page->addParam("lodging_session_id", Page::PARAM_VALID_SESSION_ID, $dbh);
        $page->addScript('js/survey.js');
        $title = "RixRefugee Survey";
        include "../include/template.php";
        break;
    case "/add_survey":
        $page = new Page('include/add_survey.php', "RixRefugee add survey", $AUTH, Page::coordinator);
        $page->addParam("lodging_session_id", Page::PARAM_VALID_SESSION_ID, $dbh);
        $page->addScript('js/survey.js');
        $title = "";
        include "../include/template.php";
        break;
    case "/coordinator":
        $page = new Page('include/coordinator.php', "RixRefugee Coordinateur", $AUTH, Page::volunteer);
        $title = "";
        include "../include/template.php";
        break;
    case "/info_coordinator":
        $page = new Page('include/info_coordinator.php', "RixRefugee Coordinateur", $AUTH, Page::volunteer);
        $page->addParam("coord_id", Page::PARAM_VALID_COORD_ID, $dbh);
        include "../include/template.php";
        break;
    case "/validating_coordinator":
        $page = new Page('include/validating_coordinator.php', "RixRefugee Coordinateur en demande", $AUTH, Page::coordinator);
        include "../include/template.php";
        break;
    case "/edit_user":
        $page = new Page('include/edit_user.php', "RixRefugee Modification du profile", $AUTH, Page::coordinator);
        include "../include/template.php";
        break;
    case "/volunteer":
        $page = new Page('include/volunteer.php', "RixRefugee Bénévoles", $AUTH, Page::coordinator);
        $title = "";
        include "../include/template.php";
        break;
    case "/info_volunteer":
        $page = new Page('include/info_volunteer.php', "Bénévole", $AUTH, Page::coordinator);
        $page->addParam("volunteer_id", Page::PARAM_VALID_VOLUNTER_USER_ID, $dbh);
        include "../include/template.php";
        break;
    case "/ask_access":
        $page = new Page('include/ask_access.php', "Demande d'accès");
        include "../include/template.php";
        break;
    case "/inventory_management":
        $page = new Page('include/inventory_management.php', "RixRefugee Gestion des stocks", $AUTH, Page::coordinator);
        $page->addScript('js/inventory.js');
        include "../include/template.php";
        break;
    case "/fb-callback":
        include "../php_function/fb-callback.php";
        break;
    case "/policy":
        include "../policy/privacy_policy.html";
        break;
    case "/terms":
        include "../policy/terms_conditions.html";
        break;
    case "/501":
        include "../error/50x.html";
        break;
    case "/404":
    default:
        include "../error/404.html";
}
