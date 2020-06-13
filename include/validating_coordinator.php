<?php
require_once "../php_function/db_connection.php";
$sql = "
    SELECT Coordinator_request.id, facebook_id, request, name, small_picture_url, picture_url, email, telephone, request_date
    FROM rix_refugee.Coordinator_request
    INNER JOIN User on Coordinator_request.user_id = User.id
    order by request_date DESC;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([]);
$coordinators = $sth->fetchAll(PDO::FETCH_ASSOC);

$title = "RixRefugee demande coordinateur";

?>
<main>
        <section>
            <div class="d-none d-sm-block" id="titlePage">
                <div class="container">
                    <h1>Demande de validation</h1>
                </div>
                <hr class="headerSep">
            </div>

            <div class="container">
                <div class="listLodging">

                    <h2>Liste des personnes qui ont demandés à être coordinateur</h2>
                    <div class="lodging-item">
                        <?php foreach ($coordinators as $coordinator):?>
                        <div class="lodgingOption-item row justify-content-between" style="background-color: #f2f2f2; padding: 1em">
                            <div class="pl-3">
                                <?php if(!empty($coordinator['small_picture_url'])){echo '<img alt="pic_of_'.$coordinator['name'].'" src="'.$coordinator['small_picture_url'].'">';}?>
                                <span><?=$coordinator['name']?></span>
                                <p><?=$coordinator['request']?></p>
                            </div>
                          <div>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#askAuthorize" onclick="$('#coordName').text('<?=$coordinator['name']?>')">Autoriser</button>
                                <button class="btn btn-secondary" onclick="removeCoord('<?=$coordinator['id']?>')">Supprimer</button>

                            </div>
                        </div>

                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        </section>
    </main>

<!-- Modal -->
    <div id="askAuthorize" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Etes-vous sûr?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Etes-vous sûr de vouloir autoriser <span id="coordName"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="authorize('<?=$coordinator['id']?>')">Oui</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Non</button>
                </div>
            </div>
        </div>
    </div>
