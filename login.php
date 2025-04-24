<?php
//a = 86f7e437faa5a7fce15d1ddcb9eaeaea377667b8
$logged = false;
if (isset($_POST['action']) && $_POST['action'] == "login") {
    if (isset($_POST['login']) && isset($_POST['pass']) && sha1($_POST['login']) == "86f7e437faa5a7fce15d1ddcb9eaeaea377667b8" &&  sha1($_POST['pass']) == "86f7e437faa5a7fce15d1ddcb9eaeaea377667b8" ) {
        $logged = true;
        $_SESSION['bamColletifOK'] = TRUE;
    }
}
if ($logged == false) {
?>
<form method="post" action="">
    <label for="login">login :</label><input id="login" type="text" name="login" />
    <label for="pass">pass :</label><input id="pass" type="password" name="pass" />
    <input id="action" type="hidden" name="action" value="login" />
    <input type="submit" value="Valider" />
</form>
<?php
} else {
    include 'manage.php';
}
?>