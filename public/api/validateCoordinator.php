<?php

require_once "../../php_function/db_connection.php";

$result = [];

$coordinator_id = htmlspecialchars($_POST['id_coord']);


// insert data in the database
$dbh->beginTransaction();
$sql = "SELECT Coordinator_request.id, user_id, facebook_id, request, name, small_picture_url, picture_url, email, telephone, request_date
        FROM rix_refugee.Coordinator_request
        INNER JOIN User on Coordinator_request.user_id = User.id
        WHERE Coordinator_request.id = ?";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$coordinator_id]);
$validating_coord = $sth->fetchAll(PDO::FETCH_ASSOC)[0];

$sql = "INSERT INTO rix_refugee.Coordinator (user_id) VALUES (:user_id)";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sqlResult = $sth->execute([
    ':user_id' => $validating_coord['user_id']
]);

$sql = "DELETE FROM rix_refugee.Coordinator_request WHERE id = ?";

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