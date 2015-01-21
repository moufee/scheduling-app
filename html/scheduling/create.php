<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ben
 * Date: 30/05/2014
 * Time: 18:04
 */

//error_reporting(E_ALL);
//ini_set("display_errors", 1);
function error_handler($errno, $errstr, $errfile, $errline ) {
    sendPlainMessage('benferris2@gmail.com','Scheduling Error Report','Error Message: '.$errstr.'<br><br>On line '.$errline.'<br><br>In File '.$errfile);
    echo 'An error has occurred. The developer has been notified.';
    die();
    //throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("error_handler");

include('email.php');

define('CONSUMER_KEY', 'ZtQ5fkQrfsKqgq7NJxCI');
define('CONSUMER_SECRET', 'Ga70aAu2iiolkqynBBcum5KPeHkOYtu3PgRAcriD');

// Obtain these keys at http://accesstoken.io
define('ACCESS_TOKEN_KEY', 'I9MsYDyPFhjpcd3NvmJD');
define('ACCESS_TOKEN_SECRET', 'QNQQ7MF2iNpJUayzR7aOpQcXc8xKaaLCLysTk93k');

$oauth = new OAuth(CONSUMER_KEY, CONSUMER_SECRET);
$oauth->setToken(ACCESS_TOKEN_KEY, ACCESS_TOKEN_SECRET);

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$email = $request->email;

require('Person.php'); //defines Person class
require('Resolution.php'); //defines Resolution class



//fetches the details of the plan to be resolved
function getPlanToResolve(){

    global $oauth, $request;
    $oauth->fetch('https://www.planningcenteronline.com/plans/'.$request->planID.'.json');
    $planJSON=$oauth->getLastResponse();
    $planToResolve = json_decode($planJSON);
    return $planToResolve;
}

//fetches list of all plans for 146th street service type

function getPlansOverview($serviceTypeID){
    global $oauth;
    $oauth->fetch('https://www.planningcenteronline.com/service_types/'.$serviceTypeID.'/plans.json');
    $plans = json_decode($oauth ->getLastResponse());
    return $plans;
}

function getNeededPosition($planToResolve, $requesterID){
    $people = $planToResolve->plan_people;
//finds the name of the needed position
//todo: handle one person with multiple positions
    $position = "not found";
    foreach($people as $value){
        if($value->person_id==$requesterID){
            $position = $value->position;
        }
    }
return $position;
}


//finds people to contact from checked weekends only and adds them to array
function getPeopleToContact($position){
    global $request, $oauth;
    $peopleToContact = [];
    $peopleToContactIDs = [];
    foreach($request->checkedPlans as $id){
        $oauth->fetch('https://www.planningcenteronline.com/plans/'.$id.'.json');
        $planJSON=$oauth->getLastResponse();
        $plan = json_decode($planJSON);
        if($position=='Camera 1'||$position=='Camera 2'||$position=='Camera 3'||$position=='Camera 4') {
            foreach ($plan->plan_people as $person) {
                if (($person->position == 'Camera 1' || $person->position == 'Camera 2' || $person->position == 'Camera 3' || $person->position == 'Camera 4')) {
                    array_push($peopleToContact, new Person($person->person_id, $plan->dates, $person->position));
                    array_push($peopleToContactIDs, $person->person_id);

                }
            }
        }else {
            foreach ($plan->plan_people as $person) {

                if ($person->position == $position && $person->person_id != $request->userID) {
                    array_push($peopleToContact, new Person($person->person_id, $plan->dates, $person->position));
                    array_push($peopleToContactIDs,$person->person_id);
                }
            }
        }
    }
    return $peopleToContact;
}


//fills remaining information in peopleToContact array using the planningCenterID property of each person to contact object in the array
/*function fillPeopleToContact($peopleToContact){
    global $oauth;
    foreach($peopleToContact as $person){
        $oauth->fetch('https://www.planningcenteronline.com/people/'.$person->planningCenterID.'.json');
        $currentPersonPCObject=json_decode($oauth->getLastResponse());
        $person->name = $currentPersonPCObject->name;
        $person->firstName = $currentPersonPCObject->first_name;
        $person->lastName = $currentPersonPCObject->last_name;
        $person->email = $currentPersonPCObject->contact_data->email_addresses[0]->address;
    }
    return $peopleToContact;
}*/



function verifyCreationTime($planToResolve){
    $cutoffInterval = new DateInterval('P3D'); //must be submitted at least 3 days before the upcoming weekend
    $currentDate = new DateTime('now');


    $time = strtotime($planToResolve->sort_date);
    $date = new DateTime();
    $date->setTimestamp($time);
    $cutoffDate = $date->sub($cutoffInterval);
    $cutoffDate->setTime(12,0,0);               //cutoff is at noon
    //returns true if the current date is before the cutoff date, otherwise false
    if($cutoffDate >= $currentDate)
        return true;
    else
        return false;


}

//calculates the time at which the request expires
function calculateExpirationDate($planToResolve){
    $currentDate = new DateTime();

    $planTime = strtotime($planToResolve->sort_date);
    $planDate = new DateTime();
    $planDate->setTimestamp($planTime);
    $planDate->setTime(12,0);                   //noon switchover to 24 hr expiration time
    $difference = $planDate->diff($currentDate);
    if($difference->days<7) {
        $expirationInterval = new DateInterval('P1D'); //P1D - one day to respond if submitted less than a week in advance

    }
    else {
        $expirationInterval = new DateInterval('P3D'); //P3D - a period of 3 days to respond if submitted more than a week in advance
    }
    $expirationDate = $currentDate->add($expirationInterval);
    return $expirationDate;
}

//creates the resolution object and stores it in a json file
function assembleResolution($planToResolve,$position,$peopleToContact){
    global $request;

    $time = strtotime($planToResolve->sort_date)-3600;
    //$dateArray = date_parse($plan->sort_date);
    $date = date_create_from_format('U',$time);


    //create the requester person object
    $requester = new Person($request->userID,$planToResolve->dates,$position);
    //check for duplicates and add new resolution to currently stored resolutions

    $currentResolutions = json_decode(file_get_contents('/var/www/resolutions.json'));
    $newResolution = new Resolution($date,$planToResolve->dates,$request->planID,$position,$requester,$peopleToContact,$time);
    $newResolution->expirationDate = calculateExpirationDate($planToResolve);
    $newResolution->expirationDate_unix = $newResolution->expirationDate->getTimestamp();
    foreach($currentResolutions as $resolution){
        if($resolution->requester->planningCenterID==$newResolution->requester->planningCenterID&&$resolution->planID==$newResolution->planID&&$newResolution->position==$resolution->position&&$resolution->isCancelled==false){

            echo"You have already made a request for the selected weekend.";
            die();
        }
    }
    array_push($currentResolutions,$newResolution);
    if(file_put_contents('/var/www/resolutions.json',json_encode($currentResolutions))){
        return $newResolution;
    }

}

function sendCreationRequestEmails($newResolution)
{

    foreach ($newResolution->contacts as $personToContact) {
        //send person an email
        sendMessage('benferris2@gmail.com', 'Scheduling Request', $personToContact->firstName, $personToContact->position, $personToContact->currentlyScheduledWeekend, $newResolution->position, $newResolution->weekendDate, $newResolution->resolutionID, $personToContact->planningCenterID,$newResolution->expirationDate->format('F jS').' at '.$newResolution->expirationDate->format('g:i A'));


    }

}




$planToResolve = getPlanToResolve();
if(verifyCreationTime($planToResolve)) {
    $neededPosition = getNeededPosition($planToResolve, $request->userID);

    $peopleToContact = getPeopleToContact($neededPosition);

    if ($newResolution = assembleResolution($planToResolve, $neededPosition, $peopleToContact)) echo true;

    sendCreationRequestEmails($newResolution);

    sendCreationNotificationToRequester('benferris2@gmail.com'/*$newResolution->requester->email*/,$newResolution->requester->firstName,$newResolution->weekendDate);

}else
    echo "Please contact Kris Rinas at 317-379-3389 and he will work with you to find a trade.";





