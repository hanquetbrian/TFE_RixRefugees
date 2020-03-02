<?php
if (!isset($_GET['lodging_id'])) {
    header('Location: /');
    exit;
}

$imgSrc = 'img/house.jpg';
$date = 'Lundi 13/01 au jeudi 16/01';
$coordinateur = "Prénom nom";
$nombrePlaceDispo = 25;
$address = "4924 Barlett Avenue";
$equipement = ['Climatisation', 'Wifi', 'Sèche-cheveux', '2 Douche'];

?>


<?php
$title = "RixRefugiees";
include_once "../include/header.php"

?>

    <main>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1>HC</h1>
            </div>

            <hr class="headerSep">
        </div>

        <section>
            <div class="container">
                <h3>Informations</h3>
                <div class="row">
                    <div class="col-sm">
                        <img class="img-fluid thumbnail" src="img/house.jpg" alt="Construction house">
                    </div>

                    <div class="col-sm">
                        <ul class="info_lodging">
                            <li>Date: <?= $date ?></li>
                            <li>Coordinateur: <?= $coordinateur ?></li>
                            <li>Nombre de place disponible: <?= $nombrePlaceDispo ?></li>
                            <li class="address"><?= $address ?></li>
                        </ul>
                    </div>

                    <div class="col-sm d-flex flex-column align-items-end">
                        <button class="btn btn-primary">
                            Renouveler l'hébergement
                        </button>
                    </div>
                </div>

                <h3>Equipements</h3>
                <ul class="info_lodging">
                    <li>Climatisation</li>
                    <li>Wifi</li>
                    <li>Sèche-cheveux</li>
                    <li>douche</li>
                </ul>
                <button class="btn btn-primary">Voir le stock</button>

                <div class="event">
                    <h3>Evenment à venir</h3>
                    <div class="listLodging">
                        <div class="lodging-item">
                            <p>Atelier 6 jours</p>
                        </div>
                    </div>
                </div>

            </div>
        </section>


    </main>

<?php
include_once "../include/footer.php"
?>