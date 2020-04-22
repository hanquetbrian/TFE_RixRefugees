<?php
if (!isset($_GET['lodging_id'])) {
    header('Location: /');
    exit;
}

$idLodging = $_GET['lodging_id'];
//TODO add security for lodging_id

require_once "../php_function/db_connection.php";
require_once "../php_function/utils.php";

// Get lodging info
$sql = "
    SELECT Lodging.id, lodging_name, date_from, date_to, address, nb_place, Coordinator.name, Lodging_equipment.equipment_name
    FROM rix_refugee.Lodging
    LEFT JOIN Coordinator on Lodging.coordinator_id = Coordinator.id
    LEFT JOIN Lodging_equipment ON Lodging.id = Lodging_equipment.lodging_id
    WHERE Lodging.id = ?;
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$idLodging]);
$lodgings = $sth->fetchAll(PDO::FETCH_ASSOC);

$imgSrc = 'img/house.jpg';

// Get surveys info
$sql = "
    SELECT id, survey_name, description, content
    FROM rix_refugee.Survey
    WHERE lodging_id = ?;
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$idLodging]);
$surveys = $sth->fetchAll(PDO::FETCH_ASSOC);

?>
<?php
include_once "../include/header.php";

?>

    <main>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1><?=$lodgings[0]['lodging_name']?></h1>
            </div>

            <hr class="headerSep">
        </div>

        <section>
            <div class="container">
                <h3>Informations</h3>
                <div class="row">
                    <div class="col-sm">
                        <img class="img-fluid thumbnail" src="<?=$imgSrc?>" alt="Construction house">
                    </div>

                    <div class="col-sm">
                        <ul class="info_lodging">
                            <li>Date: <?= formatStrDate($lodgings[0]['date_from'])?> au <?=formatStrDate($lodgings[0]['date_to'])?></li>
                            <li>Coordinateur: <?= $lodgings[0]['name'] ?></li>
                            <li>Nombre de places disponibles: <?= $lodgings[0]['nb_place'] ?></li>
                            <li class="address"><?= $lodgings[0]['address'] ?></li>
                        </ul>
                    </div>

                    <div class="col-sm d-flex flex-column align-items-end">
                        <button class="btn btn-primary">
                            Renouveler l'hébergement
                        </button>
                    </div>
                </div>
                <?php if(isset($lodgings[0]['equipment_name'])):?>

                <h3>Equipements</h3>
                <ul class="info_lodging">
                    <?php foreach ($lodgings as $lodging):?>
                    <li><?=$lodging['equipment_name']?></li>
                    <?php endforeach;?>
                </ul>
                <?php endif; ?>
<!--                <button class="btn btn-primary">Voir le stock</button>-->

                <div class="event">
                    <h3>Sondage pour les bébévoles</h3>
                    <div class="listLodging">
                        <a href="/add_survey?id_lodging=<?=$idLodging?>&id_survey=-1" class="btn btn-secondary">Ajouter un sondage</a>
                        <div class="lodging-item">
                            <?php foreach ($surveys as $survey):?>
                            <p><a href="/survey?id_survey=<?=$survey['id']?>"><?=$survey['survey_name']?></a></p>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>

<?php
include_once "../include/footer.php"
?>