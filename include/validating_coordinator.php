<?php
require_once "../php_function/db_connection.php";
$sql = "
    SELECT id, name, small_picture_url, picture_url, facebook_id, email, telephone, added_day
    FROM rix_refugee.validating_coordinator
    order by added_day DESC;
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
                        <div class="lodgingOption-item row justify-content-between pr-5">
                            <div class="pl-3">
                                <?php if(!empty($coordinator['small_picture_url'])){echo '<img alt="pic_of_'.$coordinator['name'].'" src="'.$coordinator['small_picture_url'].'">';}?>
                                <span><a href="info_coordinator?coord_id=<?=$coordinator['id']?>"><?=$coordinator['name']?></a></span>
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
