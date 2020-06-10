<?php
if(isset($_POST['submit'])) {
    $sql = "
            INSERT INTO rix_refugee.Coordinator_request (facebook_id, request, request_date)
            VALUES (:facebook_id, :request, :request_date)
        ";
    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute([
        ':facebook_id' => $AUTH->getFbId(),
        ':request' => $_POST['coord_req'],
        ':request_date' => date('Y-m-d H:i:s')
    ]);
}

$sql = "
            SELECT facebook_id, request, request_date
            FROM rix_refugee.Coordinator_request
            WHERE facebook_id = :facebook_id
        ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([':facebook_id' => $AUTH->getFbId()]);
$login = $sth->fetchAll(PDO::FETCH_ASSOC);

?>

<main>
<section class="container">
    <h1>Demande de coordinateur</h1>

    <div class="listLodging" style="margin-bottom: 3em">
        <?php if(empty($login)) : ?>
        <form action="#" method="post">
            <h2>Pourquoi voulez-vous être coordinateur?</h2>
            <textarea name="coord_req" class="form-control" rows="10"></textarea>
            <input class="btn-secondary form-control mt-3" type="submit" name="submit">
        </form>
        <?php else: ?>
            <p>Votre demande à bien été enregistré au nom de <?=$AUTH->getName()?>. Vous pouvez demander à un coordinateur d'accepter votre demande.</p>
            <div class="lodging-item" style="margin-bottom: 2em">
                <h3 style="font-size: 1.2em; text-decoration: underline; margin: 0">Votre demande </h3>
                <span style="font-size: 0.7em; color: rgba(145,145,145,0.85)">Modifié le <?=$login[0]['request_date']?></span>

                <p><?=htmlspecialchars($login[0]['request'])?></p>
            </div>
            <a class="btn btn-primary" href="#">Modifier votre demande</a>
<!--            TODO modify the request to be coordinator-->
        <?php endif;?>
    </div>


</section>
</main>