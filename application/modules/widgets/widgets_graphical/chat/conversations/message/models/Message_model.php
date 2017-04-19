<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Advanced_model.php';

class Message_model extends MY_Advanced_model
{
    public $sClassName = 'Message_model';

    public $sBody;

    //store in the Base

    //public $sAuthorId;
    //public $dtCreationDate
    //public $dtLastChangeDate


    public function __construct()
    {
        parent::__construct(true);
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p,$bEnableChildren);

        if (isset($p["Body"])) $this->sBody = $p["Body"];

        //$this->AlertsContainer->addAlert('g_msgGeneralSuccess','success','Logged in successfully');
    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if (isset($this->sBody)) $arrResult = array_merge($arrResult, array("Body"=>$this->sBody));

        return $arrResult;
    }

}
