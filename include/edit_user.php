<?php
require_once "../php_function/db_connection.php";
$sql = "
    SELECT id, facebook_id,
           CAST(AES_DECRYPT(name, :secret_key) AS CHAR(60)) AS name,
           password,
           small_picture_url,
           picture_url,
           CAST(AES_DECRYPT(email, :secret_key) AS CHAR(255)) AS email,
           visible_email,
           CAST(AES_DECRYPT(telephone, :secret_key) AS CHAR(20)) AS telephone,
           visible_telephone
    FROM rix_refugee.User
    where id = :id;
    ";

$userId = $AUTH->getUserId();
$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
$sth->bindParam(':id', $userId, PDO::PARAM_INT);
$sth->execute();
$user = $sth->fetchAll(PDO::FETCH_ASSOC)[0];

?>

<main>
    <section>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1>Modification du profil</h1>
            </div>
            <hr class="headerSep">
        </div>

        <div class="container">
            <div class="listLodging">
                <form action="/api/editUser.php" method="post">

                    <div class="row" style="margin-bottom: 2em">
                        <div class="col-auto">
                            <img src="<?=$user['picture_url']?>" alt="picture_of_<?=$user['name']?>" width="100">
                        </div>
                        <div class="col mt-3" style="padding: 0">
                            <span style=""><input class="form-control" style="width: 80%; font-weight: bold; font-size: 1.3em" type="text" value="<?=$user['name']?>" name="name"></span>
                            <p><?=($AUTH->isCoordinator()?'Coordinateur': 'Bénévole')?></p>
                        </div>
                    </div>
                    <?php
                    if(!empty($_SESSION['fb_access_token'])) {
                        echo '<a class="btn btn-primary" href="/api/update_info.php"><i class="fas fa-sync-alt"></i> Mettre à jour les informations</a>';
                    }
                    ?>

                    <div class="lodging-item">
                        <div>
                            <label>Email:
                                <input class="form-control" type="text" value="<?=$user['email']?>" name="email">
                            </label>
                            <div style="display: inline-block; margin-left: 2em">
                                <input class="form-check-input" type="checkbox" id="show_email" <?=($user['visible_email']?'checked':'')?> name="show_email">
                                <label class="form-check-label" for="show_email">Afficher l'email</label>
                            </div>
                        </div>

                        <div>
                            <label>Téléphone:
                                <input class="form-control" type="text" value="<?=$user['telephone']?>" name="telephone">
                            </label>
                            <div style="display: inline-block; margin-left: 2em">
                                <input class="form-check-input" type="checkbox" id="show_telephone" <?=($user['visible_telephone']?'checked':'')?> name="show_telephone">
                                <label class="form-check-label" for="show_telephone">Afficher le téléphone</label>
                            </div>
                        </div>
                        <input class="btn btn-primary" type="submit" value="Modifier">
                    </div>

                </form>
                <?php if(!$AUTH->isCoordinator()):?>
                <div style="margin-top: 2em">
                    <p><a class="btn btn-success" href="/ask_access">Devenir coordinateur</a></p>
                </div>
                <?php endif;?>
                <div style="margin-top: 2em">
                    <a class="btn btn-primary" style="color: white" href="/change_password">
                        <?php
                        if(isset($user['password'])) {
                            echo 'Modifier le mot de passe';
                        } else {
                            echo 'Créer un nouveau mot de passe';
                        }
                        ?>
                    </a>
                </div>
                <div style="margin-top: 2em" id="danger-zone">

                    <button class="btn btn-danger" data-toggle="modal" data-target="#delUser">Supprimer le compte</button>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Modal -->
<div id="delUser" class="modal" tabindex="-1" role="dialog" >
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border: #cb332e 2px solid">
            <div class="modal-header">
                <h5 class="modal-title">Êtes-vous sûr?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir <b>supprimer</b> votre compte ?</p>
                <p><b>Vous n'aurez plus accès à votre compte</b> et une nouvelle demande sera nécessaire.</p>
                <p>Toutes les données vous concernant seront <b>supprimées du système</b>.</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" href="/api/delUserAccount.php">Oui</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Non</button>
            </div>
        </div>
    </div>
</div>