<?php
if(!isset($_GET['coord_id'])) {
    header('Location: coordinator');
    exit(0);
}


require_once "../php_function/db_connection.php";
$sql = "
    SELECT id, name, small_picture_url, picture_url, facebook_id, email, telephone
    FROM rix_refugee.valid_coordinator
    where id = ?;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$_GET['coord_id']]);
$coordinator = $sth->fetchAll(PDO::FETCH_ASSOC)[0];

$title = "RixRefugee " . $coordinator['name'];

require_once 'header.php';
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
<!--                TODO allow the modifications of the coordinator info-->
                <img src="<?=$coordinator['picture_url']?>" alt="picture_of_<?=$coordinator['name']?>" width="100">
                <div class="lodging-item">
                    <p>Nom: <?=$coordinator['name']?></p>
                    <p>Email: <?=$coordinator['email']?></p>
                    <p>Téléphone: <?=$coordinator['telephone']?></p>
                </div>
            </div>
        </div>
    </section>
</main>
