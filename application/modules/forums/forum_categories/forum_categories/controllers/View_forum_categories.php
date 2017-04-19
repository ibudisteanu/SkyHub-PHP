<?php

class View_forum_categories extends  MY_Controller
{

    private $ViewForumCategory;
    public function __construct()
    {
        parent::__construct();

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');

        $this->load->model('forum/Forums_model','Forums');
        $this->load->model('forum_categories/Forum_categories_model','ForumCategoriesModel');

        $this->ViewForumCategory = modules::load('forum_category/view_forum_category');
    }

    public function displayContainer($Forum, $Action='', $iPageIndex=1, $iNumberCategories=2, $iNumberTopics=10, $bInfiniteScrollContentLoader=false)
    {
        if (is_string($Forum)) $Forum = $this->Forums->findForum($Forum);

        if ($Forum != null) {
            //$arrCategories = $Forum->sortCategories(1,$iNumberCategories);

            $arrCategories = $this->ForumCategoriesModel->findForumCategoriesByForumId($Forum->sID);

            return $this->renderForumCategoriesContainer($arrCategories , $iPageIndex, $iNumberTopics,  $Action, $bInfiniteScrollContentLoader);
        }

        $this->AlertsContainer->addAlert('g_msgAddForumCategoryWarning','warning','No Forum found ');
    }

    public function getForumCategories($Forum, $iPageIndex=1, $iNumberCategories=2, $iNumberTopics=10, $bInfiniteScrollContentLoader=false)
    {
        if (is_string($Forum)) $Forum = $this->Forums->findForum($Forum);

        if ($Forum != null) {
            //$arrCategories = $Forum->sortCategories($iPageIndex,$iNumberCategories);
            $arrCategories = $this->ForumCategoriesModel->findForumCategoriesByForumId($Forum->sID);

            return $this->renderForumCategoriesContainer($Forum, $arrCategories, $iPageIndex, $iNumberTopics, '',false, $bInfiniteScrollContentLoader);
        }
    }

    protected function renderForumCategoriesContainer($Forum, $arrCategories, $iPageIndex=1, $iNumberTopics=10, $sAction, $bRender=true, $bInfiniteScrollContentLoader=false)
    {
        $content = '';

        if ($arrCategories != null)
        foreach ($arrCategories  as $forumCategory) {

            /*if ($bInfiniteScrollContentLoader) {
            }
            else {
                $array = $this->ViewForumCategory->renderForumCategory($forumCategory, $iPageIndex, $iNumberTopics, $sAction, $bRender);
                $content .= $array['sContent'];
            }*/

            $content .= $this->ViewForumCategory->renderForumCategory($forumCategory, $iPageIndex, $iNumberTopics, false, false, $bRender);

            //$content .= modules::load('display_content/display_top_content_loader')->getTopContentJavaScriptLoader((is_object($forumCategory) ? $forumCategory->sID : $forumCategory), $iPageIndex, $iNumberTopics, false, $bInfiniteScrollDisplayForums=false, false, false, false, $forumCategory->sName);

        }

        if ($bRender == true)
            ;
            //$this->ContentContainer->addObject($content,'<div class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp" style="margin-left:35px;margin-right:5px">',5);
        else
            return $content;
    }

}