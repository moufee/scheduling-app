<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// connect
$m = new MongoClient();

// select a database
$db = $m->scheduling;

// select a collection (analogous to a relational database's table)
$collection = $db->resolutions;

// add a record
$document = array( "resolutionID" => 1234, "isResolved" => false );
$collection->insert($document);

// add another record, with a different "shape"
$document = array( "resolutionID" => 6543, "isResolved" => true );
$collection->insert($document);

// find everything in the collection
$cursor = $collection->find();

// iterate through the results
foreach ($cursor as $document) {
    echo $document["resolutionID"] . "\n";
}