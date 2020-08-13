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
            <form>
                <input type="text" id="login" name="login" placeholder="email">
                <input type="text" id="password" name="login" placeholder="mot de passe">
                <input type="submit" value="Connecter">
            </form>

        </div>
    </div>
</div>