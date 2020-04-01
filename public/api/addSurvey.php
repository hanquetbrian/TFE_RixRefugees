<?php
require_once "../../php_function/db_connection.php";
require_once "../../php_function/utils.php";

$result = [];

$title = htmlspecialchars($_POST['title']);
$description = htmlspecialchars($_POST['description']);
$options = [];
// TODO Send an error when there is no options
if(isset($_POST['options'])) {
    foreach ($_POST['options'] as $option) {
        array_push($options, htmlspecialchars($option));
    }
}


// insert data in the database
$dbh->beginTransaction();
$sql = "INSERT INTO rix_refugee.Survey (lodging_id, survey_name, description, content) VALUES (:lodging_id, :name, :desc, :content);";
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sqlResult = $sth->execute(array(
    ':lodging_id' => 1,
    ':name' => $title,
    ':desc' => $description,
    ':content' => json_encode($options)));

if($sqlResult) {
    $result['success'] = true;
} else {
    $result["error"]["type"] = "invalid request";
    $result["error"]["msg"] = "Could not create the survey";
}
$dbh->commit();

echo json_encode($result);