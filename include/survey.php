<?php
require_once "../php_function/db_connection.php";
require_once "../php_function/utils.php";

$sessionId = $_GET["lodging_session_id"];
//TODO add security and redirection

$sql = "
    SELECT Lodging_session.id, Lodging.lodging_name, date_from, date_to, Lodging_session.survey_id, Survey_options.id AS option_id, description, option_name, Coordinator.id as coord_id, Coordinator.name as coord_name
    FROM rix_refugee.Lodging_session
    LEFT JOIN Survey ON Survey.id = Lodging_session.survey_id
    INNER JOIN Lodging ON Lodging.id = Lodging_session.lodging_id
    LEFT JOIN Coordinator ON Coordinator.id = Lodging_session.coordinator_id
    LEFT JOIN Survey_options ON Survey.id = Survey_options.survey_id
    WHERE Lodging_session.id = ?;
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$sessionId]);
$survey = $sth->fetchAll(PDO::FETCH_ASSOC);


// Get what the user already check
$sql = "
SELECT comment, survey_option_id
FROM Volunteer_request
LEFT JOIN Result_list ON Result_list.volunteer_request_id = Volunteer_request.id
WHERE facebook_id = :facebook_id AND survey_id = :survey_id
ORDER BY survey_option_id 
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([
    ':facebook_id' => $AUTH->getFbId(),
    ':survey_id' => $survey[0]['survey_id']
]);
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

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
                <?php if($AUTH->isCoordinator()): ?>
                <a href="/add_survey?lodging_session_id=<?=$sessionId?>" class="btn btn-secondary">Modification des demandes</a>
                <?php endif;?>
                <form action="/api/saveSurveyResult.php?id_survey=<?=$survey[0]['survey_id']?>" method="post">
                    <div class="survey-from-group survey-form-header">
                        <div>
                            <h2 class="survey-form-title"><?=$survey[0]['lodging_name']?> du <?= formatStrDate($survey[0]['date_from'])?> au <?=formatStrDate($survey[0]['date_to'])?></h2>
                            <p class="coordinator">Coordinateur: <a href="info_coordinator?coord_id=<?=$survey[0]["coord_id"]?>"><?=$survey[0]["coord_name"]?></a></p>
                        </div>

                        <div>
                            <p>
                                <?=nl2br($survey[0]['description'])?>
                            </p>
                        </div>
                    </div>

<!--                    ERROR MESSAGE-->
                    <div class="alert alert-danger fade show" style="display: none" role="alert">
                        <span id="error_message"></span>
                    </div>


                    <div class="ml-5 survey-from-group form-group form-check">
                        <?php $i = 0; foreach ($survey as $key => $option):?>
                            <div class="survey-form-option">
                                <input class="form-check-input"
                                       type="checkbox"
                                       value="<?=$option['option_id']?>"
                                       id="survey-option<?=$key?>"
                                       name="option<?=$key?>"
                                        <?php
                                        if(isset($result[$i]['survey_option_id']) && $result[$i]['survey_option_id'] == $option['option_id']) {
                                            echo 'checked';
                                            $i += 1;
                                        }
                                        ?>
                                >
                                <label class="form-check-label" for="survey-option<?=$key?>">
                                    <?=$option['option_name']?>
                                </label>
                            </div>
                        <?php endforeach;?>

                        <label class="form-check-label" for="survey-comment">Commentaire: </label><br>
                        <textarea class="form-control m-3" id="survey-comment" rows="5" name="comment"><?=isset($result[0]['comment'])?$result[0]['comment']:''?></textarea>
                        <button id="btn_save_survey_result" type="submit" class="btn btn-primary">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
