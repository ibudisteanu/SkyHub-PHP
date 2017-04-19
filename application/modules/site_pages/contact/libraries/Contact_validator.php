<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//require APPPATH.'libraries';

class Contact_validator extends Validator
{
    public $MyUser;

    public function __construct($MyUser)
    {
        $this->MyUser=$MyUser;
    }


    public function CheckPosts()
    {
        $sError='';

        if ($this->MyUser->bLogged)
        {

        } else
        {
            if (!isset($_POST['contact-Email']))
                $sError .=  "<strong>Email</strong> ";

            if (!isset($_POST['contact-FullName']))
                $sError .=  "<strong>Email</strong> ";
        }

        if (!isset($_POST['contact-Topic']))
            $sError .=  "<strong>Topic</strong> ";

        if (!isset($_POST['contact-Message']))
            $sError .=  "<strong>Message</strong> ";

        if (!isset($_POST['contact-Captcha']))
            $sError .=  "<strong>Captcha</strong> ";

        if ($sError!='')
        {
            $this->sError ='No '.$sError.' specified';
            return false;
        }

        if (!$this->MyUser->bLogged)
        {
            $sEmail=$this->StringsAdvanced->processText($_POST['contact-Email'],'html|xss|whitespaces');

            if (! $this->checkValidEmail($sEmail))
                $sError .= "<strong>Email Address</strong> ".$this->sError.'<br/>';

            $sFullName = $this->StringsAdvanced->processText($_POST['contact-FullName'],'html|xss|whitespaces');

            if (! $this->checkValidText($sFullName,3))
                $sError .= "<strong>Name</strong> ".$this->sError.'<br/>';
        }
        $sTopic = $_POST['contact-Topic'];
        $sMessage = $_POST['contact-Message'];
        $sCaptcha = $_POST['contact-Captcha'];


        if (! $this->checkValidLength($sTopic,5))
            $sError .= "<strong>Subject</strong> ".$this->sError.'<br/>';

        if (! $this->checkValidLength($sMessage,10))
            $sError .= "<strong>Contact Message</strong> ".$this->sError.'<br/>';

        if ($sError != '')
        {
            $this->sError=$sError;
            return false;
        }

        return true;

    }

}