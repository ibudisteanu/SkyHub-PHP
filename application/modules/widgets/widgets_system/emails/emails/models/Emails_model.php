<?php

require_once APPPATH.'modules/widgets/widgets_system/emails/emails/models/EmailStatus.php';
require_once APPPATH.'modules/widgets/widgets_system/emails/emails/models/Email_model.php';

class Emails_model extends Email_model
{
    public $sClassName = 'Email_model';

    public function __construct()
    {
        parent::__construct();

        $this->initDB('Emails',TUserRole::notLogged,TUserRole::notLogged,TUserRole::notLogged,TUserRole::notLogged);
    }

    protected function sortTopCategoriesByPosts($a, $b){
        return $a['order'] - $b['order'];
    }

    public function findEmail($sId){
        return $this->loadContainerById($sId,array(),true);
    }

    public function findAllUnSentEmails(){
        return $this->convertToArray($this->loadContainerByQuery(['Status'=>['$exists'=>false]] ));
    }

    public function findAllErrorEmails(){
        return $this->convertToArray($this->loadContainerByQuery(['Status'=>EmailStatus::Error] ));
    }

    public function insertEmail($sActionName,  $sSubject, $sMessage, $Destination='', $From='',$sURL='', $arrProperties=[],  $enStatus=0, $bStoreEmail=true)
    {
        if (($Destination == '')&&($this->MyUser->bLogged))
            $Destination = $this->MyUser->sID;

        if (($Destination == null)||((is_string($Destination))&&($Destination == '')))
        {
            //error
            return false;
        }

        $email = new Email_model(false);
        $email->setData($sActionName, $sSubject, $sMessage, $Destination, $From, $sURL, $arrProperties, $enStatus);

        if ($bStoreEmail) $bResult = $email->storeUpdate();
        else $bResult = true;

        $email->processTemplates();

        if ($bResult) return $email;
        else return false;
    }

    public function insertActionEmail($sActionName, $Destination='', $From='', $arrProperties=[], $enStatus=0, $bStoreEmail=true)
    {
        return $this->insertEmail($sActionName,'','',$Destination,$From,'',$arrProperties,$enStatus, $bStoreEmail);
    }

}