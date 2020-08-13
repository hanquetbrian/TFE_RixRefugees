<?php
require_once "../php_function/db_connection.php";

$sql = "
    SELECT id, name, password
    FROM rix_refugee.User
    where id = ?;
    ";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([$AUTH->getUserId()]);
$user = $sth->fetchAll(PDO::FETCH_ASSOC)[0];

$editOldPassword = false;
if(isset($user['password'])) {
    $editOldPassword = true;
}

$errors = [];
if(isset($_POST['submit'])) {

    if(!empty($_POST['newPassword'])) {
        // Check if a password is already present and ask a confirm of it if it is
        $correctPassword = true;
        if($editOldPassword) {
            $correctPassword = password_verify($_POST['oldPassword'], $user['password']);
        }

        if($correctPassword) {
            $password = $_POST['newPassword'];
            if(strlen($password) < 8) {
                array_push($errors, 'Mot de passe trop court');
            } elseif (!preg_match("#[0-9]+#", $password)) {
                array_push($errors, 'Le mot de passe doit contenir au moins un chiffre');
            } elseif (!preg_match('#[a-zA-Z]+#', $password)) {
                array_push($errors, 'Le mot de passe doit contenir au moins une lettre');
            }

            if(empty($errors)) {
                $id = $AUTH->getUserId();
                $hashPassword = password_hash($password, PASSWORD_BCRYPT);
                $sql = "
            UPDATE rix_refugee.User SET password = :password WHERE id = :id
            ";

                $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $sth->bindParam(':id', $id);
                $sth->bindParam(':password', $hashPassword);

                $sth->execute();
                $_SESSION['msg'] = 'Votre mot de passe a bien été enregistré.';
                header('location:/edit_user');
                exit(0);
            }
        } else {
            array_push($errors, 'Le mot de passe actuel que vous avez indiqué n\'est pas correcte');
        }
    }
}

?>


<main>
    <section>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1>Modification du mot de passe</h1>
            </div>
            <hr class="headerSep">
        </div>

        <div class="container">
            <div class="listLodging">
                <form id="formPassword" action="#" method="post">
                    <div id="msg">
                        <?php
                            if(!empty($errors)) {
                                foreach ($errors as $error) {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
  '. $error .'
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>';
                                }
                            }
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                        if ($editOldPassword) {
                            echo '<label for="oldPassword">Mot de passe actuel: </label> <input class="form-control" id="oldPassword" type="password" name="oldPassword">';
                        }
                        ?>

                        <label for="newPassword">Nouveau mot de passe: </label> <input class="form-control" id="newPassword" type="password" name="newPassword">
                        <label for="confirmNewPassword">Confirmer le nouveau mot de passe: </label> <input class="form-control" id="confirmNewPassword" type="password">
                    </div>
                    <input class="btn btn-primary" type="submit" value="Confirmer" name="submit">
                </form>

            </div>
        </div>
    </section>
</main>

<script>
    $('#formPassword').submit(function (e) {
        let newPassword = $('#newPassword');
        let confirmNewPassword = $('#confirmNewPassword');
        if(newPassword.val() !== confirmNewPassword.val()) {
            newPassword.addClass('form-error');
            confirmNewPassword.addClass('form-error');
            $('#error_msg').append('<div class="alert alert-danger alert-dismissible fade show" role="alert">\n' +
                'Les nouveaux mots de passe ne correspondent pas.\n' +
                '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                '    <span aria-hidden="true">&times;</span>\n' +
                '  </button>\n' +
                '</div>');
            return false;
        }

        if(newPassword.val().length < 8) {
            newPassword.addClass('form-error');
            confirmNewPassword.addClass('form-error');
            $('#msg').append('<div class="alert alert-danger alert-dismissible fade show" role="alert">\n' +
                'Nouveau mot de passe est trop court.\n' +
                '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                '    <span aria-hidden="true">&times;</span>\n' +
                '  </button>\n' +
                '</div>');
            return false;
        }
    });
</script>