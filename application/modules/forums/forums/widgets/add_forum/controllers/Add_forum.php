<?php

require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Add_forum extends MY_AdvancedController
{
    public $objForum = null;
    public $objSiteCategoryParent = null;

    function __construct()
    {
        parent::__construct();
        $this->load->model('forum/forums_model', 'Forums');
        $this->load->model('site_main_categories/site_categories_model', 'SiteCategories');
        $this->load->model('users/Users_minimal', 'Users');

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
        $this->includeWebPageLibraries('country-select');
    }


    public function index($sActionName, $sFullURLLink, $CategoryParent='', $Forum=null)
    {

        if (($Forum == null) || ($Forum == ''))
            //I don't have any information but I have forum_id
            if ((isset($_POST['forum_id']))&&($_POST['forum_id']!=''))
                $Forum= (string) $_POST['forum_id'];

        if (!is_string($Forum)) $this->objForum = $Forum;
        else
            if ($Forum != '')
                $this->objForum = $this->Forums->findForum($Forum, $sFullURLLink);

        if ((($CategoryParent == null)||($CategoryParent == ''))&&($this->objForum != null))
        {
            $this->objSiteCategoryParent = $this->SiteCategories->findCategory($this->objForum->sParentCategoryId, $sFullURLLink);
        } else
        if ((!is_string($CategoryParent))&&(get_class($CategoryParent) == 'Forum_category_model')) $this->objSiteCategoryParent = $CategoryParent;
        else
            if ($CategoryParent  != '')
                $this->objSiteCategoryParent = $this->SiteCategories->findCategory($CategoryParent, $sFullURLLink);

        if (($this->objSiteCategoryParent==null))
        {
            $this->AlertsContainer->addAlert('g_msgAddForumError','error','Parent Category not found: <strong>'.$CategoryParent.'</strong> ');
            return;
        }

        $bAllow=false;
        switch ($sActionName)
        {
            case 'add-forum':
                if (TUserRole::checkUserRights(TUserRole::User))
                    $bAllow=true;
                break;
            case 'edit-forum':
            case 'delete-forum':
                //I have to check if the ForumId ownership is the same with my user
                if ($this->objForum == null)
                {
                    $this->AlertsContainer->addAlert('g_msgGeneralError','error','Forum not found: <strong>'.$Forum.'</strong> ');
                    $bAllow=false;
                } else
                if (!$this->objForum->checkOwnership())
                {
                    $this->AlertsContainer->addAlert('g_msgGeneralError','error','Insufficient rights to add a forum ');
                    $bAllow=false;
                    return;
                } else
                $bAllow=true;

                break;
        }

        if (!$bAllow)
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Insufficient rights to add a forum ');
            return;
        } else
            $this->processPage($sActionName, $sFullURLLink);

    }

    protected function addForum($sAction='insert')
    {
        $this->load->model('add_forum/query_trials_blocked_add_forum_too_many_times','QueryTrialsBlockedAddForumTooManyTimes');
        $this->load->library('../modules/forums/forums/widgets/add_forum/libraries/Add_forum_validator',$this->Users,'Validator');

        if (!TUserRole::checkUserRights(TUserRole::User))
        {
            $this->AlertsContainer->addAlert('g_msgAddForumError','error','You are not registered');
            return false;
        }

        if (! $this->Validator->CheckPosts($this->SiteCategories))
        {
            $this->AlertsContainer->addAlert('g_msgAddForumError','error',$this->Validator->sError);
            return false;
        }

        if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsBlockedAddForumTooManyTimes))
        {
            $this->AlertsContainer->addAlert('g_msgAddForumError','error',$this->QueryTrials->sError);
            return false;
        }

        $sName = $this->StringsAdvanced->processText($_POST['addForum-name'], 'html|xss|whitespaces');

        $sURLName='';
        if (isset($_POST['addForum-urlName'])) $sURLName=$_POST['addForum-urlName'];
        if ($sURLName == '') $sURLName = $sName;

        $sURLName = $this->StringsAdvanced->processURLString($sURLName);

        $sParentCategoryId = $this->StringsAdvanced->processText($_POST['addForum-parentCategory'], 'html|xss|whitespaces');
        $fImportanceValue=(float)$this->StringsAdvanced->processText($_POST['addForum-importance'], 'html|xss|whitespaces');
        $imageIcon = $this->StringsAdvanced->processText($_POST['addForum-imageIcon'],'html|xss|whitespaces');
        $imageUpload = $this->StringsAdvanced->processText($_FILES['addForum-imageUpload']['name'], 'html|xss|whitespaces');
        $coverImage = $this->StringsAdvanced->processText($_POST['addForum-coverImage'], 'html|xss|whitespaces');
        $coverImageUpload = $this->StringsAdvanced->processText($_FILES['addForum-coverImageUpload']['name'], 'html|xss|whitespaces');
        $sDescription = $this->StringsAdvanced->processText($_POST['addForum-description'], 'xss|whitespaces');
        $sDetailedDescription = $this->StringsAdvanced->processText($_POST['addForum-detailedDescription'], 'xss|whitespaces');
        $sInputKeywords = $this->StringsAdvanced->processText($_POST['addForum-inputKeywords'], 'html|xss|whitespaces');

        $sCountry = $this->StringsAdvanced->processText($_POST['addForum-country'], 'html|xss|whitespaces');
        $sCity = $this->StringsAdvanced->processText($_POST['addForum-city'], 'html|xss|whitespaces');

        $vInputKeywords = $this->Validator->checkValidKeywords($sInputKeywords);

        $sImage=''; $sCoverImage ='';
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

        if (($sImage == '') || ($sCoverImage == ''))
        {
            $this->AlertsContainer->addAlert('g_msgAddForumError','error','Error adding <strong>'.$sName.'</strong> because no pictures selected/uploaded');
            return false;
        }

        $newParentCategory=null;
        if (($sParentCategoryId!=''))
        {
            if (($this->objSiteCategoryParent != null) && ($this->objSiteCategoryParent->sID == $sParentCategoryId))
                $newParentCategory = $this->objSiteCategoryParent;
            else
                $newParentCategory = $this->SiteCategories->findCategory($sParentCategoryId);
        }

        if ($newParentCategory == null)
        {
            $this->AlertsContainer->addAlert('g_msgAddForumError','error','Error adding <strong>'.$sName.'</strong> because no PARENT category');
            return false;
        }

        $this->objSiteCategoryParent = $newParentCategory;

        $sFullURLLink=rtrim($this->objSiteCategoryParent->sFullURLLink,'/').'/'.$sURLName;
        $sFullURLName=rtrim($this->objSiteCategoryParent->sFullURLName,'/').'/'.$sName;
        $sFullURLDomains = rtrim($this->objSiteCategoryParent->sFullURLDomains,'/').'/'.'forum';


        //echo $currentForumForUpdate->sName."<br/><br/>";

        switch ($sAction)
        {
            case 'add-forum':
                $this->objForum= new Forum_model(true);
                $this->objForum->sURLName = $sURLName;
                $this->objForum->sFullURLName = $sFullURLName;
                $this->objForum->sFullURLLink = $sFullURLLink;
                $this->objForum->sFullURLDomains = $sFullURLDomains;
                $this->objForum->sSiteCategoryParents = $this->objSiteCategoryParent->getSiteCategoryMaterializedParents();
                $this->objForum->sImage = $sImage;
                $this->objForum->sAuthorId=$this->MyUser->sID;
                $this->objForum->sName = $sName;
                $this->objForum->sDescription = $sDescription;
                $this->objForum->sDetailedDescription = $sDetailedDescription;
                $this->objForum->sCoverImage = $sCoverImage;
                $this->objForum->sCountry = $sCountry;
                $this->objForum->sCity = $sCity;

                $this->objForum->iNoComments = 0;
                $this->objForum->iNoSubCategories = 0;
                $this->objForum->iNoTopics = 0;
                $this->objForum->iNoUsers = 0;
                $this->objForum->sParentCategoryId = new MongoId($this->objSiteCategoryParent->sID);
                //$this->objForum->sAttachedParentId = new MongoId($this->objSiteCategoryParent->sID);
                $this->objForum->arrInputKeywords = $vInputKeywords;

                if ($fImportanceValue != -666) $this->objForum->fImportance = $fImportanceValue;

                $this->load->model('counter/counter_statistics','CounterStatistics');
                $this->CounterStatistics->increaseForums(1);

                if ($this->objForum->storeUpdate($this->objSiteCategoryParent)) {
                    $this->AlertsContainer->addAlert('g_msgAddForumSuccess', 'success', 'Forum <strong>' . $sName . '</strong> has been created successfully in category <strong>' . $this->objSiteCategoryParent->sName . '</strong> ');
                    redirect($this->objForum->getFullURL(), 'refresh');
                    return true;
                }
                return false;

            case 'edit-forum':

                if (($this->objForum==null)||(!$this->objForum->checkOwnership()))
                {
                    $this->AlertsContainer->addAlert('g_msgAddForumError','error',"You don't have enough rights to update the forum ");
                    return false;
                }

                if (($this->objForum->sName != $sName) || ($this->objForum->sDescription != $sDescription)||($this->objForum->sDetailedDescription != $sDetailedDescription)||($this->objForum->sImage != $sImage)||($this->objForum->sCoverImage != $sCoverImage)||($this->objForum->sCity != $sCity)||($this->objForum->sCountry != $sCountry)) {
                    $this->objForum->sName = $sName;
                    $this->objForum->sDescription = $sDescription;
                    $this->objForum->sDetailedDescription = $sDetailedDescription;
                    $this->objForum->sImage = $sImage;
                    $this->objForum->sCoverImage = $sCoverImage;
                    $this->objForum->sCountry = $sCountry;
                    $this->objForum->sCity = $sCity;

                    $this->objForum->bLastChanged = true;
                }
                if ($sURLName != '') $this->objForum->sURLName = $sURLName;
                if ($sFullURLName != '') $this->objForum->sFullURLName = $sFullURLName;
                if ($sFullURLLink != '') $this->objForum->sFullURLLink = $sFullURLLink;
                if ($sFullURLDomains != '') $this->objForum->sFullURLDomains = $sFullURLDomains;

                $this->objForum->sSiteCategoryParents = $this->objSiteCategoryParent->getSiteCategoryMaterializedParents();

                $this->objForum->sParentCategoryId = new MongoId($this->objSiteCategoryParent->sID);
                //$this->objForum->sAttachedParentId = new MongoId($this->objForumCategoriesContainer->sID);
                $this->objForum->arrInputKeywords = $vInputKeywords;

                $this->objForum->arrInputKeywords = $vInputKeywords;
                if ($fImportanceValue != -666) $this->objForum->fImportance = $fImportanceValue;

                //if ($this->ForumCategories->Update(array("Children._id"=>new MongoId($currentReplyForUpdate->sID)),array("Children.$"=>$MongoData),'$set'))
                if ($this->objForum->storeUpdate($this->objSiteCategoryParent))
                {
                    $this->AlertsContainer->addAlert('g_msgAddForumSuccess', 'success', 'Forum <strong>' . $sName . '</strong> has been eddited successfully in <strong>' . $this->objSiteCategoryParent->sName . '</strong> ');
                    return true;
                }
                return false;

            case 'delete-forum':
                $result = $this->objForum->delete();
                if ($result['result'])
                {
                    $this->load->model('counter/counter_statistics','CounterStatistics');
                    $this->CounterStatistics->increaseForums(-1);

                    $this->AlertsContainer->addAlert('g_msgAddForumSuccess', 'success', 'Forum <strong>' . $sName. '</strong> has been deleted correctly <strong>' . $this->objSiteCategoryParent->sName . '</strong> category');
                    redirect($this->objSiteCategoryParent->getFullURL(), 'refresh');
                    return true;
                } else {
                    $this->AlertsContainer->addAlert('g_msgAddForumError', 'error', 'Forum <strong>' . $sName . "</strong> couldn't be deleted <strong>" . $this->objSiteCategoryParent->sName . '</strong> category'.'<br/>'.$result['message']);
                    return false;
                }
            default:
                $this->AlertsContainer->addAlert('g_msgAddForumError','error','The request was rejected: <strong>'.$this->URI->sFormId.'</strong> ');
                return false;
        }

    }

    protected function processPage($sActionName, $sFullURLLink)
    {

        $sFormAction = '/category/'.$sFullURLLink.'/'.$this->objSiteCategoryParent->sID.'/#AddForum';

        if (($_POST)&&isset($_POST["deleteForum"]))  $sActionName = 'delete-forum';
        if (($_POST)&&isset($_POST["editForum"]))  $sActionName = 'edit-forum';

        if (($_POST)&&((isset($_POST["addForum"])) || (isset($_POST['editForum'])) || (isset($_POST['deleteForum']))) ) $result = $this->addForum($sActionName);

        if (($sActionName!= '')&&($this->objSiteCategoryParent!=null))
        {

            switch ($sActionName)
            {
                case 'edit-forum':
                    if (!isset($_POST['addForum-parentCategoryName'])) $_POST['addForum-parentCategoryName'] = $this->objSiteCategoryParent->sName;
                    if (!isset($_POST['addForum-parentCategory'])) $_POST['addForum-parentCategory'] = $this->objForum->sParentCategoryId;
                    if (!isset($_POST['addForum-name'] )) $_POST['addForum-name'] = $this->objForum->sName;
                    if (!isset($_POST['addForum-urlName'] )) $_POST['addForum-urlName'] = $this->objForum->sURLName;
                    if (!isset($_POST['addForum-imageIcon'])) $_POST['addForum-imageIcon'] = $this->objForum->sImage;
                    if (!isset($_POST['addForum-description'])) $_POST['addForum-description'] = $this->objForum->sDescription;
                    if (!isset($_POST['addForum-detailedDescription'])) $_POST['addForum-detailedDescription'] = $this->objForum->sDetailedDescription;
                    if (!isset($_POST['addForum-coverImage'])) $_POST['addForum-coverImage'] = $this->objForum->sCoverImage;
                    if (!isset($_POST['addForum-importance'])) $_POST['addForum-importance'] = $this->objForum->fImportance;
                    if (!isset($_POST['addForum-inputKeywords'])) $_POST['addForum-inputKeywords'] = $this->objForum->getInputKeywordsToString();

                    if (!isset($_POST['addForum-country'])) $_POST['addForum-country'] = ($this->objForum->sCountry != '' ? $this->objForum->sCountry : strtolower($this->MyUser->sCountry));
                    if (!isset($_POST['addForum-city'])) $_POST['addForum-city'] = ($this->objForum->sCity != '' ? $this->objForum->sCity : $this->MyUser->sCity);

                    $sFormAction = '/forum/'.$this->objForum->sURLName.'/'.$this->objForum->sID.'/edit-forum/#AddForum';
                    break;
                case 'add-forum'://adding a new forum by importing data from parent category
                    if (!isset($_POST['addForum-parentCategory'])) $_POST['addForum-parentCategory'] = $this->objSiteCategoryParent->sID;
                    if (!isset($_POST['addForum-parentCategoryName'])) $_POST['addForum-parentCategoryName'] = $this->objSiteCategoryParent->sName;
                    if (!isset($_POST['addForum-name'])) $_POST['addForum-name'] = '';
                    if (!isset($_POST['addForum-imageIcon'])) $_POST['addForum-imageIcon'] = $this->objSiteCategoryParent->sImage;
                    if (!isset($_POST['addForum-coverImage'])) $_POST['addForum-coverImage'] = $this->objSiteCategoryParent->sCoverImage;

                    if (!isset($_POST['addForum-country'])) $_POST['addForum-country'] = strtolower($this->MyUser->sCountry);
                    if (!isset($_POST['addForum-city'])) $_POST['addForum-city'] = $this->MyUser->sCity;

                    $sFormAction = '/category/'.$sFullURLLink.'/'.$this->objSiteCategoryParent->sID.'/add-forum/#AddForum';
                    $Category=null;
                    break;
                case 'delete-forum':
                    break;
            }
        }

        $this->data['sActionName'] = $sActionName;
        $this->data['sFormAction'] = $sFormAction;
        $this->data['dtCurrentForum']= $this->objForum;
        $this->data['dtSiteCategories']=$this->SiteCategories->findAll();
        $this->ContentContainer->addObject($this->renderModuleView('add_forum_view',$this->data, TRUE),'<div class="container">',2);
    }



    protected function uploadCategoryImageIcon($categoryName)
    {
        $sUploadedFileLocation = $this->uploadFileForm($categoryName,'addForum-imageUpload',"uploads/images/forums/icons",
            "jpg|jpeg|ico|gif|png",'g_msgAddForumError','Icon Image','forumIconImage_upload');

        return $sUploadedFileLocation;
    }

    protected function uploadCategoryCoverImage($categoryName)
    {
        $sUploadedFileLocation = $this->uploadFileForm($categoryName,'addForum-coverImageUpload',"uploads/images/forums/covers/",
            "jpg|jpeg|gif|png",'g_msgAddForumError','Cover Image','forumCoverImage_upload');

        return $sUploadedFileLocation;
    }

}