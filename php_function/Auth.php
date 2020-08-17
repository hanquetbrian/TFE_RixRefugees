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
    private $fb_email;
    private $fb_id;
    private $user_id;
    private $coord_id;
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

        if (isset($_SESSION['fb_name'])&&
            isset($_SESSION['fb_email'])&&
            isset($_SESSION['user_id'])&&
            isset($_SESSION['coord_id'])&&
            isset($_SESSION['isCoordinator'])) {

            $this->fb_access_token = new AccessToken($_SESSION['fb_access_token']);
            $this->name = $_SESSION['fb_name'];
            $this->fb_small_profile_pic = $_SESSION['fb_small_profile_pic'];
            $this->fb_profile_pic = $_SESSION['fb_profile_pic'];
            $this->fb_email = $_SESSION['fb_email'];
            $this->fb_id = $_SESSION['fb_id'];
            $this->user_id = $_SESSION['user_id'];
            $this->coord_id = $_SESSION['coord_id'];
            $this->isCoordinator = (bool)$_SESSION['isCoordinator'];
            $this->isConnected = true;

        }
    }

    public function callbackLogin($fb_access_token, $dbh, $config) {
        $this->fb_access_token = $fb_access_token;

        try {
            $response = $this->fb_object->get('/me/?fields=id', $this->fb_access_token);
            $user = $response->getGraphUser();
        } catch (FacebookSDKException $e) {
            $this->disconnect();
            return;
        }
        $facebookId = $user['id'];

        $sql = "
            SELECT User.id,
                   CAST(AES_DECRYPT(name, :secret_key) AS CHAR(60)) AS name,
                   small_picture_url,
                   picture_url,
                   CAST(AES_DECRYPT(email, :secret_key) AS CHAR(255)) AS email,
                   facebook_id,
                   Coordinator.id as coord_id
            FROM User
            LEFT JOIN Coordinator on User.id = Coordinator.user_id
            WHERE facebook_id = :facebook_id;
        ";

        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
        $sth->bindParam(':facebook_id', $facebookId, PDO::PARAM_INT);
        $sth->execute();
        $login = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($login)) {
            $this->updatePrivateInfo($fb_access_token, $dbh, $config);
        } else {
            $login = $login[0];
            $_SESSION['fb_access_token'] = (string) $this->fb_access_token;
            $_SESSION['fb_name'] = $login['name'];
            $_SESSION['fb_small_profile_pic'] = $login['small_picture_url'];
            $_SESSION['fb_profile_pic'] = $login['picture_url'];
            $_SESSION['fb_email'] = $login['email'];
            $_SESSION['fb_id'] = $login['facebook_id'];
            $_SESSION['user_id'] = $login['id'];

            if(isset($login['coord_id'])) {
                $_SESSION['coord_id'] = $login['coord_id'];
                $_SESSION['isCoordinator'] = true;
            } else {
                $_SESSION['coord_id'] = false;
                $_SESSION['isCoordinator'] = false;
            }
        }
    }

    /**
     * Update the info on the connected user.
     * @param AccessToken $fb_access_token set the access token to get access to user info
     * @param $dbh PDO Is a database connection
     * @param $config
     */
    public function updatePrivateInfo($fb_access_token, $dbh, $config) {
        $this->fb_access_token = $fb_access_token;

        try {
            $response = $this->fb_object->get('/me/?fields=picture,name,id,email', $this->fb_access_token);
            $user = $response->getGraphUser();

            $picture_url = $this->fb_object->get('/me/picture?redirect=0&type=normal', $this->fb_access_token)->getGraphNode()['url'];

        } catch (FacebookSDKException $e) {
            $this->isConnected = false;
            return;
        }

        $facebookId = $user['id'];

        $sql = "
            SELECT User.id,
                   CAST(AES_DECRYPT(name, :secret_key) AS CHAR(60)) AS name,
                   facebook_id,
                   Coordinator.id as coord_id
            FROM User
            LEFT JOIN Coordinator on User.id = Coordinator.user_id
            WHERE facebook_id = :facebook_id;
        ";

        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
        $sth->bindParam(':facebook_id', $facebookId, PDO::PARAM_INT);
        $sth->execute();
        $login = $sth->fetchAll(PDO::FETCH_ASSOC);

        $this->name = $user['name'];
        $this->fb_small_profile_pic = $user['picture']['url'];
        $this->fb_profile_pic = $picture_url;
        $this->fb_email = $user['email'];
        $this->fb_id = $user['id'];
        $this->isConnected = true;
        $this->isCoordinator = false;
        $this->coord_id = false;
        $dbh->beginTransaction();

        if(empty($login)) {
            $sql = "INSERT INTO rix_refugee.User(name, email, facebook_id) VALUES ( AES_ENCRYPT(:name, '".$config['db.secret_key']."'),
                                                                                    AES_ENCRYPT(:email, '".$config['db.secret_key']."'),
                                                                                    :facebook_id)";
            $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sth->execute([
                ':name' => $user['name'],
                ':email' => $user['email'],
                ':facebook_id' => $user['id']
            ]);
            $this->user_id = $dbh->lastInsertId();
        } else {
            $sql = "UPDATE rix_refugee.User SET name = AES_ENCRYPT(:name, '".$config['db.secret_key']."'),
                                                email = AES_ENCRYPT(:email, '".$config['db.secret_key']."')
                                            WHERE facebook_id = :facebook_id";
            $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sth->execute([
                ':name' => $user['name'],
                ':email' => $user['email'],
                ':facebook_id' => $user['id']
            ]);
            $this->user_id = $login[0]['id'];

            if(isset($login[0]['coord_id'])) {
                $this->isCoordinator = true;
                $this->coord_id = $login[0]['coord_id'];
            }
        }

        try {
            file_put_contents(__DIR__ . '/../p_images/user_picture/small/' . $this->user_id . '.jpg', file_get_contents($user['picture']['url']));
            file_put_contents(__DIR__ . '/../p_images/user_picture/normal/' . $this->user_id . '.jpg', file_get_contents($picture_url));
        } catch (Exception $e) {}

        $sql = "UPDATE rix_refugee.User SET small_picture_url = :small_picture, picture_url = :picture WHERE id = :user_id";
        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute([
            ':user_id' => $this->user_id,
            ':small_picture' => '/api/get_picture.php?src=user_picture/small/' . $this->user_id . '.jpg',
            ':picture' => '/api/get_picture.php?src=user_picture/normal/' . $this->user_id . '.jpg'
        ]);

        $dbh->commit();

        // Store the info in SESSION
        $_SESSION['fb_access_token'] = (string) $this->fb_access_token;
        $_SESSION['fb_name'] = $this->name;
        $_SESSION['fb_small_profile_pic'] = $this->fb_small_profile_pic;
        $_SESSION['fb_profile_pic'] = $this->fb_profile_pic;
        $_SESSION['fb_email'] = $this->fb_email;
        $_SESSION['fb_id'] = $this->fb_id;
        $_SESSION['user_id']=$this->user_id;
        $_SESSION['isCoordinator'] = $this->isCoordinator;
        $_SESSION['coord_id'] = $this->coord_id;
    }

    public function disconnect(){
        $_SESSION['fb_access_token'] = "";
        $_SESSION['fb_name'] = "";
        $_SESSION['fb_small_profile_pic'] = "";
        $_SESSION['fb_profile_pic'] = "";
        $_SESSION['fb_email'] = "";
        $_SESSION['fb_id'] = "";
        $_SESSION['user_id'] = "";
        $_SESSION['coord_id'] = "";
        $_SESSION['isCoordinator'] = "";
        session_unset();

        unset($this->fb_access_token);
        unset($this->name);
        unset($this->fb_small_profile_pic);
        unset($this->fb_profile_pic);
        unset($this->fb_email);
        unset($this->fb_id);
        unset($this->usedr_id);
        unset($this->coord_id);
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
     * @return string email of the auth person
     */
    public function getEmail()
    {
        return $this->fb_email;
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
     * @return string id of the login user
     * @return
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return string id of the login coordinator
     * @return bool return false if he isn't a coordinator
     */
    public function getCoordId()
    {
        return $this->coord_id;
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
        $permissions = ['email'];
        $loginUrl = $helper->getLoginUrl('https://rixrefugee.site/fb-callback', $permissions);
        header('Location: ' . $loginUrl);
    }

    /**
     * @param $email string Email of the user
     * @param $password string Password of the user
     * @param $dbh PDO Database access
     * @param $config
     * @return bool return true if successfully connected return false if password is incorrect
     */
    public function connectWithPassword($email, $password, $dbh, $config) {
        $email = trim($email);
        $sql = "

SELECT * FROM (
  SELECT User.id,
       facebook_id,
       CAST(AES_DECRYPT(name, :secret_key) AS CHAR(60)) AS name,
       small_picture_url,
       picture_url,
       CAST(AES_DECRYPT(email, :secret_key) AS CHAR(255)) AS email,
       password,
       Coordinator.id AS coord_id
FROM rix_refugee.User
LEFT JOIN Coordinator ON User.id = user_id
) AS UserInfo
WHERE email = :email
            ";

        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
        $sth->bindParam(':email', $email, PDO::PARAM_STR);
        $sth->execute();
        $user = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($user)) {
            $user = $user[0];
            if(password_verify($password, $user['password'])) {

                $_SESSION['fb_access_token'] = "";
                $_SESSION['fb_name'] = $user['name'];
                $_SESSION['fb_small_profile_pic'] = $user['small_picture_url'];
                $_SESSION['fb_profile_pic'] = $user['picture_url'];
                $_SESSION['fb_email'] = $user['email'];
                $_SESSION['fb_id'] = $user['facebook_id'];
                $_SESSION['user_id'] = $user['id'];

                if(isset($user['coord_id'])) {
                    $_SESSION['coord_id'] = $user['coord_id'];
                    $_SESSION['isCoordinator'] = true;
                } else {
                    $_SESSION['coord_id'] = false;
                    $_SESSION['isCoordinator'] = false;
                }
                if(isset($_SESSION['requested_page'])) {
                    header('Location: ' . $_SESSION['requested_page']);
                    unset($_SESSION['requested_page']);
                } else {
                    header('Location: /');
                }
                return true;
            }
        }
        return false;
    }
}