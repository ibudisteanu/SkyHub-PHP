<?php

require_once APPPATH.'modules/widgets/widgets_system/emails/emails/models/EmailStatus.php';

class Cron_emails_controller extends MY_Controller
{
    public function __construct($bDisableTemplate = false)
    {
        parent::__construct($bDisableTemplate);
    }

    public function runCronJob($bUnSentEmails =true, $bErrorEmails=false)
    {
        if ($bUnSentEmails== 'true') $bUnSentEmails = true; else $bUnSentEmails = false;
        if ($bErrorEmails== 'true') $bErrorEmails = true; else $bErrorEmails = false;

        ini_set('max_execution_time', 0);

        $this->load->model('emails/emails_model','EmailsModel');

        $arrEmails = [];

        if ($bUnSentEmails )
        {
            $emails = $this->EmailsModel->findAllUnSentEmails();
            if ($emails != null)
                $arrEmails = array_merge($arrEmails, $emails);
        }

        if ($bErrorEmails)
        {
            $emails =$this->EmailsModel->findAllErrorEmails();
            if ($emails != null)
                $arrEmails = array_merge($arrEmails, $emails);
        }

        //var_dump($arrEmails);

        try
        {
            $iCountSent = 0;

            $EmailController = modules::load('emails/email_controller');

            $arrErrorEmails = [];

            if ($arrEmails != null)
                foreach ($arrEmails as $email)
                    if ($email != null)
                    {
                        $sResultMessage=''; $bResult=false;

                        try {
                            $bResult = $EmailController->resolveEmail($email);
                        } catch (Exception $exception) {
                            $sResultMessage = $exception->getMessage();
                        }

                        if ($bResult) $iCountSent++;
                        else
                            array_push($arrErrorEmails, [$email->sID, $sResultMessage]);

                    }

            if (count($arrEmails) == 0)
                return $this->returnMessage(true,'No Message to Send');
            else
            {
                if (count($arrErrorEmails) == 0)
                    return $this->returnMessage(true, 'All Emails has been sent');
                else
                    return $this->returnMessage(false, 'Only some of the emails has been sent '.count($arrEmails).'/'.$iCountSent,$arrErrorEmails);
            }

            //$Email = $this->Emails->findEmail($sEmailId);
        }
        catch (Exception $ex)
        {
            return $this->returnMessage(false, 'Exception encountered '.$ex->getMessage(),$arrErrorEmails);
        }
    }

    protected function returnMessage($bReturnValue, $sMessage='', $arrErrorEmails=[])
    {
        echo json_encode(['return'=>$bReturnValue,'message'=>$sMessage,'errorEmails'=>$arrErrorEmails]);

        return $bReturnValue;
    }

}