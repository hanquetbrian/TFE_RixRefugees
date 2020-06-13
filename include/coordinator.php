<?php
require_once "../php_function/db_connection.php";
$sql = "
    SELECT Coordinator.id, name, small_picture_url, picture_url, facebook_id, email, telephone, added_day
    FROM rix_refugee.Coordinator
    INNER JOIN User on Coordinator.user_id = User.id;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([]);
$coordsList = $sth->fetchAll(PDO::FETCH_ASSOC);
array_shift($coordsList);

$sql = "
    SELECT facebook_id, request, name, small_picture_url, picture_url, email, telephone, request_date
    FROM rix_refugee.Coordinator_request
    INNER JOIN User on Coordinator_request.user_id = User.id;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([]);
$waitingCoords = $sth->fetchAll(PDO::FETCH_ASSOC);

?>
    <main>
        <section>
            <div class="d-none d-sm-block" id="titlePage">
                <div class="container">
                    <h1>Coordinateur</h1>
                </div>
                <hr class="headerSep">
            </div>

            <div class="container">
                <div class="listLodging">
                    <?php if(!empty($waitingCoords)): ?>
                    <div class="lodging-item">
                        <a href="validating_coordinator">Demande de coordinateur</a>
                        <span class="badge badge-secondary"><?=count($waitingCoords) ?></span>
                    </div>
                    <?php endif;?>

                    <h2>Liste des coordinateurs</h2>
                    <div class="lodging-item">
                        <?php foreach ($coordsList as $validCoord):?>
                        <div>
                            <img alt="pic_of_<?=$validCoord['name']?>" src="<?=$validCoord['small_picture_url']?>">
                            <span><a href="info_coordinator?coord_id=<?=$validCoord['id']?>"><?=$validCoord['name']?></a></span>
                        </div>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        </section>
    </main>
