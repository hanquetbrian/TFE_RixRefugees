<?php
$title = "RixRefugiees";
include_once "../include/header.php"

?>

    <main>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1>Hébergement</h1>
            </div>

            <hr class="headerSep">
        </div>

        <section>
            <div class="container">
                <div class="listLodging">
                    <article>
                        <div class="lodging-item">
                            <h3>HC du lundi 13/01 au jeudi 16/01</h3>
                            <div class="lodging-item-content row justify-content-between mb-4">
                                <div class="lodgingOptions col-sm-7">
                                    <div class="row justify-content-between lodgingOption-item ">
                                        <div class="col-8">Nombre de place disponibles</div>
                                        <div class="col-4"><span class="lodgingOption-nbDispo">8</span></div>
                                    </div>
                                    <div class="row justify-content-between lodgingOption-item ">
                                        <div class="col-8"><span
                                                    class="lodgingOption-nbMax">Nombre maximun de places</span></div>
                                        <div class="col-4">25</div>
                                    </div>
                                    <div class="row justify-content-between lodgingOption-item ">
                                        <div class="col-8">Adresse</div>
                                        <div class="col-4"><span
                                                    class="address">4924 Bartlett Avenue</span></div>
                                    </div>
                                    <div class="row justify-content-between lodgingOption-item ">
                                        <div class="col-8">Coordinateur</div>
                                        <div class="col-4"><span class="lodgingOption-coordinator">Prénom Nom</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <img class="img-fluid thumbnail" src="img/house.jpg" alt="Construction house">
                                </div>

                            </div>
                            <button class="btn btn-primary"
                                    onclick="document.location.href = 'info_lodging.php?lodging_id=1';">Plus d'info ▶
                            </button>
                        </div>
                    </article>

                    <article>
                        <div class="lodging-item">
                            <h3>Porte de Rosières du samedi 28/12 au vendredi 03/01/2020</h3>
                            <div class="lodging-item-content row justify-content-between mb-4">
                                <div class="lodgingOptions col-sm-7">
                                    <div class="row justify-content-between lodgingOption-item ">
                                        <div class="col-8">Nombre de place disponibles</div>
                                        <div class="col-4"><span class="lodgingOption-nbDispo">10</span></div>
                                    </div>
                                    <div class="row justify-content-between lodgingOption-item ">
                                        <div class="col-8"><span
                                                    class="lodgingOption-nbMax">Nombre maximun de places</span></div>
                                        <div class="col-4">20</div>
                                    </div>
                                    <div class="row justify-content-between lodgingOption-item ">
                                        <div class="col-8">Adresse</div>
                                        <div class="col-4"><span
                                                    class="address">93 Deans Lane</span></div>
                                    </div>
                                    <div class="row justify-content-between lodgingOption-item ">
                                        <div class="col-8">Coordinateur</div>
                                        <div class="col-4"><span class="lodgingOption-coordinator">Prénom Nom</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <img class="img-fluid thumbnail" src="img/house.jpg" alt="Construction house">
                                </div>

                            </div>
                            <button class="btn btn-primary">Plus d'info ▶</button>
                        </div>
                    </article>
                </div>
            </div>
        </section>


    </main>

<?php
include_once "../include/footer.php"
?>