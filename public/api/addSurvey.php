<?php
require_once "../../php_function/db_connection.php";
require_once "../../php_function/utils.php";

$result = [];

$sessionId = htmlspecialchars($_POST['sessionId']);
$description = htmlspecialchars($_POST['description']);

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

$modify = isset($survey['survey_id']);

// insert data in the database
$dbh->beginTransaction();
$sql="";
$data=[];
if($modify) {
    $sql = "UPDATE rix_refugee.Survey SET description = :desc WHERE id = :id_survey; ";
    $data = [
        ':id_survey' => $survey['survey_id'],
        ':desc' => $description
    ];

    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sqlResult = $sth->execute($data);
} else {
    $sql = "INSERT INTO rix_refugee.Survey (description) VALUES (:desc);";
    $data = [
        ':desc' => $description
    ];

    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sqlResult = $sth->execute($data);

    $survey['survey_id'] = $dbh->lastInsertId();
    // assign the survey to the session
    $sql = "UPDATE rix_refugee.Lodging_session SET survey_id = ? WHERE id = ?;";

    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute([$survey['survey_id'], $sessionId]);

}

// Add the option of the survey
if(isset($_POST['options'])) {
    // Get the current options
    $sql = "
        SELECT id, option_name
        FROM rix_refugee.Survey_options
        WHERE survey_id = ?;
    ";

    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute([$survey['survey_id']]);
    $current_options = $sth->fetchAll(PDO::FETCH_ASSOC);

    foreach ($_POST['options'] as $option) {
        $sql="";
        $data=[];
        if($option['id'] == -1) {
            $sql = "INSERT INTO rix_refugee.Survey_options (survey_id, option_name) VALUES (:survey_id, :option_name);";
            $data = [
                ':survey_id' => $survey['survey_id'],
                ':option_name' => $option['name']
            ];
        } else {
            foreach ($current_options as $key => $current_option) {
                if($current_option['id'] == $option['id']) {
                    unset($current_options[$key]);
                    break;
                }
            }

            $sql = "UPDATE rix_refugee.Survey_options SET option_name = :option_name WHERE id = :id_option; ";
            $data = [
                ':option_name' => $option['name'],
                ':id_option' => $option['id']
            ];
        }
        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sqlResult = $sth->execute($data);
    }


    foreach ($current_options as $current_option) {

        $sql = "
        DELETE FROM rix_refugee.Survey_options
        WHERE id = ?;
        ";

        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute([$current_option['id']]);
        $current_options = $sth->fetchAll(PDO::FETCH_ASSOC);
    }

}

if($sqlResult) {
    $result['success'] = true;
} else {
    $result["error"]["type"] = "invalid request";
    $result["error"]["msg"] = "Could not create the survey";
}

$dbh->commit();

echo json_encode($result);