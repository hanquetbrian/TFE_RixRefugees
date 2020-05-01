<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande d'accès</title>

    <style>
        .contain {
            width: 60%;
            margin: 10em auto;
        }
    </style>
</head>
<body>
<div class="contain">
    <h1>Demande de coordinateur</h1>

<?php
require_once 'db_connection.php';

$sql = "
            SELECT name, facebook_id
            FROM rix_refugee.validating_coordinator
            WHERE facebook_id = :facebook_id
        ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([':facebook_id' => $AUTH->getFbId()]);
$login = $sth->fetchAll(PDO::FETCH_ASSOC);

if (!empty($login)) {
    echo '<p>Une demande à déja été enregistré à votre nom. Vous pouvez demander à un coordinateur d\'accepter votre demande.</p>';
} else {
    $sql = "
            INSERT INTO rix_refugee.Coordinator (name, small_picture_url, picture_url, email, facebook_id)
            VALUES (:name, :small_picture, :picture, :email, :facebook_id)
        ";
    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute([
        ':name' => $AUTH->getName(),
        ':small_picture' => $AUTH->getFbSmallProfilePic(),
        ':picture' => $AUTH->getFbProfilePic(),
        ':email' => $AUTH->getEmail(),
        ':facebook_id' => $AUTH->getFbId()
    ]);

    echo '<p>Votre demande a bien été envoyé</p>';
    echo '<p>Veillez maintenant à attendre qu\'un coordinateur vous autorise l\'accès.</p>';
}
?>
</div>
</body>
</html>