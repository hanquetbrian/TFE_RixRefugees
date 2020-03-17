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

        <!-- Modal -->
        <div class="modal fade" id="addLodging" tabindex="-1" role="dialog" aria-labelledby="addLodgingTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLodgingTitle">Ajouter un hébergement</h5>
                    </div>
                    <div class="modal-body">
                        <form id="addLodgingForm">
                            <div class="form-group">
                                <div class="btn-image-picker">
                                    <img src="" alt="preview_file" id="previewFile" class="thumbnail">
                                    <label for="upload-photo"><i class="fas fa-images"></i></label>
                                    <input type="file" name="photo" id="upload-photo" class="d-none" accept="image/*">
                                </div>

                                <label for="inputLodgingName">Nom de l'hébergement:</label>
                                <input type="text" class="form-control" id="inputLodgingName" required>
                                <label for="inputLodgingDateFrom">Date Début de l'hébergement:</label>
                                <input type="date" class="form-control" id="inputLodgingDateFrom" required>
                                <label for="inputLodgingDateTo">Date fin de l'hébergement:</label>
                                <input type="date" class="form-control" id="inputLodgingDateTo" required>
                                <label for="inputMaxPlaces">Nombre maximun de places: </label>
                                <input type="number" class="form-control" id="inputMaxPlaces" required>
                                <label for="inputAddress">Adresse:</label>
                                <input type="text" class="form-control" id="inputAddress">

                                <div id="listEquipments">
                                    <label for="inputListEquipments">Liste des équipements:</label>
                                    <ul class="list-group">
                                        <li class="list-group-item"><input type="text" id="inputListEquipments" placeholder="Equipement"></li>
                                    </ul>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="addLodgingButton">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <section>
            <div class="container">
                <div class="listLodging">
                    <article>
                        <button class="btn btn-secondary d-block w-100" data-toggle="modal" data-target="#addLodging">
                            Ajouter un hébergement
                        </button>

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