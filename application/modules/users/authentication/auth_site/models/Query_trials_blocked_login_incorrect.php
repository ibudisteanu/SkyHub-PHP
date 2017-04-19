<?php

require_once APPPATH.'modules/query_trials/models/Query_trials_cases_blocked.php';

class Query_trials_blocked_login_incorrect extends Query_trials_cases_blocked
{
    function __construct()
    {
        parent::__construct();

        $this->sActionName = "login";
        $this->sActionValue = "incorrect";
        $this->sActionWrongMessage = 'Wrong <strong>username/email</strong> or <strong>password</strong>';
        $this->sActionBlockedMessage = 'Too many login trials. Try again in ';
        $this->sActionBlockedStartedMessage = 'You got blocked to login. Try again in ';
        $this->iActionTimeSec = 180;
        $this->iActionBlockedTimeSec = 180;
        $this->iActionMaxTrials = 6;
    }
}