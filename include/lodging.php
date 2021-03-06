<?php
require_once "../php_function/db_connection.php";
require_once "../php_function/utils.php";
$sql = "
    SELECT Lodging_session.id,
           Lodging.lodging_name,
           Lodging_session.date_from,
           Lodging_session.date_to,
           Lodging.address,
           Lodging.nb_place,
           Lodging.pic_url,
           Coordinator.id AS coord_id,
           CAST(AES_DECRYPT(User.name, :secret_key) AS CHAR(60)) AS coord_name,
           COUNT(Hosts.id) AS nb_hosts
    FROM (SELECT Lodging_session.lodging_id, MAX(Lodging_session.date_from) AS recent_date_from
            FROM Lodging_session
            GROUP BY lodging_id) AS latest_session
    INNER JOIN Lodging_session ON
        Lodging_session.lodging_id = latest_session.lodging_id AND
        Lodging_session.date_from = latest_session.recent_date_from
    INNER JOIN Lodging ON Lodging.id = Lodging_session.lodging_id
    LEFT JOIN Coordinator ON Lodging_session.coordinator_id = Coordinator.id
    LEFT JOIN User on Coordinator.user_id = User.id
    LEFT JOIN Hosts on Lodging_session.id = Hosts.lodging_session_id
    GROUP BY id, lodging_name, date_from, date_to, address, nb_place, coord_id, coord_name
    ORDER BY date_from DESC
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
$sth->execute();
$lodgings = $sth->fetchAll(PDO::FETCH_ASSOC);

?>

    <main>
        <!-- Modal -->
        <div class="modal fade" id="addLodging" tabindex="-1" role="dialog" aria-labelledby="addLodgingTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="addLodgingTitle">Ajouter un hébergement</h4>
                    </div>
                    <div class="modal-body">
                        <form id="addLodgingForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <div class="btn-image-picker">
                                    <label for="upload-photo"><i class="fas fa-images"></i></label>
                                    <input type="file" name="photo" id="upload-photo" class="d-none" accept="image/*">
                                </div>

                                <label for="inputLodgingName">Nom de l'hébergement:</label>
                                <input type="text" class="form-control" id="inputLodgingName" required>
                                <label for="inputMaxPlaces">Nombre maximum de places: </label>
                                <input type="number" class="form-control" id="inputMaxPlaces" required>
                                <label for="inputLodgingDateFrom">Date du début de la session:</label>
                                <input type="date" class="form-control" id="inputLodgingDateFrom" required>
                                <label for="inputLodgingDateTo">Date de la fin de la session:</label>
                                <input type="date" class="form-control" id="inputLodgingDateTo" required>
                                <label for="inputAddress">Adresse:</label>
                                <input type="text" class="form-control" id="inputAddress">

                                <div id="listEquipments">
                                    <label for="inputListEquipments">Liste d'équipement:</label>
                                    <ul class="list-group">
                                        <li class="list-group-item"><input type="text" id="inputListEquipments" placeholder="Equipement"></li>
                                    </ul>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" id="addLodgingButton">Ajouter</button>
                    </div>
                </div>
            </div>
        </div>

        <section>
            <div class="d-none d-sm-block" id="titlePage">
                <div class="container">
                    <h1>Hébergement</h1>
                </div>
                <hr class="headerSep">
            </div>

            <div class="container">
                <div id="lodgings" class="listLodging">
                    <button class="btn btn-secondary d-block w-100" data-toggle="modal" data-target="#addLodging">
                        Ajouter un hébergement
                    </button>
                    <?php foreach($lodgings as $lodging): ?>
                        <article>
                            <div class="lodging-item">
                                <h3><?=$lodging['lodging_name']?> du <?= formatStrDate($lodging['date_from'])?> au <?=formatStrDate($lodging['date_to'])?></h3>
                                <div class="lodging-item-content row justify-content-between mb-4">
                                    <div class="lodgingOptions col-sm-7">
                                        <div class="row justify-content-between lodgingOption-item ">
                                            <div class="col-8">Nombre de places disponibles</div>
                                            <div class="col-4"><span class="lodgingOption-nbDispo"><?=$lodging['nb_place'] - $lodging['nb_hosts']?></span></div>
                                        </div>
                                        <div class="row justify-content-between lodgingOption-item ">
                                            <div class="col-8"><span class="lodgingOption-nbMax">Nombre maximum de places</span></div>
                                            <div class="col-4"><?=$lodging['nb_place']?></div>
                                        </div>
                                        <div class="row justify-content-between lodgingOption-item ">
                                            <div class="col-8">Adresse</div>
                                            <div class="col-4"><span
                                                    class="address"><?=$lodging['address']?></span></div>
                                        </div>
                                        <div class="row justify-content-between lodgingOption-item ">
                                            <div class="col-8">Coordinateur</div>
                                            <div class="col-4"><span class="lodgingOption-coordinator"><a href="info_coordinator?coord_id=<?=$lodging['coord_id']?>"><?=$lodging['coord_name']?></a></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <img class="img-fluid thumbnail" src="<?=!empty($lodging['pic_url']) && file_exists('./'.$lodging['pic_url'])?$lodging['pic_url']:'img/house.jpg'?>" alt="Construction house">
                                    </div>

                                </div>
                                <button class="btn btn-primary"
                                        onclick="document.location.href = 'info_lodging?lodging_session_id=<?=$lodging['id']?>';">Plus d'infos <i class="fas fa-caret-right"></i>
                                </button>
                            </div>
                        </article>
                    <?php endforeach;?>
                </div>
            </div>
        </section>


    </main>
