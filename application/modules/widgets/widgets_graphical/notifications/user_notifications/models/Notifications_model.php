<?php

require_once APPPATH.'modules/widgets/widgets_graphical/notifications/user_notifications/models/Notification_model.php';

class Notifications_model extends MY_Advanced_model
{
    public $sClassName = 'Notification_model';

    public function __construct($bEnableMaterializedParents = false)
    {
        parent::__construct($bEnableMaterializedParents);

        $this->initDB('UserNotifications',TUserRole::User,TUserRole::User,TUserRole::notLogged,TUserRole::User);
    }

    public function findNotificationsFromUserId($sUserId='')
    {
        if ($sUserId == '') $sUserId = $this->MyUser->sID;
        if ($sUserId == '') return [];
        return $this->convertToArray($this->loadContainerByFieldName("UserId",new MongoId($sUserId),[],[]));
    }

    public function findNotificationsFromUserIdOlderThanDate($sUserId, $dtLastDate)
    {
        $arrNotifications = $this->findNotificationsFromUserId($sUserId);

        $arrNewerNotifications = [];
        foreach ($arrNotifications as $notification)
            if ($notification->dtCreationDate->sec > $dtLastDate->sec)
                array_push($arrNewerNotifications, $notification);

        return $arrNewerNotifications;
    }

    public function findNotificationsFromUserIdYoungerThanDate($sUserId, $dtLastDate)
    {
        $arrNotifications = $this->findNotificationsFromUserId($sUserId);

        $arrNewerNotifications = [];
        foreach ($arrNotifications as $notification)
            if ($notification->dtCreationDate->sec <= $dtLastDate->sec)
                array_push($arrNewerNotifications, $notification);

        return $arrNewerNotifications;
    }



}