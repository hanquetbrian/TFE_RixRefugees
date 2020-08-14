<?php
require_once "../../php_function/db_connection.php";
session_start();

require_once '../../php_function/Auth.php';
require_once '../../config.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);
if(!$AUTH->isConnected()) {
    die(0);
}

$id = $AUTH->getUserId();
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$telephone = htmlspecialchars($_POST['telephone']);
$show_email = isset($_POST['show_email']);
$show_telephone = isset($_POST['show_telephone']);

// Update data in the database

$sql = "UPDATE rix_refugee.User SET name = :name, email = :email, telephone = :telephone, visible_email = :show_email, visible_telephone = :show_telephone WHERE id = :id;";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->bindParam(':id', $id);
$sth->bindParam(':name', $name);
$sth->bindParam(':email', $email);
$sth->bindParam(':telephone', $telephone);
$sth->bindParam(':show_email', $show_email, PDO::PARAM_INT);
$sth->bindParam(':show_telephone', $show_telephone, PDO::PARAM_INT);
$sth->closeCursor();

$sth->execute();

if($sth) {
    $_SESSION['fb_name'] = $name;
    $_SESSION['fb_email'] = $email;
}

header("Location: /edit_user");