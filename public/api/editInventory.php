<?php

require_once "../../php_function/db_connection.php";

$result = [];

$part_name = $_POST['part_name'];
$location = $_POST['location'];
$quantity = intval($_POST['quantity']);
$id = intval($_POST['id']);

$dbh->beginTransaction();
$sql = "UPDATE rix_refugee.Inventory SET part_name = :part_name, location = :location, quantity = :quantity WHERE id = :id";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sqlResult = $sth->execute([
    ':id' => $id,
    ':part_name' => $part_name,
    ':location' => $location,
    ':quantity' => $quantity
]);

$dbh->commit();

if($sqlResult) {
    $result['success'] = true;
    $result['id'] = htmlspecialchars($id);
    $result['part_name'] = htmlspecialchars($part_name);
    $result['location'] = htmlspecialchars($location);
    $result['quantity'] = htmlspecialchars($quantity);
} else {
    $result["error"]["type"] = "invalid request";
    $result["error"]["msg"] = "Could not edit the item from the inventory";
}

echo json_encode($result);