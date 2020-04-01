<?php
include_once "../include/header.php";

?>

    <main>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1>Création d'un sondage</h1>
            </div>

            <hr class="headerSep">
        </div>

        <section>
            <div class="container">
                <form>
                    <div class="survey-from-group survey-form-header">
                        <div>
                            <input class="survey-form-control survey-form-title" value="Sans titre" placeholder="Titre du sondage">
                        </div>

                        <div>
                            <textarea class="survey-form-control" placeholder="description..."></textarea>
                        </div>
                    </div>

                    <div class="survey-from-group">
                        <div id="listOption">
                        </div>
                        <div>
                            <input class="survey-form-check" type="checkbox" disabled>
                            <input class="survey-form-control survey-form-check-text" type="text" placeholder="Ajouter une option">
                        </div>
                    </div>
                    <input class="btn btn-primary" type="submit" value="Enregistrer">
                </form>
            </div>
        </section>
    </main>

<?php
include_once "../include/footer.php"
?>