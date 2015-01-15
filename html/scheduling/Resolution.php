<?php

class Resolution {
    public $resolutionID;
    public $creationDate;
    public $date;
    public $date_unix;
    public $creationDate_unix;
    public $expirationDate;
    public $expirationDate_unix;
    public $weekendDate;
    public $planID;
    public $position;
    public $requester;
    public $contacts;
    public $isResolved;
    public $resolver;
    public $isExpired;
    public $isCancelled;

     public function __construct($date,$weekendDate,$planID,$position,$requester,$contacts,$date_unix){
         $this->resolutionID = uniqid();
         $this->creationDate = new DateTime();
         $this->date_unix = $date_unix;
         $this->date = $date;
         $this->creationDate_unix = time();
         $this->weekendDate = $weekendDate;
         $this->planID = $planID;
         $this->position = $position;
         $this->requester = $requester;
         $this->contacts = $contacts;
         $this->isResolved = false;
         $this->isExpired = false;
         $this->isCancelled = false;
     }
} 