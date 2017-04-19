<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View_chat_controller extends  MY_Controller
{
    public $ViewConversationController;

    public function __construct()
    {
        parent::__construct();

        $this->ViewConversationController = modules::load('conversation/view_conversation_controller');

        $this->Template->addFooterContentArray("<script>".$this->renderModuleView('js/chat_functions.js.php',null,TRUE)."</script>");
        $this->Template->addFooterContentArray($this->renderModuleView('chat_loader_view.php',null,TRUE));

        modules::load('notifications_api/notifications_api_controller');
        modules::load('sounds/sounds_controller');

        $this->renderChatSupport();
    }

    public function renderChat($Conversation='', $bEcho=false)
    {
        if (is_string($Conversation))
        {
            $this->load->model('conversation/Conversations_model', 'ConversationsModel');
            $Conversation = $this->ConversationsModel->findConversationById($Conversation);
        }

        if ($Conversation == null)
            return 'NO CHAT FOUND';

        $data ['Conversation'] = $Conversation;
        $data ['sConversationId'] = (string)$Conversation->sID;

        $this->load->model('opened_chats/Opened_chats_models', 'OpenedChatsModels');
        $data ['chatDialogInformation'] = $this->OpenedChatsModels->getChatDialogInformation('',$Conversation->sID);

        $this->load->model('users/users_minimal','UsersMinimal');

        $sContent = $this->renderModuleView('chat_dialog_view.php',$data,true);

        if ($bEcho == false) return $sContent;
        else
            $this->Template->addFooterContentArray($sContent);
    }

    protected function renderChatSupport()
    {
        if (!(($this->MyUser->bLogged)&&(!TUserRole::checkUserRights(TUserRole::Admin)))) return ;

        $this->Template->addFooterContentArray("<script>".$this->renderModuleView('js/chat_support_dialog.js.php',null,TRUE)."</script>");

    }


}