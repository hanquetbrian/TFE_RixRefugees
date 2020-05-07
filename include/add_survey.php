<?php
if(isset($_GET['id_survey']) && $_GET['id_survey'] > 0) {
    $survey_id = $_GET['id_survey'];
    require_once '../php_function/db_connection.php';
    // Get surveys info
    $sql = "
    SELECT id, survey_name, description, content
    FROM rix_refugee.Survey
    WHERE id = ?;
    ";

    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute([$survey_id]);
    $survey = $sth->fetchAll(PDO::FETCH_ASSOC)[0];
}

include_once "../include/header.php";
?>

    <main>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1>Création d'un sondage</h1>
            </div>

            <hr class="headerSep">
        </div>

        <section>
            <div class="container">
                <a href=""><i class="fas fa-arrow-circle-left"></i></a>
                <div id="survey">
                    <form>
                        <?php
                            if(isset($_GET['id_lodging'])) {
                                echo '<input id="id_lodging" name="id_lodging" type="hidden" value="' . $_GET['id_lodging'] .'">';
                            }

                            if(isset($_GET['id_survey'])) {
                                echo '<input id="id_survey" name="id_survey" type="hidden" value="' . $_GET['id_survey'] . '">';
                            }
                        ?>

                        <div class="survey-from-group survey-form-header">
                            <div>
                                <input id="survey-title" class="survey-form-control survey-form-title" value="<?php echo (isset($survey) ? $survey['survey_name'] : 'Sans titre')?>" name ="survey_title" placeholder="Titre du sondage">
                            </div>

                            <div>
                                <textarea id="survey-description" class="survey-form-control" name="survey_description" placeholder="description..."><?php echo (isset($survey) ? $survey['description'] : '')?></textarea>
                            </div>
                        </div>

                        <div class="survey-from-group">
                            <div id="listOption">
                                <?php if(isset($survey)) {
                                    $options = json_decode($survey["content"]);
                                    foreach ($options as $option) {
                                        echo '<div class="survey-form-check-group">';
                                        echo '<input class="survey-form-check" type="checkbox" disabled>';
                                        echo '<input class="survey-form-control survey-form-check-text" type="text" name="survey_options" value="' . $option . '">';
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