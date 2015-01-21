<?php
require('oauth_config.php');
require('email.php');

error_reporting(E_ALL);
ini_set("display_errors", 1);

$oauth->fetch('https://planningcenteronline.com/me.json');
$currentUser = json_decode($oauth->getLastResponse());

$authorisedAdminIDs = array(987202,497948);

//check for requested action and authorised user
if(isset($_GET['resolutionID'])&&$_GET['action']=='delete') {
    if(!in_array($currentUser->id,$authorisedAdminIDs)){
        echo'You are not permitted to delete requests.';
    }else {
        $resolutions = json_decode(file_get_contents('../../resolutions.json'));
        foreach ($resolutions as $index => $resolution) {
            if ($resolution->resolutionID == $_GET['resolutionID']) {
                array_splice($resolutions, $index, 1);
                if (file_put_contents('../../resolutions.json', json_encode($resolutions)))
                    echo true;
                break;
            }
        }
    }
}

if(isset($_GET['resolutionID'])&&$_GET['action']=='cancel'){

    $resolutions=json_decode(file_get_contents('../../resolutions.json'));
    foreach($resolutions as $index=>$resolution){
        if($resolution->resolutionID==$_GET['resolutionID']&&$resolution->requester->planningCenterID==$currentUser->id){
            if($resolution->isResolved==false&&$resolution->isExpired==false) {
                $resolution->isCancelled = true;
                $resolutions[$index] = $resolution;
                if (file_put_contents('../../resolutions.json', json_encode($resolutions))) {
                    echo true;
                    foreach($resolution->contacts as $person){
                        if($person->response==null)
                        sendCancellationNotification('benferris2@gmail.com',$resolution->position,$resolution->weekendDate);
                    }
                    break;
                }
            }else
                echo 'You cannot cancel resolved or expired requests.';
            break;
        }
    }
}
