<?php

require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Api_notifications extends MY_AdvancedController
{

    public function getNewerNotifications()
    {
        if (!$this->MyUser->bLogged)
        {
            echo json_encode(["result"=>false,"message"=>"Error! Either you are not logged in or you don't have rights for this request"]);
            return false;
        }

        if (!isset($_POST["iLastNotificationDateSec"]))
        {
            echo json_encode(["result"=>false,"message"=>"POST - doesn't contain dtLastNotificationDate"]);
            return false;
        }
        $dtLastNotificationDateSec = new MongoDate((int)$_POST["iLastNotificationDateSec"]);

        $this->load->model('user_notifications/notifications_model', 'NotificationsModel');

        $arrNewerNotifications = $this->NotificationsModel->findNotificationsFromUserIdOlderThanDate('',$dtLastNotificationDateSec);

        $NotificationsController =  modules::load('user_notifications/notifications_controller');

        if (count ($arrNewerNotifications) > 0 )
        {

            $arrResult = ["result"=>true,"newerNotificationsHTMLEmbeddedCode"=>$NotificationsController->renderNotifications(false, $arrNewerNotifications),"newerNotificationsCount"=>count($arrNewerNotifications),
                          "iNewerLastNotificationDateSec"=>$arrNewerNotifications[count($arrNewerNotifications)-1]->dtCreationDate->sec, "iTotalNewNotifications"=>$NotificationsController->getNewNotificationsCount()];
            echo json_encode($arrResult);
            return true;
        } else
        {
            $arrResult = ["result"=>true,"message"=>"No new notifications","newerNotificationsCount"=>0,"iTotalNewNotifications"=>$NotificationsController->getNewNotificationsCount()];
            echo json_encode($arrResult);
            return false;
        }

    }

    public function viewedNewerNotifications()
    {
        if (!$this->MyUser->bLogged)
        {
            echo json_encode(["result"=>false,"message"=>"Error! Either you are not logged in or you don't have rights for this request"]);
            return false;
        }

        if (!isset($_POST["iLastNotificationDateSec"]))
        {
            echo json_encode(["result"=>false,"message"=>"POST - doesn't contain dtLastNotificationDate"]);
            return false;
        }
        $dtLastNotificationDateSec = new MongoDate((int)$_POST["iLastNotificationDateSec"]);

        $this->load->model('user_notifications/notifications_model', 'NotificationsModel');

        $arrNewerNotifications = $this->NotificationsModel->findNotificationsFromUserIdYoungerThanDate('',$dtLastNotificationDateSec);

        if (count ($arrNewerNotifications) > 0 )
        {

            foreach ($arrNewerNotifications as $newerNotification)
                if ($newerNotification->bNotificationNew)
                {
                    $newerNotification->bNotificationNew=false;
                    $newerNotification->storeUpdate();
                }

            $arrResult = ["result"=>true,"message"=>"Done"];
            echo json_encode($arrResult);
            return true;
        } else
        {
            $arrResult = ["result"=>false,"message"=>"No new notifications"];
            echo json_encode($arrResult);
            return false;
        }

    }

}