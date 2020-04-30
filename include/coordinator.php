<?php
require_once "../php_function/db_connection.php";
$sql = "
    SELECT id, name, facebook_id, email, telephone, valid
    FROM rix_refugee.Coordinator;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([]);
$coordsList = $sth->fetchAll(PDO::FETCH_ASSOC);

$waitingCoord = [];
$validCoord = [];
// Separate the valid and not valid coordinator
foreach ($coordsList as &$coord) {
    if(isset($coord) && $coord['valid'] == 0) {
        array_push($waitingCoord, $coord);
    } elseif (isset($coord)) {
        array_push($validCoord, $coord);
    }
}

require_once "../php_function/fb-object.php";

require_once 'header.php';
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
                    <?php if(!empty($waitingCoord)): ?>
                    <div class="lodging-item">
                        <a href="#">Demande de coordinateur</a>
                        <span class="badge badge-secondary"><?=count($waitingCoord) ?></span>
                    </div>
                    <?php endif;?>

                    <h2>Liste des coordinateurs</h2>

                </div>
            </div>
        </section>
    </main>

<?php
require_once 'footer.php';
?>