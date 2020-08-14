<?php
session_start();

require_once "../../php_function/db_connection.php";
require_once '../../php_function/Auth.php';
require_once '../../config.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);
if(!$AUTH->isConnected()) {
    die(0);
}

if(!empty($_SESSION['fb_access_token'])) {
    $AUTH->updatePrivateInfo($AUTH->getFbAccessToken(), $dbh);
}

header('location: /edit_user');