<?php

require_once "../../php_function/db_connection.php";

$result = [];

$coordinator_id = htmlspecialchars($_POST['id_coord']);

// insert data in the database
$dbh->beginTransaction();
$sql = "UPDATE rix_refugee.Coordinator SET valid = true WHERE id = ?";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sqlResult = $sth->execute([$coordinator_id]);

$dbh->commit();

if($sqlResult) {
    $result['success'] = true;
} else {
    $result["error"]["type"] = "invalid request";
    $result["error"]["msg"] = "Could not validate the coordinator";
}

echo json_encode($result);