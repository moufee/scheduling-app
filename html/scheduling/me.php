<?php
  require_once("oauth_config.php");
  $oauth->fetch("https://planningcenteronline.com/me.json");
  $person = json_decode($oauth->getLastResponse());
  $oauth->fetch("https://planningcenteronline.com/organization.json");
  $organization = $oauth->getLastResponse();
  $oauth->fetch('https://www.planningcenteronline.com/service_types/42921/plans.json?all=true');
  $plans = $oauth ->getLastResponse();
  require_once('email.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <title>Grace Church Scheduling</title>
</head>
<body>
    <div class="container">
        <h3>
            Hello <?php echo $person->first_name; ?>
        </h3>
        <p>
    <?php //echo $person->contact_data->email_addresses[0]->address; ?>
            <?php //sendMessage($plans)  ?>
        </p>
    </div>
</body>
</html>
