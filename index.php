<?php
include './imports.php';

$login = '';
$password = '';

$showFailedAuthMessage = false;

if ($_POST['loginButton']) {
    $login = $_POST['login'] ?: '';
    $password = $_POST['password'] ?: '';

    if (userLogIn($login, $password)) {
        // Successful authorization. Let's go to phones page )
        header("Location: appeals.php");
        die();
    } else {
        $showFailedAuthMessage = true;
    }
}

?>

<?php include './templates/header.php'; ?>

<?php if ($showFailedAuthMessage) { ?>
    <div class="alert alert-danger" role="alert">
       Неправильне ім'я користувача, або пароль
    </div>
<?php } ?>

<div class="d-flex justify-content-center vh-100">
    <div class="align-self-center">
        <div class="container p-3 bg-white rounded border border-primary">
            <div class="row">
                <div class="col-12">
                    <form action="index.php" method="post">
                        <div class="form-group">
                            <input
                                type="text"
                                name="login"
                                class="form-control"
                                id="exampleInputEmail1"
                                aria-describedby="emailHelp"
                                placeholder="Ім'я користувача"
                                value="<?= $login ?>"
                            >
                        </div>
                        <div class="form-group">
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                id="exampleInputPassword1"
                                placeholder="Пароль"
                                value="<?= $password ?>"
                            >
                        </div>
                        <button type="submit" class="btn btn-primary btn-block"
                                name="loginButton" value="sent"
                                style="border-radius: 5px 5px 5px 5px">
                            Авторизуватись
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php //include './templates/footer.php'; ?>

