<?php

$db = $config['db'];

try {
    $pdo = new PDO("mysql:host={$db['server']};dbname={$db['db']}; charset=utf8;", $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Something went wrong (');
}

/**
 * @param $query string SQL query
 * @param $params array Array of query parameters
 * @return bool|PDOStatement
 */
function performQuery($query, $params)
{
    global $pdo;

    $statement = $pdo->prepare($query);
    $statement->execute($params);

    return $statement;
}

/**
 * Returns all rows for a given $query
 *
 * @param $query
 * @return array
 */
function getAllRows($query, $params = [])
{
    $statement = performQuery($query, $params);

    if ($statement === false) {
        return [];
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Returns a first row for a given $query
 *
 * @param $query
 * @param $params
 * @return array
 */
function getRow($query, $params = [])
{
    $statement = performQuery($query, $params);

    if ($statement === false) {
        return [];
    }

    return $statement->fetch(PDO::FETCH_ASSOC);
}

/**
 * Executes SQL query (e.g. INSERT, DELETE, UPDATE)
 *
 * @param $query
 * @param $params
 * @return bool|int
 */
function execQuery($query, $params) {
    $statement = performQuery($query, $params);

    return $statement === false ? false : $statement->rowCount();
}
