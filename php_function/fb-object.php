<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../config.php';

session_start();
$fb = new Facebook\Facebook([
    'app_id' => $config['fb.app_id'],
    'app_secret' => $config['fb.app_secret']
]);
