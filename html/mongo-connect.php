<?php
$connection = new MongoClient('mongodb://ben:moufxz@ds031681.mongolab.com:31681/heroku_app33381743');
$db = $connection->selectDB('heroku_app33381743');
$collection = $db->resolutions;