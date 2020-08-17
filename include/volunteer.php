<?php
require_once "../php_function/db_connection.php";
$sql = "
    SELECT DISTINCT user_id,
                    CAST(AES_DECRYPT(name, :secret_key) AS CHAR(60)) AS name,
                    small_picture_url
    FROM rix_refugee.Volunteer_request
    INNER JOIN User on Volunteer_request.user_id = User.id
    WHERE user_id <> 0;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$sth->bindParam(':secret_key', $config['db.secret_key'], PDO::PARAM_STR);
$sth->execute();
$volunteers = $sth->fetchAll(PDO::FETCH_ASSOC);


?>
    <main>
        <section>
            <div class="d-none d-sm-block" id="titlePage">
                <div class="container">
                    <h1>Bénévoles</h1>
                </div>
                <hr class="headerSep">
            </div>

            <div class="container">
                <div class="listLodging">
                    <h2>Liste des bénévoles</h2>
                    <div class="lodging-item">
                        <?php foreach ($volunteers as $volunteer):?>
                            <div class="mb-3">
                                <img alt="pic_of_<?=$volunteer['name']?>" src="<?=$volunteer['small_picture_url']?>">
                                <span><a href="info_volunteer?volunteer_id=<?=$volunteer['user_id']?>"><?=$volunteer['name']?></a></span>
                            </div>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        </section>
    </main>
