<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_resources extends MX_Controller
{

    function swap3(&$x,&$y){
        $tmp=$x; $x=$y; $y=$tmp;
    }

    public function loadJSResource($sPage='', $sParam1='', $sParam2='', $sParam3='', $sParam4='', $sParam5='')
    {
       /* if ($sParam4 != ''){//1 2 3 4 5 => 5 1 2 3 4
            $this->swap3($sPage, $sParam4); //1 2 3 4 5 => 5 2 3 4 1
            $this->swap3($sParam1, $sParam4); //5 1 3 4 2
            $this->swap3($sParam2, $sParam4); //5 1 2 4 3
            $this->swap3($sParam3, $sParam4); //5 1 2 3 4
        }
        if ($sParam3 != '') { //1 2 3 4 => 4 1 2 3
            $this->swap3($sPage, $sParam3);
            $this->swap3($sParam1, $sParam3);
            $this->swap3($sParam2, $sParam3);
        } else
        if ($sParam2 != '') {
            $this->swap3($sPage, $sParam2);
            $this->swap3($sParam1, $sParam2);
        }
        else
        if ($sParam1 != '') $this->swap3($sPage, $sParam1);*/

        $this->load->helper('url');

        switch ($sPage)
        {
            case 'login-validation.js':  //min-js
                return $this->load->view('auth_site/js/login_validation.js.php');

            case 'registration-validation.js': //min-js
                return $this->load->view('auth_site/js/registration_validation.js.php');

            case 'reply-inline-functions.js':
                return $this->load->view('add_reply_inline/js/reply-inline-functions.js.php');

            case 'add-edit-forum-topic-functions.js': //min-js
                return $this->load->view('add_topic/js/add-edit-forum-topic-functions.js.php');

            case 'topic-inline-functions.js':  //min-js
                return $this->load->view('add_topic_inline/js/topic-inline-functions.js.php');

            case 'login-popup-authentication.js': //min-js
                return $this->load->view('popup_auth/js/popup_authentication.js');

            case 'infinite-scroll-content-loader.js': //min-js
                return $this->load->view('content_loader/js/infiniteScrollContentLoader.js.php');

            case "notifications-loader.js": //min-js
                return $this->load->view('user_notifications/js/notifications_loader.js.php', $data);

            case 'vote-functions.js'://min-js
                return $this->load->view('voting/js/voting-functions.js.php');

            /*case 'replies-functions.js':
                return $this->load->view('voting/js/replies-functions.js.php');*/

            case 'files-upload-system.js':
                return $this->load->view('files_upload_system/js/file_upload_system.js.html');

        }

        echo 'FILE NOT FOUND: '.$sPage;
    }

    public function loadCSSResource($sPage='')
    {
        switch ($sPage)
        {
        }
    }

}