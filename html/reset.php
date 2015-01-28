<?php
require('oauth_config.php');
require('email.php');
require('mongo-connect.php');

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
        if($collection->remove(array('resolutionID'=>$_GET['resolutionID']))) echo true;
        /*$resolutions = json_decode(file_get_contents('../../resolutions.json'));
        foreach ($resolutions as $index => $resolution) {
            if ($resolution->resolutionID == $_GET['resolutionID']) {
                array_splice($resolutions, $index, 1);
                if (file_put_contents('../../resolutions.json', json_encode($resolutions)))
                    echo true;
                break;
            }
        }*/
    }
}

if(isset($_GET['resolutionID'])&&$_GET['action']=='cancel'){
    $query = array('requester.planningCenterID'=>$currentUser->id,'resolutionID'=>$_GET['resolutionID'],'isExpired'=>false,'isCancelled'=>false);
    $resolutions = [];
    $cursor = $collection->find();
    while($cursor->hasNext()){
        array_push($resolutions,$cursor->getNext());
    }
    if(count($resolutions)>0){
        if($collection->update($query,array('$set'=>array('isCancelled'=>true))))
            echo true;
    }else
        echo 'You cannot cancel resolved or expired requests';

    //$resolutions=json_decode(file_get_contents('../../resolutions.json'));
    /*foreach($resolutions as $index=>$resolution){
        if($resolution->resolutionID==$_GET['resolutionID']&&$resolution->requester->planningCenterID==$currentUser->id){
            if($resolution->isResolved==false&&$resolution->isExpired==false) {
                $resolution->isCancelled = true;
                $resolutions[$index] = $resolution;
                if (file_put_contents('../../resolutions.json', json_encode($resolutions))) {
                    echo true;
                    foreach($resolution->contacts as $person){
                        if($person->response==null){

                        }
                        //notifies people that haven't responded that they don't need to respond
                        //sendCancellationNotification('benferris2@gmail.com',$person->firstName,$resolution->position,$resolution->weekendDate);
                    }
                    break;
                }
            }else
                echo 'You cannot cancel resolved or expired requests.';
            break;
        }
    }*/
}
