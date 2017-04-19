<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Advanced_model.php';

abstract class TOpenedConversationMaximizationStatus
{
    const conversationMinimized = 0;
    const conversationMaximized = 1;
}

class Opened_conversation_model extends MY_Advanced_model
{
    public $sClassName = 'Opened_chats_model';

    public $sConversationId;
    public $enConversationMaximizationStatus;
    public $iNewMessages;

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

        if (isset($p["ConversationId"])) $this->sConversationId = $p["ConversationId"];
        if (isset($p["MaximizationStatus"])) $this->enConversationMaximizationStatus = $p['MaximizationStatus'];
        if (isset($p["NewMessages"])) $this->iNewMessages = $p['NewMessages'];

        //$this->AlertsContainer->addAlert('g_msgGeneralSuccess','success','Logged in successfully');
    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if (isset($this->sConversationId)) $arrResult = array_merge($arrResult, array("ConversationId"=>$this->sConversationId));
        if (isset($this->enConversationMaximizationStatus)) $arrResult = array_merge($arrResult, array("MaximizationStatus"=>$this->enConversationMaximizationStatus));
        if (isset($this->iNewMessages)) $arrResult = array_merge($arrResult, array("NewMessages"=>$this->iNewMessages));

        return $arrResult;
    }

}
