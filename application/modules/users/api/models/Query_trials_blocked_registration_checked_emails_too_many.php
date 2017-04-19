<?php

require_once APPPATH.'modules/query_trials/models/Query_trials_cases_blocked.php';

class Query_trials_blocked_registration_checked_emails_too_many extends Query_trials_cases_blocked
{
    function __construct()
    {
        parent::__construct();

        $this->sActionName = "registr-checked-email";
        $this->sActionValue = "reg-email";
        $this->sActionWrongMessage = '';
        $this->sActionBlockedMessage = 'You checked way too many email addresses. Try again in ';
        $this->sActionBlockedStartedMessage = 'You checked way too many email addresses. Try again in ';
        $this->iActionTimeSec = 180;
        $this->iActionBlockedTimeSec = 180;
        $this->iActionMaxTrials = 25;
    }
}