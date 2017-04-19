<?php

require_once APPPATH.'modules/query_trials/models/Query_trials_cases_blocked.php';

class Query_trials_blocked_send_emails_too_many_times extends Query_trials_cases_blocked
{
    function __construct()
    {
        parent::__construct();

        $this->sActionName = "send_emails";
        $this->sActionValue = "sent";
        $this->sActionWrongMessage = 'NO MESSAGE';
        $this->sActionBlockedMessage = 'You sent too many emails. Try again in ';
        $this->sActionBlockedStartedMessage = 'You got blocked because you sent too many emails. Try again in ';
        $this->iActionTimeSec = 180;
        $this->iActionBlockedTimeSec = 140;
        $this->iActionMaxTrials = 4;
    }
}