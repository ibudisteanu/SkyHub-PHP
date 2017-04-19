<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//require APPPATH.'libraries';

class Change_password_validator extends Validator
{
    public $Users;

    public function __construct($Users)
    {
        parent::__construct();

        $this->Users=$Users;
        $this->sFormName='changePassword';
    }

    public function CheckPosts($user)
    {
        $sError='';

        $sError = $this->checkFormSets(
            [
                ['newEmail','<strong>Email</strong>'],
                ['password','<strong>Password</strong>'],
                ['newPassword','<strong>New Password</strong>'],
                ['retypeNewPassword','<strong>Retype New Password</strong>']

                //,['rePassword','<strong>Retry Password</strong>']
                //,['flag','<strong>Country</strong>']
            ]);

        if ($sError!='')
        {
            $this->sError =$sError.' Not Specified';
            return false;
        }


        /*if (!isset($_POST['register-flag']))
        {
            $this->sError = "Error: No <strong>Country</strong> specified";
            return false;
        }*/

        $password=$this->StringsAdvanced->processText($_POST[$this->sFormName.'-password'], 'xss|whitespaces');
        $newEmail=$this->StringsAdvanced->processText($_POST[$this->sFormName.'-newEmail'], 'xss|whitespaces');
        $newPassword = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-newPassword'], 'xss|whitespaces');
        $retypeNewPassword = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-retypeNewPassword'], 'xss|whitespaces');

        if ($newEmail != '')
        {
            if (! $this->checkValidEmail($newEmail))
                $sError .= "<strong>Email Address</strong> - ".$this->sError;
        }
        if ($newPassword  != '')
        {
            if (! $this->checkValidPassword($newPassword))
                $sError .= "<strong>New Password</strong> - ".$this->sError.'<br/>';

            if (! $this->checkValidPassword($retypeNewPassword))
                $sError .= "<strong>Retype New Password</strong> - ".$this->sError.'<br/>';

            if ($newPassword != $retypeNewPassword)
                $sError .= "<strong>The passwords introduced don't match</strong><br/>";
        }

        $userEmail = $this->Users->userByEmail($newEmail) ;
        if (($userEmail != null) && ($userEmail->sUserName != $user->sUserName))
            $sError .='Email <strong>'.$newEmail.'</strong> is already used<br/>';

        if ($sError != '')
        {
            $this->CI->StringsAdvanced->removeLastBRTag($sError);

            $this->sError=$sError;
            return false;
        }

        return true;
    }

}