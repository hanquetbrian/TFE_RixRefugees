<?php
require_once "../../php_function/db_connection.php";
require_once "../../php_function/utils.php";

$result = [];
//TODO add more check
if(!checkStrDate($_POST['date_to']) or !checkStrDate($_POST['date_from'])) {
    //TODO Check that the date_to is after the date_from

    $result["error"]["type"] = "date_invalid";
    $result["error"]["msg"] = "The Date is not valid";

    echo json_encode($result);
    die();
}
$sql = "INSERT INTO rix_refugee.Lodging (lodging_name, date_from, date_to, address, nb_place) VALUES (:lodging_name, :date_from, :date_to, :address, :nb_place);";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->execute(array(
    ':lodging_name' => $_POST['name'],
    ':date_from' => $_POST['date_from'],
    ':date_to' => $_POST['date_to'],
    ':address' => $_POST['address'],
    ':nb_place' => $_POST['nb_place']));

$result["success"] = true;
echo json_encode($result);