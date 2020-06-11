<?php

require_once "../../php_function/db_connection.php";

$result = [];

$coordinator_id = htmlspecialchars($_POST['id_coord']);

// insert data in the database
$dbh->beginTransaction();
$sql = "SELECT id, facebook_id, request, name, small_picture_url, picture_url, email, telephone, request_date FROM rix_refugee.Coordinator_request WHERE id = ?";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$validating_coord = $sth->execute([$coordinator_id])[0];

$sql = "INSERT INTO rix_refugee.Coordinator (name, small_picture_url, picture_url, email, telephone, facebook_id) VALUES (:name, :small_picture_url, :picture_url, :email, :telephone, :facebook_id)";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sqlResult = $sth->execute([
    ':name' => $validating_coord['name'],
    ':small_picture_url' => $validating_coord['small_picture_url'],
    ':picture_url' => $validating_coord['picture_url'],
    ':email' => $validating_coord['email'],
    ':telephone' => $validating_coord['telephone'],
    ':facebook_id' => $validating_coord['facebook_id']
]);

$dbh->commit();

if($sqlResult) {
    $result['success'] = true;
} else {
    $result["error"]["type"] = "invalid request";
    $result["error"]["msg"] = "Could not validate the coordinator";
}

echo json_encode($result);