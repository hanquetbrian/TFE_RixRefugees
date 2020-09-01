<?php
if(!isset($_GET['coord_id'])) {
    header('Location: coordinator');
    exit(0);
}

require_once "../php_function/db_connection.php";
$sql = "
    SELECT CAST(AES_DECRYPT(name, :secret_key) AS CHAR(60)) AS name,
           small_picture_url,
           picture_url,
           facebook_id,
           CAST(AES_DECRYPT(email, :secret_key) AS CHAR(255)) AS email,
           visible_email,
           CAST(AES_DECRYPT(telephone, :secret_key) AS CHAR(20)) AS telephone,
           visible_telephone,
           added_day
    FROM rix_refugee.Coordinator
    INNER JOIN User on Coordinator.user_id = User.id
    where Coordinator.id = :coord_id;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
$sth->bindParam(':coord_id', $_GET['coord_id'], PDO::PARAM_INT);
$sth->execute();
$coordinator = $sth->fetchAll(PDO::FETCH_ASSOC)[0];

?>

<main>
    <section>
        <div class="container" style="margin-top: 2em">
            <div class="listLodging">
                <?php
                if($AUTH->getCoordId() == $_GET['coord_id']) {
                    echo '<div style="text-align: right"><a class="btn btn-primary" href="/edit_user">Modifier</a></div>';
                }
                ?>

                <div class="row">
                    <div class="col-auto">
                        <img src="<?=$coordinator['picture_url']?>" alt="picture_of_<?=$coordinator['name']?>" width="100">
                    </div>
                    <div class="col mt-3" style="padding: 0">
                        <p style="width: 80%; font-weight: bold; font-size: 1.3em"><?=$coordinator['name']?></p>
                        <p>Coordinateur</p>
                    </div>
                </div>

                <div class="lodging-item">
                    <p>Nom: <?=$coordinator['name']?></p>
                    <?php
                    if($coordinator['visible_email'] == 1) {
                        echo '<p>Email: ' . $coordinator['email'] . '</p>';
                    }

                    if($coordinator['visible_telephone'] == 1) {
                        echo '<p>Téléphone: ' . $coordinator['telephone'] . '</p>';
                    }
                    ?>


                </div>
            </div>
        </div>
    </section>
</main>
