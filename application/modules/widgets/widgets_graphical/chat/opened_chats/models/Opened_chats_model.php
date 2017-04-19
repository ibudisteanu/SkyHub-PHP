<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Advanced_model.php';
require_once APPPATH.'modules/widgets/chat/opened_chats/models/Opened_conversation_model.php';

class Opened_chats_model extends MY_Advanced_model
{
    public $sClassName = 'Opened_chats_model';

    public $arrOpenedConversations;

    //store in the Base

    //public $sAuthorId;
    //public $dtCreationDate
    //public $dtLastChangeDate

    public function __construct()
    {
        parent::__construct(true);

        $this->initDB('ConversationsOpened',TUserRole::notLogged,TUserRole::notLogged,TUserRole::User,TUserRole::User);
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p,$bEnableChildren);

        if (isset($p["Conversations"]))
        {
            if (!isset($this->arrOpenedConversations)) $this->arrOpenedConversations = [];
            $arrConversationsCursor = $p["Conversations"];
            foreach ($arrConversationsCursor as $conversationCursor)
            {
                $openedConversation = new Opened_conversation_model();
                $openedConversation->readCursor($conversationCursor);

                array_push($this->arrOpenedConversations, $openedConversation);
            }
        }

        //$this->AlertsContainer->addAlert('g_msgGeneralSuccess','success','Logged in successfully');
    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if ((isset($this->arrOpenedConversations)))
        {
            $arrOpenedConversationsSerialized = [];
            foreach ($this->arrOpenedConversations as $openedConversation)
                array_push($arrOpenedConversationsSerialized, $openedConversation->serializeProperties());

            $arrResult = array_merge($arrResult, array("Conversations"=>$arrOpenedConversationsSerialized));
        }

        return $arrResult;
    }

    public function closeOpenedConversation($sConversationId)
    {
        $iPosition = $this->findOpenedConversation($sConversationId);

        if ($iPosition != -1) {
            unset($this->arrOpenedConversations[$iPosition]);
            return true;
        }
        return false;
    }

    public function addOpenedConversation($sConversationId, $enConversationMaximizationStatus = TOpenedConversationMaximizationStatus::conversationMaximized)
    {
        $iPosition = $this->findOpenedConversation($sConversationId);
        if ( $iPosition == -1)
        {
            $openedConversation = new Opened_conversation_model();
            $openedConversation->sConversationId = $sConversationId;
            $openedConversation->enConversationMaximizationStatus = $enConversationMaximizationStatus;

            if (!isset($this->arrOpenedConversations)) $this->arrOpenedConversations = [];

            array_push($this->arrOpenedConversations, $openedConversation);
            return $openedConversation;
        } else return $this->arrOpenedConversations[$iPosition];
    }

    public function findOpenedConversation($sConversationId)
    {
        for ($i=0; $i < count($this->arrOpenedConversations); $i++)
        {
            $conversation = $this->arrOpenedConversations[$i];
            if ($conversation->sConversationId == $sConversationId)
                return $i;

        }
        return -1;
    }

    public function getOpenedChats()
    {
        $result = [];

        foreach ($this->arrOpenedConversations as $openedConversation)
            array_push($result, ['ConversationId'=>$openedConversation->sConversationId,'MaximizationStatus'=>(int)$openedConversation->enConversationMaximizationStatus]);

        return $result;
    }

}
