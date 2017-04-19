<?php

require_once APPPATH.'modules/query_trials/models/Query_trials_cases_blocked.php';

class Query_trials_blocked_change_password_too_many extends Query_trials_cases_blocked
{
    function __construct()
    {
        parent::__construct();

        $this->sActionName = "change_password";
        $this->sActionValue = "changed";
        $this->sActionWrongMessage = 'NO MESSAGE';
        $this->sActionBlockedMessage = 'You changed the password too many times. Try again in ';
        $this->sActionBlockedStartedMessage = 'You got blocked when you changed the password too many times. Try again in ';
        $this->iActionTimeSec = 180;
        $this->iActionBlockedTimeSec = 180;
        $this->iActionMaxTrials = 4;
    }
}