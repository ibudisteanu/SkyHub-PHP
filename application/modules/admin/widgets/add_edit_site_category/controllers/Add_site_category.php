<?php

require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Add_site_category extends MY_AdvancedController
{
    public $objSiteCategory;
    public $objParentSiteCategory;

    function __construct()
    {
        parent::__construct();
        $this->load->model('users/users_minimal','UsersMinimal');
        $this->load->model('site_main_categories/site_categories_model','SiteCategoriesModel');

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
    }

    public function index($sActionName, $objParentSiteCategory=null, $objSiteCategory=null)
    {
        if (isset($_POST['category_id'])) $objSiteCategory = $_POST['category_id'];

        if (($objSiteCategory!=null)&&(!is_string($objSiteCategory))&&(get_class($objSiteCategory) == 'Site_category_model')) $this->objSiteCategory = $objSiteCategory;
        else
            if (($objSiteCategory != '')&&(is_string($objSiteCategory)))  $this->objSiteCategory = $this->SiteCategoriesModel->findCategory($objSiteCategory);


        if (($objParentSiteCategory!=null)&&(!is_string($objParentSiteCategory))&&(get_class($objParentSiteCategory) == 'Site_category_model')) $this->objParentSiteCategory = $objParentSiteCategory;
        else
            if (($objParentSiteCategory != '')&&(is_string($objParentSiteCategory)))
                $this->objParentSiteCategory = $this->SiteCategoriesModel->findCategory($objParentSiteCategory);

        if (($this->objSiteCategory != null) && ($this->objSiteCategory->sParentId != ''))
            if (($this->objParentSiteCategory == null) || ($this->objParentSiteCategory->sID != $this->objSiteCategory->sParentId))
                $this->objParentSiteCategory = $this->SiteCategoriesModel->findCategory($this->objSiteCategory->sParentId);

        if (!TUserRole::checkUserRights(TUserRole::Admin))
        {
            $this->showErrorPage('You don\'t have privileges to access this page','error','admin');
            return;
        } else
            $this->processPage($sActionName);
    }

    protected function addSiteCategory($Action='insert')
    {
        $this->load->model('add_edit_site_category/query_trials_blocked_add_site_category_too_many_times','QueryTrialsBlockedAddSiteCategoryTooManyTimes');
        $this->load->library('../modules/admin/widgets/add_edit_site_category/libraries/add_site_category_validator',$this->UsersMinimal,'Validator');

        if (!TUserRole::checkUserRights(TUserRole::Admin))
        {
            $this->AlertsContainer->addAlert('g_msgAddSiteCategoryError','error','You are not registered');
            return false;
        }

        if (! $this->Validator->CheckPosts($this->SiteCategoriesModel))
        {
            $this->AlertsContainer->addAlert('g_msgAddSiteCategoryError','error',$this->Validator->sError);
            return false;
        }

        if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsBlockedAddSiteCategoryTooManyTimes))
        {
            $this->AlertsContainer->addAlert('g_msgAddSiteCategoryError','error',$this->QueryTrials->sError);
            return false;
        }


        $sName = $this->StringsAdvanced->processText($_POST['addSiteCategory-name'], 'html|xss|whitespaces');

        $sURLName = '';
        if (isset($_POST['addSiteCategory-urlName'])) $sURLName = $_POST['addSiteCategory-urlName'];
        if ($sURLName == '') $sURLName = $sName;

        $sURLName = $this->StringsAdvanced->processURLString($sURLName);

        $sParentCategoryId = $this->StringsAdvanced->processText($_POST['addSiteCategory-parentCategory'], 'html|xss|whitespaces');
        $fImportanceValue = (float)$this->StringsAdvanced->processText($_POST['addSiteCategory-importance'], 'html|xss|whitespaces');
        $imageIcon = $this->StringsAdvanced->processText($_POST['addSiteCategory-imageIcon'], 'html|xss|whitespaces');
        $imageUpload = $this->StringsAdvanced->processText($_FILES['addSiteCategory-imageUpload']['name'], 'html|xss|whitespaces');
        $coverImage = $this->StringsAdvanced->processText($_POST['addSiteCategory-coverImage'], 'html|xss|whitespaces');
        $coverImageUpload = $this->StringsAdvanced->processText($_FILES['addSiteCategory-coverImageUpload']['name'], 'html|xss|whitespaces');
        $sDescription = $this->StringsAdvanced->processText($_POST['addSiteCategory-description'], 'xss|whitespaces');
        $sShortDescription = $this->StringsAdvanced->processText($_POST['addSiteCategory-shortDescription'], 'xss|whitespaces');
        $sInputKeywords = $this->StringsAdvanced->processText($_POST['addSiteCategory-inputKeywords']);
        $vInputKeywords = $this->Validator->checkValidKeywords($sInputKeywords);

        if ((isset($_POST['addSiteCategory-hideNameIconImage']))&&($_POST['addSiteCategory-hideNameIconImage']=='checked')) $bHideNameIconImage = true;
        else $bHideNameIconImage  = false;


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
            $this->AlertsContainer->addAlert('g_msgAddSiteCategoryError','error','Error adding <strong>'.$sName.'</strong> because no pictures selected/uploaded');
            return false;
        }

        if (($this->objSiteCategory != null) && ($this->objSiteCategory->sID == $sParentCategoryId)) {
            $this->objParentSiteCategory = null;
            $sParentCategoryId = '';
        }

        if ( (($this->objParentSiteCategory == null) && ($sParentCategoryId != ''))||
             (($this->objParentSiteCategory != null) && ($this->objParentSiteCategory->sID != $sParentCategoryId))) {
            $this->objParentSiteCategory = $this->SiteCategoriesModel->findCategory($sParentCategoryId);
            if ($this->objParentSiteCategory == null)
            {
                $this->AlertsContainer->addAlert('g_msgAddSiteCategoryError', 'error', 'Error adding <strong>' . $sName . '</strong> because no parent found '.$sParentCategoryId);
                return false;
            }
        }

        $sFullURLName = ''; $sFullURLLink =''; $sFullURLDomains  ='';

        if (($this->objParentSiteCategory != null))
        {
            $sFullURLName=rtrim($this->objParentSiteCategory->sFullURLName,'/').'/';
            $sFullURLLink=rtrim($this->objParentSiteCategory->sFullURLLink,'/').'/';
            $sFullURLDomains = rtrim($this->objParentSiteCategory->sFullURLDomains,'/').'/';
            //echo 'New Parent Category: '.$newParentCategory->sID.'   '.$newParentCategory->sName.'   <br/>';
        }
        $sFullURLName.=$sName;
        $sFullURLLink.=$sURLName;
        $sFullURLDomains  .= 'category';

        //echo $currentCategoryForUpdate->sName."<br/><br/>";

        switch ($Action)
        {
            case 'add-category':
            case 'add-subcategory':
                $this->objSiteCategory = new Site_category_model(true);
                $this->objSiteCategory->sURLName = $sURLName;
                $this->objSiteCategory->sFullURLName = $sFullURLName;
                $this->objSiteCategory->sFullURLLink = $sFullURLLink;
                $this->objSiteCategory->sFullURLDomains = $sFullURLDomains;
                $this->objSiteCategory->sAuthorId=$this->MyUser->sID;
                $this->objSiteCategory->sName = $sName;
                $this->objSiteCategory->sDescription = $sDescription;
                $this->objSiteCategory->sShortDescription = $sShortDescription;
                $this->objSiteCategory->sImage = $sImage;
                $this->objSiteCategory->sCoverImage = $sCoverImage;
                $this->objSiteCategory->iNoComments = 0;
                $this->objSiteCategory->iNoForums = 0;
                $this->objSiteCategory->iNoUsers = 0;
                $this->objSiteCategory->iNoTopics = 0;
                $this->objSiteCategory->bHideNameIconImage = $bHideNameIconImage;

                if ($this->objParentSiteCategory != null)
                    $this->objSiteCategory->sParentId = $this->objParentSiteCategory->sID;;

                $this->objSiteCategory->arrInputKeywords = $vInputKeywords;

                if ($fImportanceValue != -666)
                    $this->objSiteCategory->fImportance = $fImportanceValue;

                if ($this->objSiteCategory->storeUpdate($this->objParentSiteCategory)) {

                    if ($this->objParentSiteCategory != null) $this->AlertsContainer->addAlert('g_msgAddSiteCategorySuccess','success','Sub Category <strong>'.$sName.'</strong> from <strong>'.$this->objParentSiteCategory->sName.'</strong> has been added successfully');
                    else $this->AlertsContainer->addAlert('g_msgAddSiteCategorySuccess','success','Category <strong>'.$sName.'</strong> has been added successfully');

                    redirect($this->objSiteCategory->getFullURL(), 'refresh');

                    return true;
                }
                return false;
                break;
            case 'edit-category':
                if (($this->objSiteCategory->sName != $sName) || ($this->objSiteCategory->sDescription != $sDescription)||
                    ($this->objSiteCategory->sImage != $sImage)||($this->objSiteCategory->sCoverImage != $sCoverImage)||
                    ($this->objSiteCategory->sShortDescription != $sShortDescription)) {
                    $this->objSiteCategory->sName = $sName;
                    $this->objSiteCategory->sDescription = $sDescription;
                    $this->objSiteCategory->sShortDescription = $sShortDescription;
                    $this->objSiteCategory->sImage = $sImage;
                    $this->objSiteCategory->sCoverImage = $sCoverImage;
                    $this->objSiteCategory->bLastChanged = true;
                }
                if ($sURLName != '') $this->objSiteCategory->sURLName = $sURLName;
                if ($sFullURLName != '') $this->objSiteCategory->sFullURLName = $sFullURLName;
                if ($sFullURLLink != '') $this->objSiteCategory->sFullURLLink = $sFullURLLink;
                if ($sFullURLDomains != '') $this->objSiteCategory->sFullURLDomains = $sFullURLDomains;
                $this->objSiteCategory->bHideNameIconImage = $bHideNameIconImage;

                if ($this->objParentSiteCategory != null)
                    $this->objSiteCategory->sParentId = $this->objParentSiteCategory->sID;
                else
                    $this->objSiteCategory->sParentId  = null;

                $this->objSiteCategory->arrInputKeywords = $vInputKeywords;

                if ($fImportanceValue != -666)
                    $this->objSiteCategory->fImportance = $fImportanceValue;

                if ($this->objSiteCategory->storeUpdate($this->objParentSiteCategory))
                {
                    $sLink = '<br/>View <a href="'.$this->objSiteCategory->getFullURL().'"><strong>'.$this->objSiteCategory->sName.'</strong> site category </a>';
                    if ($this->objParentSiteCategory != null) $this->AlertsContainer->addAlert('g_msgAddSiteCategorySuccess','success','Sub Category <strong>'.$sName.'</strong> from <strong>'.$this->objParentSiteCategory->sName.'</strong> has been edited successfully'.$sLink);
                    else $this->AlertsContainer->addAlert('g_msgAddSiteCategorySuccess','success','Category <strong>'.$sName.'</strong> has been edited successfully'.$sLink);

                    return true;
                }
                return false;
            case 'delete-category':
                $result = $this->objSiteCategory->delete($this->objParentSiteCategory);

                if ($result['result'])
                    $this->AlertsContainer->addAlert('g_msgAddSiteCategorySuccess', 'success', 'Category <strong>' . $sName . '</strong> has been removed successfully');
                else
                    $this->AlertsContainer->addAlert('g_msgAddSiteCategoryError', 'error', 'Category <strong>' . $sName . "</strong> couldn't be removed ".$result['message']);

                if ($this->objParentSiteCategory != null) redirect($this->objParentSiteCategory->getFullURL(), 'refresh');
                else redirect('', 'refresh');

                return true;
            default:
                $this->AlertsContainer->addAlert('g_msgAddForumCategoryError','error','The request was rejected: <strong>'.$this->URI->sFormId.'</strong> ');
                return false;
        }

        return false;
    }

    protected function processPage($sActionName)
    {
        $sFormAction = '/admin/site/categories/#AddSiteCategory';
        if ($sActionName=='') $sActionName = 'add-category';

        if (($_POST)&&isset($_POST["deleteSiteCategory"]))  $sActionName = 'delete-category';
        if (($_POST)&&isset($_POST["editSiteCategory"]))  $sActionName = 'edit-category';

        if (($_POST)&&((isset($_POST["addSiteCategory"]))||(isset($_POST["deleteSiteCategory"]))||(isset($_POST["editSiteCategory"]))) ) $this->addSiteCategory($sActionName);

        if ($sActionName!= '')
        {
            switch ($sActionName)
            {
                case 'add-category':
                    $sFormAction = '/admin/site/categories/add-category/#AddSiteCategory';
                    break;

                case 'add-subcategory':
                    if ($this->objParentSiteCategory==null)
                    {
                        $this->AlertsContainer->addAlert('g_msgAddSiteCategoryError','error','Parent category not found: <strong>'.' '.'</strong> ');
                        break;
                    }
                    if (!isset($_POST['addSiteCategory-parentCategory'])) $_POST['addSiteCategory-parentCategory'] = $this->objParentSiteCategory->sID;
                    if (!isset($_POST['addSiteCategory-name'])) $_POST['addSiteCategory-name'] = $this->objParentSiteCategory->sName.' => ';
                    if (!isset($_POST['addSiteCategory-imageIcon'])) $_POST['addSiteCategory-imageIcon'] = $this->objParentSiteCategory->sImage;
                    if (!isset($_POST['addSiteCategory-coverImage'])) $_POST['addSiteCategory-coverImage'] = $this->objParentSiteCategory->sCoverImage;
                    if (!isset($_POST['addSiteCategory-description'])) $_POST['addSiteCategory-description'] = $this->objParentSiteCategory->sDescription;
                    if (!isset($_POST['addSiteCategory-shortDescription'])) $_POST['addSiteCategory-shortDescription'] = $this->objParentSiteCategory->sShortDescription;

                    $sFormAction = '/admin/site/categories/add-category/'.$this->objParentSiteCategory->sID.'/#AddSiteCategory';
                    $this->objSiteCategory=null;
                    break;
                case 'edit-category':
                    if ($this->objSiteCategory==null)
                    {
                        $this->AlertsContainer->addAlert('g_msgAddSiteCategoryError','error','Category not found: <strong>'.''.'</strong> ');
                        break;
                    }

                    if (!isset($_POST['addSiteCategory-parentCategory'])) $_POST['addSiteCategory-parentCategory'] = $this->objSiteCategory->sParentId;
                    if (!isset($_POST['addSiteCategory-name'])) $_POST['addSiteCategory-name'] = $this->objSiteCategory->sName;
                    if (!isset($_POST['addSiteCategory-urlName'])) $_POST['addSiteCategory-urlName'] = $this->objSiteCategory->sURLName;
                    if (!isset($_POST['addSiteCategory-imageIcon'])) $_POST['addSiteCategory-imageIcon'] = $this->objSiteCategory->sImage;
                    if (!isset($_POST['addSiteCategory-description'])) $_POST['addSiteCategory-description'] = $this->objSiteCategory->sDescription;
                    if (!isset($_POST['addSiteCategory-shortDescription'])) $_POST['addSiteCategory-shortDescription'] = $this->objSiteCategory->sShortDescription;
                    if (!isset($_POST['addSiteCategory-coverImage'])) $_POST['addSiteCategory-coverImage'] = $this->objSiteCategory->sCoverImage;
                    if (!isset($_POST['addSiteCategory-importance'])) $_POST['addSiteCategory-importance'] = $this->objSiteCategory->fImportance;
                    if (!isset($_POST['addSiteCategory-inputKeywords'])) $_POST['addSiteCategory-inputKeywords'] = $this->objSiteCategory->getInputKeywordsToString();
                    if (!isset($_POST['addSiteCategory-hideNameIconImage']))
                        if ($this->objSiteCategory->bHideNameIconImage==true) $_POST['addSiteCategory-hideNameIconImage']='checked';
                        else $_POST['addSiteCategory-hideNameIconImage'] = '';

                    $sFormAction = '/admin/site/categories/edit-category/'.$this->objSiteCategory->sID;
                    break;
                case 'delete-category':

                    if ($this->objSiteCategory==null)
                    {
                        $this->AlertsContainer->addAlert('g_msgAddSiteCategoryError','error','Category not found for deleting: <strong></strong> ');
                        break;
                    }

                    $newParentCategory = $this->SiteCategoriesModel->exists($this->objSiteCategory->sParentId);
                    $this->SiteCategoriesModel->Update(array("_id"=>new MongoId($newParentCategory->sID)),array("Children"=>array("_id"=>new MongoId($this->objSiteCategory->sID))),'$pull');
                    $this->SiteCategoriesModel->deleteById($this->objSiteCategory->sID);
                    if ($newParentCategory != null)
                        redirect($newParentCategory->getFullURL(), 'refresh');

                    break;
            }
        }
        $this->data['sActionName'] = $sActionName;
        $this->data['sFormAction'] = $sFormAction;
        $this->data['dtSiteCurrentCategory']= $this->objSiteCategory;
        $this->data['dtSiteCategories']=$this->SiteCategoriesModel->findAll();
        $this->ContentContainer->addObject($this->renderModuleView('add_site_category_view',$this->data, TRUE),'<section class="col-lg-7 connectedSortable ui-sortable">');

    }

    protected function uploadCategoryImageIcon($categoryName)
    {
        $sUploadedFileLocation = $this->uploadFileForm($categoryName,'addSiteCategory-imageUpload',"uploads/images/categories/icons",
            "jpg|jpeg|ico|gif|png",'g_msgAddSiteCategoryError','Icon Image','categoryIconImage_upload');

        return $sUploadedFileLocation;
    }

    protected function uploadCategoryCoverImage($categoryName)
    {
        $sUploadedFileLocation = $this->uploadFileForm($categoryName,'addSiteCategory-coverImageUpload',"uploads/images/categories/covers/",
            "jpg|jpeg|gif|png",'g_msgAddSiteCategoryError','Cover Image','categoryCover_upload');

        return $sUploadedFileLocation;
    }

}