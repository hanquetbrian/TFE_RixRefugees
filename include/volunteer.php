<?php
require_once "../php_function/db_connection.php";
$sql = "
    SELECT DISTINCT facebook_id
        FROM rix_refugee.Survey_result
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([]);
$facebook_id_volunteer = $sth->fetchAll(PDO::FETCH_ASSOC);
$volunteers = [];
$fb_object = $AUTH->getFbObject();

try {

    foreach ($facebook_id_volunteer as $fb_id) {
        $response = $fb_object->get($fb_id['facebook_id'] . '/?fields=picture,name,id', $AUTH->getFbAccessToken());
        array_push($volunteers, $response->getGraphUser());
    }
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    include '../error/50x.html';
    die(0);
}

require_once 'header.php';
?>
    <main>
        <section>
            <div class="d-none d-sm-block" id="titlePage">
                <div class="container">
                    <h1>Bénévole</h1>
                </div>
                <hr class="headerSep">
            </div>

            <div class="container">
                <div class="listLodging">
                    <h2>Liste des bénévoles</h2>
                    <div class="lodging-item">
                        <?php foreach ($volunteers as $volunteer):?>
                            <div class="mb-3">
                                <img alt="pic_of_<?=$volunteer['name']?>" src="<?=$volunteer['picture']['url']?>">
                                <span><a href="info_volunteer?facebook_id=<?=$volunteer['id']?>"><?=$volunteer['name']?></a></span>
                            </div>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php
require_once 'footer.php';
?>