<?php

/**
 * Initiation of user session
 */
function initSession()
{
    session_start();
}

/**
 * Checks if user authorized:
 * Function returns TRUE if user is authorized, otherwise - FALSE
 *
 * @return bool
 */
function isAuth()
{
    return isset($_SESSION['auth']);
}

/**
 * Checks if authorized user is administrator
 * function returns TRUE if user is administrator, otherwise - FALSE
 *
 * @return bool
 */
function isAuthAdmin()
{
    return isset($_SESSION['auth']) && ($_SESSION['auth']['type'] == 'administrator') ? true : false;
}

/**
 * Performs user authorization action
 *
 * @param $login
 * @param $password
 * @return bool
 */
function userLogIn($login, $password)
{
    $data = getRow(
        'SELECT * FROM `users` WHERE `username` = :login',
        [
            'login' => $login,
        ]
    );

    if (empty($data) || !password_verify($password, $data['password'])) {
        return false;
    }

    $_SESSION['auth'] = [
        'id' => $data['id'],
        'username' => $data['username'],
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name']
    ];

    return true;
}

