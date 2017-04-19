<?php

require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Api_registration  extends MY_AdvancedController
{
    public function checkEmailUsedPOST()
    {
        if ( !isset($_POST) )
        {
            $arrResult = ["result"=>false,"message"=>"No POST found"];
            echo json_encode($arrResult);
            return false;
        }

        if (!isset($_POST['Email'])) {
            $arrResult = ["result"=>false,"message"=>'<strong>Email POST</strong> not presented <br/>'];
            echo json_encode($arrResult);
            return false;
        }

        $this->load->model('api/Query_trials_blocked_registration_checked_emails_too_many','QueryTrialsBlockedRegistrationTooMany');

        if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsBlockedRegistrationTooMany))
        {
            $arrResult = ["result"=>false,"rejected"=>true,"message"=>$this->QueryTrials->sError];
            echo json_encode($arrResult);
            return false;
        }

        $this->QueryTrials->addAttempt();

        $this->load->model('users/Users','Users');

        $bEmailUsed = $this->Users->checkEmailUsed($_POST['Email']);
        if ($bEmailUsed) $sMessage = 'Email is <strong>already used </strong> by somebody else';
        else $sMessage = 'The email address looks ok';

        $arrResult = ["result"=>$bEmailUsed,"rejected"=>false,"message"=>$sMessage];
        echo json_encode($arrResult);

        return true;
    }

    public function checkUsernameUsedPOST()
    {
        if ( !isset($_POST) )
        {
            $arrResult = ["result"=>false,"message"=>"No POST found"];
            echo json_encode($arrResult);
            return false;
        }

        if (!isset($_POST['Username'])) {
            $arrResult = ["result"=>false,"message"=>'<strong>Username POST</strong> not presented <br/>'];
            echo json_encode($arrResult);
            return false;
        }

        $this->load->model('api/Query_trials_blocked_registration_checked_usernames_too_many','QueryTrialsBlockedRegistrationTooMany');

        if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsBlockedRegistrationTooMany))
        {
            $arrResult = ["result"=>false,"rejected"=>true,"message"=>$this->QueryTrials->sError];
            echo json_encode($arrResult);
            return false;
        }

        $this->QueryTrials->addAttempt();

        $this->load->model('users/Users','Users');

        $bEmailUsed = $this->Users->checkUsernameUsed(strtolower($_POST['Username']));
        if ($bEmailUsed) $sMessage = 'Username is <strong> already used </strong> by somebody else';
        else $sMessage = 'The username looks ok';

        $arrResult = ["result"=>$bEmailUsed,"rejected"=>false,"message"=>$sMessage];
        echo json_encode($arrResult);

        return true;
    }

}