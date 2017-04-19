<?php

class Query_trials_cases_blocked extends MY_Model
{
    public $sActionName;
    public $sActionValue;
    public $sActionBlockedMessage;
    public $sActionWrongMessage;
    public $sActionBlockedStartedMessage;
    public $iActionTimeSec;
    public $iActionBlockedTimeSec;
    public $iActionMaxTrials ;

    public function __construct()
    {
        parent::__construct();

        $this->sActionWrongMessage="Wrong";
        $this->sActionBlockedMessage="Blocked";
        $this->sActionBlockedStartedMessage ="You got blocked. Try again later ";
        $this->sActionName = "none";
        $this->sActionValue = "none";
        $this->iActionTimeSec = 180;
        $this->iActionBlockedTimeSec = 180;
        $this->iActionMaxTrials = 3;
    }
}
