<?php

require_once APPPATH.'modules/widgets/widgets_system/emails/emails/models/EmailStatus.php';

class Email_controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('emails/emails_model','Emails');
    }

    public function index($sId='')
    {
        $this->load->helper('url');

        $Email = $this->Emails->findEmail($sId);

        if ($Email == null)
        {
            echo 'NO EMAIL FOUND';
            return false;
        }

        $this->renderEmail($Email,false);
    }

    protected function renderEmail($Email, $bRender)
    {
        if (is_string($Email)) $Email = $this->Emails->findEmail($Email);
        else $Email = $Email;

        $data['dtEmail'] = $Email;
        $viewData = '';
        $viewData .= $this->renderModuleView('template/email_header_view',$data,$bRender);

        if ($Email->sContentView !='')
            $viewData .= $this->renderModuleView($Email->sContentView,$data,$bRender);

        $viewData .= $this->renderModuleView('template/email_footer_view',$data,$bRender);

        return $viewData;
    }

    protected function insertEmail($sActionName,  $sSubject, $sMessage, $Destination='', $From='',$sURL='', $arrProperties=[],  $enStatus=0)
    {
        $this->load->model('emails/emails_model','Emails');
        $Email = $this->Emails->insertEmail($sActionName, $sSubject,$sMessage, $Destination, $From, $sURL, $arrProperties, $enStatus);

        if ($sActionName == 'registration') {
            try{
                $this->resolveEmail($Email);
            } catch (Exception $exception) {

            }
        }

    }

    public function sendActionEmail($sActionName, $Destination='', $From='', $arrProperties=[], $enStatus=0)
    {
        $this->load->model('emails/emails_model','Emails');
        $Email = $this->Emails->insertActionEmail($sActionName, $Destination, $From, $arrProperties, $enStatus);

        //send the registration email
        if ($sActionName == 'registration') {
            try{
                $this->resolveEmail($Email);
            } catch (Exception $exception) {

            }
        }
    }

    public function resolveEmail($sEmailId='', $bStoreUpdate=true)
    {
        if (is_string($sEmailId))
            $Email = $this->Emails->findEmail($sEmailId);
        else $Email = $sEmailId;

        if ($Email == null)
            throw new Exception('NO EMAIL FOUND');

        if ($Email->getDestinationEmail() == '') {
            if ($bStoreUpdate) $Email->updateStatus(EmailStatus::Error);
            throw new Exception('Destination EMAIL is NULL');
        }

        try {
            $sEmailBody = $this->renderEmail($Email, true);
        }
        catch (Exception $ex) {
            if ($bStoreUpdate) $Email->updateStatus(EmailStatus::Error);
            throw new Exception('Error rendering Email.... '.$ex->getMessage());
        }

        try
        {
            $this->load->library('emails/PHP_Mailer_Library',null,'PHP_Mailer_Library');
            //$this->php_mailer_library->useGoogleSMTP();
            $this->php_mailer_library->useVisionBotSMTP();
        }
        catch (Exception $ex) {
            if ($bStoreUpdate) $Email->updateStatus(EmailStatus::Error);
            throw new Exception('Error setting the SMTP data '.$ex->getMessage());
        }

        try
        {
            if ((defined('WEBSITE_OFFLINE'))&&(1==2))
            {
                throw new Exception('Email not sent, because it is in OFFLINE MODE');
            } else
            {
                if ($this->php_mailer_library->sendEmail($Email->sSubject,$sEmailBody,'',$Email->getFromEmail(),'',$Email->getDestinationEmail(),'')) {
                    if ($bStoreUpdate)
                        $Email->updateStatus(EmailStatus::Sent);
                }
            }

        } catch (Exception $ex){
            if ($bStoreUpdate) $Email->updateStatus(EmailStatus::Error);
            throw new Exception('Error setting the SMTP data '.$ex->getMessage());
        }

        return true;

//        try
//        {
//            $config = Array(
//                'protocol' => 'smtp',
//                'smtp_host' => 'mail.visionbot.net',
//                'smtp_port' => 345,
//                'smtp_user' => 'visionbo',
//                'smtp_pass' => '1r9xg2Ck1L',
//                'smtp_timeout' => '4',
//                'mailtype' => 'html',
//                'charset' => 'iso-8859-1'
//            );
//            $this->load->library('email', $config);
//            $this->email->set_newline("\r\n");
//
//        }
//        catch (Exception $ex) {
//            if ($bStoreUpdate) $Email->updateStatus(EmailStatus::Error);
//            throw new Exception('Error setting the SMTP data '.$ex->getMessage());
//        }
//
//        $this->email->from($Email->getFromEmail());
//
//        /*echo'From:';
//        var_dump($Email->getFromEmail());
//        echo'Destination:';
//        var_dump($Email->getDestinationEmail());*/
//
//        $this->email->set_mailtype("html");
//
//        $this->email->to($Email->getDestinationEmail());  // replace it with receiver mail id
//        $this->email->subject($Email->sSubject); // replace it with relevant subject
//
//        $this->email->message($sEmailBody);
//
//        try {
//
//            if (!defined('WEBSITE_OFFLINE') )
//            {
//                throw new Exception('Email not sent, because it is in OFFLINE MODE');
//            } else
//            {
//                //var_dump($Email->getDestinationEmail().' '.$Email->getFromEmail().' - '.$Email->sSubject);
//
//                if ($this->email->send()) $Email->updateStatus(EmailStatus::Sent);
//                else
//                {
//                    if ($bStoreUpdate) $Email->updateStatus(EmailStatus::Error);
//                    throw new Exception('Email '.$Email->sID.' has not been sent of various reasons. ');
//                }
//            }
//        }
//        catch (Exception $ex) {
//            throw new Exception('Error sending the SMTP email' . $ex->getMessage());
//        }
//
//        return true;
    }

    public function unitTestingEmail()
    {
        $this->load->model('emails/emails_model','Emails');
        $bResult = true;

        $Email = $this->Emails->insertActionEmail('registration', 'ionutbudisteanu@yahoo.com', '',[],0,false); //creating a dummy email

        try {
            if ($this->resolveEmail($Email, false))   //sending email
                echo 'Email to ionutbudisteanu@yahoo.com has been sent successfully </br>';
            else{
                echo 'ERROR SENDING email to ionutbudisteanu@yahoo.com</br>';
                $bResult=false;
            }
        } catch (Exception $exception)
        {
            echo 'ERROR SENDING email to ionutbudisteanu@yahoo.com</br>'.$exception->getMessage();
            $bResult=false;
        }

        return $bResult;

    }

}