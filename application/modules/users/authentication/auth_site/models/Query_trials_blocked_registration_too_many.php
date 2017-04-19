<?php

require_once APPPATH.'modules/query_trials/models/Query_trials_cases_blocked.php';

class Query_trials_blocked_registration_too_many extends query_trials_cases_blocked
{
    function __construct()
    {
        parent::__construct();

        $this->sActionName = "registration";
        $this->sActionValue = "registered";
        $this->sActionWrongMessage = 'Wrong <strong>username/email</strong> or <strong>password</strong>';
        $this->sActionBlockedMessage = 'You registered too many. Try again in ';
        $this->sActionBlockedStartedMessage = 'You got blocked to register. Try again in ';
        $this->iActionTimeSec = 180;
        $this->iActionBlockedTimeSec = 180;
        $this->iActionMaxTrials = 3;
    }
}