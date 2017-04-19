<?php

/**
 * Created by PhpStorm.
 * User: BIT TECHNOLOGIES
 * Date: 10/28/2016
 * Time: 1:53 PM
 */

class View_avatar extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('users/users_minimal','UsersMinimal');
    }

    public function showReplyAvatar($sUserId='')
    {
        return $this->showAvatar($sUserId, 'reply',false);
    }

    public function showTopicAvatar($sUserId='')
    {
        return $this->showAvatar($sUserId,'topic-question',false);
    }

    public function showTopicPreviewAvatar($sUserId='')
    {
        return $this->showAvatar($sUserId,'topic-preview',false);
    }

    public function showEmailNotificationPreviewAvatar($sUserId='')
    {
        return $this->showAvatar($sUserId,'topic-preview',true);
    }


    public function showAvatar($sUserId='', $sStyle = 'reply', $bShowUserName=false)
    {
        if ($sUserId == '') return false;

        $User = $this->UsersMinimal->userByMongoId($sUserId);
        if ($User  == null) return false;

        $data['User'] = $User;
        $data['styleClass'] = $sStyle;
        $data['data'] = $data;
        $data['bShowUserName'] = $bShowUserName;

        $this->load->view('user/user_avatar_view',$data);
    }

}