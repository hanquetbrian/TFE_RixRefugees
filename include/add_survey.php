<?php
if(!isset($_GET['lodging_session_id'])) {
    header('Location: /');
    exit(0);
}

require_once '../php_function/db_connection.php';
require_once '../php_function/utils.php';

$sessionId = $_GET['lodging_session_id'];
// Get surveys info
$sql = "
SELECT Lodging_session.id, Lodging.lodging_name, date_from, date_to, description, content
FROM rix_refugee.Lodging_session
LEFT JOIN Survey ON Survey.id = Lodging_session.survey_id
INNER JOIN Lodging ON Lodging.id = Lodging_session.lodging_id
WHERE Lodging_session.id = ?;
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$sessionId]);
$survey = $sth->fetchAll(PDO::FETCH_ASSOC);

if (empty($survey)) {
    header('Location: /');
    exit(0);
}

$survey = $survey[0];
$modify = false;
if(!empty($survey['content'])) {
    $modify = true;
}

include_once "../include/header.php";
?>

    <main>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <?php if($modify): ?>
                <h1>Modification du sondage</h1>
                <?php else: ?>
                <h1>Création d'un sondage</h1>
                <?php endif;?>
            </div>

            <hr class="headerSep">
        </div>

        <section>
            <div class="container">
                <a href=""><i class="fas fa-arrow-circle-left"></i></a>
                <div id="survey">
                    <form>
                        <input id="lodging_session_id" name="lodging_session_id" type="hidden" value="<?=$sessionId?>">

                        <div class="survey-from-group survey-form-header">
                            <div>
                                <h2 class="survey-form-title"><?=$survey['lodging_name']?> du <?= formatStrDate($survey['date_from'])?> au <?=formatStrDate($survey['date_to'])?></h2>
                            </div>

                            <div>
                                <textarea id="survey-description" class="survey-form-control" name="survey_description" placeholder="description..."><?php echo (isset($modify) ? $survey['description'] : '')?></textarea>
                            </div>
                        </div>

                        <div class="survey-from-group">
                            <div id="listOption">
                                <?php if($modify) {
                                    $options = json_decode($survey["content"]);
                                    foreach ($options as $option) {
                                        echo '<div class="survey-form-check-group">';
                                        echo '<input class="survey-form-check" type="checkbox" disabled>';
                                        echo '<input class="survey-form-control" type="text" name="survey_options" value="' . $option . '">';
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

<?php
include_once "../include/footer.php"
?>