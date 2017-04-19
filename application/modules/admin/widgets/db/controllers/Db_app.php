<?php

class Db_app extends  MY_Controller
{
    public function index($sParam1='',$sParam2='',$sParam3='')
    {
        if (TUserRole::checkUserRights(TUserRole::Admin))
        {
            $this->showContent($sParam1, $sParam2);
        } else
        {
            $this->showErrorPage('Not enough rights for updating');
            return;
        }
    }

    public function showHome($sParam)
    {
        $this->Template->loadMeta('Database Apps','DB Apps for '.WEBSITE_TITLE);
        $this->Template->renderHeader('admin');

        $this->showContent;

        $this->Template->renderContainer();

        $this->Template->renderFooter();
    }

    public function showContent($sParam1='', $sParam2='', $sParam3='')
    {
        //$this->ContentContainer->addObject($this->renderModuleView('db_app_view',$this->data,true));

        $sContent ='';

        $this->data['content']='';

        if ($sParam1 == 'apps')
        {
            switch ($sParam2)
            {
                case 'createTables':
                    $this->data['content'] .= '<br/>'.$this->createTables();
                    break;
                case 'refreshUsers':
                    $this->data['content'] .= '<br/>'.$this->refreshUsers();
                    break;
                case 'refreshForumsByReSaving':
                    $this->data['content'] .= '<br/>'.$this->refreshForumsByReSaving();
                    break;
                case 'refreshSiteCategoriesByReSaving':
                    $this->data['content'] .= '<br/>'.$this->refreshSiteCategoriesByReSaving();
                    break;
                case 'refreshForumCategoriesByReSaving':
                    $this->data['content'] .= '<br/>'.$this->refreshForumCategoriesByReSaving();
                    break;
                case 'refreshTopicsComponentReplies':
                    $this->data['content'] .= '<br/>'.$this->refreshTopicsComponentReplies();
                    break;
                case 'refreshTopicsByReSaving':
                    $this->data['content'] .= '<br/>'.$this->refreshTopicsByReSaving();
                    break;
                case 'refreshMaterializedChildrenSiteCategories':
                    $this->data['content'] .= '<br/>'.$this->refreshMaterializedChildrenSiteCategories();
                    break;
                case 'sortingCoefficientsRefresh':
                    $this->data['content'] .= '<br/>'.$this->sortingCoefficientsRefresh();
                    break;
                case 'sortingCoefficientsDelete':
                    $this->data['content'] .= '<br/>'.$this->sortingCoefficientsDelete();
                    break;
            }
        }

        $sContent .= $this->renderModuleView('db_app_view',$this->data,true);

        $this->ContentContainer->addObject($sContent,'<section class="col-lg-7 connectedSortable">');
    }


    public function createTables()
    {
        $sContent = '';
        $this->load->model('forum/Forums_model','ForumsModel');
        $this->load->model('topics/Topics_model','TopicsModel');
        $this->load->model('forum/forum_categories_model','ForumCategoriesModel');

        $forums = $this->ForumsModel->findAllForums();

        if ($forums == null) return;

        foreach ($forums as $forum)
        {
            $arrForumCategories = $this->ForumCategories->findForumCategoriesByForumId($forum->sID);

            foreach ($arrForumCategories as $Category)
            {

            }

        }

        $sContent .= 'Done successfully'.'<br/>';
        return $sContent;
    }

    public function refreshUsers()
    {
        ini_set('max_execution_time', 0);

        $sContent ='';
        $this->load->model('users/users', 'Users');
        $users = $this->Users->findAllUsers();

        foreach ($users as $user)
        {
            if ((!isset($user->sFullURLLink)) && (!isset($user->sFullURLName))&& (!isset($user->sFullURLDomains)))
            {
                $user->sURLName = $user->sUserName;
                $user->sFullURLLink = "profile/".$user->sUserName;
                $user->sFullURLDomains = "page/profile";
                $user->sFullURLName = "profile/".$user->sName;
                $user->storeUpdate();


                $sContent .= 'Porcessing '.$user->sName.'<br/>';
            }
        }

        $sContent .= '<b>Done successfully'.'<br/></b>';
        return $sContent;
    }

    public function refreshForumsByReSaving()
    {
        ini_set('max_execution_time', 0);
        $sContent = '';

        $this->load->model('forum/Forums_model','ForumsModel');
        $this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');

        $forums = $this->ForumsModel->findAllForums();

        if ($forums== null) {
            return 'No Forums Found';
        }

        $sContent = count($forums).' found in the DB';

        foreach ($forums as $forum)
        {
            $siteCategory = $this->SiteCategoriesModel->findCategory($forum->sParentCategoryId);
            if ($siteCategory  != null)
            {
                $sContent .= $forum->sName.' processing from <b>'.$siteCategory->sName.'</b> <br/>';

                $forum->storeUpdateOnlyChild();

            } else
            {
                $sContent .= $forum->sName.' NO CATEGORY FOUND <br/>';
            }
        }

        return $sContent;
    }

    public function refreshTopicsComponentReplies()
    {
        ini_set('max_execution_time', 0);
        $sContent = '';

        $this->load->model('topics/Topics_model','TopicsModel');
        $this->load->model('reply/Replies_model','RepliesModel');

        $topics = $this->TopicsModel->findAllTopics();

        if ($topics == null) {
            return 'No Topics Found';
        }

        $sContent = count($topics).' found in the DB';

        foreach ($topics as $topic)
        {
            $objRepliesContainer = $this->RepliesModel->findTopRepliesByAttachedParentId($topic->sID);
            if (($objRepliesContainer != null) && ($topic->objRepliesComponent != null) && (!is_array($objRepliesContainer))){
                $topic->objRepliesComponent->iNoReplies = $objRepliesContainer->iNoReplies;
                $topic->objRepliesComponent->iNoUsersReplies = $objRepliesContainer->iNoUsersReplies;
                $topic->objRepliesComponent->refreshReplies($objRepliesContainer);

                $topic->storeUpdateOnlyChild();

                $sContent .= 'TOPIC '.$topic->sID.' <b> '.$topic->sTitle.' </b> processed <br/>';
            } else{
                $sContent .= 'TOPIC '.$topic->sID.' <b> '.$topic->sTitle.' </b> NO REPLIES container <br/>';
            }
        }

        return $sContent;
    }

    public function refreshTopicsByReSaving()
    {
        ini_set('max_execution_time', 0);
        ob_implicit_flush(true); ob_start();

        $sContent = '';

        $this->load->model('topics/Topics_model','TopicsModel');
        $this->load->library('StringsAdvanced',null,'StringsAdvanced');

        $topics = $this->TopicsModel->findAllTopics();

        if ($topics == null) {
            return 'No Topics Found';
        }

        echo 'Topics '.count($topics).' found in the DB';


        if (is_array($topics))
        foreach ($topics as $topic)
        {
            //var_dump($topic);

            //$objParent = $this->AdvancedCache->getObjectFromId($topic->sParentId);
            $objParent = null;
            //var_dump($topic->sParentId);

            echo 'TOPIC '.$topic->sID.' <b> '.$topic->sTitle.' </b> processed ';
            try
            {
                $sURLName = $this->StringsAdvanced->processURLString($topic->sTitle);

                if ($objParent == null)
                {
                    if (($objParent == null)&&($topic->sAttachedParentId != ''))
                    {
                        $topic->sParentId = $topic->sAttachedParentId ;
                        $objParent = $this->AdvancedCache->getObjectFromId($topic->sParentId);
                    }

                    if (($objParent == null)&&($topic->sParentForumCategoryId != ''))
                    {
                        $topic->sParentId = $topic->sParentForumCategoryId;
                        $objParent = $this->AdvancedCache->getObjectFromId($topic->sParentId);
                    }

                    if (($objParent == null)&&($topic->sParentForumId != ''))
                    {
                        $topic->sParentId = $topic->sParentForumId;
                        $objParent = $this->AdvancedCache->getObjectFromId($topic->sParentId);
                    }

                    if (($objParent == null)&&($topic->sParentSiteCategoryId != ''))
                    {
                        $topic->sParentId = $topic->sParentSiteCategoryId;
                        $objParent = $this->AdvancedCache->getObjectFromId($topic->sParentId);
                    }
                }

                if ($objParent != null){
                    $sFullURLName=rtrim($objParent->sFullURLName,'/').'/'.$topic->sTitle;
                    $sFullURLLink=rtrim($objParent->sFullURLLink,'/').'/'.$sURLName;
                    $sFullURLDomains = rtrim($objParent->sFullURLDomains,'/').'/'.'topic';

                    $topic->sURLName = $sURLName;
                    $topic->sFullURLName = $sFullURLName;
                    $topic->sFullURLLink = $sFullURLLink;
                    $topic->sFullURLDomains = $sFullURLDomains;
                }

                $topic->calculateParents($objParent);
                $topic->storeUpdateOnlyChild();
            }
            catch(Exception $ex)
            {
                echo ' - <b>ERRROR PROCESSING '.$ex->getMessage().' </b>';
            }

            ob_flush();

            echo '<br/>';
        }
        ob_end_flush();

        return $sContent;
    }

    public function refreshForumCategoriesByReSaving()
    {
        ini_set('max_execution_time', 0);

        $sContent = '';

        $this->load->model('forum/Forums_model','ForumsModel');
        $this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');
        $this->load->model('forum_categories/Forum_categories_model','ForumCategoriesModel');

        $forums = $this->ForumsModel->findAllForums();

        if ($forums== null) return 'No Forums Found';

        $sContent = count($forums).' found in the DB <br/>';

        foreach ($forums as $forum)
        {
            $siteCategory = $this->SiteCategoriesModel->findCategory($forum->sParentCategoryId);
            if ($siteCategory  != null)
            {
                $sContent .= $forum->sName.' processing from <b>'.$siteCategory->sName.'</b> <br/>';

                $arrForumCategories = $this->ForumCategoriesModel->findForumCategoriesByForumId($forum->sID);
                if ($arrForumCategories != null) {
                    foreach ($arrForumCategories as $category) {

                        $category->sSiteCategoryParents = $forum->sSiteCategoryParents;

                        $category->storeUpdate();
                        $sContent .= $forum->sName . ' processing from ' . $siteCategory->sName . ' <b>' . $category->sName . '</b> <br/>';
                    }
                }


            } else
            {
                $sContent .= $forum->sName.' NO SITE FOUND <br/>';
            }
        }

        return $sContent;
    }

    private $arrSitesVisited=[];
    private function processSiteCategories($siteCategories, $sPrefix='', $parentSite=null)
    {
        $sContent = '';
        if (($siteCategories != null)&&(is_array($siteCategories)))
        foreach ($siteCategories as $siteCategory)
        if ((is_object($siteCategory))&&($siteCategory!= null))
        {

            $notVisited=false;
            foreach ($this->arrSitesVisited as $visited)
                if ($siteCategory->sID == $visited->sID)
                    $notVisited=true;

            if ($notVisited == false)
            {
                array_push($this->arrSitesVisited, $siteCategory);

                try{
                    $siteCategory->storeUpdate($parentSite);
                } catch (Exception $exception){
                    echo 'Error with <b>'.$siteCategory->sID.' '.$siteCategory->sName.'</b> '.$exception->getMessage().'<br/>';
                    var_dump($siteCategory);
                    //var_dump($parentSite);
                }

                $sContent .= $sPrefix.'-'.$siteCategory->sName.' processed <br/>';

                $arrSub = $this->SiteCategoriesModel->findCategories($siteCategory->sID);

                $sContent .= $this->processSiteCategories($arrSub,$sPrefix.'---',$siteCategory);
            }
        }
        return $sContent;
    }

    public function refreshSiteCategoriesByReSaving()
    {
        ini_set('max_execution_time', 200);

        $this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');
        $siteCategories = $this->SiteCategoriesModel->findTopCategories();

        if ($siteCategories == null) {
            return 'No Site Categories Found';
        }
        $sContent = count($siteCategories).' found in the DB';

        $sContent .= $this->processSiteCategories($siteCategories,'',null);

        return $sContent;
    }



    public function refreshMaterializedChildrenSiteCategories()
    {
        ini_set('max_execution_time', 0);

        $this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');
        $siteCategories = $this->SiteCategoriesModel->findTopCategories();

        if ($siteCategories == null) {
            return 'No Site Categories Found';
        }
        $sContent = count($siteCategories).' found in the DB';

        $arrSiteCategoriesQueue = $siteCategories;
        for ($i=0; $i<count($arrSiteCategoriesQueue); $i++)
        {
            $siteCategory = $arrSiteCategoriesQueue[$i];

            $arrCategoriesSub = $this->SiteCategoriesModel->findCategories($siteCategory->sID);
            if ($arrCategoriesSub != null)
            foreach ($arrCategoriesSub as $sub) {
                $bFound = false;
                foreach ($arrSiteCategoriesQueue as $siteCat) {
                    if ($siteCat->sID == $sub->sID)
                        $bFound = true;
                }

                if (!$bFound)
                    array_push($arrSiteCategoriesQueue, $sub);
            }

            $this->load->model('forum/Forums_model','ForumsModel');
            $arrForums = $this->ForumsModel->findForumsFromSiteCategory($siteCategory->sID);

            if ($arrForums != null)
            foreach ($arrForums as $forum){
                $forum->sSiteCategoryParents = $siteCategory->getSiteCategoryMaterializedParents();
                $forum->storeUpdate();

                $sContent .= 'Processed <b>Forum</b> successfully '.$forum->sID . '<br/>';
            }

            $this->load->model('forum_categories/Forum_categories_model','ForumCategoriesModel');
            $arrForumCategories = $this->ForumCategoriesModel->findForumCategoriesFromSiteCategoryId($siteCategory->sID);

            if ($arrForumCategories != null)
                foreach ($arrForumCategories as $category){

                        $category->sSiteCategoryParents = $siteCategory->getSiteCategoryMaterializedParents();
                        $category->storeUpdate();

                        $sContent .= 'Processed <b>Forum Category</b> successfully '.$category->sID . '<br/>';
                    }


            $this->load->model('topics/Topics_model','TopicsModel');
            $arrTopics = $this->TopicsModel->TopTopics($siteCategory->sID);

            if ($arrTopics != null)
            foreach ($arrTopics as $topic){
                $topic->sSiteCategoryParents = $siteCategory->getSiteCategoryMaterializedParents();
                $topic->storeUpdate();

                $sContent .= 'Processed <b>Topic</b> successfully '.$topic->sID . '<br/>';
            }

            $sContent .= 'Processed <b>SiteCategory</b> successfully '.$siteCategory->sID . '<br/>';
        }

        $sContent .= count($arrSiteCategoriesQueue).' found in the DB';

        return $sContent;

    }

    public function sortingCoefficientsRefresh(){

        ini_set('max_execution_time', 0);
        ob_implicit_flush(true); ob_start();

        $this->load->model('topics/Topics_model','TopicsModel');
        $topics = $this->TopicsModel->findAllTopics();

        $sContent = 'Topics '.count($topics);
        if ($topics != null)
            foreach ($topics as $topic)
            {
                echo 'Processing Topic '.$topic->sID.' '.$topic->sTitle.' '.$topic->getCreationDateString().' Coefficient '.$topic->calculateOrderCoefficient()->toString().'<br/>';
                ob_flush();
                $topic->recalculateKeepSortedData();
            }


        echo '<strong> -------------------- TOPICS FINISHED ---------------------- </strong><br/>';

        $this->load->model('forum_categories/Forum_categories_model','ForumCategoriesModel');
        $arrForumCategories = $this->ForumCategoriesModel->findAllForumCategories();

        $sContent .= 'Forums '.count($arrForumCategories).' found in the DB <br/>';

        if ($arrForumCategories != null)
            foreach ($arrForumCategories as $category){
                echo 'Processing Forum Category '.$category->sID.' '.$category->sName.' '.'<br/>';
                ob_flush();
                $category->recalculateKeepSortedData();
            }

        echo '<strong> -------------------- FORUM CATEGORIES FINISHED ----------------------</strong> <br/>';

        $this->load->model('forum/Forums_model','ForumsModel');
        $forums = $this->ForumsModel->findAllForums();

        $sContent .= 'Forums '.count($forums).' found in the DB <br/>';

        if ($forums != null)
            foreach ($forums as $forum)
            {
                echo 'Processing Forum '.$forum->sID.' '.$forum->sName.' '.'<br/>';
                ob_flush();
                $forum->recalculateKeepSortedData();
            }

        echo '<strong> -------------------- JOB FINISHED ----------------------</strong> <br/>';

        ob_end_flush();
        return '';
    }

    public function sortingCoefficientsDelete(){
        $this->load->model('keep_sorted/Keep_sorted_algorithm_model','KeepSortedAlgorithmModel');
        if ($this->KeepSortedAlgorithmModel->dropCollections())
            return 'Success Removing the Sorting Coefficients <br/>';
        else
            return 'Error Removing the Sorting Coefficients <br/>';
    }

}