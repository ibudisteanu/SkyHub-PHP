<?php

require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Add_forum_category extends MY_AdvancedController
{
    protected $objForumCategory;
    protected $objForumCategoriesContainer;
    protected $objForum;

    function __construct()
    {
        parent::__construct();
        $this->load->model('forum/forums_model', 'Forums');
        $this->load->model('forum_categories/forum_categories_model', 'ForumCategories');
    }

    public function index($sActionName, $sFullURLLink, $Forum, $ParentForumCategory=null)
    {
        if ($ParentForumCategory != null)
        {
            if (get_class($ParentForumCategory) == 'Forum_category_model')
                $this->objForumCategory = $ParentForumCategory;
            else
                if (is_string($ParentForumCategory))
                    $this->objForumCategory = $this->ForumCategories->getForumCategory($ParentForumCategory);
        }

        if ((isset($_POST['forumCategoryId']))&&(($this->objForumCategory==null)||($_POST['forumCategoryId'] != $this->objForumCategory->sID))) {
            $this->objForumCategory = $this->ForumCategories->getForumCategory($_POST['forumCategoryId']);
        }

        if (($Forum != null) && (is_object($Forum)))
            $this->objForum = $Forum;
        else
        if ($this->objForumCategory != null)
            $this->objForum = $this->Forums->findForum($this->objForumCategory->sParentForumId);

        if (($this->objForum==null))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Forum was not found in the database');
            return;
        }

        if (($this->objForumCategory == null)&&(!$this->objForum->checkOwnership()))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Insufficient rights to add a forum category');
            return;
        }

        if (($this->objForumCategory != null)&&(!$this->objForumCategory->checkOwnership()))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Insufficient rights to process this forum category');
            return;
        }

        $bAllow=false;
        switch ($sActionName)
        {
            case 'add-forum-category':
                $bAllow=true;
                break;
            case 'delete-forum-category':
                $bAllow=true;
                break;
            case 'edit-forum-category':
                $bAllow=true;
                break;
        }

        if (!$bAllow)
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Invalid action');
            return;
        } else
            $this->processPage($sActionName, $sFullURLLink);
    }

    protected function addForumCategory($sAction='insert')
    {
        $this->load->model('add_forum_category/query_trials_blocked_add_forum_category_too_many_times','QueryTrialsBlockedAddForumCategoryTooManyTimes');
        $this->load->library('../modules/forums/forum_categories/widgets/add_forum_category/libraries/Add_forum_category_validator',null,'Validator');

        if (!TUserRole::checkUserRights(TUserRole::User))
        {
            $this->AlertsContainer->addAlert('g_msgAddForumCategoryError','error','You are not registered');
            return false;
        }

        if (! $this->Validator->CheckPosts())
        {
            $this->AlertsContainer->addAlert('g_msgAddForumCategoryError','error',$this->Validator->sError);
            return false;
        }

        if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsBlockedAddForumCategoryTooManyTimes))
        {
            $this->AlertsContainer->addAlert('g_msgAddForumCategoryError','error',$this->QueryTrials->sError);
            return false;
        }

        $sName=$this->StringsAdvanced->processText($_POST['addForumCategory-name'], 'xss|whitespaces');

        $sURLName = '';
        if (isset($_POST['addForumCategory-urlName'])) $sURLName = $_POST['addForumCategory-urlName'];
        if ($sURLName == '') $sURLName = $sName;

        $sURLName = $this->StringsAdvanced->processURLString($sURLName);

        if (isset($_POST['addForumCategory-importance'])) $iImportanceValue=(float)$this->StringsAdvanced->processText($_POST['addForumCategory-importance'],'html|xss|whitespaces');
        else $iImportanceValue = -666;

        $imageIcon= $this->StringsAdvanced->processText($_POST['addForumCategory-imageIcon'], 'html|xss|whitespaces');
        $imageUpload = $this->StringsAdvanced->processText($_FILES['addForumCategory-imageUpload']['name'], 'html|xss|whitespaces');
        $coverImage=$this->StringsAdvanced->processText($_POST['addForumCategory-coverImage'], 'html|xss|whitespaces');
        $coverImageUpload = $this->StringsAdvanced->processText($_FILES['addForumCategory-coverImageUpload']['name'], 'html|xss|whitespaces');
        $sDescription = $this->StringsAdvanced->processText($_POST['addForumCategory-description'], 'xss|whitespaces');
        $sInputKeywords = $this->StringsAdvanced->processText($_POST['addForumCategory-inputKeywords'], 'html|xss|whitespaces');
        $vInputKeywords = $this->Validator->checkValidKeywords($sInputKeywords);

        $sImage='';
        if ($imageIcon != '') $sImage = $imageIcon;
        if ($imageUpload != '')
        {
            $sLocation = $this->uploadCategoryImageIcon($sName);

            if ($sLocation != '')
                $sImage = $sLocation;
        }

        if ($coverImage!='') $sCoverImage=$coverImage;
        if ($coverImageUpload != '')
        {
            $sLocation = $this->uploadCategoryCoverImage($sName);

            if ($sLocation != '')
                $sCoverImage = $sLocation;
        }


        if (/*($sImage == '') ||*/ ($sCoverImage == ''))
        {
            $this->AlertsContainer->addAlert('g_msgAddForumError','error','Error adding <strong>'.$sName.'</strong> because no pictures selected/uploaded');
            return false;
        }

        $sFullURLName=rtrim($this->objForum->sFullURLName,'/').'/'.$sName;
        $sFullURLLink = rtrim($this->objForum->sFullURLLink,'/').'/'.$sURLName;
        $sFullURLDomains = rtrim($this->objForum->sFullURLDomains,'/').'/'.'forum_category';
        //echo 'New Parent Category: '.$newParentCategory->sID.'   '.$newParentCategory->sName.'   <br/>';

        switch ($sAction)
        {
            case 'add-forum-category':
                $this->objForumCategory = new Forum_category_model(true,null,null,true);
                $this->objForumCategory->sURLName = $sURLName;
                $this->objForumCategory->sFullURLName = $sFullURLName;
                $this->objForumCategory->sFullURLLink = $sFullURLLink;
                $this->objForumCategory->sFullURLDomains = $sFullURLDomains;
                $this->objForumCategory->sSiteCategoryParents = $this->objForum->sSiteCategoryParents;
                $this->objForumCategory->sImage = $sImage;
                $this->objForumCategory->sAuthorId=$this->MyUser->sID;
                $this->objForumCategory->sName = $sName;
                $this->objForumCategory->sDescription = $sDescription;
                $this->objForumCategory->sCoverImage = $sCoverImage;
                $this->objForumCategory->iNoComments = 0;
                $this->objForumCategory->iNoTopics = 0;
                $this->objForumCategory->iNoUsers = 0;
                $this->objForumCategory->sParentForumId = new MongoId($this->objForum->sID);
                $this->objForumCategory->arrInputKeywords = $vInputKeywords;

                if ($iImportanceValue != -666)
                    $this->objForumCategory->fImportance = $iImportanceValue;

                //if ($this->objForumCategoriesContainer->insertContainerChild($this->objForumCategory)) {
                if ($this->objForumCategory->storeUpdate()) {
                    $this->AlertsContainer->addAlert('g_msgAddForumCategoryError', 'success', 'Forum Category <strong>' . $sName . '</strong> has been created successfully in forum <strong>' . $this->objForum->sName . '</strong> ');
                    redirect($this->objForumCategory->getFullURL(), 'refresh');
                    return true;
                }
                return false;

            case 'edit-forum-category':
                if (($this->objForumCategory->sName != $sName) || ($this->objForumCategory->sDescription != $sDescription)||($this->objForumCategory->sImage != $sImage)||($this->objForumCategory->sCoverImage != $sCoverImage)) {
                    $this->objForumCategory->sName = $sName;
                    $this->objForumCategory->sDescription = $sDescription;
                    $this->objForumCategory->sImage = $sImage;
                    $this->objForumCategory->sCoverImage = $sCoverImage;
                    $this->objForumCategory->bLastChanged = true;
                }
                if ($sURLName != '') $this->objForumCategory->sURLName = $sURLName;
                if ($sFullURLName != '') $this->objForumCategory->sFullURLName = $sFullURLName;
                if ($sFullURLLink != '') $this->objForumCategory->sFullURLLink = $sFullURLLink;
                if ($sFullURLDomains != '') $this->objForumCategory->sFullURLDomains = $sFullURLDomains;

                $this->objForumCategory->sSiteCategoryParents = $this->objForum->sSiteCategoryParents;

                $this->objForumCategory->sParentForumId = new MongoId($this->objForum->sID);
                $this->objForumCategory->arrInputKeywords = $vInputKeywords;

                $this->objForumCategory->arrInputKeywords = $vInputKeywords;
                if ($iImportanceValue != -666)
                    $this->objForumCategory->iImportance = $iImportanceValue;

                //$this->objForum->updateChild($this->objForumCategory, "arrCategories");
                //if ($this->objForumCategory->storeUpdate())

                //if ($this->objForumCategoriesContainer->updateContainerChild($this->objForumCategory)) {
                if ($this->objForumCategory->storeUpdate()) {
                    $this->AlertsContainer->addAlert('g_msgAddForumCategorySuccess', 'success', 'Forum Category <strong>' . $sName . '</strong> has been eddited successfully in <strong>' . $this->objForum->sName . '</strong> ');
                    return true;
                }
                return false;

            case 'delete-forum-category':

                //if ($this->objForumCategoriesContainer->deleteContainerChild($this->objForumCategory)) {
                if ($this->objForumCategory->delete()){
                    $this->AlertsContainer->addAlert('g_msgAddForumCategorySuccess', 'success', 'Forum Category <strong>' . $sName . '</strong> has been deleted correctly <strong>' . $this->objForum->sName . '</strong> category');
                    return true;
                }

                return false;
            default:
                $this->AlertsContainer->addAlert('g_msgAddForumCategoryError','error','The request was rejected: <strong>'.$this->URI->sFormId.'</strong> ');
                return false;
        }

    }

    protected function processPage($sActionName, $sFullURLLink)
    {
        $sFormActionPrefix='/forum/';

        if ($this->objForumCategory != null)
            $sFormActionPrefix = '/forum/category/';

        //$sFormAction = $sFormActionPrefix.$sFullURLLink.'/'.$this->objForum->sID.'/#AddForumCategory';

        if (($_POST)&&isset($_POST["val"]) && ($_POST["val"] == 'addForumCategory'))
            $this->addForumCategory($sActionName);
        else
            if (($_POST)&&isset($_POST["val"]) && ($_POST["val"] == 'editForumCategory'))
            {
                $sActionName = 'edit-forum-category';
                $this->addForumCategory($sActionName);
            }

        if ($this->objForumCategory != null) $objImport = $this->objForumCategory;
        else $objImport = $this->objForum;

        if ($objImport == null)
        {
            $this->AlertsContainer->addAlert('g_msgAddForumCategoryError','error','Category not found for editing');
            return;
        }

        if ($sActionName!= '')
        {
            switch ($sActionName)
            {
                case 'edit-forum-category':

                    if ($this->objForumCategory==null)
                    {
                        $this->AlertsContainer->addAlert('g_msgAddForumCategoryError','error','Category not found for editing');
                        break;
                    }

                    if (!isset($_POST['addForumCategory-parentCategoryName'])) $_POST['addForumCategory-parentCategoryName'] = $this->objForum->sName;
                    if (!isset($_POST['addForumCategory-name'])) $_POST['addForumCategory-name'] = $this->objForumCategory->sName;
                    if (!isset($_POST['addForumCategory-urlName'])) $_POST['addForumCategory-urlName'] = $this->objForumCategory->sURLName;
                    if (!isset($_POST['addForumCategory-imageIcon'])) $_POST['addForumCategory-imageIcon'] = $this->objForumCategory->sImage;
                    if (!isset($_POST['addForumCategory-coverImage'])) $_POST['addForumCategory-coverImage'] = $this->objForumCategory->sCoverImage;
                    if (!isset($_POST['addForumCategory-description'])) $_POST['addForumCategory-description'] = $this->objForumCategory->sDescription;
                    if (!isset($_POST['addForumCategory-importance'])) $_POST['addForumCategory-importance'] = $this->objForumCategory->fImportance;
                    if (!isset($_POST['addForumCategory-inputKeywords'])) $_POST['addForumCategory-inputKeywords'] = $this->objForumCategory->getInputKeywordsToString();
                    $sFormAction = $sFormActionPrefix.$sFullURLLink.'/'.$this->objForumCategory->sID.'/edit-forum-category/#AddForumCategory';
                    break;
                case 'add-forum-category'://adding a new forum by importing data from parent category
                    if (!isset($_POST['addForumCategory-parentCategoryName'])) $_POST['addForumCategory-parentCategoryName'] = $objImport->sName;
                    if (!isset($_POST['addForumCategory-name'])) $_POST['addForumCategory-name'] = $objImport->sName.'  Category';

                    if (!isset($_POST['addForumCategory-imageIcon'])) $_POST['addForumCategory-imageIcon'] = $objImport->sImage;
                    if (!isset($_POST['addForumCategory-imageIcon'])) $_POST['addForumCategory-imageIcon'] = $objImport->sImage;
                    if (!isset($_POST['addForumCategory-coverImage'])) $_POST['addForumCategory-coverImage'] = $objImport->sCoverImage;
                    $sFormAction = $sFormActionPrefix.$sFullURLLink.'/'.$objImport->sID.'/add-forum-category/#AddForumCategory';
                    break;
                case 'delete-forum-category':

                    if ($this->objForumCategory==null)
                    {
                        $this->AlertsContainer->addAlert('g_msgAddForumCategoryError','error','Category not found for deleting');
                        break;
                    }

                    /*
                    $newParentCategory = $this->Forums->exists($this->objForumCategory->sParentId);
                    $this->Forums->Update(array("_id"=>new MongoId($newParentCategory->sID)),array("Children"=>array("_id"=>new MongoId($this->objForumCategory->sID))),'$pull');
                    $this->Forums->deleteById($this->objForumCategory->sID);

                    if ($newParentCategory != null)
                        redirect($newParentCategory->getFullURL(), 'refresh');*/

                    break;
            }
        }

        $this->data['sActionName'] = $sActionName;
        $this->data['sFormAction'] = $sFormAction;

        if ($this->objForumCategory != null)  $this->data['dtCurrentForumCategory']= $this->objForumCategory;
        if ($this->objForum != null)  $this->data['dtCurrentForum']= $this->objForum;

        $this->ContentContainer->addObject($this->renderModuleView('add_forum_category_view',$this->data, TRUE),'<div class="container">',4);
    }

    protected function uploadCategoryImageIcon($categoryName)
    {
        $sUploadedFileLocation = $this->uploadFileForm($categoryName,'addForumCategory-imageUpload',"uploads/images/forums/icons",
            "jpg|jpeg|ico|gif|png",'g_msgAddForumCategoryError','Icon Image','forumIconImage_upload');

        return $sUploadedFileLocation;
    }

    protected function uploadCategoryCoverImage($categoryName)
    {
        $sUploadedFileLocation = $this->uploadFileForm($categoryName,'addForumCategory-coverImageUpload',"uploads/images/forums/covers/",
            "jpg|jpeg|gif|png",'g_msgAddForumError','Cover Image','forumCoverImage_upload');

        return $sUploadedFileLocation;
    }


}