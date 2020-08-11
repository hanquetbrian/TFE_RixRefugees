<?php
require_once "../../php_function/db_connection.php";
session_start();

require_once '../../php_function/Auth.php';
require_once '../../config.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);
if(!$AUTH->isCoordinator()) {
    header('Location: edit_user');
    die(0);
}

$id = $AUTH->getUserId();

// Aonymize the user
$dbh->beginTransaction();

$sql = "DELETE FROM rix_refugee.Coordinator WHERE user_id = :id;";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->bindParam(':id', $id, PDO::PARAM_INT);
$sth->execute();

$sql = "UPDATE rix_refugee.Volunteer_request SET user_id = 0 WHERE user_id = :id;";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->bindParam(':id', $id, PDO::PARAM_INT);
$sth->execute();

$sql = "DELETE FROM rix_refugee.User WHERE id = :id;";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->bindParam(':id', $id, PDO::PARAM_INT);
$sth->execute();

$dbh->commit();

if($sth) {
    $_SESSION['msg'] = "Votre compte a bien été supprimé";
}

$AUTH->disconnect();
header("Location: /");