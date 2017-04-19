<?php

class Top_content_controller extends MY_Controller
{
    protected $ForumPreviewController;
    protected $DisplayPreviewParentController;

    public function __construct()
    {
        parent::__construct();

        $this->DisplayPreviewParentController = modules::load('display_preview_parent/display_preview_parent_controller');
    }

    public function renderTopContentFromParent($Parent, &$arrIDsAlreadyUsed, $iPageIndex=0, $iNoElementsCount=20, $arrDisplayContentType=['topic'], $bHidden=false)
    {
        if (is_object($Parent))
        {
            $Parent = $Parent->sID;
            $sParentName = $Parent->sName;
        } else
            if (is_string($Parent))
                $sParentName =  $Parent;
            else $sParentName = '';

        ini_set('max_execution_time', 300);

        $bHasNext = true;

        $this->load->model('top_content/top_content_models','TopContentModels');

        $arrContent = $this->TopContentModels->findTopContent($Parent, $arrIDsAlreadyUsed, $iPageIndex, $iNoElementsCount, $arrDisplayContentType);

        if (count($arrContent) == 0)
        {
            $bHasNext = false;

            /*if ($iPageIndex <= 1) {
                $this->AlertsContainer->addAlert('g_msgGeneralWarning', 'warning', 'No <strong>Content</strong> found in the: <strong>' . $sParentName . '</strong>');
                return ["arrContent"=>[],"bHasNext"=>$bHasNext,"sContent"=>''];
            } else*/
            return ["arrContent"=>[],"bHasNext"=>$bHasNext,"sContent"=>$this->renderContent([$Parent], 1, 1, $iNoElementsCount, $bHidden, true)];
        }

        return ["bHasNext"=>$bHasNext,"sContent"=>$this->renderContent($arrContent, 1, 1, $iNoElementsCount, $bHidden, true)];
    }

    private function groupTopicsBySameParent(&$arrTopics)
    {
        //generate the list of topics with the same parent
        //ex listTopics ['sID'=>0231333,'topics'=>[topic1,topic2,topic3]];
        $listTopics = [];
        foreach ($arrTopics as $topic)
            if ($topic != null)
            {
                $bFound=false;

                //foreach ($listTopics as $listTopicElement)
                for ($i=0; $i<count($listTopics); $i++)
                {
                    $listTopicElement = $listTopics[$i];

                    if ((string)$listTopicElement['sID'] == (string)$topic->sParentId) {
                        $bFound=true;
                        array_push($listTopics[$i]['topics'], $topic);
                        break;
                    }
                }

                //if it s a new parent Id
                if(!$bFound)
                    array_push($listTopics,[(string)'sID'=>$topic->sParentId,'topics'=>[$topic]]);
            }

        $sContent='';
        foreach ($listTopics as $listTopicElement)
            $sContent .= $this->DisplayPreviewParentController->renderPreviewTopicsArrayParentTable($listTopicElement['sID'],$listTopicElement['topics']);

        return $sContent;
    }

    private function getObjectParent($obj)
    {
        if ((isset($obj->sParentForumCategoryId))&&($obj->sParentForumCategoryId != '')) return $obj->sParentForumCategoryId ;
        if ((isset($obj->sParentForumId))&&($obj->sParentForumId != '')) return $obj->sParentForumId;
        if ((isset($obj->sParentSiteCategoryId))&&($obj->sParentSiteCategoryId != '')) return $obj->sParentSiteCategoryId;
        if ((isset($obj->sParentId))&&($obj->sParentId != '')) return $obj->sParentId;

        return '';
    }

    private function groupContentConsecutiveBySameParent(&$arrContent)
    {
        $sContent='';
        //var_dump(count($arrTopics));
        //var_dump($arrTopics);

        /*//Printing the types of the arrTopics data
           for ($i=0; $i<count($arrTopics); $i++)
            echo(get_class($arrTopics[$i]));*/

        if (is_array($arrContent))
        for ($i=0; $i < count($arrContent); $i++)
        {
            $arrDisplayObjects = [$arrContent[$i]];

            while (($i+1 < count($arrContent))&&($this->getObjectParent($arrContent[$i]) == $this->getObjectParent($arrContent[$i+1])))
            {
                $i++;
                array_push($arrDisplayObjects, $arrContent[$i]);
            }

            if (is_string($arrDisplayObjects[0]))
                $sContent .= $this->DisplayPreviewParentController->renderPreviewTopicsArrayParentTable($arrDisplayObjects[0],$arrDisplayObjects);
            else
            switch (get_class($arrDisplayObjects[0]))
            {
                case 'Topic_model':
                case 'Forum_category_model':
                    $sContent .= $this->DisplayPreviewParentController->renderPreviewTopicsArrayParentTable($this->getObjectParent($arrDisplayObjects[0]),$arrDisplayObjects);
                    break;
                case 'Forum_model':
                    $sContent .= $this->DisplayPreviewParentController->renderPreviewForumArray($this->getObjectParent($arrDisplayObjects[0]),$arrDisplayObjects, 1, 2, 0);
                    break;
            }
        }

        return $sContent;
    }

    protected function renderContent($arrContent, $sliceCount=1, $iPageIndex=1, $iNoTopicsCount=20, $bHidden=false, $bNoStyle=false)
    {
        if (!is_array($arrContent)) $arrContent = array($arrContent);

        //$sContent = $this->groupTopicsBySameParent($arrTopics);
        $sContent = $this->groupContentConsecutiveBySameParent($arrContent);

        if ($bHidden) return $sContent;
        $this->ContentContainer->addObject($sContent,'<div class="container">',3);
    }


    /*private function renderPreviewForumArrayView($arrForumsPanel,  $iPageIndex=1, $iNumberTopics=20, $bNoStyle=false)
    {
        if (!is_array($arrForumsPanel)) $arrForumsPanel = array($arrForumsPanel);

        $result = '';
        switch (count($arrForumsPanel))
        {
            case 0: return '';
            case 1: $data['boxSize'] = 'col-md-12 col-sm-12 col-xs-12 col-xxs-12 col-tn-12 item '; break; //item enables masonry
            case 2: $data['boxSize'] = 'col-md-6 col-sm-6 col-xs-6 col-xxs-12 col-tn-12 item '; break; //item enables masonry //2 columns
            case 3: $data['boxSize'] = 'col-md-4 col-sm-4 col-xs-4 col-xxs-12 col-xxs-12 col-tn-12 item'; break;
            case 4: $data['boxSize'] = 'col-md-3 col-sm-3 col-xs-3 col-xxs-12 col-xxs-12 col-tn-12 item'; break;
        }

        for ($index=0; $index < count($arrForumsPanel); $index++ )
        {
            $forum = $arrForumsPanel[$index];

            $data['boxStyle'] = 'padding: 10px 10px 10px 0px; left: 0px; top: 0px;';

            if (!$bNoStyle) {
                $data['boxStyle'] = '';
                $data['boxSize'] ='';
            }

            $result .= $this->ForumPreviewController->renderPreviewForumView($forum, $data, $iPageIndex, 2, $iNumberTopics, false);
        }
        return $result;
    }*/

}