<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Advanced_model.php';
require_once APPPATH.'modules/widgets/chat/opened_chats/models/Opened_conversation_model.php';
require_once APPPATH.'modules/widgets/chat/opened_chats/models/Opened_chats_model.php';

class Opened_chats_models extends MY_Advanced_model
{
    public $sClassName = 'Opened_chats_model';

    public function __construct()
    {

        parent::__construct(true);

        $this->initDB('ConversationsOpened',TUserRole::notLogged,TUserRole::notLogged,TUserRole::User,TUserRole::User);

        /*$this->load->library('StringsAdvanced',null,'StringsAdvanced');
        $this->load->library('TimeLibrary',null,'TimeLibrary');*/
    }

    public function findOpenedChatsByAuthorId($sAuthorId='')
    {
        if ($sAuthorId == '')
            if (!$this->MyUser->bLogged) return null;
            else $sAuthorId = $this->MyUser->sID;

        return $this->loadContainerByAuthorId($sAuthorId);
    }

   /* public function getOpenedChatsFromAuthorId($sAuthorId='', $arrChatConversationsOnClient)
    {

        if ($sAuthorId == '')
            if (!$this->MyUser->bLogged) return [];
            else $sAuthorId = $this->MyUser->sID;

        $openedChatsDB = $this->loadContainerByAuthorId($sAuthorId,array(),true);

        $result = [];

        return $result;

    }*/

    public function closeOpenedChatsConversation($sConversationId, $Users=[])
    {
        if ($Users==[])
            if (!$this->MyUser->bLogged) return false;
            else $Users = [$this->MyUser->sID];

        $result = true;
        foreach ($Users as $user)
        {
            $openedChats = $this->findOpenedChatsByAuthorId($user);

            if ($openedChats!= null) {
                $result = $openedChats->closeOpenedConversation($sConversationId) && $result;
                $openedChats->storeUpdate();
            }
        }

        return $result;

    }

    //it also increment New Messages
    public function openNewChatConversation($sConversationId, $iNewMessagesCount=1, $enConversationMaximizationStatus = TOpenedConversationMaximizationStatus::conversationMaximized, $Users=[])
    {
        //var_dump($Users);
        if (is_string($Users)&&($Users !='')) $Users = [$Users];
        else
        if ($Users==[])
            if (!$this->MyUser->bLogged) return false;
            else $Users = [$this->MyUser->sID];

        $result = true;
        foreach ($Users as $user)
        {
            $openedChats = $this->findOpenedChatsByAuthorId($user);

            if ($openedChats == null)
            {
                $openedChats = new Opened_chats_model();
                $openedChats->sAuthorId = $user;
            }

            $iOpenedConversationPosition = $openedChats->findOpenedConversation($sConversationId);
            if ( $iOpenedConversationPosition == -1)
            {
                $openedConversation = $openedChats->addOpenedConversation($sConversationId, $enConversationMaximizationStatus);

                if (($iNewMessagesCount > 0)&&($user != $this->MyUser->sID))
                    $openedConversation->iNewMessages += $iNewMessagesCount;

                $openedChats->storeUpdate();
            } else//Optimization
                if (($iNewMessagesCount > 0)&&($user != $this->MyUser->sID)) {
                    $openedChats->arrOpenedConversations[$iOpenedConversationPosition]->iNewMessages += $iNewMessagesCount;
                    $openedChats->storeUpdate();
                }
        }

        return true;
    }

    public function getOpenedConversationFromUserAndConversationIds($sUserId='', $sConversationId='')
    {
        if ($sUserId== '') $sUserId= $this->MyUser->sID;

        $openedChats = $this->findOpenedChatsByAuthorId($sUserId);
        if ($openedChats != null) {
            $iOpenedConversation = $openedChats->findOpenedConversation($sConversationId);
            if ($iOpenedConversation != -1) {
                return ['openedChats'=>$openedChats,'openedConversation'=>$openedChats->arrOpenedConversations[$iOpenedConversation]];
            }
        }
        return [];
    }

    public function getChatDialogInformation($sUserId='', $sConversationId='')
    {
        $data = $this->getOpenedConversationFromUserAndConversationIds($sUserId, $sConversationId);

        if ($data != []) {
            $openedConversation = $data['openedConversation'];
            return ['iNewMessages' => (int)$openedConversation->iNewMessages, 'iConversationMaximizationStatus' => (int)$openedConversation->enConversationMaximizationStatus];
        }

        return ['iNewMessages'=>0,'iConversationMaximizationStatus'=>1];
    }

    public function resetNewMessagesNotification($sConversationId, $sUserId='')
    {
        $data = $this->getOpenedConversationFromUserAndConversationIds($sUserId, $sConversationId);
        if ($data != [])
        {
            $data['openedConversation']->iNewMessages = 0;
            $data['openedChats']->storeUpdate();

            return $data['openedConversation'];
        }
        return null;
    }



    public function changeMaximizationChatDialogStatus($sConversationId, $iNewMaximizationValue, $sUserId='')
    {
        $data = $this->getOpenedConversationFromUserAndConversationIds($sUserId, $sConversationId);
        if ($data != [])
        {
            $data['openedConversation']->enConversationMaximizationStatus = $iNewMaximizationValue;
            $data['openedChats']->storeUpdate();

            return $data['openedConversation'];
        }
        return null;
    }

}
