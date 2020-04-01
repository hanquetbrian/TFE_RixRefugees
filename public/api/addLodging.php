<?php
require_once "../../php_function/db_connection.php";
require_once "../../php_function/utils.php";

$result = [];

//TODO add more check
$dateFrom = htmlspecialchars($_POST['date_from']);
$dateTo = htmlspecialchars($_POST['date_to']);
$name = htmlspecialchars($_POST['name']);
$nbPlace = htmlspecialchars($_POST['nb_place']);
$address = htmlspecialchars($_POST['address']);

if(!checkStrDate($dateFrom) or !checkStrDate($dateTo)) {
    //TODO Check that the date_to is after the date_from

    $result["error"]["type"] = "date_invalid";
    $result["error"]["msg"] = "The Date is not valid";

    echo json_encode($result);
    die();
}

// insert data in the database
$dbh->beginTransaction();
$sql = "INSERT INTO rix_refugee.Lodging (lodging_name, date_from, date_to, address, nb_place) VALUES (:lodging_name, :date_from, :date_to, :address, :nb_place);";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->execute(array(
    ':lodging_name' => $name,
    ':date_from' => $dateFrom,
    ':date_to' => $dateTo,
    ':address' => $address,
    ':nb_place' => $nbPlace));

$lodgingId = $dbh->lastInsertId();
$sth->closeCursor();
$sql = "INSERT INTO Lodging_equipment (lodging_id, equipment_name) VALUES ($lodgingId, :equipment_name)";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

foreach ($_POST['equipments'] as $equipment) {
    $sth->execute(array(':equipment_name' => htmlspecialchars($equipment)));
}
$sth->closeCursor();

$dbh->commit();

$result["success"] = true;
echo json_encode($result);