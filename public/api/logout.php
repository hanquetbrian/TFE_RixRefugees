<?php
session_start();

require_once '../../php_function/Auth.php';
require_once '../../config.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);
$AUTH->disconnect();
header('location: /');