<?php
require_once "../../php_function/db_connection.php";



$sql = "INSERT INTO rix_refugee.Lodging (lodging_name, date_from, date_to, address, nb_place) VALUES (:lodging_name, :date_from, :date_to, :address, :nb_place);";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->execute(array(
    ':lodging_name' => $_POST['name'],
    ':date_from' => $_POST['date_from'],
    ':date_to' => $_POST['date_to'],
    ':address' => $_POST['address'],
    ':nb_place' => $_POST['nb_place']));
