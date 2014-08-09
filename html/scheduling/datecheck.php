<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ben
 * Date: 04/07/2014
 * Time: 13:59
 */

////determines the number of plans before and after the selected one that exist in planning center
$numberFutureServices = 0;
$existingPlansBefore = 0;
$existingPlansAfter = 0;
foreach($plans as $key=>$service){
    if($service->id==$request->planID){
        //echo "index ".$key.'of'.count($plans);
        $numberFutureServices = count($plans);
        $existingPlansBefore = $key;
        $existingPlansAfter = (count($plans)-$key-1);
        //echo $numberFutureServices.' future services, '.$existingPlansBefore.' plans before and '.$existingPlansAfter.'plans after<br><br>';

    }
}
$time = strtotime($plan->sort_date);
//$dateArray = date_parse($plan->sort_date);
$date = date_create_from_format('U',$time);
$month = $dateArray['month'];

$newMonth = $month;
$numWeeksAfter = 0;
$numWeeksBefore = 0;
$newDate = $date;
//determines the number of weekends after the selected one in the same month and next month
while($newMonth==$month||$newMonth==$month+1){
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    date_add($newDate,date_interval_create_from_date_string("7 days"));
    $newMonth = $newDate->format("n");
    if($newMonth==$month||$newMonth==$month+1){
        $numWeeksAfter++;
    }

}
$date = date_create_from_format('U',$time);
$newDate = $date;
$newMonth = $month;
//determines the number of weekends before the selected one in the same month
while($newMonth==$month){
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    date_sub($newDate,date_interval_create_from_date_string("7 days"));
    $newMonth = $newDate->format("n");
    //echo $newMonth;
    if($newMonth==$month){
        $numWeeksBefore+=1;
    }
}

$peopleToContact = array();
$previousPlan = $plan;

//check to make sure it doesn't get past weekends or weeks not yet active in planning center
if($numWeeksBefore>$existingPlansBefore){
    $numWeeksBefore = $existingPlansBefore;
}
if($numWeeksAfter>$existingPlansAfter){
    $numWeeksAfter = $existingPlansAfter;
}

//fetches people in same position from plans before the selected one and adds them to the contact array
for($i=0;$i<$numWeeksBefore;$i++){
    $oauth->fetch('https://www.planningcenteronline.com/plans/'.$previousPlan->prev_plan_id.'.json');
    $previousPlan=json_decode($oauth->getLastResponse());
    if($position=='Camera 1'||$position=='Camera 2'||$position=='Camera 3'||$position=='Camera 4'){
        foreach ($previousPlan->plan_people as $person) {
            if ($person->position == 'Camera 1'||$person->position == 'Camera 2'||$person->position == 'Camera 3'||$person->position == 'Camera 4') {
                array_push($peopleToContact, new Person($person->person_id, $previousPlan->dates,$person->position));
            }
        }
    }else {
        foreach ($previousPlan->plan_people as $person) {
            if ($person->position == $position && $person->person_id != $request->userID) {
                array_push($peopleToContact, new Person($person->person_id, $previousPlan->dates,$person->position));
            }
        }
    }
}



$oauth->fetch('https://www.planningcenteronline.com/plans/'.$request->planID.'.json');
$previousPlan=json_decode($oauth->getLastResponse());

//fetches people in same position from plans after the selected one and adds them to the contact array
for($i=0;$i<$numWeeksAfter;$i++){

    $oauth->fetch('https://www.planningcenteronline.com/plans/'.$previousPlan->next_plan_id.'.json');
    $previousPlan=json_decode($oauth->getLastResponse());

    if($position=='Camera 1'||$position=='Camera 2'||$position=='Camera 3'||$position=='Camera 4'){
        foreach ($previousPlan->plan_people as $person) {
            if ($person->position == 'Camera 1'||$person->position == 'Camera 2'||$person->position == 'Camera 3'||$person->position == 'Camera 4') {
                array_push($peopleToContact, new Person($person->person_id, $previousPlan->dates,$person->position));
            }
        }
    }
    else {
        foreach ($previousPlan->plan_people as $person) {
            if ($person->position == $position && $person->person_id != $request->userID) {
                array_push($peopleToContact, new Person($person->person_id, $previousPlan->dates,$person->position));
            }
        }
    }
}

//update array of people objects to contact with more info

foreach($peopleToContact as $person){
    $oauth->fetch('https://www.planningcenteronline.com/people/'.$person->planningCenterID.'.json');
    $currentPersonPCObject=json_decode($oauth->getLastResponse());
    $person->name = $currentPersonPCObject->name;
    $person->firstName = $currentPersonPCObject->first_name;
    $person->lastName = $currentPersonPCObject->last_name;
    $person->email = $currentPersonPCObject->contact_data->email_addresses[0]->address;
}