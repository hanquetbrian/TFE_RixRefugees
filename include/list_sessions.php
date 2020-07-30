<?php
$sql = "
SELECT lodging_id, lodging_name, date_from, date_to, coordinator_id, User.name
FROM Lodging_session
INNER JOIN Lodging ON Lodging_session.lodging_id = Lodging.id
LEFT JOIN Coordinator ON Coordinator.id = coordinator_id
INNER JOIN User ON User.id = Coordinator.user_id
WHERE lodging_id = ?; 
";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$_GET['lodging_id']]);
$lodgingSessions = $sth->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <section>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1>Historique des sessions</h1>
            </div>
            <hr class="headerSep">
            <?php if (!empty($lodgingSessions)): ?>
                <div class="container">
                    <h2><?=$lodgingSessions[0]['lodging_name']?></h2>
                </div>
            <?php endif;?>
        </div>
        <div class="container">

            <?php if (empty($lodgingSessions)): ?>
                <p>Aucune session n'a été trouvé pour cette hébergement</p>
            <?php else:?>
                <?php foreach ($lodgingSessions as $session):?>
                
                <?php endforeach;?>


            <?php endif;?>
        </div>
    </section>
</main>
