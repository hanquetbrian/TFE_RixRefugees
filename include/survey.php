<?php
require_once "../include/header.php";
require_once "../php_function/db_connection.php";
require_once "../php_function/utils.php";

$idSurvey = $_GET["id_survey"];
//TODO add security and redirection

$sql = "
    SELECT lodging_name, date_from, date_to, survey_name, Survey.description, Survey.content, Coordinator.name
    FROM rix_refugee.Survey
    LEFT JOIN Lodging on lodging_id = Lodging.id
    LEFT JOIN Coordinator on Lodging.coordinator_id = Coordinator.id
    WHERE Survey.id = ?
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$idSurvey]);
$survey = $sth->fetchAll(PDO::FETCH_ASSOC)[0];
$options = json_decode($survey["content"]);
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
                <form>
                    <div class="survey-from-group survey-form-header">
                        <div>
                            <h2 class="survey-form-title"><?=$survey['lodging_name']?> du <?= formatStrDate($survey['date_from'])?> au <?=formatStrDate($survey['date_to'])?></h2>
                            <p class="coordinator">Coordinateur: <?=$survey["name"]?></p>
                        </div>

                        <div>
                            <p>
                                <?=nl2br($survey['description'])?>
                            </p>
                        </div>
                    </div>

                    <div class="ml-5 survey-from-group form-group form-check">
                        <?php foreach ($options as $key => $option):?>
                            <div class="survey-form-option">
                                <input class="form-check-input" type="checkbox" value="" id="survey-option<?=$key?>">
                                <label class="form-check-label" for="survey-option<?=$key?>">
                                    <?=$option?>
                                </label>
                            </div>
                        <?php endforeach;?>

                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php
include_once "../include/footer.php"
?>