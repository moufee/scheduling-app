<?php
  define("CONSUMER_KEY",    "ZtQ5fkQrfsKqgq7NJxCI");
  define("CONSUMER_SECRET", "Ga70aAu2iiolkqynBBcum5KPeHkOYtu3PgRAcriD");
  define("CALLBACK_URL",    "http://beta.floret.us/scheduling/callback.php");

  // Don't change this. This needs to run at the beginning of every request.
  $oauth = new OAuth(CONSUMER_KEY, CONSUMER_SECRET);
  $oauth->setToken($_COOKIE["oauth_token"], $_COOKIE["oauth_token_secret"]);
?>