<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('email.php');
$resolutions = json_decode(file_get_contents('../../resolutions.json'));


$currentDate = new DateTime();
$currentDate = $currentDate->getTimestamp();

foreach($resolutions as $index=>$resolution){
//check resolution
    if($resolution->expirationDate_unix <= $currentDate && $resolution->isExpired == false&&$resolution->isResolved==false&&$resolution->isCancelled==false) {
        sendPlainMessage('benferris2@gmail.com'/*$resolution->requester->email*/, 'Scheduling request has expired.', '<p style="font-size:16px;font-family:Arial;">'.$resolution->requester->firstName.',</p><p style="font-size:16px; font-family:Arial;">Your request to find a replacement for ' . $resolution->weekendDate . ' has expired. Please contact Kris Rinas as krisr@gracechurchin.org and he will work with you to find a trade.</p>');
        sendPlainMessage('benferris2@gmail.com'/*$resolution->requester->email*/, 'Scheduling request has expired.', '<p style="font-size:16px; font-family:Arial;">'.$resolution->requester->name.'\'s request to find a replacement for ' . $resolution->weekendDate . ' has expired. '.$resolution->requester->firstName.' has been instructed to contact you.</p>');
        $resolution->isExpired = true;
        $resolutions[$index] = $resolution;
        file_put_contents('../../resolutions.json', json_encode($resolutions));
    }
}

