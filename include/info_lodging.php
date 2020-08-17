<?php
if (!isset($_GET['lodging_session_id'])) {
    header('Location: /');
    exit;
}

$idLodgingSession = $_GET['lodging_session_id'];

require_once "../php_function/db_connection.php";
require_once "../php_function/utils.php";

// Get lodging info
$sql = "
SELECT Lodging_session.lodging_id,
       lodging_name,
       date_from,
       date_to,
       address,
       nb_place,
       pic_url,
       COUNT(DISTINCT Hosts.id) AS nb_hosts,
       Coordinator.id AS coord_id,
       CAST(AES_DECRYPT(User.name, :secret_key) AS CHAR(60)) AS coord_name,
       CONCAT('[\"',GROUP_CONCAT(DISTINCT Lodging_equipment.equipment_name SEPARATOR  '\",\"'),'\"]') AS equipments
FROM Lodging_session
INNER JOIN Lodging ON Lodging.id = Lodging_session.lodging_id
LEFT JOIN Coordinator on Lodging_session.coordinator_id = Coordinator.id
LEFT JOIN User on Coordinator.user_id = User.id
LEFT JOIN Lodging_equipment ON Lodging.id = Lodging_equipment.lodging_id
LEFT JOIN Hosts on Lodging_session.id = lodging_session_id
WHERE Lodging_session.id = :idSession;
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
$sth->bindParam(':idSession', $idLodgingSession, PDO::PARAM_INT);
$sth->execute();
$lodgingInfo = $sth->fetchAll(PDO::FETCH_ASSOC)[0];
$equipments = json_decode($lodgingInfo["equipments"]);


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
$survey = $sth->fetchAll(PDO::FETCH_ASSOC);

if(!empty($survey)) {
    // Get comments
    $sql = "
SELECT CAST(AES_DECRYPT(User.name, :secret_key) AS CHAR(60)) AS name, comment
FROM Volunteer_request
INNER JOIN User on Volunteer_request.user_id = User.id
WHERE survey_id = :survey_id
";

    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
    $sth->bindParam(':survey_id', $survey[0]['id'], PDO::PARAM_INT);
    $sth->execute();
    $listOfComments = $sth->fetchAll(PDO::FETCH_ASSOC);

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
    $sth->execute([$survey[0]['id']]);

    $votes =  [];
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        $votes[$row['id']] = $row;
    }
}

?>
<!-- Modal -->
<div class="modal fade" id="newSession" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nouvelle session</h4>
            </div>
            <form id="newSessionForm" action="api/newSession.php" method="post">
            <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" value="<?=$lodgingInfo['lodging_id']?>" name="lodging_id">
                        <label for="inputSessionDateFrom">Début de la session:</label>
                        <input type="date" class="form-control" id="inputSessionDateFrom" required name="date_from">
                        <label for="inputSessionDateTo">Fin de la session:</label>
                        <input type="date" class="form-control" id="inputSessionDateTo" required name="date_to">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                <input type="submit" class="btn btn-primary" id="createNewSession" value="Créer">
            </div>
            </form>
        </div>
    </div>
</div>

    <main>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1><?=$lodgingInfo['lodging_name']?></h1>
            </div>

            <hr class="headerSep">
        </div>

        <section>
            <div class="container">
                <h3>Informations</h3>
                <div class="row">
                    <div class="col-sm">
                        <img class="img-fluid thumbnail" src="<?=!empty($lodgingInfo['pic_url']) && file_exists('./'.$lodgingInfo['pic_url'])?$lodgingInfo['pic_url']:'img/house.jpg'?>" alt="Construction house">
                    </div>

                    <div class="col-sm">
                        <ul class="info_lodging">
                            <li>Date: <?= formatStrDate($lodgingInfo['date_from'])?> au <?=formatStrDate($lodgingInfo['date_to'])?></li>
                            <li>Coordinateur: <a href="info_coordinator?coord_id=<?=$lodgingInfo['coord_id']?>"><?=$lodgingInfo['coord_name']?></a></li>
                            <li>Places occupées: <?= $lodgingInfo['nb_hosts'] ?> / <?=$lodgingInfo['nb_place']?></li>
                            <li>Adresse: <span class="address"><?= $lodgingInfo['address'] ?></span></li>
                        </ul>
                    </div>

                    <div class="col-sm d-flex flex-column">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#newSession">
                            Renouveler l'hébergement
                        </button>
                        <a href="/hosts?lodging_session_id=<?=$idLodgingSession?>" class="btn btn-primary mt-5">
                            Liste des hébergés
                        </a>
                        <a href="/list_sessions?lodging_id=<?=$lodgingInfo['lodging_id']?>" class="btn btn-primary mt-5">
                            Historique des anciennes sessions
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

                <?php if(isset($votes)): ?>
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
                                    SELECT id, name, small_picture_url
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
                                        <span><a href="info_volunteer?volunteer_id=<?=$volunteer['id']?>"><?=$volunteer['name']?></a></span>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
                <?php endif;?>

                <div class="event">
                    <h3 id="volunteer_request">Demande de bénévoles</h3>
                    <div class="listLodging">
                    <?php if(empty($survey)): ?>
                        <a href="/add_survey?lodging_session_id=<?=$idLodgingSession?>" class="btn btn-secondary">Ajouter des demandes</a>
                    <?php else:?>
                        <a href="/add_survey?lodging_session_id=<?=$idLodgingSession?>" class="btn btn-secondary">Modifier les demandes</a>

                        <h4 style="margin: 1em 0; text-decoration: underline">Description</h4>
                        <p><?=$survey[0]['description']?></p>
                        <div class="lodging-item">
                            <div style="font-size: 1.2em; margin-bottom: 2em">
                                <?php
                                    $survey_url = $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER['SERVER_NAME'] . '/survey?lodging_session_id=' . $idLodgingSession
                                ?>
                                Partager ce lien aux bénévoles: <input id="inputSurveyUrl" type="text" class="form-control input-monospace input-sm" data-autoselect="" value="<?=$survey_url?>" readonly="">
                                <div style="position: absolute">
                                    <span id="clipboard_return" style="font-size: 0.6em; position: relative"></span>
                                </div>

                            </div>
                            <?php foreach ($survey as $option):?>
                                <span style="font-size: 0.9em"><a href="" data-toggle="modal" <?php if($votes[$option['option_id']]['nb_vote'] > 0) {echo 'data-target="#option'.$option['option_id'].'"';} ?>><?=$votes[$option['option_id']]['nb_vote']?> bénévole(s)</a></span>
                            <p><?=$option['option_name']?></p>

                            <?php endforeach;?>
                        </div>
                        <?php endif;?>
                    </div>
                </div>

                <?php if(!empty($listOfComments)):?>
                <div>
                    <h3>Commentaires</h3>
                    <div class="listLodging">
                        <?php
                        $fb_object = $AUTH->getFbObject();

                        foreach ($listOfComments as $comment) {
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