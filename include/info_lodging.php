<?php
if (!isset($_GET['lodging_session_id'])) {
    header('Location: /');
    exit;
}

$idLodgingSession = $_GET['lodging_session_id'];
//TODO add security for lodging_id

require_once "../php_function/db_connection.php";
require_once "../php_function/utils.php";

// Get lodging info
$sql = "
SELECT lodging_name, date_from, date_to, address, nb_place, COUNT(DISTINCT Hosts.id) AS nb_hosts, Coordinator.id AS coord_id, Coordinator.name AS coord_name, CONCAT('[\"',GROUP_CONCAT(DISTINCT Lodging_equipment.equipment_name SEPARATOR  '\",\"'),'\"]') AS equipments
FROM Lodging_session
INNER JOIN Lodging ON Lodging.id = Lodging_session.lodging_id
LEFT JOIN Coordinator on Lodging_session.coordinator_id = Coordinator.id
LEFT JOIN Lodging_equipment ON Lodging.id = Lodging_equipment.lodging_id
LEFT JOIN Hosts on Lodging_session.id = lodging_session_id
WHERE Lodging_session.id = ?;
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$idLodgingSession]);
$lodgings = $sth->fetchAll(PDO::FETCH_ASSOC)[0];
$equipments = json_decode($lodgings["equipments"]);


// Get survey info
$sql = "
SELECT description, option_name
FROM Lodging_session
INNER JOIN Survey ON survey_id = Survey.id
LEFT JOIN Survey_options ON Survey_options.survey_id = Survey.id
WHERE Lodging_session.id = ?
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$idLodgingSession]);
$surveyOptions = $sth->fetchAll(PDO::FETCH_ASSOC);

$imgSrc = 'img/house.jpg';

?>


    <main>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1><?=$lodgings['lodging_name']?></h1>
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
                            <li>Date: <?= formatStrDate($lodgings['date_from'])?> au <?=formatStrDate($lodgings['date_to'])?></li>
                            <li>Coordinateur: <a href="info_coordinator?coord_id=<?=$lodgings['coord_id']?>"><?=$lodgings['coord_name']?></a></li>
                            <li>Nombre de places disponibles: <?= $lodgings['nb_hosts'] ?> / <?=$lodgings['nb_place']?></li>
                            <li class="address"><?= $lodgings['address'] ?></li>
                        </ul>
                    </div>

                    <div class="col-sm d-flex flex-column align-items-end">
                        <button class="btn btn-primary">
                            Renouveler l'hébergement
                        </button>
                        <a href="/hosts?lodging_session_id=<?=$idLodgingSession?>" class="btn btn-primary mt-5">
                            Listes des hébergeurs
                        </a>
                    </div>
                </div>
                <?php if(!empty($equipments)):?>

                <h3>Equipements</h3>
                <ul class="info_lodging">
                    <?php foreach ($equipments as $equipment):?>
                    <li><?=$equipment?></li>
                    <?php endforeach;?>
                </ul>
                <?php endif; ?>
<!--                <button class="btn btn-primary">Voir le stock</button>-->

                <div class="event">
                    <h3>Sondage pour les bébévoles</h3>
                    <div class="listLodging">
                    <?php if(empty($surveyOptions)): ?>
                        <a href="/add_survey?lodging_session_id=<?=$idLodgingSession?>" class="btn btn-secondary">Ajouter un sondage</a>
                    <?php else:?>
                        <a href="/add_survey?lodging_session_id=<?=$idLodgingSession?>" class="btn btn-secondary">Modifier le sondage</a>

                        <h4 style="margin: 1em 0; text-decoration: underline">Description</h4>
                        <p><?=$surveyOptions[0]['description']?></p>
                        <div class="lodging-item">
                            <a href="/survey?lodging_session_id=<?=$idLodgingSession?>">Lien vers le sondage</a>
                            <?php foreach ($surveyOptions as $option):?>
                            <p><?=$option['option_name']?></p>
                            <?php endforeach;?>
                        </div>
                        <?php endif;?>
                    </div>
                </div>

            </div>
        </section>
    </main>