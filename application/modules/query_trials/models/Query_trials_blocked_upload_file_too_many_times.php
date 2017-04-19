<?php

require_once APPPATH.'modules/query_trials/models/Query_trials_cases_blocked.php';

class Query_trials_blocked_upload_file_too_many_times extends Query_trials_cases_blocked
{
    function __construct($sActionName="upload_avatar",$sActionValue="uploaded")
    {
        parent::__construct();

        $this->sActionName = $sActionName;
        $this->sActionValue = $sActionValue;
        $this->sActionWrongMessage = 'NO MESSAGE';
        $this->sActionBlockedMessage = 'You uploaded too many times your avatar. Try again in ';
        $this->sActionBlockedStartedMessage = 'You got blocked because you uploaded too many times your avatar. Try again in ';
        $this->iActionTimeSec = 180;
        $this->iActionBlockedTimeSec = 180;
        $this->iActionMaxTrials = 4;
    }
}