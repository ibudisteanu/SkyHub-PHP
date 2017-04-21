<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Edit_profile_validator extends Validator
{
    public $Users;

    public function __construct($Users)
    {
        parent::__construct();

        $this->Users=$Users;
        $this->sFormName='editProfile';
    }

    /*public function EditProfileValidator()
    {
        $this->Users=$Users;
        $this->sFormName='editProfile';
    }*/

    public function CheckPosts()
    {
        $sError='';

        $sError = $this->checkFormSets(
            [
                ['bio','<strong>Bio</strong>'],
                ['company','<strong>Company</strong>'],
                ['webSite','<strong>Website</strong>'],
                ['timeZone','<strong>Time Zone</strong>'],
                ['firstName','<strong>First Name</strong>'],
                ['lastName','<strong>LastName</strong>'],
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

        $bio = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-bio'],'xss|whitespaces');
        $firstName = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-firstName'],'xss|whitespaces');
        $lastName = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-lastName'],'xss|whitespaces');
        $company = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-company'],'xss|whitespaces');
        $webSite = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-webSite'],'xss|whitespaces');
        $timeZone = $this->StringsAdvanced->processText($_POST[$this->sFormName.'-timeZone'],'xss|whitespaces');


        if (! $this->checkValidName($firstName))
            $sError .= "<strong>FirstName</strong> ".$this->sError.'<br/>';

        if (! $this->checkValidName($lastName))
            $sError .= "<strong>LastName</strong> ".$this->sError.'<br/>';

        if (! $this->checkValidText($bio))
            $sError .= "<strong>Biography</strong> ".$this->sError.'<br/>';

        if (! $this->checkValidName($company))
            $sError .= "<strong>Company</strong> ".$this->sError.'<br/>';

        if (! $this->checkValidURL($webSite))
            $sError .= "<strong>WebSite</strong> ".$this->sError.'<br/>';
        $_POST[$this->sFormName.'-webSite'] = $webSite;

        if (! $this->checkValidTimeZone($timeZone))
            $sError .= "<strong>Time Zone</strong> ".$this->sError.'<br/>';

        if ($sError != '')
        {
            $this->CI->StringsAdvanced->removeLastBRTag($sError);

            $this->sError=$sError;
            return false;
        }

        return true;

    }

}