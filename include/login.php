<?php
if(isset($_POST['submit'])) {
    if(!empty($_POST['email']) && !empty($_POST['password'])) {
        if(!$AUTH->connectWithPassword($_POST['email'], $_POST['password'], $dbh, $config)) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
  L\'email ou le mot de passe est incorrect.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>';
        }
    }
}
?>

<script>
    $().ready(function () {
        let formContent = $('#standard_login');
        formContent.hide();
        $('#more_logging_option').click(function (e) {
            formContent.slideToggle();
        });
    })

</script>

<div class="wrapper">
    <div id="formContent">
        <div id="connect">
            <a href="/connect_facebook" class="fb btn">
                <i class="fab fa-facebook"></i> Connecter avec Facebook
            </a>
        </div>

        <p id="more_logging_option">Connecter sans Facebook <i class="fas fa-sort-down"></i></p>
        <hr>
        <div id="standard_login">

            <!-- Login Form -->
            <form action="#" method="post">
                <input class="form-input" type="email" id="login" name="email" placeholder="email">
                <input class="form-input" type="password" id="current-password" name="password" placeholder="mot de passe">
                <input type="submit" value="Connecter" name="submit">
            </form>

        </div>
    </div>
</div>