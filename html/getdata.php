<?php
header('Content-Type: application/json');
require_once("oauth_config.php");
require_once('email.php');
require('mongo-connect.php');
error_reporting(E_ALL);
ini_set("display_errors", 1);

define('ACCESS_TOKEN_KEY', 'I9MsYDyPFhjpcd3NvmJD');
define('ACCESS_TOKEN_SECRET', 'QNQQ7MF2iNpJUayzR7aOpQcXc8xKaaLCLysTk93k');

//oauth2 uses an account that has viewer permissions (as opposed to scheduled viewer)
$oauth2 = new OAuth(CONSUMER_KEY, CONSUMER_SECRET);
$oauth2->setToken(ACCESS_TOKEN_KEY, ACCESS_TOKEN_SECRET);

$authorisedAdminIDs = array(987202,497948);



$oauth->fetch("https://planningcenteronline.com/me.json");
$user = json_decode($oauth->getLastResponse());


switch ($_GET['requesting']){
    case 'me':
        $oauth->fetch("https://planningcenteronline.com/me.json");
        $JSONPerson = $oauth->getLastResponse();
        echo $JSONPerson;
        break;
    case 'plans':
        $oauth->fetch('https://www.planningcenteronline.com/service_types/'.$_GET['serviceTypeID'].'/plans.json');
        $plans = $oauth ->getLastResponse();
        echo $plans;
        break;
    case 'scheduledPlans':

        //an array to hold the plans that have have had a person scheduled in the requested position
        $scheduledPlans = array();
        //an array to hold the ids of the plans that the user is scheduled for so that requests are not sent to other people scheduled for those weekends
        $scheduledPlanIDs = array();

        if(isset($_GET['serviceTypeID'])&&isset($_GET['selectedWeekendID'])){


            $oauth->fetch('https://www.planningcenteronline.com/service_types/'.$_GET['serviceTypeID'].'/plans.json');
            $plans = json_decode($oauth ->getLastResponse());
            //todo: don't show plans that are dated in next few days
            foreach($plans as $plan){
                if($plan->scheduled==true){
                    array_push($scheduledPlanIDs,$plan->id);
                }
            }


            //fetches object containing overview of all plans with selected service type ID
            $oauth2->fetch('https://www.planningcenteronline.com/service_types/'.$_GET['serviceTypeID'].'/plans.json');
            $plans = json_decode($oauth2->getLastResponse());
            //fetches the plan detail for the selected weekend
            $oauth2->fetch('https://www.planningcenteronline.com/plans/'.$_GET['selectedWeekendID'].'.json');
            $planJSON=$oauth2->getLastResponse();
            $planToResolve = json_decode($planJSON);
            $people = $planToResolve->plan_people;
//finds the name of the needed position
//todo: handle one person with multiple positions
            $position = "not found";
            foreach($people as $value){
                if($value->person_id==$_GET['userID']){
                    $position = $value->position;
                }
            }
//fetches plan details for all future plans
            foreach ($plans as $plan){
                //only check plan if the user is not scheduled in it
                if (!in_array($plan->id,$scheduledPlanIDs)) {
                    $oauth2->fetch('https://www.planningcenteronline.com/plans/' . $plan->id . '.json');
                    $currentPlan = json_decode($oauth2->getLastResponse());
                    //if there is a person scheduled for the requested position, add the plan id to an array
                    foreach ($currentPlan->plan_people as $person) {
                        if ($person->position == $position && $person->person_id != $_GET['userID']) {
                            array_push($scheduledPlans, $currentPlan);
                        }
                    }
                }
            }
            echo json_encode($scheduledPlans);
        }
        break;


    case 'organization':
        $oauth->fetch("https://planningcenteronline.com/organization.json");
        $organization = $oauth->getLastResponse();
        echo $organization;
        break;
    case'myRequests':
        $myRequests = array();
        $query = array('requester.planningCenterID'=>$user->id);
        $cursor = $collection->find($query);
        foreach($cursor as $id=>$value){
            array_push($myRequests,$value);
        }
        echo json_encode($myRequests);

        break;

    case 'resolutions':
        if(in_array($user->id,$authorisedAdminIDs)) {
            $resolutions = array();
        $cursor = $collection->find();
            foreach($cursor as $id=>$value){
                array_push($resolutions,$value);
            }
            echo json_encode($resolutions);
            //echo file_get_contents('../../resolutions.json');
        }
        else echo 'permissionError';
        break;
    case'isAdmin':
        if(in_array($user->id,$authorisedAdminIDs))
            echo true;
        else
            echo false;
        break;

}
