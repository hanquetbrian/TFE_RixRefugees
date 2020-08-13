<?php
if(!isset($_GET['coord_id'])) {
    header('Location: coordinator');
    exit(0);
}

require_once "../php_function/db_connection.php";
$sql = "
    SELECT name, small_picture_url, picture_url, facebook_id, email, visible_email, telephone, visible_telephone, added_day
    FROM rix_refugee.Coordinator
    INNER JOIN User on Coordinator.user_id = User.id
    where Coordinator.id = ?;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$_GET['coord_id']]);
$coordinator = $sth->fetchAll(PDO::FETCH_ASSOC)[0];

?>

<main>
    <section>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1><?=$coordinator['name']?></h1>
            </div>
            <hr class="headerSep">
        </div>

        <div class="container">
            <div class="listLodging">
                <?php
                if($AUTH->getCoordId() == $_GET['coord_id']) {
                    echo '<div style="text-align: right"><a class="btn btn-primary" href="/edit_user">Modifier</a></div>';
                }
                ?>
                <div>
                    <img src="<?=$coordinator['picture_url']?>" alt="picture_of_<?=$coordinator['name']?>" width="100">
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
