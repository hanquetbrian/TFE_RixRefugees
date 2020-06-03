<?php
require_once "../php_function/db_connection.php";
require_once "../php_function/utils.php";

$sessionId = $_GET["lodging_session_id"];
//TODO add security and redirection

$sql = "
    SELECT Lodging_session.id, Lodging.lodging_name, date_from, date_to, survey_id, description, content, Coordinator.id as coord_id, Coordinator.name as coord_name
    FROM rix_refugee.Lodging_session
    LEFT JOIN Survey ON Survey.id = Lodging_session.survey_id
    INNER JOIN Lodging ON Lodging.id = Lodging_session.lodging_id
    LEFT JOIN Coordinator ON Coordinator.id = Lodging_session.coordinator_id
    WHERE Lodging_session.id = ?;
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$sessionId]);
$survey = $sth->fetchAll(PDO::FETCH_ASSOC)[0];
$options = json_decode($survey["content"]);


require_once "../include/header.php";
?>

<main>
    <div class="d-none d-sm-block" id="titlePage">
        <div class="container">
            <h1>Demande de bénévoles</h1>
        </div>

        <hr class="headerSep">
    </div>

    <section>
        <div class="container">
            <div id="survey">
                <form action="/api/saveSurveyResult.php?id_survey=<?=$survey['survey_id']?>" method="post">
                    <div class="survey-from-group survey-form-header">
                        <div>
                            <h2 class="survey-form-title"><?=$survey['lodging_name']?> du <?= formatStrDate($survey['date_from'])?> au <?=formatStrDate($survey['date_to'])?></h2>
                            <p class="coordinator">Coordinateur: <a href="info_coordinator?coord_id=<?=$survey["coord_id"]?>"><?=$survey["coord_name"]?></a></p>
                        </div>

                        <div>
                            <p>
                                <?=nl2br($survey['description'])?>
                            </p>
                        </div>
                    </div>

<!--                    ERROR MESSAGE-->
                    <div class="alert alert-danger fade show" style="display: none" role="alert">
                        <span id="error_message"></span>
                    </div>


                    <div class="ml-5 survey-from-group form-group form-check">
                        <?php foreach ($options as $key => $option):?>
                            <div class="survey-form-option">
                                <input class="form-check-input" type="checkbox" value="<?=$option?>" id="survey-option<?=$key?>" name="option<?=$key?>">
                                <label class="form-check-label" for="survey-option<?=$key?>">
                                    <?=$option?>
                                </label>
                            </div>
                        <?php endforeach;?>

                        <button id="btn_save_survey_result" type="submit" class="btn btn-primary">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php
include_once "../include/footer.php"
?>