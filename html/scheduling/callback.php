<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
  require_once("oauth_config.php");

  $access_token = $oauth->getAccessToken("https://planningcenteronline.com/oauth/access_token");

  setcookie("oauth_token",        $access_token['oauth_token']);
  setcookie("oauth_token_secret", $access_token['oauth_token_secret']);

  header("Location: http://dev.floret.us/scheduling/home");
?>