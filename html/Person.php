<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ben
 * Date: 04/07/2014
 * Time: 14:06
 */


class Person {

    public $name;
    public $firstName;
    public $lastName;
    public $email;
    public $position;
    public $currentlyScheduledWeekend;
    public $planningCenterID;
    public $response;

    public function __construct($planningCenterID,$currentlyScheduledWeekend,$position){
        global $oauth;
        $this->currentlyScheduledWeekend = $currentlyScheduledWeekend;
        $this->planningCenterID = $planningCenterID;
        $this->position=$position;
        $oauth->fetch('https://planningcenteronline.com/people/'.$planningCenterID.'.json');
        $newPerson = json_decode($oauth->getLastResponse());
        $this->name = $newPerson->name;
        $this->firstName = $newPerson->first_name;
        $this->lastName = $newPerson->last_name;
        $this->email = $newPerson->contact_data->email_addresses[0]->address;
    }
} 