<?php

use Predis\Client;
use Predis\Connection\ConnectionException;

function getDbCon()
{
    $dbh = new PDO('mysql:host='.\host.';dbname='.\db, \user, \pass);
    return $dbh;
}

function getRedis()
{
    try {
        $redis = new Client();
    } catch (ConnectionException $e) {
        echo 'Couldn\'t connected to Redis';
        echo $e->getMessage();
    }
    return $redis;
}