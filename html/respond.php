<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Respond</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        body{
            padding-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once('checkresolutions.php');
require_once('email.php');
require_once('Person.php');
require_once('Resolution.php');
require_once('mongo-connect.php');

$resolutions = [];
$query = array('resolutionID'=>$_GET['resolutionID']);
$resolution = $collection->findOne($query);
if($resolution){
    if($resolution['isResolved']){
        if($resolution['resolver']['planningCenterID']==$_GET['responderID'])
            echo '<h1 class="alert alert-warning">You have already responded "yes" to this request.</h1>';
        else
            echo '<h1 class="alert alert-warning">This position has already been filled.</h1>';
    }
    elseif($resolution['isExpired']) {
        echo '<h1 class="alert alert-danger">This request has expired.</h1>';
    }
    elseif($resolution['isCancelled']){
        echo '<h1 class="alert alert-danger">This request has been cancelled.</h1>';
    }
    else{
        foreach($resolution['contacts'] as $index => $contact){
            if($contact['planningCenterID']==$_GET['responderID']){
                if($_GET['response']=='yes'){
                    $contact['response'] = 'yes';
                    if($collection->update($query,array('$set' =>array('isResolved'=>true,'contacts.'.$index.'.response' =>'yes','resolver'=>$contact)))) {
                        echo '<div class="alert alert-success"><h1>Your response has been received.</h1>
                                <h3>A scheduler has been notified to make the required changes.</h3></div>';
                        //todo: send emails
                        //should send to Kris or scheduler
                        sendSchedulingInstructions('benferris2@gmail.com',$resolution['requester']['name'],$resolution['position'],$resolution['resolver']['currentlyScheduledWeekend'],$resolution['resolver']['name'],$resolution['position'],$resolution['weekendDate']);
                        //sendResolutionNotification('krisr@gracechurchin.org',$resolution->requester->name,$resolution['position'],$resolution['resolver']['currentlyScheduledWeekend'],$resolution->resolver->name,$resolution['position'],$resolution['weekendDate']);
                        //goes to selectedResolution->requester->email, notifies requester of his/her new weekend
                        sendPlainMessage('benferris2@gmail.com','Your request has been resolved','<p style="font-size:16px;font-family:Arial;">'.$resolution['requester']['firstName'].',</p><p style="font-size:16px;">Your request to find a replacement for your scheduled weekend, '.$resolution['weekendDate'].', has been successfully resolved. You will be scheduled on <strong>'.$resolution['resolver']['currentlyScheduledWeekend'].'</strong> instead of '.$resolution['weekendDate'].'. A scheduler has been notified to make these changes.</p>');
                        //should send to $resolution->resolver->email, notifies resolver of when their new weekend is
                        sendPlainMessage('benferris2@gmail.com','Response Received','<p style="font-size:16px;font-family:Arial;">'.$resolution['resolver']['firstName'].',</p><p style="font-size:16px;font-family:Arial;">Your response has been received and you will now be scheduled on <strong>'.$resolution['weekendDate'].'</strong> instead of '.$resolution['resolver']['currentlyScheduledWeekend'].'. A scheduler has been notified to make these changes.');
                        //send notification to contacted people that have not responded
                        foreach($resolution['contacts'] as $contactInner) {
                            if ($contactInner['planningCenterID'] != $resolution['resolver']['planningCenterID'] && $contactInner['response'] == null) //do not send to resolver or person that has responded
                                //send to $contactInner->email
                                sendPlainMessage('benferris2@gmail.com', 'Request Resolved', '<p style="font-size:16px;font-family:Arial;">' . $contact->firstName . ',</p><p style="font-size:16px;font-family:Arial;">The request you received to fill ' . $resolution['position'] . ' on ' . $resolution['weekendDate'] . ' has been resolved. You no longer need to respond.');
                        }
                    }
                }elseif($_GET['response']=='no'){
                    if($collection->update($query,array('$set'=>array('contacts.'.$index.'.response' =>'no')))){
                        echo '<div class="alert alert-success"><h1>Your response has been received.</h1>
                                      <h3>You may change your response at any time by clicking the "yes" link in the email you received.</h3></div>';
                        //todo: email if all responded no
                        $resolution['contacts'][$index]['response'] = 'no';
                        $numberOfPeople = count($resolution['contacts']);
                        $numberRespondingNo=0;
                        foreach($resolution['contacts'] as $index2=>$contactedPerson2){
                            if($contactedPerson2['response']=='no'){
                                $numberRespondingNo++;
                            }
                        }
                        if($numberRespondingNo==$numberOfPeople) {
                            //this message goes to Kris
                            sendPlainMessage('benferris2@gmail.com', 'Problem Resolving Scheduling Conflict', '<p style="font-size:16px;font-family:Arial;">All contacted people have responded "no" to '.$selectedResolution['requester']['name'].'\'s request to fill '.$selectedResolution['position'].' on '.$selectedResolution['weekendDate'],'</p>');
                        }

                    }
                }
            }
        }
    }

}else {
    echo '<div class = "alert alert-danger"><h1>Request Not Found</h1><h2>The request has likely been deleted from the system.</h2></div>';
}
/*if(file_get_contents('../../resolutions.json')){
    $resolutions = json_decode(file_get_contents('../../resolutions.json'));
    if(isset($_GET['resolutionID'])&&isset($_GET['responderID'])&&isset($_GET['response'])){
        $isFound = false;
        foreach($resolutions as $index=>$resolution) {
            if ($resolution->resolutionID == $_GET['resolutionID']) {
                $selectedResolution = $resolution;
                $selectedResolutionIndex = $index;
                $isFound = true;
            }
        }
        if($isFound){
            if($selectedResolution->isResolved){
                if($selectedResolution->resolver->planningCenterID==$_GET['responderID']) {
                    echo '<h1 class="alert alert-warning">You have already responded "yes" to this request.</h1>';
                }else
                    echo '<h1 class="alert alert-warning">This position has already been filled.</h1>';
            }elseif($selectedResolution->isExpired){
                echo '<h1 class="alert alert-danger">This request has expired.</h1>';
            }
            elseif($selectedResolution->isCancelled){
                echo '<h1 class="alert alert-danger">This request has been cancelled.</h1>';
            }
            else{
                foreach($selectedResolution->contacts as $index=>$contactedPerson){
                    if($contactedPerson->planningCenterID == $_GET['responderID']){
                        if($_GET['response']=='yes') {
                            $selectedResolution->isResolved = true;
                            $selectedResolution->resolver = $contactedPerson;
                            $selectedResolution->contacts[$index]->response = 'yes';
                            $resolutions[$selectedResolutionIndex]=$selectedResolution;
                            if(file_put_contents('../../resolutions.json',json_encode($resolutions))) {
                                echo '<div class="alert alert-success"><h1>Your response has been received.</h1>
                                <h3>A scheduler has been notified to make the required changes.</h3></div>';
                                //should send to Kris or scheduler?
                                sendSchedulingInstructions('benferris2@gmail.com',$selectedResolution->requester->name,$selectedResolution->position,$selectedResolution->resolver->currentlyScheduledWeekend,$selectedResolution->resolver->name,$selectedResolution->position,$selectedResolution->weekendDate);
                                //sendResolutionNotification('krisr@gracechurchin.org',$selectedResolution->requester->name,$selectedResolution->position,$selectedResolution->resolver->currentlyScheduledWeekend,$selectedResolution->resolver->name,$selectedResolution->position,$selectedResolution->weekendDate);
                                //goes to selectedResolution->requester->email
                                sendPlainMessage('benferris2@gmail.com','Your request has been resolved','<p style="font-size:16px;font-family:Arial;">'.$selectedResolution->requester->firstName.',</p><p style="font-size:16px;">Your request to find a replacement for your scheduled weekend, '.$selectedResolution->weekendDate.', has been successfully resolved. You will be scheduled on <strong>'.$selectedResolution->resolver->currentlyScheduledWeekend.'</strong> instead of '.$selectedResolution->weekendDate.'. A scheduler has been notified to make these changes.</p>');
                                //should send to $selectedResolution->resolver->email, notifies resolver of when their new weekend is
                                sendPlainMessage('benferris2@gmail.com','Response Received','<p style="font-size:16px;font-family:Arial;">'.$selectedResolution->resolver->firstName.',</p><p style="font-size:16px;font-family:Arial;">Your response has been received and you will now be scheduled on <strong>'.$selectedResolution->weekendDate.'</strong> instead of '.$selectedResolution->resolver->currentlyScheduledWeekend.'. A scheduler has been notified to make these changes.');
                                //send notification to contacted people
                                foreach($selectedResolution->contacts as $contact){
                                    if($contact->planningCenterID!=$selectedResolution->resolver->planningCenterID&&$contact->response==null) //do not send to resolver or person that has responded
                                    //send to $contact->email
                                    sendPlainMessage('benferris2@gmail.com','Request Resolved','<p style="font-size:16px;font-family:Arial;">'.$contact->firstName.',</p><p style="font-size:16px;font-family:Arial;">The request you received to fill '.$selectedResolution->position.' on '.$selectedResolution->weekendDate.' has been resolved. You no longer need to respond.');

                                }
                            }else {echo "Error saving file.";}

                        }elseif($_GET['response']=='no'){
                            $selectedResolution->contacts[$index]->response = 'no';
                            $resolutions[$selectedResolutionIndex]=$selectedResolution;
                            if(file_put_contents('../../resolutions.json',json_encode($resolutions))) {
                                echo '<div class="alert alert-success"><h1>Your response has been received.</h1>
                                      <h3>You may change your response at any time by clicking the "yes" link in the email you received.</h3></div>';
                                //email if all people have responded "no"
                                $numberOfPeople = count($selectedResolution->contacts);
                                $numberRespondingNo=0;
                                foreach($selectedResolution->contacts as $index2=>$contactedPerson2){
                                    if($selectedResolution->contacts[$index2]->response=='no'){
                                        $numberRespondingNo++;
                                    }
                                }
                                if($numberRespondingNo==$numberOfPeople) {
                                    //this message goes to Kris
                                    sendPlainMessage('benferris2@gmail.com', 'Problem Resolving Scheduling Conflict', '<p style="font-size:16px;font-family:Arial;">All contacted people have responded "no" to '.$selectedResolution->requester->name.'\'s request to fill '.$selectedResolution->position.' on '.$selectedResolution->weekendDate,'</p>');
                                }
                            }else echo "Error saving File";

                        }
                    }
                }
            }
        }else {echo '<div class = "alert alert-danger"><h1>Resolution Not Found</h1><h2>The resolution has likely been deleted from the system.</h2></div>';}
    }else{echo '<p class="alert alert-danger">Invalid Link</p>';}
}
else{
    echo 'error, unable to open file';
}*/
?>
</div>
</body>
</html>