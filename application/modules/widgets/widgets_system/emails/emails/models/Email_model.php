<?php

require_once APPPATH.'modules/widgets/widgets_system/emails/emails/models/EmailStatus.php';

class Email_model extends MY_Advanced_model
{
    public $sClassName = 'Email_model';

    public $sActionName;

    public $Destination;//can be User or String
    public $From;//can be User or String

    public $sSubject;
    public $sMessage;
    public $sContentView;
    public $sURL;

    public $arrProperties=[];

    public $enStatus;

    public function __construct()
    {
        parent::__construct(false);

        $this->initDB('Emails',TUserRole::notLogged,TUserRole::notLogged,TUserRole::notLogged,TUserRole::notLogged);

        $this->sActionName ='NO NAME';
        $this->sSubject='NO SUBJECT';
        $this->enStatus = 0;
    }


    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p);

        if (isset($p['ActionName'])) $this->sActionName = $p['ActionName'];
        if (isset($p['Subject'])) $this->sSubject = $p['Subject'];
        if (isset($p['Message'])) $this->sMessage = $p['Message'];

        if (isset($p['Destination'])) $this->Destination = $p['Destination'];
        if (isset($p['From'])) $this->From = $p['From'];

        $this->loadDestinationAndFrom();

        if (isset($p['Properties'])) $this->arrProperties = $p['Properties'];

        if (isset($p['URL'])) $this->sURL = $p['URL'];
        else $this->sURL = "";

        if (isset($p['Status'])) $this->enStatus = $p['Status'];

        $this->processTemplates();
    }

    public function setData($sActionName,  $sSubject, $sMessage, $Destination='', $From='',$sURL='',$arrProperties=[], $enStatus=0)
    {
        $this->sActionName = $sActionName;
        $this->sSubject = $sSubject;
        $this->sMessage = $sMessage;
        $this->sURL = $sURL;
        $this->Destination = $Destination;
        $this->From = $From;
        $this->arrProperties = $arrProperties;
        $this->enStatus = $enStatus;

        $this->loadDestinationAndFrom();
    }

    protected function loadDestinationAndFrom()
    {
        if (((is_string($this->From))&&($this->From!='')&&(ctype_xdigit($this->From)) && (MongoId::isValid($this->From)))||((is_object($this->From))&&(get_class($this->From)=='MongoId'))) {
            $this->load->model('users/users', 'Users');
            $User = $this->Users->userByMongoId($this->From);
            if ($User != null) $this->From = $User;
        }

        /*var_dump($this->Destination);
        var_dump((boolean)((is_object($this->Destination))&&(get_class($this->Destination)==='MongoId')));*/

        if (((is_string($this->Destination))&&($this->Destination!='')&&(ctype_xdigit($this->Destination)) && (MongoId::isValid($this->Destination)))||((is_object($this->Destination))&&(get_class($this->Destination)==='MongoId'))) {

            $this->load->model('users/users', 'Users');
            $User = $this->Users->userByMongoId($this->Destination);
            $this->Destination = $User;
        }
    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if ($this->sActionName != '') $arrResult = array_merge($arrResult, array("ActionName"=>$this->sActionName));
        if ($this->sSubject != '') $arrResult = array_merge($arrResult, array("Title"=>$this->sSubject));
        if ($this->sMessage != '') $arrResult = array_merge($arrResult, array("Message"=>$this->sMessage));

        if ((!is_object($this->Destination))&&(is_string($this->Destination)))
            $arrResult = array_merge($arrResult, array("Destination"=>$this->Destination));
        else
            if ((is_object($this->Destination)))
                $arrResult = array_merge($arrResult, array("Destination"=>new MongoId($this->Destination->sID)));

        if ((!is_object($this->From))&&(is_string($this->From)))
            $arrResult = array_merge($arrResult, array("From"=>$this->From));
        else
            if ((is_object($this->From)))
                $arrResult = array_merge($arrResult, array("From"=>new MongoId($this->From->sID)));

        if ($this->sURL != '') $arrResult = array_merge($arrResult, array("URL"=>$this->sURL));

        if ($this->enStatus != '') $arrResult = array_merge($arrResult, array("Status"=>$this->enStatus));
        if (($this->arrProperties != null)&&(count($this->arrProperties) > 0))
        {
            $arrPropertiesSerialized = [];

            foreach ($this->arrProperties as $name => $property)
                if (!is_object($property))
                {
                    $arrPropertiesSerialized = array_merge($arrPropertiesSerialized, [$name => (string) $property]);
                } else
                    $arrPropertiesSerialized = array_merge($arrPropertiesSerialized, [$name => (string) (isset($property->sID) ? $property->sID : '')] );

            $arrResult = array_merge($arrResult, ["Properties"=>$arrPropertiesSerialized]);
        }

        return $arrResult;
    }


    public function getEmailURL()
    {
        return base_url('emails/view'.'/'.$this->sID);
    }


    public function getDestinationEmail()
    {
        $sDestinationEmail = '';
        if ((is_object($this->Destination)))
        {
            if (isset($this->Destination->sEmail))
                $sDestinationEmail = $this->Destination->sEmail;
        }
        else
        if ((is_string($this->Destination))&&(strlen($this->Destination) > 3)) $sDestinationEmail = $this->Destination;

        return $sDestinationEmail;
    }

    public function getFromEmail()
    {
        $sFromEmail = 'no-reply@'.WEBSITE_URL;

        if ((is_object($this->From))) {
            if (isset($this->From->sEmail))
                $sFromEmail = $this->From->sEmail;
        } else
        if (($this->From != null)&&(!is_object($this->From))&&(is_string($this->From))&&(strlen($this->From) > 3))
            $sFromEmail = $this->From;

        return $sFromEmail;
    }

    public function processTemplates()
    {
        try
        {
            switch ($this->sActionName)
            {
                case 'registration':
                    try {
                        if ($this->Destination == null) throw new Exception('Destination is null');

                        $this->sSubject = 'Welcome to '.WEBSITE_NAME;
                        $this->sMessage = '<strong>'.(is_object($this->Destination) ? $this->Destination->getFullName() : $this->Destination).'</strong>, we are thrilled that you decided to <strong>join</strong></strong> this amazing community. You can login anytime and start change the world on our platform.';
                        $this->sContentView = 'content/email_welcome_view';
                    } catch (Exception $exception) {
                        return ['bResult'=>false,'sMessage'=>'Registration Template error '.$exception->getMessage()];
                    }
                    break;
                case 'contact-email':
                    try{
                        $this->sSubject = "New ".WEBSITE_NAME." CONTACT Email :: ".$this->arrProperties['FullName'] ." :: ".$this->getFromEmail();
                        $this->sContentView = 'content/contact/email_contact_view';
                    } catch (Exception $exception) {
                        echo 'Contact Template error '.$exception->getMessage();
                        return ['bResult'=>false,'sMessage'=>'Contact Template error '.$exception->getMessage()];
                    }
                    break;
                case 'contact-confirmation-email':
                    try {
                        $this->sSubject = "Confirmation Email for " . WEBSITE_NAME;
                        $this->sContentView = 'content/contact/email_contact_confirmation_view';
                    } catch (Exception $exception) {
                        echo 'Contact Confirmation Template error '.$exception->getMessage();
                        return ['bResult'=>false,'sMessage'=>'Contact Confirmation Template error '.$exception->getMessage()];
                    }

                    break;
                case 'notification-email-system':
                    try {
                        $this->sSubject = $this->arrProperties['Title'] . " on " . WEBSITE_NAME;
                        $this->sContentView = 'content/notifications/email_notification_system';
                    } catch (Exception $exception) {
                        echo 'Notification Email System Template error '.$exception->getMessage();
                        return ['bResult'=>false,'sMessage'=>'Notification Email System Template error '.$exception->getMessage()];
                    }

                    break;
                case 'notification-email-from-user':
                    try {
                        $this->load->model('users/Users_minimal', 'UsersModel');
                        $User = $this->UsersModel->userByMongoId($this->arrProperties['SourceUserId'], ['Email']);
                        if ($User != null)
                            $this->arrProperties['SourceUser'] = $User;

                        $this->sSubject = $this->arrProperties['Title'] . " from " . $User->getFullName() . " on " . WEBSITE_NAME;
                        $this->sContentView = 'content/notifications/email_notification_from_user';
                    } catch (Exception $exception) {
                        echo 'Notification Email From User template error '.$exception->getMessage();
                        return ['bResult'=>false,'sMessage'=>'Notification Email From User template error '.$exception->getMessage()];
                    }
                    break;
            }
        }
        catch (Exception $exception) {
            return ['bResult'=>false,'sMessage'=>$exception->getMessage()];
        }
    }

    public function updateStatus($enNewValue)
    {
        if ($this->enStatus != $enNewValue) {

            $this->enStatus = $enNewValue;
            $this->storeUpdate();
        }
    }

}