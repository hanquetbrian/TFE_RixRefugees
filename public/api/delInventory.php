<?php

require_once "../../php_function/db_connection.php";

$result = [];

$item_id = htmlspecialchars($_POST['item_id']);

$dbh->beginTransaction();
$sql = "DELETE FROM rix_refugee.Inventory WHERE id = ?";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sqlResult = $sth->execute([$item_id]);

$dbh->commit();

if($sqlResult) {
    $result['success'] = true;
    $result['id'] = htmlspecialchars($item_id);
} else {
    $result["error"]["type"] = "invalid request";
    $result["error"]["msg"] = "Could not remove the item from the inventory";
}

echo json_encode($result);