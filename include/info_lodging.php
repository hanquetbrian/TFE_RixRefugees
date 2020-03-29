<?php
if (!isset($_GET['lodging_id'])) {
    header('Location: /');
    exit;
}


$idLodging = $_GET['lodging_id'];
//TODO add security for lodging_id

require_once "../php_function/db_connection.php";
require_once "../php_function/utils.php";
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
$coordinateur = "Prénom nom";
$nombrePlaceDispo = 25;
$address = "4924 Barlett Avenue";
$equipement = ['Climatisation', 'Wifi', 'Sèche-cheveux', '2 Douche'];

?>


<?php
include_once "../include/header.php"

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
                            <li>Nombre de place disponible: <?= $lodgings[0]['equipment_name'] ?></li>
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
                    <h3>Evénment à venir</h3>
                    <div class="listLodging">
                        <div class="lodging-item">
                            <p>Atelier 6 jours</p>
                            <a href="survey">Lien temporaire vers survey</a>
                        </div>
                    </div>
                </div>

            </div>
        </section>


    </main>

<?php
include_once "../include/footer.php"
?>