<?php

class Notifications_controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct(false);
    }

    public function createDummyNotifications()
    {
        $addNotificationsController = modules::load('user_notifications/Add_notifications_controller');

        $addNotificationsController->addNotificationSystem('1st Notification','New Notification','fa fa-users text-aqua');
        $addNotificationsController->addNotificationSystem('2st Notification','New Notification','fa fa-users text-aqua');
        $addNotificationsController->addNotificationSystem('3st Notification','New Notification','fa fa-users text-aqua');

        $addNotificationsController->addNotificationFromUser($this->MyUser->sID,'4st Notification','New comment from','57557b1908b8f7840400002b');
        $addNotificationsController->addNotificationFromUser($this->MyUser->sID,'5st Notification','New comment from','5756be9708b8f7f41d000033');
    }

    protected $arrNotifications=[];
    public function getNotifications()
    {
        if ($this->arrNotifications != []) return $this->arrNotifications;

        if (!$this->MyUser->bLogged) return;

        $sUserId = $this->MyUser->sID;

        $this->load->model('user_notifications/notifications_model', 'NotificationsModel');
        $this->arrNotifications = $this->NotificationsModel->findNotificationsFromUserId($sUserId);

        /*for ($i=count($arrNotifications)-1; $i>=0; $i++)
            array_push($this->arrNotifications, $arrNotifications[$i]);*/

        if (($this->arrNotifications == null)||(!is_array($this->arrNotifications)))
            $this->arrNotifications = [];

        return $this->arrNotifications;
    }

    protected $arrNewNotifications=[];
    public function getNewNotifications()//unread notifications
    {
        if ($this->arrNewNotifications != []) return $this->arrNewNotifications;

        $arrNotifications = $this->getNotifications();

        foreach ($arrNotifications as $notification)
            if ($notification->bNotificationNew)
            {
                array_push($this->arrNewNotifications, $notification);
            }

        return $this->arrNewNotifications;
    }

    public function getNewNotificationsCount(){
        return count($this->getNewNotifications());
    }

    public function renderNotificationsMenu()
    {
        if (!$this->MyUser->bLogged) return;

        //MANUALLY rendered
        $data['iNewNotificationsCount'] = $this->getNewNotificationsCount();
        $data['newNotificationsContent'] = $this->renderNotifications(false);

        $this->renderModuleView('menu_user_notifications_view', $data);

        $iLastNotificationDateSec=0;
        if (count($this->arrNotifications) > 0)
            $iLastNotificationDateSec = $this->arrNotifications[count($this->arrNotifications)-1]->dtCreationDate->sec;

        $this->renderNotificationsLoader($iLastNotificationDateSec);
    }

    public function renderNotifications($bEcho = false, $arrNotifications=[])
    {
        if ($arrNotifications == []) $arrNotifications = $this->getNotifications();

        $sContent = '';
        for ($i=count($arrNotifications)-1; $i>=0; $i--)
        {
            $newNotification = $arrNotifications[$i];

            $sContent .= $newNotification->renderNotification( $bEcho ) ;
        }

        if (!$bEcho) return $sContent;
        else return '';
    }

    public function renderNotificationsLoader($iLastNotificationDateSec=0)
    {
        $this->BottomScriptsContainer->addScriptResFile(base_url(defined('WEBSITE_OFFLINE') ? "app/res/js/notifications-loader.js" : "assets/min-js/notifications-loader-min.js"));
        $this->BottomScriptsContainer->addScript("initializeNewerUserNotifications($iLastNotificationDateSec)", TRUE);
    }

}