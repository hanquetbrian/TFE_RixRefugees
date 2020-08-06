<?php
session_start();

require_once '../../php_function/Auth.php';
require_once '../../config.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);
if(!$AUTH->isCoordinator()) {
    include '../../error/404.html';
    die();
}


$image_request = $_GET['src'];
$image_request = str_replace(['..'], '', $image_request);
$url = "../../p_images/$image_request";

$pic = file_get_contents($url);
header('Content-type: image/jpeg');
echo $pic;