<?php
require_once '../php_function/db_connection.php';
$sql = "
    SELECT id
    FROM rix_refugee.Coordinator_request;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([]);
$waitingCoords = $sth->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="fr">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/fontawesome.min.css"
          integrity="sha256-/sdxenK1NDowSNuphgwjv8wSosSNZB0t5koXqd7XqOI=" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/solid.min.css"
          integrity="sha256-8DcgqUGhWHHsTLj1qcGr0OuPbKkN1RwDjIbZ6DKh/RA=" crossorigin="anonymous"/>
    <!-- Base CSS -->
    <link rel="stylesheet" href="css/base.css">

    <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
            integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
            crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webshim/1.16.0/minified/polyfiller.js" ></script>

    <?php
        foreach ($page->getScript() as $script) {
            echo $script;
        }
    ?>

    <title><?= $page->getTitle() ?></title>
</head>
<body>
<div class="main-content" style="min-height: 100vh; position: relative; padding-bottom: 5rem;">
<header class="navbar navbar-expand-lg navbar-dark main-navbar">
    <div class="container">
        <a class="navbar-brand mr-auto" href="/">RixRefugee</a>
        <span class="user-name"><?=$AUTH->getName()?></span>
<!--        TODO add a logout button-->
    </div>

</header>

<?php if($AUTH->isCoordinator()): ?>
<nav class="navbar navbar-expand-sm navbar-light sub-navbar">
    <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#nav"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="container">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="/">Hébergements</i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/volunteer">Bénévoles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/inventory_management">Inventaires</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/coordinator">
                        <?php if(!empty($waitingCoords)): ?><span class="badge badge-secondary"><?=count($waitingCoords) ?></span><?php endif; ?>
                        Coordinateur</a>
                </li>
            </ul>
        </div>
    </div>

</nav>
<?php endif;?>


<script>
    var url = window.location.href;
    var li = document.querySelectorAll('.nav li a');
    for (var i=0; i<li.length; i++) {
        if(url === li[i].href) {
            li[i].parentNode.className = 'nav-item active';
        }
    }
</script>