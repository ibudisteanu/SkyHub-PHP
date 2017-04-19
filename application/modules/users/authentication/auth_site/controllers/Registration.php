<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Registration extends MY_Controller
{

    static $iRegistrationNo=0;

    function __construct()
    {
        parent::__construct();

        $this->load->model('users/users', 'Users');
        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
    }

    public function index($sRegistrationPageType = 'RegistrationContainer')
    {
        return $this->processLoginForm($sRegistrationPageType);
    }

    public function Register()
    {
        $this->load->model('auth_site/Query_trials_blocked_registration_too_many','QueryTrialsBlockedRegistrationTooMany');
        $this->load->library('../modules/users/authentication/auth_site/libraries/Register_validator',$this->Users,'RegisterValidator');

        if (! $this->RegisterValidator->CheckPosts())
        {
            $this->AlertsContainer->addAlert('g_msgRegistrationError','error',$this->RegisterValidator->sError);
            return false;
        }

        if ($this->MyUser->bLogged)
        {
            //$this->QueryTrials->removeOldAttempts();
            $this->AlertsContainer->addAlert('g_msgRegistrationError','error','You are already registered');
            return false;
        }

        if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsBlockedRegistrationTooMany))
        {
            $this->AlertsContainer->addAlert('g_msgRegistrationError','error',$this->QueryTrials->sError);
            return false;
        }


        $username = $this->StringsAdvanced->processText($_POST['register-username'],'html|xss|whitespaces');
        $email = $this->StringsAdvanced->processText($_POST['register-email'],'html|xss|whitespaces');
        $firstName = $this->StringsAdvanced->processText($_POST['register-firstName'],'html|xss|whitespaces');
        $lastName = $this->StringsAdvanced->processText($_POST['register-lastName'],'html|xss|whitespaces');
        $sCountry = $this->StringsAdvanced->processText($_POST['register-country'],'html|xss|whitespaces');
        $sCity = $this->StringsAdvanced->processText($_POST['register-city'],'html|xss|whitespaces');

        $sInitialPassword = $this->StringsAdvanced->processText($_POST['register-password'],'html|xss|whitespaces');

        $UserNew = new User_model();
        $UserNew->sUserName = $username;
        $UserNew->sEmail = $email;
        $UserNew->sFirstName = $firstName;
        $UserNew->sLastName = $lastName;
        $UserNew->setNewPassword($sInitialPassword);
        $UserNew->sCountry=$sCountry;
        $UserNew->sCity=$sCity;
        $UserNew->sTimeZone=$this->IP->sTimeZone;
        $UserNew->sURLName = $username;
        $UserNew->sFullURLLink = "profile/".$username;
        $UserNew->sFullURLDomains = "page/profile";
        $UserNew->sFullURLName = "profile/".$firstName.' '.$lastName;

        $UserNew->storeUpdate();

        /*$this->load->model('activities/User_activities','UserActivities');
        $this->UserActivities->createActivityContainer($UserNew->sID);*/

        $this->QueryTrials->addAttempt();
        $this->MyUser->loginWithPassword($username, $sInitialPassword);

        if($this->MyUser->bLogged)
        {
            $this->CounterStatistics->increaseUsers(1);

            //send email
            modules::load('emails/email_controller')->sendActionEmail('registration');
            /*
            $this->load->model('emails/emails_model','Emails');
            modules::load('emails/email_controller')->sendEmail($this->Emails->insertActionEmail('registration'));
            */

            return true;
        }
        else
        {
            $this->AlertsContainer->addAlert('g_msgRegistrationError','error','A <strong>problem</strong> appeared registering your data');
            unset($username);
            return false;
        }

    }

    protected function processLoginForm($sLoginPageType)
    {

        if ($this->MyUser->bLogged)
        {
            switch ($sLoginPageType )
            {
                case 'RegistrationContainer':
                    break;
            }
        } else
        {
            if (($_POST)&&isset($_POST["val"]) && ($_POST["val"] == 'register'))
            {
                if ($this->Register())
                {
                    $this->AlertsContainer->addAlert('g_msgRegistrationSuccess','success','You had been registered successfully');
                    redirect(base_url(''), 'refresh');
                } else
                {
                    return $this->getRegistrationView($sLoginPageType);
                }

            } else
            {
                return $this->getRegistrationView($sLoginPageType);
            }
        }
    }

    public function getRegistrationView($sLoginPageType)
    {
        $this->includeWebPageLibraries('country-select');
        $this->includeWebPageLibraries('tooltip');
        $this->includeWebPageLibraries('validation');

        $this->OAuth2 = modules::load('oauth2/Oauth2_controller', NULL);
        $this->data['OAuth2LoginButtons'] = $this->OAuth2->renderOAuth2Buttons(true);

        $this->load->model('ip/ip','IP');
        if (!isset($_POST['register-country']))
            $_POST['register-country'] = strtolower($this->IP->sCountryCode);

        $this->data['sRegisterCityPlaceHolder'] = $this->IP->sCity;
        $this->data['iRegistrationNo'] = Registration::$iRegistrationNo++;

        switch ($sLoginPageType )
        {
            case 'box':
                $sContent = $this->renderModuleView('registration_box',$this->data,TRUE);
                break;
            case 'form':
                $sContent = $this->renderModuleView('registration_form',$this->data,TRUE);
                break;
            case 'RegistrationContainer':
                $this->ContentContainer->addObject($this->renderModuleView('registration_container',$this->data,TRUE),'',5);
                return '';
                break;
        }
        return $sContent;
    }

}