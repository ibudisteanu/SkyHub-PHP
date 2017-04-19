<?php

class Contact extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->processLoginForm();
    }

    protected function processLoginForm()
    {
        if (($_POST)&&isset($_POST["val"]) && ($_POST["val"] == 'contact'))
        {
            if ($this->submitContact())
                $this->loadContactMessageSentView();
            else
                $this->loadContactFormView();

        } else
            $this->loadContactFormView();

        $this->renderContactContainer();
    }

    public function submitContact()
    {

        $this->load->model('contact/contact_messages_blocked_too_many','ContactMessagesBlockedTooMany');
        $this->load->library('contact/contact_validator',$this->MyUser,'ContactValidator');

        if (! $this->ContactValidator->CheckPosts())
        {
            $this->AlertsContainer->addAlert('g_msgContactError','error',$this->ContactValidator->sError);
            return false;
        }


        if (!$this->QueryTrials->checkIPAddress($this->ContactMessagesBlockedTooMany ))
        {
            $this->AlertsContainer->addAlert('g_msgContactError','error',$this->QueryTrials->sError);
            return false;
        }

        $sUsername='';
        if ($this->MyUser->bLogged)
        {
            $sEmail=$this->MyUser->sEmail;
            $sFullName =$this->MyUser->getFullName();
        } else
        {
            $sEmail = $this->StringsAdvanced->processText($_POST['contact-Email'], 'xss|whitespaces');
            $sFullName = $this->StringsAdvanced->processText($_POST['contact-FullName'], 'html|xss|whitespaces');
        }
        $sTopic = $_POST['contact-Topic'];
        $sMessage = $_POST['contact-Message'];
        $sCaptcha = $_POST['contact-Captcha'];

        if ($sCaptcha != '17')
        {
            $this->AlertsContainer->addAlert('g_msgContactError','error','Wrong Captcha');
            return false;
        }

        $this->load->model('contact/contact_messages','ContactMessages');
        $this->ContactMessages->createNewMessage($sFullName, $sEmail, $sTopic, $sMessage);

        $this->QueryTrials->addAttempt();

        $this->load->model('counter/counter_statistics','CounterStatistics');
        $this->CounterStatistics->increaseContactMessages(1);


        modules::load('emails/email_controller')->sendActionEmail('contact-email','ionutbudisteanu@yahoo.com',$sEmail,['FullName'=>$sFullName,'Message'=>$sMessage]);
        modules::load('emails/email_controller')->sendActionEmail('contact-confirmation-email',$sEmail,'',['FullName'=>$sFullName,'Message'=>$sMessage]);

        $this->AlertsContainer->addAlert('g_msgContactSuccess','success','Your message has been successfully sent to <strong>SkyHub</strong> <br/>Shortly, you will receive a confirmation email.');

        return true;
    }

    public function renderContactContainer()
    {
        $this->ContentContainer->addObject($this->renderModuleView('contact_view',$this->data,TRUE));
    }

    public function loadContactFormView()
    {
        $this->data['sContactForm'] = $this->renderModuleView('contact_form/contact_form_view',$this->data,TRUE);
    }

    public function loadContactMessageSentView()
    {
        $this->data['sContactForm'] = $this->renderModuleView('contact_form/contact_message_sent_view',$this->data,TRUE);
    }

}