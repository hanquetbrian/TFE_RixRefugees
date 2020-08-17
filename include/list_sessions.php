<?php
require_once "../php_function/utils.php";

$sql = "
SELECT Lodging_session.id,
       lodging_id,
       lodging_name,
       pic_url,
       date_from,
       date_to,
       coordinator_id,
       CAST(AES_DECRYPT(name, :secret_key) AS CHAR(60)) AS coord_name
FROM Lodging_session
INNER JOIN Lodging ON Lodging_session.lodging_id = Lodging.id
LEFT JOIN Coordinator ON Coordinator.id = coordinator_id
LEFT JOIN User ON User.id = Coordinator.user_id
WHERE lodging_id = :lodging_id
ORDER BY date_from DESC; 
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
$sth->bindParam(':lodging_id', $_GET['lodging_id'], PDO::PARAM_INT);
$sth->execute();
$lodgingSessions = $sth->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <section>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1>Liste des sessions précédentes</h1>
            </div>
            <hr class="headerSep">
            <?php if (!empty($lodgingSessions)): ?>
                <div class="container">
                    <h2><?=$lodgingSessions[0]['lodging_name']?></h2>
                    <div style="margin: 2em">
                        <img class="img-thumbnail" alt="lodging_picture" src="<?=$lodgingSessions[0]['pic_url']?>"
                    </div>

                </div>
            <?php endif;?>
        </div>
        <div class="container">

            <?php if (empty($lodgingSessions)): ?>
                <p>Aucune session n'a été trouvé pour cette hébergement</p>
            <?php else:?>
            <div id="lodgings" class="listLodging">

                <?php foreach ($lodgingSessions as $session):?>
                    <article>
                        <div class="lodging-item">
                            <p style="margin-bottom: 0"><a href="/info_lodging?lodging_session_id=<?=$session['id']?>"><?= formatStrDate($session['date_from'])?> au <?=formatStrDate($session['date_to'])?></a></p>
                            <div style="margin-left: 2em">
                                Coordinateur: <?=$session['coord_name']?>
                            </div>
                        </div>
                    </article>
                <?php endforeach;?>
            </div>
            <?php endif;?>
        </div>
    </section>
</main>
