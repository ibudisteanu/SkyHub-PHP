<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'modules/users/my_user/user/user_advanced_properties/third_parties_social_networks/models/Third_party_social_network.php';
require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Send_facebook_notification_controller extends MY_AdvancedController
{
    public function sendFacebookNotificationToUser($User)
    {
        if ($User == '')  $User = $this->MyUser;

        if ((is_string($User)) && ($User != ''))
        {
            $this->load->model('users/Users','UsersModel');
            $User = $this->UsersModel->userByMongoId($User);
        }

        if (($User == null) || ($User=='')) throw new Exception('No user specified');

        $facebookSocialId = null;  $facebookAccessToken = null;
        if ($User->ThirdPartiesSocialNetworks != null)
        {
            $facebookSocialId = $User->ThirdPartiesSocialNetworks->getSocialIDFromSocialNetworkName('facebook');
            $facebookAccessToken = $User->ThirdPartiesSocialNetworks->getSocialAccessTokenFromSocialNetworkName('facebook');
        }

        //var_dump($User->ThirdPartiesSocialNetworks);

        if ($facebookSocialId == null) throw new Exception('No Facebook Social ID');
        if ($facebookAccessToken == null) throw new Exception('No Facebook Access Token');

        $this->load->library('facebook/Facebook_notification_user_library');

        var_dump($facebookSocialId);
        var_dump($facebookAccessToken['sToken']);

        try{
            $this->facebook_notification_user_library->sendUserNotification($facebookSocialId, $facebookAccessToken['sToken']);
        } catch (Exception $exception)
        {
            echo 'Error: '.$exception->getMessage();
        }

        return true;
    }

    public function sendDummyFacebookNotificationToUser()
    {
        try {
            //$this->sendFacebookNotificationToUser("57c5d830f23ffef8c58b4567");
            //$this->sendFacebookNotificationToUser("58d6e674f23ffeae4c8b4568");
        } catch (Exception $exception)
        {

        }

    }

}