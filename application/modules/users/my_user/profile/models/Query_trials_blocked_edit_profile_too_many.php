<?php

require_once APPPATH.'modules/query_trials/models/Query_trials_cases_blocked.php';

class Query_trials_blocked_edit_profile_too_many extends Query_trials_cases_blocked
{
    function __construct()
    {
        parent::__construct();

        $this->sActionName = "edit_profile";
        $this->sActionValue = "changed";
        $this->sActionWrongMessage = 'NO MESSAGE';
        $this->sActionBlockedMessage = 'You changed too many times the profile. Try again in ';
        $this->sActionBlockedStartedMessage = 'You got blocked to change the profile. Try again in ';
        $this->iActionTimeSec = 180;
        $this->iActionBlockedTimeSec = 180;
        $this->iActionMaxTrials = 4;
    }
}