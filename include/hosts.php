<?php
require_once '../php_function/utils.php';
require_once '../config.php';

$sql = "
    SELECT Hosts.id, lodging_name, date_from, date_to, CAST(AES_DECRYPT(name, :secret_key) AS CHAR(100)) AS name, comment, Lodging_session.id AS lodging_session_id
    FROM rix_refugee.Lodging_session
    LEFT JOIN Hosts ON Lodging_session.id = Hosts.lodging_session_id
    LEFT JOIN Lodging on Lodging_session.lodging_id = Lodging.id
    WHERE Lodging_session.id = :session_id;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
$sth->bindParam(':session_id', $_GET['lodging_session_id'], PDO::PARAM_INT);

$sth->execute();

$hosts = $sth->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- Modal -->
<div class="modal fade" id="addHost" tabindex="-1" role="dialog" aria-labelledby="addHostTitle"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addHostTitle">Ajouter un hébergé</h4>
            </div>
            <div class="modal-body">
                <form id="addHostForm">
                    <div class="form-group">
                        <input id="lodging_session_id" name="lodging_session_id" type="hidden" value="<?=$_GET['lodging_session_id']?>">
                        <label for="inputHostName">Nom de la personne hébergé:</label>
                        <input type="text" class="form-control" id="inputHostName">
                        <label for="inputComment">Commentaire:</label>
                        <textarea class="form-control" id="inputComment"></textarea>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addHostButton">Ajouter</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<main>
    <section>
        <div class="container mt-5">
            <h2><a style="color: #5a718c" href="/info_lodging?lodging_session_id=<?=$_GET['lodging_session_id']?>"><?=$hosts[0]['lodging_name']?> du <?= formatStrDate($hosts[0]['date_from'])?> au <?=formatStrDate($hosts[0]['date_to'])?></a></h2>
            <hr>
            <div id="hosts_list" class="listLodging">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addHost">Ajouter un hébergé</button>
                <h3>Liste des hébergés</h3>
                <?php if(isset($hosts[0]['id'])) :?>
                <div class="lodging-item">
                    <div class="row" style="background-color: rgba(173,173,173,0.85); padding: 0.5em 0; margin-bottom: 0.5em;border-radius: 3px">
                        <div class="col">Nom</div>
                        <div class="col">Commentaire</div>
                    </div>
                    <div id="hostContent">
                    <?php foreach ($hosts as $host) :?>
                        <div class="mb-3 row">

                            <div class="col">
                                <span class="host_name"><?=$host['name']?></span>
                            </div>
                            <div class="col">
                                <span class="comment"><?=$host['comment']?></span>
                            </div>
                        </div>
                    <?php endforeach;?>
                    </div>
                </div>
                <?php else:?>
                    <p>Aucun hébergé n'a été enregistré pour le moment. Veuillez les ajouter en cliquant sur le bouton adéquat.</p>
                <?php endif;?>
            </div>
        </div>
    </section>
</main>

