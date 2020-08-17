<?php
require_once "../php_function/utils.php";

if($_GET['volunteer_id'] == 0) {
    ?>
    <main>
        <section>
            <div class="container mt-5">
                <div class="listLodging">
                    <p>Désolé mais cet utilisateur n'existe pas ou il a été supprimé.</p>
                </div>
            </div>
        </section>
    </main>
    <?php
} else {

$sql = "
    SELECT CAST(AES_DECRYPT(name, :secret_key) AS CHAR(60)) AS name,
           User.picture_url,
           lodging_name,
           date_from,
           date_to,
           Survey.id as survey_id,
           Survey.description,
           comment
    FROM rix_refugee.Volunteer_request
    INNER JOIN User on Volunteer_request.user_id = User.id
    INNER JOIN rix_refugee.Survey on survey_id = Survey.id
    INNER JOIN Lodging_session ON Lodging_session.survey_id = Survey.id
    INNER JOIN Lodging ON Lodging.id = Lodging_session.lodging_id
    WHERE user_id = :user_id
    ORDER BY date_to DESC ;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
$sth->bindParam(':user_id', $_GET['volunteer_id'], PDO::PARAM_INT);
$sth->execute();
$surveyList = $sth->fetchAll(PDO::FETCH_ASSOC);

?>

<main>
    <section>
        <div class="container mt-5">
            <div class="listLodging">
                <img src="<?=$surveyList[0]['picture_url']?>" alt="picture_of_<?=$surveyList[0]['name']?>" width="100">
                <h2 style="width: 50%; display: inline-block; padding: 1em .3em; margin-left: 1em; border-left: #6a85a7 solid 3px">Nom: <?=$surveyList[0]['name']?></h2>
                <div class="lodging-item">
                    <?php foreach ($surveyList as $survey):?>
                    <h3><?=$survey['lodging_name']?> du <?= formatStrDate($survey['date_from'])?> au <?=formatStrDate($survey['date_to'])?></h3>

                    <p><?=$survey['description']?></p>

                    <?php
                        $sql = "
                        SELECT option_name
                        FROM rix_refugee.Volunteer_request
                        INNER JOIN Result_list on Volunteer_request.id = Result_list.volunteer_request_id
                        INNER JOIN Survey_options on Result_list.survey_option_id = Survey_options.id
                        WHERE Volunteer_request.survey_id = ? and user_id = ?
                        ";

                        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        $sth->execute([$survey['survey_id'], $_GET['volunteer_id']]);
                        $surveyOptions = $sth->fetchAll(PDO::FETCH_ASSOC);

                        echo '<div class="ml-4">';
                        foreach ($surveyOptions as $surveyOption) {
                            echo '<p> - '.$surveyOption['option_name'].'</p>';
                        }
                        echo '</div>';
                    ?>
                    <?php if(isset($survey['comment'])):?>
                    <div style="background-color: #eeecea; padding: 1em; margin-bottom: 3em">
                        <h4 style="font-size: 1.5em">Commentaires: </h4>
                        <p><?=$survey['comment']?></p>
                    </div>
                    <?php endif;?>
                    <hr>
                    <?php endforeach;?>

                </div>
            </div>
        </div>
    </section>
</main>

<?php }?>
