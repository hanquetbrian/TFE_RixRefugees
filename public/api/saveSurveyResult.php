<?php
session_start();

require_once "../../php_function/db_connection.php";
require_once '../../php_function/Auth.php';
require_once '../../config.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);
$error = false;

if(empty($_POST) or empty($_GET['id_survey'])) {
    $sqlResult = false;
} else {
    $comment = "";
    $options = [];

    foreach ($_POST as $key => $value) {
        $value = htmlspecialchars($value);
        if(substr( $key, 0, 6) === "option") {
            array_push($options, $value);
        } elseif ($key === 'comment') {
            $comment = $value;
        }
    }

    $dbh->beginTransaction();
    $sql = "SELECT * FROM Volunteer_request WHERE facebook_id = :facebook_id AND survey_id = :survey_id";

    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute([
        ':survey_id' => intval($_GET['id_survey']),
        ':facebook_id' => $AUTH->getFbId()
    ]);
    $current_request = $sth->fetchAll(PDO::FETCH_ASSOC);

    if(empty($current_request)) {
        $sql = "INSERT INTO rix_refugee.Volunteer_request (facebook_id, survey_id, comment) VALUES (:facebook_id, :id_survey, :comment)";

        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sqlResult = $sth->execute([
            ':facebook_id' => $AUTH->getFbId(),
            ':id_survey' => intval($_GET['id_survey']),
            ':comment' => $_POST['comment']
        ]);

        if(!$sqlResult) {$error = true;}
    } else {
        $sql = "UPDATE rix_refugee.Volunteer_request SET comment = :comment WHERE id = :id";

        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sqlResult = $sth->execute([
            ':id' => $current_request[0]['id'],
            ':comment' => $_POST['comment']
        ]);

        if(!$sqlResult) {$error = true;}
    }

    // Get current option
    $sql = "
        SELECT survey_option_id, volunteer_request_id FROM Volunteer_request
        LEFT JOIN Result_list ON Result_list.volunteer_request_id = Volunteer_request.id
        WHERE facebook_id = :facebook_id AND survey_id = :survey_id";

    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute([
        ':survey_id' => intval($_GET['id_survey']),
        ':facebook_id' => $AUTH->getFbId()
    ]);
    $listOfCurrentOptions = $sth->fetchAll(PDO::FETCH_ASSOC);
    $volunteer_request_id = $listOfCurrentOptions[0]['volunteer_request_id'];

    // insert data in the database
    foreach ($options as $option) {
        $allReadyExist = false;
        foreach ($listOfCurrentOptions as $key => $current_option) {
            if($current_option['survey_option_id'] == $option) {
                $allReadyExist = true;
                unset($listOfCurrentOptions[$key]);
                break;
            }
        }

        if(!$allReadyExist) {
          $sql = "INSERT INTO rix_refugee.Result_list(survey_option_id, volunteer_request_id) VALUES (:survey_option_id, :volunteer_request_id)";

            $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sqlResult = $sth->execute([
                ':survey_option_id' => $option,
                ':volunteer_request_id' => $volunteer_request_id
            ]);
            if(!$sqlResult) {$error = true;}
        }
    }

    foreach ($listOfCurrentOptions as $listOfCurrentOption) {

        $sql = "
        DELETE FROM rix_refugee.Result_list
        WHERE survey_option_id = ? AND volunteer_request_id = ?;
        ";

        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute([
            $listOfCurrentOption['survey_option_id'],
            $listOfCurrentOption['volunteer_request_id']
        ]);
        $current_options = $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    $dbh->commit();
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire envoyé</title>

    <style>
        .contain {
            width: 60%;
            margin: 10em auto;
        }
    </style>
</head>
<body>
<div class="contain">
    <?php if(!$error):?>
        <h1>Formualaire Envoyé</h1>
        <p>Merci de votre aide, votre demande à bien été envoyé.</p>
    <?php else:?>
        <h1>Erreur d'envoi</h1>
        <p>Désolé, il y a eu une erreur lors de l'envoi de votre demande. Essayez de renvoyer une demande.</p>
    <?php endif;?>
</div>
</body>
</html>
