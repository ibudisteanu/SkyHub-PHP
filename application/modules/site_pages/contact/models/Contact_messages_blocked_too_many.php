<?php

require_once APPPATH.'modules/query_trials/models/Query_trials_cases_blocked.php';

class Contact_messages_blocked_too_many extends Query_trials_cases_blocked
{
    function __construct()
    {
        parent::__construct();

        $this->sActionName = "contactMessages";
        $this->sActionValue = "submitted";
        $this->sActionWrongMessage = 'Wrong Contact Message Input';
        $this->sActionBlockedMessage = 'You submitted way too many messages. Try again in ';
        $this->sActionBlockedStartedMessage = 'You got temporally blocked to contact us. Try again in ';
        $this->iActionTimeSec = 180;
        $this->iActionBlockedTimeSec = 180;
        $this->iActionMaxTrials = 3;
    }
}