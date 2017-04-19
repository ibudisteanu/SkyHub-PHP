<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Advanced_model.php';
require_once APPPATH.'modules/widgets/chat/conversations/conversation/models/Conversation_model.php';

class Conversations_model extends MY_Advanced_model
{
    public $sClassName = 'Conversation_model';

    public function __construct()
    {

        parent::__construct(true);

        $this->initDB('Conversations',TUserRole::notLogged,TUserRole::notLogged,TUserRole::User,TUserRole::User);

        /*$this->load->library('StringsAdvanced',null,'StringsAdvanced');
        $this->load->library('TimeLibrary',null,'TimeLibrary');*/
    }

    public function findConversationById($sID='')
    {
        return $this->loadContainerById($sID,array(),true);
    }

    public function findConversationsByAuthors($arrAuthors)
    {
        sort($arrAuthors);
        return $this->convertToArray($this->loadContainerByQuery(["Authors" => ['$all' => $arrAuthors]]));
    }

    public function findConversationsWithAuthors($Authors, $sUserId='')
    {
        $this->includeMyUserAuthor($Authors, $sUserId);

        return $this->findConversationsByAuthors($Authors);
    }

    public function findMyConversations($sUserId='')
    {
        if ($sUserId == '') $sUserId = $this->MyUser->sID;
        if ($sUserId == '') return [];

        return $this->findConversationsByAuthors([$sUserId]);
    }

    public function createConversation($Authors)
    {
        $conversation = new Conversation_model();

        $this->includeMyUserAuthor($Authors);
        $conversation->arrAuthors = $Authors;

        $conversation->storeUpdate();

        return $conversation;
    }

    public function postMessageInConversation($sConversationId, $sMessageBody, $AuthorUser)
    {
        $Message = new Message_model();
        $Message->sBody = $sMessageBody;
        $Message->sAuthorId = $AuthorUser->sID;

        //First solution, but it is LAZY

        $Conversation = $this->findConversationById($sConversationId);
        if ($Conversation != null)
        {
            array_push($Conversation->arrMessages, $Message);
            $Conversation->storeUpdate();

            $this->load->model('opened_chats/Opened_chats_models', 'OpenedChatsModels');
            $this->OpenedChatsModels->openNewChatConversation($sConversationId,1,TOpenedConversationMaximizationStatus::conversationMaximized,$Conversation->arrAuthors);

            return $Message;
        }

        return null;
    }

    protected function includeMyUserAuthor(&$Authors, $sUserId='')
    {
        if ($sUserId == '') $sUserId = $this->MyUser->sID;
        if ($sUserId == '') return [];

        if (is_array($Authors))
            array_push($Authors, $sUserId);
        else
            if (is_string($Authors) )
                $Authors = [$sUserId, $Authors];
    }

}
