<?php
//TODO Only access this page from Facebook
require_once "../php_function/fb-object.php";

$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    //TODO increase security here (don't show error message)

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
    exit;
}

// Logged in
$response = $fb->get('/me/?fields=picture,name,id', $accessToken);
$user = $response->getGraphUser();

// Check if the user is authorized to access the page
require_once "../php_function/db_connection.php";
$sql = "
    SELECT name, facebook_id
    FROM rix_refugee.Coordinator
    WHERE facebook_id = :facebook_id
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([':facebook_id' => $user['id']]);
$login = $sth->fetchAll(PDO::FETCH_ASSOC);

if(empty($login)) {
    $_SESSION['ERROR']['FB']['NOT_AUTHORIZED'] = "The user is not authorized to access this page";
} else {
    $_SESSION['fb_access_token'] = (string) $accessToken;
    $_SESSION['fb_name'] = $user['name'];
    $_SESSION['fb_profile_pic'] = $user['picture']['url'];
    $_SESSION['fb_id'] = $user['id'];
}

header('Location: /#');