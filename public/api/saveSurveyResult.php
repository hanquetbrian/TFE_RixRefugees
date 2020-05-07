<?php
session_start();

require_once "../../php_function/db_connection.php";
require_once '../../php_function/Auth.php';
require_once '../../config.php';

$AUTH = new Auth($config['fb.app_id'], $config['fb.app_secret']);

if(empty($_POST) or empty($_GET['id_survey'])) {
    $sqlResult = false;
} else {
    foreach ($_POST as &$value) {
        $value = '"' . str_replace('"', '\'', $value) . '"';
    }
    unset($value);
    $options = '[' . implode (',', $_POST,) . ']';

// insert data in the database
    $dbh->beginTransaction();
    $sql = "INSERT INTO rix_refugee.Survey_result (survey_id, facebook_id, result) VALUES (:id_survey, :facebook_id, :result)";

    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sqlResult = $sth->execute([
        ':id_survey' => intval($_GET['id_survey']),
        ':facebook_id' => $AUTH->getFbId(),
        ':result' => $options
    ]);

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
    <?php if($sqlResult):?>
        <h1>Formualaire Envoyé</h1>
        <p>Merci de votre aide, votre demande à bien été envoyé.</p>
    <?php else:?>
        <h1>Erreur d'envoi</h1>
        <p>Désolé, il y a eu une erreur lors de l'envoi de votre demande. Essayez de renvoyer une demande.</p>
    <?php endif;?>
</div>
</body>
</html>
