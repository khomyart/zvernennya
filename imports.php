<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include './config.php';
include './lib/db.php';
include './lib/users.php';
include './lib/appeals.php';

initSession();
$isIndex = basename($_SERVER['SCRIPT_FILENAME']) == 'index.php';
$isJoinContactList =  basename($_SERVER['SCRIPT_FILENAME']) == 'joinContactList.php';

if ($isJoinContactList) {
    $_SESSION['auth']['nickname'] = 'Temp user';
}

if (isAuth()) {
    // User is authorized
    if ($isIndex) {
        // Current file is index.
        // No need to show auth form.
        // Let's show phones page
        header("Location: appeals.php");
        die();
    }
} else {
    // User is not authorized
    if (!$isIndex) {
        // Current file is not index.
        // We need to redirect user to index page
        header("Location: index.php");
        die();
    }
}

/*var_dump($_POST);
echo '<br/>';
var_dump($_SESSION);*/
