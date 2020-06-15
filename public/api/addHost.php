<?php
require_once "../../php_function/db_connection.php";
require_once "../../php_function/utils.php";
session_start();

require_once '../../php_function/Auth.php';
require_once '../../config.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);
if(!$AUTH->isCoordinator()) {
    die(0);
}

$result = [];

$name = htmlspecialchars($_POST['name']);
$comment = htmlspecialchars($_POST['comment']);
$id_session = htmlspecialchars($_POST['id_session']);

if(!is_numeric($id_session)) {die();}

// insert data in the database
$dbh->beginTransaction();
$sql = "INSERT INTO rix_refugee.Hosts (name, comment, lodging_session_id) VALUES (:host_name, :comment, :id_session);";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->execute(array(
    ':host_name' => $name,
    ':comment' => $comment,
    ':id_session' => $id_session));

$sth->closeCursor();

$dbh->commit();

$result["success"] = true;
$result['name'] = $name;
$result['comment'] = $comment;
echo json_encode($result);