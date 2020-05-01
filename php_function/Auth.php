<?php

use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

require_once __DIR__ . '/../vendor/autoload.php';

class Auth
{
    private $name;
    private AccessToken $fb_access_token;
    private $fb_small_profile_pic;
    private $fb_profile_pic;
    private $fb_id;
    private bool $isConnected = false;
    private bool $isCoordinator = false;

    private Facebook $fb_object;

    public function __construct($fb_appId, $fb_appSecret) {
        try {
            $this->fb_object = new Facebook([
                'app_id' => $fb_appId,
                'app_secret' => $fb_appSecret,
                'cookie' => true
            ]);
        } catch (FacebookSDKException $e) {
            http_response_code(500);
            die();
        }

        if (isset($_SESSION['fb_access_token']) &&
            isset($_SESSION['fb_name'])&&
            isset($_SESSION['fb_small_profile_pic'])&&
            isset($_SESSION['fb_profile_pic'])&&
            isset($_SESSION['fb_id'])&&
            isset($_SESSION['isCoordinator'])) {

            $this->fb_access_token = new AccessToken($_SESSION['fb_access_token']);
            $this->name = $_SESSION['fb_name'];
            $this->fb_small_profile_pic = $_SESSION['fb_small_profile_pic'];
            $this->fb_profile_pic = $_SESSION['fb_profile_pic'];
            $this->fb_id = $_SESSION['fb_id'];
            $this->isCoordinator = (bool)$_SESSION['isCoordinator'];
            $this->isConnected = true;
        }
    }

    /**
     * Update the info on the connected user.
     * @param AccessToken $fb_access_token set the access token to get access to user info
     * @param $dbh PDO Is a database connection
     */
    public function updatePrivateInfo($fb_access_token, $dbh) {

        $this->fb_access_token = $fb_access_token;
        try {
            $response = $this->fb_object->get('/me/?fields=picture,name,id,email', $this->fb_access_token);
            $user = $response->getGraphUser();

            $picture_url = $this->fb_object->get('/me/picture?redirect=0&type=normal', $this->fb_access_token)->getGraphNode()['url'];
        } catch (FacebookSDKException $e) {
            $this->isConnected = false;
            return;
        }

        $this->name = $user['name'];
        $this->fb_small_profile_pic = $user['picture']['url'];
        $this->fb_profile_pic = $picture_url;
        $this->fb_id = $user['id'];
        $this->isConnected = true;

        // Store the info in SESSION
        $_SESSION['fb_access_token'] = (string) $this->fb_access_token;
        $_SESSION['fb_name'] = $this->name;
        $_SESSION['fb_small_profile_pic'] = $this->fb_small_profile_pic;
        $_SESSION['fb_profile_pic'] = $this->fb_profile_pic;
        $_SESSION['fb_id'] = $this->fb_id;

        // Check if the user is authorized to access the page
        $sql = "
            SELECT name, facebook_id
            FROM rix_refugee.valid_coordinator
            WHERE facebook_id = :facebook_id
        ";

        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute([':facebook_id' => $user['id']]);
        $login = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($login)) {
            $this->isCoordinator = false;
            $_SESSION['isCoordinator'] = $this->isCoordinator;
        } else {
            $this->isCoordinator = true;
            $_SESSION['isCoordinator'] = $this->isCoordinator;

            $sql = "UPDATE rix_refugee.Coordinator SET name = :name, small_picture_url = :small_picture, picture_url = :picture, email = :email WHERE facebook_id = :facebook_id";
            $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sth->execute([
                ':name' => $user['name'],
                ':small_picture' => $user['picture']['url'],
                ':picture' => $picture_url,
                ':email' => $user['email'],
                ':facebook_id' => $user['id']
            ]);
        }
    }

    public function disconnect(){
        $_SESSION['fb_access_token'] = "";
        $_SESSION['fb_name'] = "";
        $_SESSION['fb_small_profile_pic'] = "";
        $_SESSION['fb_profile_pic'] = "";
        $_SESSION['fb_id'] = "";
        $_SESSION['isCoordinator'] = "";
        session_unset();

        unset($this->fb_access_token);
        unset($this->name);
        unset($this->fb_small_profile_pic);
        unset($this->fb_profile_pic);
        unset($this->fb_id);
        unset($this->isCoordinator);
        $this->isConnected = false;
    }

    /**
     * @return string name of the auth person
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return AccessToken access Token
     */
    public function getFbAccessToken()
    {
        return $this->fb_access_token;
    }

    /**
     * @return string url of a small profile picture (50x50)
     */
    public function getFbSmallProfilePic()
    {
        return $this->fb_small_profile_pic;
    }

    /**
     * @return string url of a normal size picture (100x100)
     */
    public function getFbProfilePic()
    {
        return $this->fb_profile_pic;
    }

    /**
     * @return string facebook id of the auth person
     */
    public function getFbId()
    {
        return $this->fb_id;
    }

    /**
     * @return Facebook return a facebook object
     */
    public function getFbObject(): Facebook
    {
        return $this->fb_object;
    }

    /**
     * @return bool return true if the user is connected
     */
    public function isConnected() {
        return $this->isConnected;
    }

    /**
     * @return bool return true if the user is a coordinator
     */
    public function isCoordinator() {
        return $this->isCoordinator;
    }

    /**
     * Try to connect the user to his facebook
     */
    public function connectToFacebook() {
        $helper = $this->fb_object->getRedirectLoginHelper();
        $loginUrl = $helper->getLoginUrl('https://rixrefugee.site/fb-callback');
        header('Location: ' . $loginUrl);
    }


    // TODO Remove the guest login
    /* TEMP FUNCTION */

    public function connectWithGuest() {
        $this->name = "Utilisateur test";
        $this->fb_small_profile_pic = "";
        $this->fb_profile_pic = "";
        $this->fb_id = "";
        $this->isCoordinator = true;
        $this->isConnected = true;

        $_SESSION['fb_access_token'] = "123";
        $_SESSION['fb_name'] = $this->name;
        $_SESSION['fb_small_profile_pic'] = $this->fb_small_profile_pic;
        $_SESSION['fb_profile_pic'] = $this->fb_profile_pic;
        $_SESSION['fb_id'] = $this->fb_id;
        $_SESSION['isCoordinator'] = $this->isCoordinator;
    }
}