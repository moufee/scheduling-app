<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Response Page</title>
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
require('email.php');
require('Person.php');
require('Resolution.php');
if(file_get_contents('/var/www/resolutions.json')){
    $resolutions = json_decode(file_get_contents('/var/www/resolutions.json'));
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
            /** @noinspection PhpUndefinedVariableInspection */
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
                            if(file_put_contents('/var/www/resolutions.json',json_encode($resolutions))) {
                                echo '<div class="alert alert-success"><h1>Your response has been received.</h1>
                                <h3>A scheduler has been notified to make the required changes.</h3></div>';
                                sendSchedulingInstructions('benferris2@gmail.com',$selectedResolution->requester->name,$selectedResolution->position,$selectedResolution->resolver->currentlyScheduledWeekend,$selectedResolution->resolver->name,$selectedResolution->position,$selectedResolution->weekendDate);
                                //sendResolutionNotification('krisr@gracechurchin.org',$selectedResolution->requester->name,$selectedResolution->position,$selectedResolution->resolver->currentlyScheduledWeekend,$selectedResolution->resolver->name,$selectedResolution->position,$selectedResolution->weekendDate);
                                sendPlainMessage('benferris2@gmail.com'/*$selectedResolution->requester->email*/,'Your request has been resolved','<p style="font-size:16px;font-family:Arial;">'.$selectedResolution->requester->firstName.',</p><p style="font-size:16px;">Your request to find a replacement for your scheduled weekend, '.$selectedResolution->weekendDate.', has been successfully resolved. You will be scheduled on <strong>'.$selectedResolution->resolver->currentlyScheduledWeekend.'</strong> instead of '.$selectedResolution->weekendDate.'. A scheduler has been notified to make these changes.</p>');
                                sendPlainMessage('benferris2@gmail.com','Response Received','<p style="font-size:16px;font-family:Arial;">'.$selectedResolution->resolver->firstName.',</p><p style="font-size:16px;font-family:Arial;">Your response has been received and you will now be scheduled on <strong>'.$selectedResolution->weekendDate.'</strong> instead of '.$selectedResolution->resolver->currentlyScheduledWeekend.'. A scheduler has been notified to make these changes.');
                                //send notification to contacted people
                                foreach($selectedResolution->contacts as $contact){
                                    if($contact->planningCenterID!=$selectedResolution->resolver->planningCenterID&&$contact->response==null) //do not send to resolver or person that has responded
                                    sendPlainMessage('benferris2@gmail.com','Request Resolved','<p style="font-size:16px;font-family:Arial;">'.$contact->firstName.',</p><p style="font-size:16px;font-family:Arial;">The request you received to fill '.$selectedResolution->position.' on '.$selectedResolution->weekendDate.' has been resolved. You no longer need to respond.');

                                }
                            }else {echo "Error saving file.";}

                        }elseif($_GET['response']=='no'){
                            $selectedResolution->contacts[$index]->response = 'no';
                            $resolutions[$selectedResolutionIndex]=$selectedResolution;
                            if(file_put_contents('/var/www/resolutions.json',json_encode($resolutions))) {
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
}
?>
</div>
</body>
</html>