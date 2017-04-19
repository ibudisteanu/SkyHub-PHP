<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Forum_preview extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('forum/forum_model','ForumModel');
    }

    public function index($Forum)
    {

    }

    public function renderPreviewForumView($Forum, $data, $iPageIndex=1, $iNumberCategories=2, $iNumberTopics=10, $bEcho=false)
    {
        if (($Forum == null)||(!is_object($Forum))||(get_class($Forum) != 'Forum_model')) return false;

        $data['dtForumPreview']=$Forum;
        $data['dtForumContent']=modules::load('forum_categories/view_forum_categories')->getForumCategories($Forum, $iPageIndex, $iNumberCategories, $iNumberTopics);

        $sContent = $this->renderModuleView('forum_preview_view',$data,TRUE);

        if ($bEcho)  $this->ContentContainer->addObject($sContent);
        else return $sContent;
    }

}