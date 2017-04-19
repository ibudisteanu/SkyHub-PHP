<?php
require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Api_crawler extends MY_AdvancedController
{

    function __construct()
    {
        parent::__construct();
    }

    public function getAllForumsNewsTags($bEcho=true)
    {
        $this->load->model('forum/forums_model', 'ForumsModel');

        $arrJSON = [];
        $arrNewsNames = ['stiri','news','stire','stirile'];

        $arrForums = $this->ForumsModel->findAllForums();
        if ($arrForums == [] )
            $arrJSON = ['result'=>false,"message"=>"No Forums in the database"];
        else
        {
            foreach ($arrForums as $forum)
            if ($forum != null)
            {
                $forum->getCategories();
                foreach ($forum->arrCategories as $category)
                if ($category != null)
                {
                    $sName = strtolower($category->sName);
                    $bNewFound=false;

                    foreach ($arrNewsNames as $newsName)
                    if (strpos($sName,$newsName) !== FALSE)
                    {
                        $bNewFound=true;
                        break;
                    }

                    if ($bNewFound)
                    {
                        $arrData = [
                            'tags'=>$category->arrInputKeywords,
                            'user'=>'muflonel2000',
                            'forum'=>$forum->getFullURL(),
                            'category'=>'api/topic/post/add/'.$category->sID.'/'];
                        array_push($arrJSON, $arrData);
                    }

                }
            }

            if (count($arrJSON) > 0)
            {
                $arrJSON = array_merge(['result'=>true],$arrJSON);
            }
        }

        if ($bEcho) echo json_encode($arrJSON);
        else return $arrJSON;
    }

    public function getAllSiteCategoriesNewsTags($bEcho=true)
    {
        ini_set('max_execution_time', 0);

        $this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');


        $arrJSON = [];
        $arrNewsNames = ['stiri','news','stire','stirile'];

        $arrSiteCategories = $this->SiteCategoriesModel->findAllCategories("57db892108e5813c0c0000ed");

        if ($arrSiteCategories == [] )
            $arrJSON = ['result'=>false,"message"=>"No Forums in the database"];
        else
        {
            foreach ($arrSiteCategories as $siteCategory)
                if ($siteCategory != null)
                {
                    $sName = strtolower($siteCategory->sName);
                    $bNewFound=false;

                    foreach ($siteCategory->arrInputKeywords as $sInputKeyword )
                    {
                        foreach ($arrNewsNames as $newsName)
                            if (strpos($sInputKeyword,$newsName) !== FALSE)
                            {
                                $bNewFound=true;
                                break;
                            }
                    }

                    if ($bNewFound)
                    {
                        $arrData = [
                            'tags'=>$siteCategory->arrInputKeywords,
                            'user'=>'muflonel2000',
                            'forum'=>$siteCategory->getFullURL(),
                            'category'=>'api/topic/post/add/'.$siteCategory->sID.'/'];

                        array_push($arrJSON, $arrData);
                    }
                }

            if (count($arrJSON) > 0)
            {
                $arrJSON = array_merge(['result'=>true],$arrJSON);
            }
        }

        if ($bEcho) echo json_encode($arrJSON);
        else return $arrJSON;
    }

    public function getAllDataForCrawler()
    {
        ini_set('max_execution_time', 0);

        $arrJSON = [];
        $arrJSON = array_merge($arrJSON, $this->getAllForumsNewsTags(false));
        $arrJSON = array_merge($arrJSON, $this->getAllSiteCategoriesNewsTags(false));

        echo json_encode($arrJSON);
    }

    public function getAllTopicsURLs($bEcho=true)
    {
        $this->load->model('topics/Topics_model','TopicsModel');

        $topics = $this->TopicsModel->findAllTopics();

        if ($topics == null) {
            return 'No Topics Found';
        }

        $arrJSON = [];

        foreach ($topics as $topic)
            if ($topic != null)
            {
                $arrData = [
                    'url'=>$topic->getUsedURL(),
                ];

                array_push($arrJSON, $arrData);
            }


        if ($bEcho) echo json_encode($arrJSON);
        else return $arrJSON;
    }

}