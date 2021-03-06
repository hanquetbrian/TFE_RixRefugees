<?php
require_once '../php_function/utils.php';

$sessionId = $_GET['lodging_session_id'];
// Get surveys info
$sql = "
    SELECT Lodging_session.id, Lodging.lodging_name, date_from, date_to, Lodging_session.survey_id, Survey_options.id AS option_id, description, option_name
    FROM rix_refugee.Lodging_session
    INNER JOIN Lodging ON Lodging.id = Lodging_session.lodging_id
    LEFT JOIN Survey ON Survey.id = Lodging_session.survey_id
    LEFT JOIN Survey_options ON Survey.id = Survey_options.survey_id
    WHERE Lodging_session.id = ?;
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$sessionId]);
$survey = $sth->fetchAll(PDO::FETCH_ASSOC);

$modify = false;
if(isset($survey[0]['survey_id'])) {
    $modify = true;
}

?>

    <main>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <?php if($modify): ?>
                <h1>Modification des demandes</h1>
                <?php else: ?>
                <h1>Demandes de bénévoles</h1>
                <?php endif;?>
            </div>

            <hr class="headerSep">
        </div>

        <section>
            <div class="container">
                <a href="/info_lodging?lodging_session_id=<?=$sessionId?>"><i class="fas fa-arrow-circle-left"></i></a>
                <div id="survey">
                    <form>
                        <input id="lodging_session_id" name="lodging_session_id" type="hidden" value="<?=$sessionId?>">

                        <div class="survey-from-group survey-form-header">
                            <div>
                                <h2 class="survey-form-title"><?=$survey[0]['lodging_name']?> du <?= formatStrDate($survey[0]['date_from'])?> au <?=formatStrDate($survey[0]['date_to'])?></h2>
                            </div>

                            <div>
                                <textarea id="survey-description" class="survey-form-control" name="survey_description" placeholder="description..."><?php echo (isset($modify) ? $survey[0]['description'] : '')?></textarea>
                            </div>
                        </div>

                        <div class="survey-from-group">
                            <div id="listOption">
                                <?php if($modify) {
                                    foreach ($survey as $option) {
                                        echo '<div class="survey-form-check-group">';
                                        echo '<input class="survey-form-check" type="checkbox" disabled>';
                                        echo '<input class="survey-form-control option" type="text" name="survey_options" value="' . $option['option_name'] . '" data-option_id="' . $option['option_id'] . '">';
                                        echo '<span class="pl-3 remove-btn" onclick="removeEquipmentItem(this)"><i class=\'fas fa-times\'></i></span>';
                                        echo '</div>';
                                    }
                                }
                                ?>

                            </div>
                            <div>
                                <input class="survey-form-check" type="checkbox" disabled>
                                <input class="survey-form-control survey-form-check-text" type="text" placeholder="Ajouter une option">
                            </div>
                        </div>
                        <input id="btn_add_survey" class="btn btn-primary" type="submit" value="Enregistrer">
                    </form>
                </div>
            </div>
        </section>
    </main>
