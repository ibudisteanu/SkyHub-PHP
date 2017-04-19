<?php

class Contact_message extends MY_Advanced_model
{

    public $sMessageTopic;
    public $sMessageBody;
    public $sFullName;
    public $sUserName;

    public $sEmail;

    function __construct($sFullName, $sEmail, $sMessageTopic, $sMessageBody )
    {
        parent::__construct(false);

        $this->initDB('ContactMessages',TUserRole::Admin,TUserRole::notLogged,TUserRole::notLogged,TUserRole::User);

        $this->sEmail = $sEmail;
        $this->sFullName = $sFullName;
        $this->sMessageBody = $sMessageBody;
        $this->sMessageTopic = $sMessageTopic;

        $this->sAuthorId = $this->MyUser->sID;
        $this->sUserName = $this->MyUser->sUserName;
    }


    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p);

        if (isset($p['MessageTopic'])) $this->sMessageTopic = $p['MessageTopic'];
        if (isset($p['MessageBody'])) $this->sMessageBody = $p['MessageBody'];
        if (isset($p['FullName'])) $this->sFullName = $p['FullName'];
        if (isset($p['Username'])) $this->sUserName = $p['Username'];
        if (isset($p['Email'])) $this->sEmail = $p['Email'];
    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if ($this->sUserName != '') $arrResult = array_merge($arrResult, array("Username"=>$this->sUserName));
        if ($this->sMessageTopic != '') $arrResult = array_merge($arrResult, array("MessageTopic"=>$this->sMessageTopic));
        if ($this->sMessageBody != '') $arrResult = array_merge($arrResult, array("MessageBody"=>$this->sMessageBody));
        if ($this->sFullName != '') $arrResult = array_merge($arrResult, array("FullName"=>$this->sFullName));
        if ($this->sEmail != '') $arrResult = array_merge($arrResult, array("Email"=>$this->sEmail));

        return $arrResult;
    }


}