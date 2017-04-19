<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class TMessageRender
{
    const renderNotDetermined = 0;
    const renderLeft=1;
    const renderRight = 2;
    const renderNormal = 3;
}

class View_message_controller extends  MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('TimeLibrary',null,'TimeLibrary');

        $this->load->model('users/users_minimal','UsersMinimal');
    }

    public function renderMessage($Message, $arrAuthors=[], $enMessageType = TMessageRender::renderNotDetermined, $bEcho=false)
    {
        if ($enMessageType == TMessageRender::renderNotDetermined)
            $enMessageType = $this->determineMessageRenderAdvanced($arrAuthors, $Message);

        $data['User'] = $this->UsersMinimal->userByMongoId($Message->sAuthorId);
        $data['Message'] = $Message;
        switch ($enMessageType)
        {
            case TMessageRender::renderLeft:
                $sContent = $this->renderModuleView('message_left_view', $data, !$bEcho);
                break;

            case TMessageRender::renderRight:
                $sContent = $this->renderModuleView('message_right_view', $data, !$bEcho);
                break;

            case TMessageRender::renderNormal:
                $sContent = $this->renderModuleView('message_normal_view', $data, !$bEcho);
                break;

            default:
                $sContent = '';
        }

        return $sContent;
    }

    public function determineMessageRenderAdvanced($Authors, $Message)
    {
        if ($Message->sAuthorId == $this->MyUser->sID)
            return TMessageRender::renderRight;
        else
            return TMessageRender::renderLeft;

        /*if (count($Authors) == 2)
        {
            if ($Message->sAuthorId == $this->MyUser->sID)
                return TMessageRender::renderRight;
            else
                return TMessageRender::renderLeft;
        } else
        {
            return TMessageRender::renderNormal;
        }*/
    }

    public function getJSONMessage($Message, $ConversationDB)
    {
        $arrReturnMessages['enTypeRender'] = $this->determineMessageRenderAdvanced($ConversationDB->arrAuthors, $Message);
        $arrReturnMessages['sMessageId'] = $Message->sID;

        $User = $this->UsersMinimal->userByMongoId($Message->sAuthorId);

        $arrReturnMessages['sUserAvatarLink'] = $User->sAvatarPicture;
        $arrReturnMessages['sFullName'] = $User->getFullName();
        $arrReturnMessages['dtFullDateTime'] = $Message->getCreationDateString();
        $arrReturnMessages['dtDateTime'] = $this->TimeLibrary->getTimeDifferenceDateAndNowString($Message->getCreationDate());
        $arrReturnMessages['sMessageBody'] = $Message->sBody;

        return $arrReturnMessages;
    }


}