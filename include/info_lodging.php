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
SELECT lodging_name, date_from, date_to, address, nb_place, COUNT(DISTINCT Hosts.id) AS nb_hosts, Coordinator.id AS coord_id, User.name AS coord_name, CONCAT('[\"',GROUP_CONCAT(DISTINCT Lodging_equipment.equipment_name SEPARATOR  '\",\"'),'\"]') AS equipments
FROM Lodging_session
INNER JOIN Lodging ON Lodging.id = Lodging_session.lodging_id
LEFT JOIN Coordinator on Lodging_session.coordinator_id = Coordinator.id
LEFT JOIN User on Coordinator.user_id = User.id
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
SELECT Survey.id, description, Survey_options.id AS option_id, option_name
FROM Lodging_session
INNER JOIN Survey ON survey_id = Survey.id
LEFT JOIN Survey_options ON Survey_options.survey_id = Survey.id
WHERE Lodging_session.id = ?
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$idLodgingSession]);
$surveyOptions = $sth->fetchAll(PDO::FETCH_ASSOC);

// Get comments
$sql = "
SELECT name, comment
FROM Volunteer_request
INNER JOIN User on Volunteer_request.user_id = User.id
WHERE survey_id = ?
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$surveyOptions[0]['id']]);
$surveyVolunteer = $sth->fetchAll(PDO::FETCH_ASSOC);

// Get list of votes
$sql = "
SELECT Survey_options.id, option_name, COUNT(Result_list.id) AS nb_vote, GROUP_CONCAT(user_id) AS facebook_id_list
FROM Survey_options
LEFT JOIN Result_list ON Result_list.survey_option_id = Survey_options.id
LEFT JOIN Volunteer_request ON Volunteer_request.id = Result_list.volunteer_request_id
WHERE Survey_options.survey_id = ?
GROUP BY Survey_options.id, option_name
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$surveyOptions[0]['id']]);

$votes =  [];
while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    $votes[$row['id']] = $row;
}

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

                <?php foreach ($votes as $vote):?>
                <!-- Modal -->
                <div class="modal fade" id="option<?=$vote['id']?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <?php
                                    $volunteers_id = explode(',', $vote['facebook_id_list']);
                                // Get comments
                                $sql = "
                                    SELECT name, small_picture_url
                                    FROM User
                                    WHERE id = ?
                                ";
                                $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                                foreach ($volunteers_id as $volunteer_id) {
                                    $sth->execute([$volunteer_id]);
                                    $volunteer = $sth->fetchAll(PDO::FETCH_ASSOC)[0];

                                    ?>
                                    <div class="mb-3">
                                        <img alt="pic_of_<?=$volunteer['name']?>" src="<?=$volunteer['small_picture_url']?>">
                                        <span><?=$volunteer['name']?></span>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>

                <div class="event">
                    <h3>Demande de bénévoles</h3>
                    <div class="listLodging">
                    <?php if(empty($surveyOptions)): ?>
                        <a href="/add_survey?lodging_session_id=<?=$idLodgingSession?>" class="btn btn-secondary">Ajouter des demandes</a>
                    <?php else:?>
                        <a href="/add_survey?lodging_session_id=<?=$idLodgingSession?>" class="btn btn-secondary">Modifier les demandes</a>

                        <h4 style="margin: 1em 0; text-decoration: underline">Description</h4>
                        <p><?=$surveyOptions[0]['description']?></p>
                        <div class="lodging-item">
                            <div>
                                <a href="/survey?lodging_session_id=<?=$idLodgingSession?>">Lien vers le sondage</a>
                            </div>
                            <?php foreach ($surveyOptions as $option):?>
                                <span style="font-size: 0.9em"><a href="" data-toggle="modal" <?php if($votes[$option['option_id']]['nb_vote'] > 0) {echo 'data-target="#option'.$option['option_id'].'"';} ?>><?=$votes[$option['option_id']]['nb_vote']?> bénévole(s)</a></span>
                            <p><?=$option['option_name']?></p>

                            <?php endforeach;?>
                        </div>
                        <?php endif;?>
                    </div>
                </div>

                <?php if(!empty($surveyVolunteer)):?>
                <div>
                    <h3>Commentaires</h3>
                    <div class="listLodging">
                        <?php
                        $fb_object = $AUTH->getFbObject();

                        foreach ($surveyVolunteer as $comment) {
                            if(!empty($comment['comment'])) {
                                ?>
                                <div>
                                    <p style="font-size: 1.2em; text-decoration: underline"><?=$comment['name']?></p>
                                    <div class="lodging-item" style="margin: 0 2em; padding: 0.8em 0 0.1em 1.2em;">
                                        <p><?=$comment['comment']?></p>
                                    </div>

                                </div>
                        <?php
                            }
                        }?>


                    </div>
                </div>
                <?php endif;?>
            </div>
        </section>
    </main>