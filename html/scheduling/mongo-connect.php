<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$connection = new MongoClient('mongodb://ben:moufxz@ds031681.mongolab.com:31681/heroku_app33381743');
$db = $connection->selectDB('heroku_app33381743');
$collection = $db->resolutions;
$doc = array('I am testing mongodb'=>true);
$collection->insert($doc);