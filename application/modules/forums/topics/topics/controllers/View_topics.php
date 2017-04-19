<?php

class View_topics extends MY_Controller
{
    protected $ForumPreviewController;

    public function __construct()
    {
        parent::__construct();

        //$this->load->model('topics/topics_model','TopicsModel');
        //$this->load->model('forum/forums_model','ForumsModel');
    }

    public function getTopicsFromForumCategory($ForumCategory, $bShowError=false, $iPageIndex=1, $iNumberTopics=10)
    {
        if ($ForumCategory == null)
            return ["sContent"=>"","bHasNext"=>false];


        $arrTopics = $ForumCategory->sortTopics($iPageIndex, $iNumberTopics);

        //var_dump($arrTopics);

        if (count($arrTopics) == 0)
        {
            if ($bShowError)
                $this->AlertsContainer->addAlert('g_msgAddForumCategoryWarning','warning','No <strong>Topics</strong> found in the Forum: <strong>'.$ForumCategory->sName.'</strong>');
            return ["sContent"=>"","bHasNext"=>false];
        }

        return ["sContent"=>$this->renderTopTopics($arrTopics),"bHasNext"=>count($arrTopics) == $iNumberTopics];
    }

    protected function renderTopTopics ($arrTopics)
    {
        //$this->renderCategoryTitleView($Category);
        $this->TopicPreviewController = modules::load('forum_topic_preview/view_forum_topic_preview');

        $result = '';
        foreach ($arrTopics as $Topic)
        {
            $result .= $this->TopicPreviewController->renderPreviewForumTopicView($Topic,true);
        }

        return $result;
    }
}