<?php
require_once "../../php_function/db_connection.php";
require_once "../../php_function/utils.php";

$result = [];

$survey_id = htmlspecialchars($_POST['id_survey']);
$lodging_id = htmlspecialchars($_POST['lodging_id']);
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
$sql="";
$data=[];
if($_POST['id_survey'] > 0) {
    $sql = "UPDATE rix_refugee.Survey SET survey_name = :name, description = :desc, content = :content WHERE id = :id_survey; ";
    $data = [
        ':id_survey' => $survey_id,
        ':name' => $title,
        ':desc' => $description,
        ':content' => json_encode($options)
    ];
} else {
    $sql = "INSERT INTO rix_refugee.Survey (lodging_id, survey_name, description, content) VALUES (:lodging_id, :name, :desc, :content);";
    $data = [
        ':lodging_id' => $lodging_id,
        ':name' => $title,
        ':desc' => $description,
        ':content' => json_encode($options)
    ];
}

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sqlResult = $sth->execute($data);
if(empty($survey_id) || $survey_id <= 0) {
    $survey_id = $dbh->lastInsertId();
}

if($sqlResult) {
    $result['success'] = true;
    $result['lastInsertId'] = $survey_id;
} else {
    $result["error"]["type"] = "invalid request";
    $result["error"]["msg"] = "Could not create the survey";
}

$dbh->commit();

echo json_encode($result);