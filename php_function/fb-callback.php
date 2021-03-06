<?php
$fb = $AUTH->getFbObject();

$helper = $fb->getRedirectLoginHelper();
try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {

    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

if (! isset($accessToken)) {
    if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
    }
    header('Location: /501');
    exit;
}

// Logged in
require_once 'db_connection.php';
require_once '../config.php';
$AUTH->callbackLogin($accessToken,$dbh, $config);

if(isset($_SESSION['requested_page'])) {
    header('Location: ' . $_SESSION['requested_page']);
    unset($_SESSION['requested_page']);
} else {
    header('Location: /#');
}