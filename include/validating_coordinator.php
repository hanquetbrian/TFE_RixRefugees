<?php

$title = "RixRefugee demande coordinateur";
require_once 'header.php';

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

                    <h2>Liste des coordinateurs qui ont demandé un access</h2>
                    <div class="lodging-item">
                        <?php foreach ($validCoords as $validCoord):?>
                        <div>
                            <img alt="pic_of_<?=$validCoord['name']?>" src="<?=$validCoord['small_picture_url']?>">
                            <span><a href="info_coordinator?coord_id=<?=$validCoord['id']?>"><?=$validCoord['name']?></a></span>
                        </div>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        </section>
    </main>