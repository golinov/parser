<?php

use Predis\Client;
use Predis\Connection\ConnectionException;

function getDbCon()
{
    $dbh = new PDO('mysql:host='.\host.';dbname='.\db, \user, \pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

function getRedis()
{
    try {
        $redis = new Client();
        return $redis;
    } catch (ConnectionException $e) {
        echo 'Couldn\'t connected to Redis';
        echo $e->getMessage();
    }
}