<?php

require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Add_forum_topic extends MY_AdvancedController
{
    public $objTopic;

    protected $objParent;

    public $bRedirectSuccess=true;
    public $bRenderForm=true;
    public $sFormCode=false;
    public $sFormIndex = 0;
    public $sFormResponseType = 'topic-preview';

    public $FilesUploadSystemController;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('topics/topics_model', 'TopicsModel');
        $this->load->model('forum_categories/forum_categories_model', 'ForumCategories');
        $this->load->model('forum/forums_model', 'ForumsModel');
        $this->load->model('users/Users_minimal', 'Users');

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
        $this->includeWebPageLibraries('country-select');

        $this->initializeAddTopicController();
    }

    public function initializeAddTopicController()
    {
        $this->includeWebpageLibraries('file-style');
        $this->includeWebPageLibraries('advanced-functions');
        $this->includeWebPageLibraries('advanced-text-editor');
        $this->BottomScriptsContainer->addScriptResFile(base_url(defined('WEBSITE_OFFLINE') ? "app/res/js/add-edit-forum-topic-functions.js" : 'assets/min-js/add-edit-forum-topic-functions-min.js'));

        //$this->FilesUploadSystemController = modules::load('files_upload_system/files_upload_System_controller');
    }

    private $sParameterActionName;
    private $sParameterFullURLLink;
    private $parameterParent;
    private $parameterTopic;

    public function index($sActionName, $sFullURLLink, $Parent, $Topic=null)
    {
        $this->sParameterActionName = $sActionName; $this->sParameterFullURLLink = $sFullURLLink; $this->parameterParent = $Parent; $this->parameterTopic=$Topic;

        // $Topic and $Parent can be String, MongoId or Object

        if (isset($_POST['addForumTopic-Id'])) $Topic = $_POST['addForumTopic-Id'];
        if (isset($_POST['addForumTopic-ParentId'])) $Parent = $_POST['addForumTopic-ParentId'];

        if (($Topic != null) && (!is_string($Topic))&& (is_object($Topic))) $this->objTopic = $Topic;
        else
            if (($Topic != '')&&(is_string($Topic)))
                $this->objTopic = $this->TopicsModel->getTopic($Topic);


        //Already Existing Topic, reading his parent
        if ($this->objTopic != null) {
            if ($this->objTopic->sParentId != '')
                $this->objParent = $this->AdvancedCache->getObjectFromId($this->objTopic->sParentId);

        }
        else
        {
            if (($Parent != null)&&(!is_string($Parent )&&(is_object($Parent)))) $this->objParent = $Parent;
            else
                if (($Parent != '')&&(is_string($Parent)))
                    $this->objParent = $this->AdvancedCache->getObjectFromId($Parent);
        }

        if (isset($_POST['addForumTopic-sFormResponseType']))
            $this->sFormResponseType = $_POST['addForumTopic-sFormResponseType'];

        if ($this->objParent == null)
        {
            $this->AlertsContainer->addAlert('g_msgAddForumTopicError', 'error', 'Error adding <strong>' . $sFullURLLink . ( isset($_POST['addForumTopic-title']) ? $this->StringsAdvanced->processText($_POST['addForumTopic-title'],'html|xss|whitespaces') : ''). '</strong> because no parent found in the database <strong>'.$Parent.'</strong>');
            return false;
        }

        $bAllow=false;
        switch ($sActionName)
        {
            case 'add-topic':
                $bAllow=true;
                break;
            case 'delete-topic':
                if ($this->objTopic != null)
                    $bAllow=true;
                break;
            case 'edit-topic':
                if ($this->objTopic != null)
                    $bAllow=true;
                break;
        }

        if (!$bAllow)
        {
            $this->AlertsContainer->addAlert('g_msgAddForumTopicError','error','Invalid Action');
            return false;
        } else
            return $this->processPage($sActionName, $sFullURLLink);
    }

    public function createAddForm()
    {

    }

    protected function processForumTopic($Action='add-topic')
    {
        $this->load->model('add_topic/query_trials_blocked_add_forum_topic_too_many_times','QueryTrialsBlockedAddForumTopicTooManyTimes');
        $this->load->library('../modules/forums/topics/widgets/add_topic/libraries/Add_forum_topic_validator',$this->Users,'Validator');

        if (! $this->Validator->CheckPosts())
        {
            $this->AlertsContainer->addAlert('g_msgAddForumTopicError','error',$this->Validator->sError);
            return false;
        }


        $sTitle=$this->StringsAdvanced->processText($_POST['addForumTopic-title'], 'xss|whitespaces');

        $sURLName = '';
        if (isset($_POST['addForumTopic-urlName'])) $sURLName = $_POST['addForumTopic-urlName'];
        if ($sURLName == '') $sURLName = $sTitle;

        $sURLName = $this->StringsAdvanced->processURLString($sURLName);

        if (isset($_POST['addForumTopic-importance'])) $iImportanceValue=(float)$this->StringsAdvanced->processText($_POST['addForumTopic-importance'], 'html|xss|whitespaces');
        else $iImportanceValue = -666;

        $sImage = $this->StringsAdvanced->processText($_POST['addForumTopic-image'], 'html|xss|whitespaces');
        $imageUpload = '';
        if (isset($_FILES['addForumTopic-imageUpload']))
            $imageUpload = $this->StringsAdvanced->processText($_FILES['addForumTopic-imageUpload']['name'], 'html|xss|whitespaces');

        $sCoverImage=$this->StringsAdvanced->processText($_POST['addForumTopic-coverImage'], 'html|xss|whitespaces');
        $coverImageUpload = '';
        if (isset($_FILES['addForumTopic-coverImageUpload']))
            $coverImageUpload = $this->StringsAdvanced->processText($_FILES['addForumTopic-coverImageUpload']['name'], 'html|xss|whitespaces');

        $sBodyCode = $this->StringsAdvanced->processText($_POST['addForumTopic-bodyCode'], 'xss|whitespaces');

        if (isset($_POST['addForumTopic-shortDescription']))
            $sShortDescription = $this->StringsAdvanced->processText($_POST['addForumTopic-shortDescription'], 'xss|whitespaces');
        else
            $sShortDescription = '';

        if ($sShortDescription == '') $sShortDescription = $sBodyCode;
        if (strlen($sShortDescription) > 800) $sShortDescription = substr($sShortDescription, 0 , 800) . '...';

        $sShortDescription =  $this->StringsAdvanced->closeTags($sShortDescription);

        if (isset($_POST['addForumTopic-imageUploadAlt']))
            $sImageUploadAlt = $this->StringsAdvanced->processText($_POST['addForumTopic-imageUploadAlt'], 'html|xss|whitespaces');
        else $sImageUploadAlt='';

        $sInputKeywords = $this->StringsAdvanced->processText($_POST['addForumTopic-inputKeywords'], 'html|xss|whitespaces');

        $vInputKeywords = $this->Validator->checkValidKeywords($sInputKeywords);

        $sCountry = $this->StringsAdvanced->processText($_POST['addForumTopic-country'], 'html|xss|whitespaces');
        $sCity = $this->StringsAdvanced->processText($_POST['addForumTopic-city'], 'html|xss|whitespaces');

        $arrAdditionalInformation = [];
        if (isset($_POST['addForumTopic-additionalInformation']))
            $arrAdditionalInformation = json_decode($this->StringsAdvanced->processText($_POST['addForumTopic-additionalInformation'], 'html|xss|whitespaces'), true);

        if ( $this->objParent == null) {
            $this->AlertsContainer->addAlert('g_msgAddForumTopicError', 'error', 'Error adding <strong>' . $sTitle . '</strong> because no parent found in the database');
            return false;
        }

        //IMAGE UPLOAD
        /*var_dump($imageUpload);
        var_dump($_FILES);*/
        if ($imageUpload != '') {
            $sLocation = $this->uploadTopicImage($sTitle);

            if ($sLocation != '')
                $sImage = $sLocation;
        }
        if (($sImage == '')||($sImage == 'none'))
            if ($this->objParent->objImagesComponent != null) $sImage = $this->objParent->objImagesComponent->getImageFirst()['src'];
            else $sImage = $this->objParent->sImage;

        //COVER UPLOAD
        if ($coverImageUpload != '') {
            $sLocation = $this->uploadCoverImage($sTitle);

            if ($sLocation != '')
                $sCoverImage = $sLocation;
        }

        if (($sCoverImage == '') || ($sCoverImage == 'none'))
            if ($this->objParent->objImagesComponent != null)
                $sCoverImage = $this->objParent->objImagesComponent->getCoverFirst()['src'];
            else
                $sCoverImage = $this->objParent->sCoverImage;

        if (($sCoverImage == ''))
        {
            $this->AlertsContainer->addAlert('g_msgAddForumError','error','Error adding <strong>'.$sTitle.'</strong> because no pictures selected/uploaded');
            return false;
        }

        if (!TUserRole::checkUserRights(TUserRole::BotUser))
            if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsBlockedAddForumTopicTooManyTimes))
            {
                $this->AlertsContainer->addAlert('g_msgAddForumTopicError','error',$this->QueryTrials->sError);
                return false;
            }

        if (!TUserRole::checkUserRights(TUserRole::User))
        {
            $this->AlertsContainer->addAlert('g_msgAddForumTopicError','error','<strong>You are not logged in.</strong> Please Login or Register <br/> To publish new content, you need to be a registered user.  <br/> You are not registered');

            if (!$this->MyUser->bLogged)
            {
                $this->load->model('session_actions/Session_actions','SessionActions');
                $this->SessionActions->createSessionAction('newTopic','newTopicPOST',['POST'=>$_POST,'ActionName'=>$this->sParameterActionName,'FullURLLink'=>$this->sParameterFullURLLink, 'Parent'=>$this->parameterParent,'Topic'=>$this->parameterTopic]);
            }

            return false;
        }

        $sFullURLName=rtrim($this->objParent->sFullURLName,'/').'/'.$sTitle;
        $sFullURLLink=rtrim($this->objParent->sFullURLLink,'/').'/'.$sURLName;
        $sFullURLDomains = rtrim($this->objParent->sFullURLDomains,'/').'/'.'topic';
        //echo 'New Parent Category: '.$newParentCategory->sID.'   '.$newParentCategory->sName.'   <br/>';


        switch ($Action)
        {
            case 'add-topic':
                $TopicNewCreated = new Topic_model(true);
                $TopicNewCreated->sURLName = $sURLName;
                $TopicNewCreated->sFullURLName = $sFullURLName;
                $TopicNewCreated->sFullURLLink = $sFullURLLink;
                $TopicNewCreated->sFullURLDomains = $sFullURLDomains;
                $TopicNewCreated->sAuthorId=$this->MyUser->sID;
                $TopicNewCreated->sTitle = $sTitle;
                $TopicNewCreated->setBodyCode($sBodyCode);

                if ($arrAdditionalInformation != [])
                    $TopicNewCreated->arrAdditionalInfo = $arrAdditionalInformation;

                $TopicNewCreated->sCountry = $sCountry;
                $TopicNewCreated->sCity = $sCity;

                $TopicNewCreated->objImagesComponent->insertUploadedImage($sImage,$sImageUploadAlt,$sImageUploadAlt,'');
                $TopicNewCreated->objImagesComponent->insertUploadedCover($sCoverImage,'','','');
                $TopicNewCreated->getImagesFromBodyCode();

                $TopicNewCreated->sParentId = new MongoId($this->objParent->sID);
                $TopicNewCreated->calculateParents($this->objParent);

                $TopicNewCreated->sSiteCategoryParents = (method_exists($this->objParent,'getSiteCategoryMaterializedParents') ? $this->objParent->getSiteCategoryMaterializedParents() : $this->objParent->sSiteCategoryParents);

                $TopicNewCreated->sShortDescription= $sShortDescription;
                $TopicNewCreated->arrInputKeywords = $vInputKeywords;

                if ($iImportanceValue != -666)
                    $TopicNewCreated->fImportance = $iImportanceValue;

                $this->load->model('counter/counter_statistics','CounterStatistics');
                $this->CounterStatistics->increaseTopics(1);

                /*if ($this->objTopicsContainer->insertContainerChild($TopicNewCreated)) {*/
                if ($TopicNewCreated->storeUpdate()) {
                    $this->AlertsContainer->addAlert('g_msgAddForumTopicSuccess', 'success', 'Topic <strong>' . $sTitle . '</strong> has been created successfully in <strong>' . $this->objParent->sName . '</strong> category');

                    $this->objTopic = $TopicNewCreated;

                    if ($this->bRedirectSuccess == true) {
                        redirect($TopicNewCreated->getUsedURL(), 'refresh');
                    }

                    return true;
                }

                return false;
                break;
            case 'edit-topic':
                if (($this->objTopic->sTitle != $sTitle) || ($this->objTopic->getBodyCode() != $sBodyCode)) {
                    $this->objTopic->sTitle = $sTitle;
                    $this->objTopic->setBodyCode($sBodyCode);
                    $this->objTopic->bLastChanged = true;
                }
                if ($sURLName != '') $this->objTopic->sURLName = $sURLName;
                if ($sFullURLName != '') $this->objTopic->sFullURLName = $sFullURLName;
                if ($sFullURLLink != '') $this->objTopic->sFullURLLink = $sFullURLLink;
                if ($sFullURLDomains != '') $this->objTopic->sFullURLDomains = $sFullURLDomains;

                $this->objTopic->sParentId = new MongoId($this->objParent->sID);
                $this->objTopic->calculateParents($this->objParent);

                $this->objTopic->sSiteCategoryParents = (method_exists($this->objParent,'getSiteCategoryMaterializedParents') ? $this->objParent->getSiteCategoryMaterializedParents() : $this->objParent->sSiteCategoryParents);

                if (($arrAdditionalInformation != [])&&($this->objTopic->arrAdditionalInfo != $arrAdditionalInformation))
                    $this->objTopic->arrAdditionalInfo = $arrAdditionalInformation;

                $this->objTopic->sShortDescription= $sShortDescription;
                $this->objTopic->arrInputKeywords = $vInputKeywords;

                $this->objTopic->sCountry = $sCountry;
                $this->objTopic->sCity = $sCity;

                if ($this->objTopic->objImagesComponent->insertUploadedImage($sImage,$sImageUploadAlt,$sImageUploadAlt,''))
                    $this->objTopic->bLastChanged= true;

                if ($this->objTopic->objImagesComponent->insertUploadedCover($sCoverImage,'','',''))
                    $this->objTopic->bLastChanged= true;

                $this->objTopic->getImagesFromBodyCode();

                if ($iImportanceValue != -666)
                    $this->objTopic->iImportance = $iImportanceValue;

                //if ($this->objTopicsContainer->updateContainerChild($this->objTopic)) {
                if ($this->objTopic->storeUpdate()) {
                    $this->AlertsContainer->addAlert('g_msgAddForumTopicSuccess', 'success', 'Topic <strong>' . $sTitle . '</strong> has been eddited successfully in <strong>' . $this->objParent->sName . '</strong> category');

                    echo $this->bRedirectSuccess;
                    /*
                    if ($this->bRedirectSuccess)
                        redirect($this->objTopic->getUsedURL(), 'refresh');*/

                    return true;
                }
                return false;
            case 'delete-topic':
                //if ($this->objParentForumCategory->storeUpdate()) {
                //if ($this->objTopicsContainer->deleteContainerChild($this->objTopic))

                $result = $this->objTopic->delete();
                if ($result['result']){
                    $this->load->model('counter/counter_statistics','CounterStatistics');
                    $this->CounterStatistics->increaseTopics(-1);

                    $this->AlertsContainer->addAlert('g_msgAddForumTopicSuccess', 'success', 'Topic <strong>' . $sTitle . '</strong> has been deleted correctly <strong>' . $this->objParent->sName . '</strong> category');


                    if ($this->bRedirectSuccess)
                        redirect($this->objParent->getFullURL(), 'refresh');
                }else
                    $this->AlertsContainer->addAlert('g_msgAddForumTopicError', 'error', 'Topic <strong>' . $sTitle . '</strong> couldn\'t be deleted from <strong>' . $this->objParent->sName . '</strong> category');

                return false;
            default:
                $this->AlertsContainer->addAlert('g_msgAddForumTopicError','error','The request was rejected: <strong>'.$this->URI->sFormId.'</strong> ');
                return false;
        }

    }

    protected function processPage($sActionName, $sFullURLLink)
    {
        $this->includeWebPageLibraries('advanced-text-editor');
        if ($this->objTopic != null)
        {
            $sFormActionPrefix = '/topic/';
            $sFormAction = rtrim($sFormActionPrefix,'/').'/'.$sFullURLLink.'/'.$this->objTopic->sID.'/#AddForumCategory';
        } else
        if ($this->objParent){
            $sFormActionPrefix = '/forum/category/';
            $sFormAction = rtrim($sFormActionPrefix,'/').'/'.$sFullURLLink.'/'.$this->objParent->sID.'/#AddForumCategory';
        }

        if (($_POST)&&isset($_POST["deleteForumTopic"]))  $sActionName = 'delete-topic';
        if (($_POST)&&isset($_POST["editForumTopic"]))  $sActionName = 'edit-topic';

        //var_dump($_POST);
        if (($_POST)&&((isset($_POST["addForumTopic"])) || (isset($_POST['editForumTopic'])) || (isset($_POST['deleteForumTopic']))) )
            $result = $this->processForumTopic($sActionName);
        else
        {
            $result = false;
            //$this->AlertsContainer->addAlert('g_msgAddForumTopicError','error','No POST Action found.');
        }

        if ($sActionName!= '')
        {
            switch ($sActionName)
            {
                case 'edit-topic':
                    //$_POST['addForumTopic-parentCategoryName'] = '';
                    //$this->objParentForumCategory = $this->ForumCategories->getForumCategory($this->objTopic->sParentForumCategoryId);

                    if (!isset($_POST['addForumTopic-title'])) $_POST['addForumTopic-title'] = $this->objTopic->sTitle;
                    if (!isset($_POST['addForumTopic-urlName'])) $_POST['addForumTopic-urlName'] = $this->objTopic->sURLName;

                    if (!isset($_POST['addForumTopic-image'])) $_POST['addForumTopic-image'] = $this->objTopic->objImagesComponent->getImageFirst() != null ? $this->objTopic->objImagesComponent->getImageFirst()['src'] : '';
                    if (!isset($_POST['addForumTopic-coverImage'])) $_POST['addForumTopic-coverImage'] = $this->objTopic->objImagesComponent->getCoverFirst() != null ? $this->objTopic->objImagesComponent->getCoverFirst()['src'] : '';

                    if (!isset($_POST['addForumTopic-shortDescription'])) $_POST['addForumTopic-shortDescription'] = $this->objTopic->sShortDescription;
                    if (!isset($_POST['addForumTopic-bodyCode'])) $_POST['addForumTopic-bodyCode'] = $this->objTopic->getBodyCodeRendered();
                    if (!isset($_POST['addForumTopic-coverImage'])) $_POST['addForumTopic-coverImage'] = $this->objTopic->sCoverImage;
                    if (!isset($_POST['addForumTopic-importance'])) $_POST['addForumTopic-importance'] = $this->objTopic->fImportance;
                    if (!isset($_POST['addForumTopic-inputKeywords'])) $_POST['addForumTopic-inputKeywords'] = $this->objTopic->getInputKeywordsToString();

                    if (!isset($_POST['addForumTopic-country'])) $_POST['addForumTopic-country'] = ($this->objTopic->sCountry != '' ? $this->objTopic->sCountry : strtolower($this->MyUser->sCountry));
                    if (!isset($_POST['addForumTopic-city'])) $_POST['addForumTopic-city'] = ($this->objTopic->sCity != '' ? $this->objTopic->sCity : $this->MyUser->sCity);

                    $sFormAction = rtrim($sFormActionPrefix,'/').'/'.rtrim($sFullURLLink,'/').'/'.$this->objTopic->sID.'/edit-topic/#AddTopic';
                    break;
                case 'add-topic'://adding a new forum by importing data from parent category
                    //$_POST['addForumTopic-parentCategoryName'] = $this->objTopic->sName;

                    /*if (!isset($_POST['addForumTopic-title'])) $_POST['addForumTopic-title'] = $this->objParent->sName.'  new topic';
                    if (!isset($_POST['addForumTopic-coverImage'])) $_POST['addForumTopic-coverImage'] = $this->objParent->sCoverImage;
                    if (!isset($_POST['addForumTopic-image'])) $_POST['addForumTopic-image'] = $this->objParent->sImage;*/

                    if (!isset($_POST['addForumTopic-country'])) $_POST['addForumTopic-country'] = strtolower($this->MyUser->sCountry);
                    if (!isset($_POST['addForumTopic-city'])) $_POST['addForumTopic-city'] = $this->MyUser->sCity;

                    $sFormAction = rtrim($sFormActionPrefix,'/').'/'.rtrim($sFullURLLink,'/').'/'.$this->objParent->sID.'/add-topic/#AddTopic';
                    break;

            }
        }


        $this->data['sActionName'] = $sActionName;
        $this->data['sFormAction'] = $sFormAction;

        $this->data['dtCurrentTopic']= $this->objTopic;
        $this->data['dtParent'] = $this->objParent;
        $this->data['sParentId'] = $this->objParent->sID;
        $this->data['sFormIndex'] = $this->sFormIndex;
        $this->data['sFormResponseType'] = $this->sFormResponseType;

        $this->sFormCode = $this->renderModuleView('add_forum_topic_view',$this->data, TRUE);

        if ($this->bRenderForm)
            $this->ContentContainer->addObject($this->sFormCode,'<div class="container">',4);

        return $result;
    }

    protected function uploadTopicImage($categoryName)
    {
        $sUploadedFileLocation = $this->uploadFileForm($categoryName,'addForumTopic-imageUpload',"uploads/images/topics/images",
            "jpg|jpeg|ico|gif|png",'g_msgAddForumTopicError','Icon Image','forumIconImage_upload');

        return $sUploadedFileLocation;
    }

    protected function uploadCoverImage($categoryName)
    {
        $sUploadedFileLocation = $this->uploadFileForm($categoryName,'addForumTopic-coverImageUpload',"uploads/images/topics/images/",
            "jpg|jpeg|gif|png",'g_msgAddForumError','Cover Image','forumCoverImage_upload');

        return $sUploadedFileLocation;
    }




}