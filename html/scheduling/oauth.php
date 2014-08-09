<?php
define('CONSUMER_KEY', 'ZtQ5fkQrfsKqgq7NJxCI');
define('CONSUMER_SECRET', 'Ga70aAu2iiolkqynBBcum5KPeHkOYtu3PgRAcriD');

// Obtain these keys at http://accesstoken.io
define('ACCESS_TOKEN_KEY', '2ukur0f8T9JJiPqigFzJ');
define('ACCESS_TOKEN_SECRET', 'BS0G4Z7XNycOzVo9rTBY8QY5KOE8QB5WynIwbZKF');

$oauth = new OAuth(CONSUMER_KEY, CONSUMER_SECRET);
$oauth->setToken(ACCESS_TOKEN_KEY, ACCESS_TOKEN_SECRET);
