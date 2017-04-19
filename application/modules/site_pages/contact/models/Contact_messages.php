<?php

require_once APPPATH.'modules/site/contact/models/Contact_message.php';

class Contact_messages extends MY_Advanced_model
{
    public $sClassName = 'Contact_message';

    public function __construct($bEnableMaterializedParents = false)
    {
        parent::__construct($bEnableMaterializedParents);

        $this->initDB('ContactMessages',TUserRole::Admin,TUserRole::notLogged,TUserRole::notLogged,TUserRole::User);
    }

    public function loadMessageFromId($sID)
    {
        return $this->loadContainerById($sID,array(),true);
    }

    public function loadMessagesFromUserNameOrEmail($sInput)
    {
        $result = $this->loadContainerByFieldName("Username",$sInput,array(),true);
        if ($result != null) return $result;

        $result = $this->loadContainerByFieldName("Email",$sInput,array(),true);
        if ($result != null) return $result;
    }

    public function loadAllMessages()
    {
        return $this->findAll();
    }

    public function createNewMessage($sFullName, $sEmail, $sMessageTopic, $sMessageBody )
    {
        $newObject = new Contact_message($sFullName, $sEmail, $sMessageTopic, $sMessageBody);
        $newObject->storeUpdate();

        return $newObject;
    }

}