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
                            <textarea class="survey-form-control" placeholder="description"></textarea>
                        </div>
                    </div>


                </form>
            </div>

            <div class="survey-from-group">
                <div>

                </div>
            </div>
        </section>
    </main>

<?php
include_once "../include/footer.php"
?>