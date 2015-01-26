<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
//allow for email sending and database access
require_once('email.php');
require_once('mongo-connect.php');
$resolutions = array();
$currentDate = new DateTime();
$currentTime = $currentDate->getTimestamp();
echo $currentTime;
//find resolutions that are not resolved, canceled, or have already been marked as expired, but have passed their expiration date
$query = array('ixExpired'=>false,'isResolved'=>false,'isCancelled'=>false,'expirationDate_unix'=>array('$lte'=>$currentTime));
$cursor = $collection->find($query);
while($cursor->hasNext()){
    var_dump($cursor->getNext());
    array_push($resolutions,$cursor->getNext());
}
echo count($resolutions);
if(count($resolutions)>0){
    $collection->update($query,array('$set'=>array('isExpired'=>true)));
    foreach($resolutions as $index=>$resolution){
        sendPlainMessage('benferris2@gmail.com'/*$resolution->requester->email*/, 'Scheduling request has expired.', '<p style="font-size:16px;font-family:Arial;">'.$resolution['requester']['firstName'].',</p><p style="font-size:16px; font-family:Arial;">Your request to find a replacement for ' . $resolution['weekendDate'] . ' has expired. Please contact Kris Rinas as krisr@gracechurchin.org and he will work with you to find a trade.</p>');
        sendPlainMessage('benferris2@gmail.com'/*kris*/, 'Scheduling request has expired.', '<p style="font-size:16px; font-family:Arial;">'.$resolution['requester']['name'].'\'s request to find a replacement for ' . $resolution['weekendDate'] . ' has expired. '.$resolution['requester']['firstName'].' has been instructed to contact you.</p>');

    }
}

