<?php
require_once "../../php_function/db_connection.php";
require_once "../../php_function/utils.php";
session_start();

require_once '../../php_function/Auth.php';
require_once '../../config.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);

$lodgingId = htmlspecialchars($_POST['lodging_id']);
$dateFrom = htmlspecialchars($_POST['date_from']);
$dateTo = htmlspecialchars($_POST['date_to']);

// insert data in the database
$dbh->beginTransaction();
$sql = "INSERT INTO rix_refugee.Lodging_session (lodging_id, date_from, date_to, coordinator_id) VALUES (:lodging_id, :date_from, :date_to, :coordinator_id);";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->execute(array(
    ':lodging_id' => $lodgingId,
    ':date_from' => $dateFrom,
    ':date_to' => $dateTo,
    ':coordinator_id' => $AUTH->getCoordId(),
));

$sessionId = $dbh->lastInsertId();
$dbh->commit();

header("location: /info_lodging?lodging_session_id=" . $sessionId);