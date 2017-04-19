<?php

require_once APPPATH.'modules/widgets/widgets_graphical/notifications/user_notifications/models/Notification_model.php';

class Add_notifications_controller extends MY_Advanced_model
{
    protected function addNotification($sNotificationType='default', $sText='', $sTitle='', $arrNotificationData=[],$sUserId='', $sNotificationLink='', $bNotificationNew=true)
    {
        if ($sUserId == '') $sUserId = $this->MyUser->sID;
        if ($sUserId == '') return null;

        $objNotification = new Notification_model();
        $objNotification->sUserId = $sUserId;
        $objNotification->sNotificationType = $sNotificationType;
        $objNotification->arrNotificationData = $arrNotificationData;
        $objNotification->arrNotificationData = $arrNotificationData;
        $objNotification->bNotificationNew = $bNotificationNew;
        $objNotification->sNotificationText = $sText;
        $objNotification->sNotificationTitle = $sTitle;
        $objNotification->sNotificationLink = $sNotificationLink;

        $objNotification->storeUpdate();

        return $objNotification;
    }

    protected function getEmailAddressFromUserId($sUserId='')
    {
        if ($sUserId == '') $sUserId = $this->MyUser->sID;

        $this->load->model('users/Users_minimal','UsersMinimalModel');
        $User = $this->UsersMinimalModel->userByMongoId($sUserId,['Email']);
        if ($User != null)
            return $User->sEmail;

        return '';
    }

    /*
     * Create a new Notification by the System (there source is SkyHub)
     */
    public function addNotificationSystem($sText, $sTitle, $sIcon='', $sDestinationUserId='', $sNotificationURL='')
    {
        $emailAddress = $this->getEmailAddressFromUserId($sDestinationUserId);

        if ($emailAddress != null)
            modules::load('emails/email_controller')->sendActionEmail('notification-email-system',$emailAddress,'',['Text'=>$sText,'Title'=>$sTitle,'Icon'=>$sIcon]);

        return $this->addNotification('system',$sText,$sTitle,($sIcon != '' ? ['icon'=>$sIcon] : []), $sDestinationUserId, $sNotificationURL);
    }

    /*
     * Create a new Notification sent by SOMEBODY
     */
    public function addNotificationFromUser($sDestinationUserId, $sText, $sTitle, $sSourceUserId='', $sNotificationURL='')
    {
        if ($sSourceUserId == '') $sSourceUserId = $this->MyUser->sID;

        $emailAddressForDestination = $this->getEmailAddressFromUserId($sDestinationUserId);

        if ($emailAddressForDestination != null)
            modules::load('emails/email_controller')->sendActionEmail('notification-email-from-user',$emailAddressForDestination,'',['Text'=>$sText,'Title'=>$sTitle,'SourceUserId'=>$sSourceUserId]);

        return $this->addNotification('from-user', $sText, $sTitle, ['fromUserId'=>new MongoId($sSourceUserId)],$sDestinationUserId, $sNotificationURL);
    }


}