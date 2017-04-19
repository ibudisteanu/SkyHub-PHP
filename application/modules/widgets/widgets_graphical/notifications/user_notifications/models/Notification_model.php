<?php

require_once APPPATH.'core/models/MY_Advanced_model.php';

class Notification_model extends MY_Advanced_model
{
    //dtCreationDate from parent

    public $arrNotificationData;
    public $sUserId;
    public $sNotificationType;
    public $sNotificationTitle;
    public $bNotificationNew=true;
    public $sNotificationLink='';
    public $sNotificationText='';

    public function __construct()
    {
        parent::__construct(false);

        $this->initDB('UserNotifications',TUserRole::User,TUserRole::User,TUserRole::notLogged,TUserRole::User);

        $this->load->library('TimeLibrary',null,'TimeLibrary');
        $this->load->model('users/users_minimal','UsersMinimal');

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p);

        if (isset($p['Data'])) $this->arrNotificationData = $p['Data'];
        if (isset($p['Type'])) $this->sNotificationType = $p['Type'];
        if (isset($p['UserId'])) $this->sUserId = (string) $p['UserId'];
        if (isset($p['New'])) $this->bNotificationNew = $p['New'];
        if (isset($p['Link'])) $this->sNotificationLink = $p['Link'];
        if (isset($p['Text'])) $this->sNotificationText = $p['Text'];
        if (isset($p['Title'])) $this->sNotificationTitle = $p['Title'];
    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        $arrResult = array_merge($arrResult, array("Data"=>$this->arrNotificationData));
        $arrResult = array_merge($arrResult, array("Type"=>$this->sNotificationType));
        $arrResult = array_merge($arrResult, array("UserId"=>new MongoId($this->sUserId)));

        if ($this->bNotificationNew != true) $arrResult = array_merge($arrResult, array("New"=>$this->bNotificationNew));
        if ($this->sNotificationLink != '') $arrResult = array_merge($arrResult, array("Link"=>$this->sNotificationLink));
        if ($this->sNotificationText != '') $arrResult = array_merge($arrResult, array("Text"=>$this->sNotificationText));
        if ($this->sNotificationTitle != '') $arrResult = array_merge($arrResult, array("Title"=>$this->sNotificationTitle));

        return $arrResult;
    }

    public function renderNotification($bEcho=false)
    {
        $data['sText'] = $this->sNotificationText;
        $data['dtCreationDate'] = $this->getCreationDate();
        $data['dtCreationDateFullDateTime'] = $this->getCreationDateString();
        $data['arrData'] = $this->arrNotificationData;
        $data['sTitle'] = $this->sNotificationTitle;
        $data['sLink'] = $this->sNotificationLink;

        switch ($this->sNotificationType)
        {
            case 'system':
                return $this->load->view('user_notifications/templates/notification_system_view',$data,!$bEcho);

            case 'from-user':
                $data['User'] = $this->UsersMinimal->userByMongoId($this->arrNotificationData['fromUserId']);
                return $this->load->view('user_notifications/templates/notification_from_user_view',$data,!$bEcho);
        }


    }

}