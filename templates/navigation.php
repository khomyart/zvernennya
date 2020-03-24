<?php
$currentScript = basename($_SERVER['SCRIPT_FILENAME']);

$contactsCss = $currentScript == 'contacts.php' ? 'active' : '';
$usersCss = $currentScript == 'users.php' ? 'active' : '';

if ($_POST['logout_button']) {
    session_destroy();
    header('location: index.php');
    die();
}

?>

<div class="container-fluid">
    <div class="row">
    <div class="col">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Реєстрація звернень</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown" >
                <ul class="navbar-nav ml-auto">
                    <?php if($_SESSION['auth']['type'] == 'administrator') { ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $contactsCss; ?>" href="./contacts.php">Contacts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $usersCss; ?>" href="./users.php">Users</a>
                        </li>
                    <?php } ?>
                </ul>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle"
                            type="button"
                            id="dropdownMenuButton"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                    >
                        <?= $_SESSION['auth']['first_name']?> <?= $_SESSION['auth']['last_name']?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <form action="" method="post">
                            <button type="submit"
                                    class="dropdown-item"
                                    name="logout_button"
                                    value="logout"
                            >
                                Вийти
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    </div>
</div>
