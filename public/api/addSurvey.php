<?php
require_once "../../php_function/db_connection.php";
require_once "../../php_function/utils.php";

$result = [];

$sessionId = htmlspecialchars($_POST['sessionId']);
$description = htmlspecialchars($_POST['description']);
$options = [];
// TODO Send an error when there is no options
if(isset($_POST['options'])) {
    foreach ($_POST['options'] as $option) {
        array_push($options, htmlspecialchars($option));
    }
}

$sql = "
SELECT id, survey_id 
FROM rix_refugee.Lodging_session
WHERE id = ?;
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$sessionId]);
$survey = $sth->fetchAll(PDO::FETCH_ASSOC);
if(empty($survey)) {
    header('Location: /');
    exit(0);
}

$survey = $survey[0];
$modify = isset($survey['survey_id']) ? true : false;

// insert data in the database
$dbh->beginTransaction();
$sql="";
$data=[];
if($modify) {
    $sql = "UPDATE rix_refugee.Survey SET description = :desc, content = :content WHERE id = :id_survey; ";
    $data = [
        ':id_survey' => $survey['survey_id'],
        ':desc' => $description,
        ':content' => json_encode($options)
    ];
} else {
    $sql = "INSERT INTO rix_refugee.Survey (description, content) VALUES (:desc, :content);";
    $data = [
        ':desc' => $description,
        ':content' => json_encode($options)
    ];
}

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sqlResult = $sth->execute($data);

//
if(!$modify) {
    $sql = "UPDATE rix_refugee.Lodging_session SET survey_id = ?;";

    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute([$dbh->lastInsertId()]);
}

if($sqlResult) {
    $result['success'] = true;
} else {
    $result["error"]["type"] = "invalid request";
    $result["error"]["msg"] = "Could not create the survey";
}

$dbh->commit();

echo json_encode($result);