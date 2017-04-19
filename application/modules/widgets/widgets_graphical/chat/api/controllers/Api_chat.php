<?php

require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Api_chat extends MY_AdvancedController
{

    public function getChatEmbeddedCode($sConversationId='')
    {
        if (!$this->MyUser->bLogged)
        {
            $arrResult = ["result"=>false,"message"=>"Error! Either you are not logged in or you don't have rights for this request"];
            echo json_encode($arrResult);
            return false;
        }

        $this->load->model('conversation/Conversations_model', 'ConversationsModel');
        $Conversation = $this->ConversationsModel->findConversationById($sConversationId);

        if ($Conversation == null)
        {
            $arrResult = ["result"=>false,"message"=>"Invalid Conversation Id"];
            echo json_encode($arrResult);
            return false;
        }

        $this->ViewChatController = modules::load('chat/view_chat_controller');
        $sContent  = $this->ViewChatController->renderChat($Conversation,false);

        if ( $sContent != '' )
        {
            //$dtLastMessageDate = $Conversation->getLastMessageDateChat();
            $conversationInitializationDates = $Conversation->getChatMessagesInitializationDates();

            $User = $this->MyUser;

            $this->load->model('opened_chats/Opened_chats_models', 'OpenedChatsModels');

            $arrResult = ["result"=>true,"message"=>"Done","sEmbeddedCode"=>$sContent,"sMyUserAvatar"=>$User->sAvatarPicture,"sMyUserFullName"=>$User->getFullName(),
                          "dtLastMessageDate"=>$conversationInitializationDates['dtFirstMessageDate'],"dtFirstMessageDate"=>$conversationInitializationDates['dtFirstMessageDate'],
                          "chatDialogInformation"=>$this->OpenedChatsModels->getChatDialogInformation('',$sConversationId)];
            echo json_encode($arrResult);
            return true;
        } else
        {
            $arrResult = ["result"=>false,"message"=>$sConversationId . " <b>Conversation not found </b>"];
            echo json_encode($arrResult);
            return false;
        }
    }

    public function getCreateConversationIDWithAuthors()
    {
        if (!$this->MyUser->bLogged) {
            echo json_encode(["result"=>false,"message"=>"Error! Either you are not logged in or you don't have rights for this request"]);
            return false;
        }

        $Authors = [];
        if (!isset($_POST['Authors'])) {
            echo json_encode(["result"=>false,"message"=>"Error! Authors specified was nt specified in the POST"]);
            return false;
        }
        $Authors = $_POST['Authors'];

        $this->load->model('conversation/Conversations_model', 'Conversations');

        $conversations = $this->Conversations->findConversationsWithAuthors($Authors);
        $conversation = null;

        if (is_array($conversations))
        {
            if (count($conversations) > 0)
                $conversation = $conversations[0];
        }
        else
            $conversation = $conversations;

        if ($conversation == null)
            $conversation = $this->Conversations->createConversation($Authors);

        if ($conversation != null)
        {
            $this->load->model('opened_chats/Opened_chats_models', 'OpenedChatsModels');
            $this->OpenedChatsModels->openNewChatConversation((string)$conversation->sID,1,TOpenedConversationMaximizationStatus::conversationMaximized,$this->MyUser->sID);


            $arrResult = ["result"=>true,"message"=>"Conversation Id","sConversationId"=>(string)$conversation->sID];
            echo json_encode($arrResult);
            return true;
        } else
        {
            echo json_encode(["result"=>false,"message"=>"Error! Couldn't find the Conversation Id"]);
            return false;
        }
    }

    public function getRefreshChats()
    {
        if (!$this->MyUser->bLogged) {
            echo json_encode(["result"=>false,"message"=>"Error! Either you are not logged in or you don't have rights for this request"]);
            return false;
        }

        $arrChatConversationsOnClient = [];
        if (isset($_POST['ChatConversations']))
            $arrChatConversationsOnClient = $_POST['ChatConversations'];

        $this->load->model('opened_chats/Opened_chats_models', 'OpenedChatsModels');
        $this->load->model('conversation/Conversations_model', 'ConversationsModel');

        $openedChatsDB = $this->OpenedChatsModels->findOpenedChatsByAuthorId();

        $result = [];

        //Calculating new Messages
        if (($openedChatsDB != null)&&(is_array($openedChatsDB->arrOpenedConversations)))
        foreach ($openedChatsDB->arrOpenedConversations as $openedChatConversationDB)
        if (isset($openedChatConversationDB->sConversationId))
        {
            $foundChatConversationOnClient = null;
            foreach ($arrChatConversationsOnClient as $conversationOnClient)
                if ($conversationOnClient['ConversationId'] == $openedChatConversationDB->sConversationId)
                    $foundChatConversationOnClient = $conversationOnClient;

            /*var_dump($openedChatsDB->arrOpenedConversations);
            var_dump($arrChatConversationsOnClient );*/

            $dataResult = ['ConversationId'=>(string)$openedChatConversationDB->sConversationId,'chatDialogInformation'=>$this->OpenedChatsModels->getChatDialogInformation('',$openedChatConversationDB->sConversationId)];
            if ($foundChatConversationOnClient != null)
            {
                $ConversationDB = $this->ConversationsModel->findConversationById($openedChatConversationDB->sConversationId);

                //computing the new messages
                if ((!isset($foundChatConversationOnClient['LastMessageDate']))&&(!isset($foundChatConversationOnClient['FirstMessageDate']))){
                    echo json_encode(["result"=>false,"message"=>"Error! FIRST or LAST message date has not been specified in post data"]);
                    return false;
                }
                if (isset($foundChatConversationOnClient['LastMessageDate']))
                    $arrProcessedMessages = $ConversationDB->getRecentlyMessages($foundChatConversationOnClient['LastMessageDate']);
                else
                    if (isset($foundChatConversationOnClient['FirstMessageDate']))
                    $arrProcessedMessages = $ConversationDB->getEarlierMessages($foundChatConversationOnClient['FirstMessageDate']);

                $arrReturnJSONMessages = [];
                $this->ViewMessageController = modules::load('message/view_message_controller');
                if (is_array($arrProcessedMessages)) {
                    $index = count($arrProcessedMessages)-1;
                    while ($index >= 0)
                    {
                        $processedMessage = $arrProcessedMessages[$index];
                        array_push($arrReturnJSONMessages, $this->ViewMessageController->getJSONMessage($processedMessage, $ConversationDB));

                        $index--;
                    }
                }

                $dtLastMessageDate = $ConversationDB->getLastMessageDateChat($arrProcessedMessages );
                $dtFirstMessageDate = $ConversationDB->getFirstMessageDateChat($arrProcessedMessages );

                $dataResult = array_merge($dataResult,['ReturnMessages'=>$arrReturnJSONMessages,'dtLastMessageDate'=>$dtLastMessageDate,'dtFirstMessageDate'=>$dtFirstMessageDate]);

            } else
            {
                $dataResult = array_merge($dataResult,['ReturnMessages'=>[]]);
            }
            array_push($result, $dataResult);
        }


        $arrResult = ["result"=>true,"message"=>"Conversations List", "arrOpenedChats"=>$result];
        echo json_encode($arrResult);
        return true;

    }

    public function closeChatConversation()
    {
        if (!$this->MyUser->bLogged) {
            echo json_encode(["result" => false, "message" => "Error! Either you are not logged in or you don't have rights for this request"]);
            return false;
        }

        if (!isset($_POST['ConversationId']))
        {
            echo json_encode(["result"=>false,"message"=>"The ConversationId POST is not assigned"]);
            return false;
        }
        $sConversationId = $_POST['ConversationId'];

        $this->load->model('opened_chats/Opened_chats_models', 'OpenedChatsModels');
        $this->OpenedChatsModels->closeOpenedChatsConversation($sConversationId);

        echo json_encode(["result"=>true,"message"=>"Closed Chat Conversation"]);
        return true;
    }

    public function resetNewMessagesNotification()
    {
        if (!$this->MyUser->bLogged) {
            echo json_encode(["result" => false, "message" => "Error! Either you are not logged in or you don't have rights for this request"]);
            return false;
        }

        if (!isset($_POST['ConversationId']))
        {
            echo json_encode(["result"=>false,"message"=>"The ConversationId POST is not assigned"]);
            return false;
        }
        $sConversationId = $_POST['ConversationId'];

        $this->load->model('opened_chats/Opened_chats_models', 'OpenedChatsModels');
        $this->OpenedChatsModels->resetNewMessagesNotification($sConversationId);

        echo json_encode(["result"=>true,"message"=>"New Messages Notification has been reset"]);
        return true;
    }

    public function changeMaximizationChatDialogStatus()
    {
        if (!$this->MyUser->bLogged) {
            echo json_encode(["result" => false, "message" => "Error! Either you are not logged in or you don't have rights for this request"]);
            return false;
        }

        if (!isset($_POST['ConversationId']))
        {
            echo json_encode(["result"=>false,"message"=>"The ConversationId POST is not assigned"]);
            return false;
        }
        $sConversationId = $_POST['ConversationId'];

        if (!isset($_POST['NewMaximizationValue']))
        {
            echo json_encode(["result"=>false,"message"=>"The NewMaximizationValue POST is not assigned"]);
            return false;
        }
        $iNewMaximizationValue = $_POST['NewMaximizationValue'];

        $this->load->model('opened_chats/Opened_chats_models', 'OpenedChatsModels');
        $this->OpenedChatsModels->changeMaximizationChatDialogStatus($sConversationId, $iNewMaximizationValue);

        echo json_encode(["result"=>true,"message"=>"New Messages Notification has been reset"]);
        return true;
    }

    public function postMessageChat()
    {
        if (!$this->MyUser->bLogged)
        {
            echo json_encode(["result"=>false,"message"=>"Error! Either you are not logged in or you don't have rights for this request"]);
            return false;
        }

        if (!isset($_POST["ConversationId"]))
        {
            echo json_encode(["result"=>false,"message"=>"POST - ConversationId is missing"]);
            return false;
        }

        if (!isset($_POST["MessageBody"]))
        {
            $arrResult = ["result"=>false,"message"=>"POST - MessageBody is missing"];
            echo json_encode($arrResult);
            return false;
        }

        $sConversationId = $_POST['ConversationId'];
        $sMessageBody = $_POST['MessageBody'];
        $User = $this->MyUser;

        $this->load->model('conversation/Conversations_model', 'Conversations');

        $NewMessage = $this->Conversations->postMessageInConversation($sConversationId, $sMessageBody, $User);

        if ($NewMessage != null)
        {

            $arrResult = ["result"=>true,"message"=>"Message has been successfully added","sNewMessageChatId"=>(string)$NewMessage->sID];
            echo json_encode($arrResult);
            return true;
        } else
        {
            $arrResult = ["result"=>false,"message"=>"Message Couldn't be processed"];
            echo json_encode($arrResult);
            return false;
        }

    }

}