<?php
require_once "../php_function/db_connection.php";
$sql = "
    SELECT name, small_picture_url, picture_url, facebook_id, email, telephone, added_day
    FROM rix_refugee.Coordinator
    INNER JOIN User on Coordinator.user_id = User.id
    where Coordinator.id = ?;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$AUTH->getCoordId()]);
$user = $sth->fetchAll(PDO::FETCH_ASSOC)[0];

?>

<main>
    <section>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1>Modification du profil</h1>
            </div>
            <hr class="headerSep">
        </div>

        <div class="container">
            <div class="listLodging">
                <!--                TODO allow the modifications of the coordinator info-->
                <img src="<?=$user['picture_url']?>" alt="picture_of_<?=$user['name']?>" width="100">
                <span style="font-weight: bold; font-size: 1.5em; margin-left: 2em"><?=$user['name']?></span>
                <div class="lodging-item">
                    <p>Nom: <?=$user['name']?></p>
                    <p>Email: <?=$user['email']?></p>
                    <p>Téléphone: <?=$user['telephone']?></p>
                </div>
            </div>
        </div>
    </section>
</main>
