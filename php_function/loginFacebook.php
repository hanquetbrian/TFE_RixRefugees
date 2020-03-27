<?php
require_once '../php_function/fb-object.php';
$helper = $fb->getRedirectLoginHelper();
$loginUrl = $helper->getLoginUrl('https://rixrefugee.site/fb-callback');
header('Location: ' . $loginUrl);