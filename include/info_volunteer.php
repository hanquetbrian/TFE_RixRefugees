<?php
if(!isset($_GET['facebook_id'])) {
    header('Location: volunteer');
    exit(0);
}

require_once "../php_function/db_connection.php";
require_once "../php_function/utils.php";

$sql = "
    SELECT facebook_id, result, lodging_name, date_from, date_to, Survey_result.survey_id
    FROM rix_refugee.Survey_result
    INNER JOIN rix_refugee.Survey on survey_id = Survey.id
    INNER JOIN Lodging_session ON Lodging_session.survey_id = Survey.id
    INNER JOIN Lodging ON Lodging.id = Lodging_session.lodging_id
    WHERE facebook_id = ?;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$_GET['facebook_id']]);
$surveyResult = $sth->fetchAll(PDO::FETCH_ASSOC);
$surveyNames = [];
foreach ($surveyResult as $result) {
    array_push($surveyNames, $result['lodging_name'] . ' du ' . formatStrDate($result['date_from']) . ' au ' . formatStrDate($result['date_to']));
}
$surveyNames = array_unique($surveyNames);

$fb_object = $AUTH->getFbObject();

try {
    $response = $fb_object->get($surveyResult[0]['facebook_id'].'/?fields=picture,name,id,email', $AUTH->getFbAccessToken());
    $volunteer = $response->getGraphUser();

    $picture_url = $fb_object->get($surveyResult[0]['facebook_id'].'/picture?redirect=0&type=normal', $AUTH->getFbAccessToken())->getGraphNode()['url'];
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    include '../error/50x.html';
    die(0);
}


$title = "RixRefugee " . $volunteer['name'];
require_once 'header.php';
?>

<main>
    <section>
        <div class="container mt-5">
            <div class="listLodging">
                <img src="<?=$picture_url?>" alt="picture_of_<?=$volunteer['name']?>" width="100">
                <h2 style="width: 50%; display: inline-block; padding: 1em .3em; margin-left: 1em; border-left: #6a85a7 solid 3px">Nom: <?=$volunteer['name']?></h2>
                <div class="lodging-item">
                    <?php





                    foreach ($surveyNames as $survey) {
                        echo '<h3>'.$survey.'</h3>';
                        echo '<div class="ml-4">';
                        foreach ($surveyResult as $result) {
                            if($result['survey_name'] == $survey) {
                                $contents = json_decode($result['result']);
                                foreach ($contents as $content) {
                                    echo '<p>'.$content.'</p>';
                                }
                            }
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
</main>

