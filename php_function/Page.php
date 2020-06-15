<?php


class Page
{


    private $file;
    private $title;
    private $auth;
    private $scripts = [];
    private $css = [];
    private $access;
    private $requiredParam = [];
    private $dbh;

    public const coordinator = 1;
    public const volunteer = 2;

    public const PARAM_NOTYPE = 0;
    public const PARAM_TEXT = 1;
    public const PARAM_NUMBER = 2;
    public const PARAM_DATE = 3;
    public const PARAM_LIST = 4;
    public const PARAM_VALID_SESSION_ID = 5;
    public const PARAM_VALID_COORD_ID = 6;
    public const PARAM_VALID_VOLUNTER_USER_ID = 7;

    public const OPTION_SHOW_HEADER = 1;

    /**
     * Create a page
     * @param $file string file of the page
     * @param $title string title of the page
     * @param null $auth Auth if we want to be accessed by only certain person we have to put an Auth Object
     * @param int $access int is the people that can access the page
     */
    public function __construct($file, $title, $auth = null, $access = 0) {
        $this->file = __DIR__ . '/../' . $file;
        $this->title = $title;
        $this->auth = $auth;
        $this->access = $access;
    }

    /**
     * @param $name string is the name of the parameter of the requested page
     * @param $type int is the type of the parameter, it's used to make some security check. Please use one constant PARAM_
     * @param null $dbh PDO should only be set for the type that required a database connection
     * @return false if you set an incorrect type
     */
    public function addParam($name, $type=0, $dbh=null) {
        if ($type < 0 || $type > 7) {
            return false;
        }

        if($type > 4) {
            if(!isset($dbh)) {
                return false;
            }
            $this->dbh = $dbh;
        }

        array_push($this->requiredParam, [$name, $type]);
        return true;
    }

    /**
     * @param $url
     * @param null $integrity
     * @param string $crossorigin
     */
    public function addScript($url, $integrity = null, $crossorigin="anonymous") {
        $html = '<script src="' . $url . '"';
        if(!empty($integrity)) {
            $html .= ' integrity="' . $integrity . '"';
            $html .= ' crossorigin="' . $crossorigin . '"';
        }
        $html .= '></script>';

        array_push($this->scripts, $html);
    }

    /**
     * @param $url
     * @param null $integrity
     * @param string $crossorigin
     */
    public function addCSS($url, $integrity = null, $crossorigin="anonymous") {
        $html = '<link rel="stylesheet" href="' . $url . '"';
        if(!empty($integrity)) {
            $html .= ' integrity="' . $integrity . '"';
            $html .= ' crossorigin="' . $crossorigin . '"';
        }
        $html .= '>';

        array_push($this->css, $html);
    }

    public function getFile() {
        if(!file_exists($this->file)) {
            return false;
        }

        // check if the user has access to the page
        $hasAccess = false;
        if(isset($this->auth)) {
            if(!$this->auth->isConnected()) {
                $this->auth->connectToFacebook();
                exit();
            }

            // authorize volunteer
            $access = $this->access;
            $access -= Page::volunteer;
            if($access >= 0) {
                $this->access = $access;
                $hasAccess = true;
            }

            // authorize coordinatoor
            $access = $this->access;
            $access -= Page::coordinator;
            if($access >= 0) {
                $this->access = $access;
                $hasAccess = $this->auth->isCoordinator();
            }

            if (!$hasAccess) {
                return false;
            }
        }

        if(!empty($this->requiredParam)) {
            $error = false;
            foreach ($this->requiredParam as $param) {
                if(!isset($_REQUEST[$param[0]])) {
                    $error = true;
                    return false;
                }
                $_REQUEST[$param[0]] = htmlspecialchars($_REQUEST[$param[0]]);
                switch ($param[1]) {

                    case Page::PARAM_TEXT:
                    case Page::PARAM_DATE:
                    case Page::PARAM_LIST:
                        $error = !is_string($param[0]);
                        break;
                    case Page::PARAM_NUMBER:
                        $error = !is_numeric($param[0]);
                        break;
                    case Page::PARAM_VALID_SESSION_ID:
                        $sql = "SELECT id FROM Lodging_session";

                        $sth = $this->dbh->query($sql);
                        $valid_session_id = $sth->fetchAll(PDO::FETCH_ASSOC);

                        $error = true;
                        foreach ($valid_session_id as $id) {
                            $id = $id['id'];
                            $paramValue = $_REQUEST[$param[0]];

                            if($id == $paramValue) {
                                $error = false;
                                break;
                            }
                        }
                        break;
                    case Page::PARAM_VALID_COORD_ID:
                        $sql = "SELECT id FROM Coordinator";

                        $sth = $this->dbh->query($sql);
                        $valid_coord_id = $sth->fetchAll(PDO::FETCH_ASSOC);

                        $error = true;
                        foreach ($valid_coord_id as $id) {
                            $id = $id['id'];
                            $paramValue = $_REQUEST[$param[0]];

                            if($id == $paramValue) {
                                $error = false;
                                break;
                            }
                        }
                        break;
                    case Page::PARAM_VALID_VOLUNTER_USER_ID:
                        $sql = "
                        SELECT user_id
                        FROM rix_refugee.Volunteer_request
                        WHERE user_id = ?;
                        ";

                        $sth = $this->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        $sth->execute([$_REQUEST[$param[0]]]);
                        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

                        if(empty($result)) {
                            return false;
                        }
                        break;
                }

                if($error) return false;
            }
        }

        return $this->file;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getCSS() {
        return $this->css;
    }

    public function getScript() {
        return $this->scripts;
    }

}