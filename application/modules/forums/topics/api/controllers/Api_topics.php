<?php
require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Api_topics extends MY_AdvancedController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('forum/forums_model', 'ForumsModel');
    }

    public function addTopicFast($sParentID, $bRedirectSuccess=false)
    {
        if ((is_string($bRedirectSuccess)))
            if ($bRedirectSuccess == 'true') $bRedirectSuccess=true;
            else $bRedirectSuccess = false;

        $addForumTopic = modules::load('add_topic/add_forum_topic');
        $addForumTopic->bRedirectSuccess=$bRedirectSuccess;
        $addForumTopic->bRenderForm=false;

        $result = $addForumTopic->index('add-topic','',$sParentID,null);
        $arrJSON = ["result"=>$result];

        if ($result) {
            $arrJSON = array_merge($arrJSON, ["message" => ($this->AlertsContainer->renderViewByName('g_msgAddForumTopicSuccess', 'none', true, true, true))]);

            $arrJSON = array_merge($arrJSON, ["BlockHTMLCode" => $this->renderNewTopic($addForumTopic->objTopic, $addForumTopic->sFormResponseType),'sNewTopicId'=>(string)$addForumTopic->objTopic->sID]);
        }
        else
            $arrJSON = array_merge($arrJSON,["message"=>($this->AlertsContainer->renderViewByName('g_msgAddForumTopicError','none',true,true,true).$this->AlertsContainer->renderViewByName('g_msgGeneralError','none',true,true,true))]);


        if ((!$this->MyUser->bLogged)&&($this->AlertsContainer->renderViewByName('g_msgAddForumTopicError','none',true,true,true)=='<strong>You are not logged in.</strong> Please Login or Register <br/> To publish new content, you need to be a registered user.  <br/> You are not registered'))
            $arrJSON = array_merge($arrJSON,["logged"=>$this->MyUser->bLogged]);


        echo json_encode($arrJSON);

        return true;
    }

    public function renderNewTopic($objTopic, $sFormResponseType)
    {
        if ($sFormResponseType == 'topic-preview') {
            $viewTopicController = modules::load('topic/view_topic');
            return $viewTopicController->renderTopicBody($objTopic, false);

        } else
        if ($sFormResponseType == 'topic-preview-table') {
            $viewTopicPreviewController = modules::load('forum_topic_preview/view_forum_topic_preview');
            return $viewTopicPreviewController->renderPreviewForumTopicView($objTopic,true);
        } else
        if ($sFormResponseType == 'topic-preview-table-body') {
            $viewTopicPreviewController = modules::load('forum_topic_preview/view_forum_topic_preview');
            return $viewTopicPreviewController->renderPreviewForumTopicBody($objTopic,true);
        }
    }

    public function showTopicForm($bRedirectSuccess=false)
    {
        if (isset($_POST['Action'])) $sActionName = $_POST['Action'];
        else
        {
            echo json_encode(["result"=>false,"message"=>"POST NO Action set"]);
            return false;
        }

        $sTopicId = '';
        if (isset($_POST['TopicId'])) $sTopicId = $_POST['TopicId'];

        $sParentId = '' ;
        if (isset($_POST['ParentId'])) $sParentId = $_POST['ParentId'];

        $sFormIndex = 0;
        if (isset($_POST['FormIndex'])) $sFormIndex = $_POST['FormIndex'];

        $addForumTopic = modules::load('add_topic/add_forum_topic');
        $addForumTopic->bRedirectSuccess=false;
        $addForumTopic->bRenderForm=false;
        $addForumTopic->sFormIndex = $sFormIndex;

        if (isset($_POST['FormResponseType'])) $addForumTopic->sFormResponseType = $_POST['FormResponseType'];

        $bResult = $addForumTopic->index($sActionName,'',$sParentId,$sTopicId);

        if (isset($_POST['FormIndex'])) $sFormIndex = $_POST['FormIndex'];

        $bResult=true;

        $arrJSON = ["result"=>$bResult];

        if ($bResult) {
            $arrJSON = array_merge($arrJSON, ["message" => ($this->AlertsContainer->renderViewByName('g_msgAddForumTopicSuccess', 'none', true, true, true)),"FormHTMLCode" => $addForumTopic->sFormCode,'language'=>($_POST['addForumTopic-country'] ? $_POST['addForumTopic-country'] : '') ]);

            $arrJSON = array_merge($arrJSON, ["FormHTMLCode" => $addForumTopic->sFormCode]);
        }
        else
            $arrJSON = array_merge($arrJSON,["message"=>($this->AlertsContainer->renderViewByName('g_msgAddForumTopicError','none',true,true,true).$this->AlertsContainer->renderViewByName('g_msgGeneralError','none',true,true,true))]);

        echo json_encode($arrJSON);
        return true;

    }

    public function deleteTopic($bRedirectSuccess=false)
    {
        if (isset($_POST['TopicId'])) $sTopicId = $_POST['TopicId'];
        else
        {
            echo json_encode(["status"=>'error',"message"=>"POST NO Post Id"]);
            return false;
        }

        $this->load->model('topics/Topics_model','TopicsModel');
        $topic = $this->TopicsModel->findTopicByIdOrFullURL($sTopicId);

        if ($topic != null)
        {
            $sTopicTitle = $topic->sTitle;

            $result = $topic->delete();

            if ($result['result'])
            {
                echo json_encode(["status"=>'success',"message"=>"Topic had been deleted ".$sTopicTitle]);
            } else
            {
                echo json_encode(["status"=>'error',"message"=>"Topic couldn't be deleted ".$sTopicTitle.'<br/>'.$result['message']]);
            }
        } else
            echo json_encode(["status"=>'error',"message"=>"No Topic Found ".$sTopicId]);
    }

}