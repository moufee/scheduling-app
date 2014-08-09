<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

define('CONSUMER_KEY', 'ZtQ5fkQrfsKqgq7NJxCI');
define('CONSUMER_SECRET', 'Ga70aAu2iiolkqynBBcum5KPeHkOYtu3PgRAcriD');

// Obtain these keys at http://accesstoken.io
define('ACCESS_TOKEN_KEY', '2ukur0f8T9JJiPqigFzJ');
define('ACCESS_TOKEN_SECRET', 'BS0G4Z7XNycOzVo9rTBY8QY5KOE8QB5WynIwbZKF');


$oauth = new OAuth(CONSUMER_KEY, CONSUMER_SECRET);
$oauth->setToken(ACCESS_TOKEN_KEY, ACCESS_TOKEN_SECRET);

try {

    //$oauth->fetch("https://services.planningcenteronline.com/people/987202.json", '{"first_name":"Ben- Testing"}', OAUTH_HTTP_METHOD_PUT, array('Content-Type'=>'application/json'));
    //$oauth->fetch("https://services.planningcenteronline.com/plans/13959414.json", '{"plan_people[0]":{"person_name":"Ben Ferris","position":"Camera 2"}}', OAUTH_HTTP_METHOD_PUT, array('Content-Type'=>'application/json'));
}
catch(Exception $ER){
    $ER->getMessage();
}
    $result = $oauth->getLastResponse();
    print_r( $result);
