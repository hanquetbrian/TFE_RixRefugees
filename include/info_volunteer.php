<?php
if(!isset($_GET['facebook_id'])) {
    header('Location: volunteer');
    exit(0);
}

require_once "../php_function/db_connection.php";
$sql = "
    SELECT facebook_id, result, survey_id, survey_name
        FROM rix_refugee.Survey_result
        LEFT JOIN rix_refugee.Survey on survey_id = Survey.id
        WHERE facebook_id = ?;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$_GET['facebook_id']]);
$surveyResult = $sth->fetchAll(PDO::FETCH_ASSOC);

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
                <!--                TODO allow the modifications of the coordinator info-->
                <img src="<?=$picture_url?>" alt="picture_of_<?=$volunteer['name']?>" width="100">
                <h2 style="width: 50%; display: inline-block; padding: 1em .3em; margin-left: 1em; border-left: #6a85a7 solid 3px">Nom: <?=$volunteer['name']?></h2>
                <div class="lodging-item">
                    <h3

                </div>
            </div>
        </div>
    </section>
</main>
