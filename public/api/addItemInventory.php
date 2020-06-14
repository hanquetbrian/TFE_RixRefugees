<?php

require_once "../../php_function/db_connection.php";

$result = [];

$part_name = $_POST['part_name'];
$location = $_POST['location'];
$quantity = intval($_POST['quantity']);

$dbh->beginTransaction();
$sql = "INSERT INTO rix_refugee.Inventory (part_name, location, quantity) VALUES (:part_name, :location, :quantity)";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sqlResult = $sth->execute([
    ':part_name' => $part_name,
    ':location' => $location,
    ':quantity' => $quantity
]);

$dbh->commit();

if($sqlResult) {
    $result['success'] = true;
    $result['id'] = htmlspecialchars($dbh->lastInsertId());
    $result['part_name'] = htmlspecialchars($part_name);
    $result['location'] = htmlspecialchars($location);
    $result['quantity'] = htmlspecialchars($quantity);
} else {
    $result["error"]["type"] = "invalid request";
    $result["error"]["msg"] = "Could not edit the item from the inventory";
}

echo json_encode($result);