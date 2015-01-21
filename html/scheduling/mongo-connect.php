<?php
$connection = new MongoClient('mongodb://ben:moufxz@ds031681.mongolab.com:31681');
$db = $connection->heroku_app33381743;
$collection = $db->resolutions;
$doc = array('I am testing mongodb'=>true);
$collection->insert($doc);