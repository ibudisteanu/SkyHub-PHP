<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View_conversation_controller extends  MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->ViewMessageController = modules::load('message/view_message_controller');
    }

    public function renderConversationTitle($Conversation)
    {
        if (is_string($Conversation))
        {
            $this->load->model('conversation/Conversations_model', 'ConversationsModel');
            $Conversation = $this->ConversationsModel->findConversationById($Conversation);
        }

        return $Conversation->getConversationTitle();
    }

    public function renderConversation($Conversation, $iLastMessagesCount=10, $bEcho=false)
    {
        $sContent = '';

        if (is_string($Conversation))
        {
            $this->load->model('conversation/Conversations_model', 'ConversationsModel');
            $Conversation = $this->ConversationsModel->findConversationById($Conversation);
        }

        if (($Conversation != null)&&(is_object($Conversation))&&(get_class($Conversation) == 'Conversation_model'))
        {
            $iCount = 0;
            $i = count($Conversation->arrMessages)-1;

            while (($i >= 0)&&($iCount < $iLastMessagesCount))
            {
                $message = $Conversation->arrMessages[$i];
                //$messageRender = $Conversation->ViewMessageController->determineMessageRender($Conversation->arrAuthors, $message);

                $sContent = $this->ViewMessageController->renderMessage($message, $Conversation->arrAuthors) . $sContent;

                $iCount++; $i--;
            }
        }


        if ($bEcho == false) return $sContent;
        else echo $sContent;
    }


}