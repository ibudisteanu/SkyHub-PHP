<?php

require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Send_emails extends MY_AdvancedController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('site_main_categories/site_categories_model','SiteCategoriesModel');
        $this->load->model('users/users','UsersModel');

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
    }

    public function index($sActionName, $sID)
    {
        if (!TUserRole::checkUserRights(TUserRole::Admin))
        {
            show_404();
            return;
        } else
            $this->processPage($sActionName, $sID);
    }

    protected function sendEmails($Action='insert', $sID='')
    {
        $this->load->model('send_emails/query_trials_blocked_send_emails_too_many_times','QueryTrialsBlockedSendEmailsTooManyTimes');
        $this->load->library('../modules/admin/widgets/send_emails/libraries/send_emails_validator',$this->UsersModel,'Validator');

        if (!TUserRole::checkUserRights(TUserRole::Admin))
        {
            $this->AlertsContainer->addAlert('g_msgSendEmailsError','error','You are not registered');
            return false;
        }

        if (! $this->Validator->CheckPosts($this->SiteCategoriesModel))
        {
            $this->AlertsContainer->addAlert('g_msgSendEmailsError','error',$this->Validator->sError);
            return false;
        }

        if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsBlockedSendEmailsTooManyTimes))
        {
            $this->AlertsContainer->addAlert('g_msgSendEmailsError','error',$this->QueryTrials->sError);
            return false;
        }

        $name = $this->StringsAdvanced->processText($_POST['sendEmails-name'],'html|xss|whitespaces');

        $sURLName ='';
        if (isset($_POST['sendEmails-urlName'])) $sURLName = $_POST['sendEmails-urlName'];
        if ($sURLName == '') $sURLName = $name;

        $sURLName = $this->StringsAdvanced->processURLString($sURLName);

        $sSelectedUser = $this->StringsAdvanced->processText($_POST['sendEmails-selectedUser'],'html|xss|whitespaces');
        $sSelectedCategory = $this->StringsAdvanced->processText($_POST['sendEmails-selectedCategory'],'html|xss|whitespaces');
        $sActionTemplate = $this->StringsAdvanced->processText($_POST['sendEmails-actionTemplate'],'html|xss|whitespaces');
        $sTitleSubject = $this->StringsAdvanced->processText($_POST['sendEmails-titleSubject'],'html|xss|whitespaces');
        $sBody = $this->StringsAdvanced->processText($_FILES['sendEmails-body'],'xss|whitespaces');


        $MongoData=array(); $idParent=null; $newParentCategory=null; $currentCategoryForUpdate=null;
        if (($sSelectedCategory!=''))
        {
            $newParentCategory = $this->SiteCategoriesModel->exists($sSelectedCategory);
            if ($newParentCategory  == null)
            {
                $this->AlertsContainer->addAlert('g_msgSendEmailsError', 'error', 'Error adding <strong>' . $name . '</strong> because no parent found');
                return false;
            }
            if ($newParentCategory->sID != $sID)//Create a subcategory
                $idParent = new MongoId($newParentCategory->sID);
        }

        if ($sSelectedUser!='')
        {
            $newUser = $this->Users->exists($sSelectedUser);
            if ($newUser == null)
            {
                $this->AlertsContainer->addAlert('g_msgSendEmailsError', 'error', 'Error adding <strong>' . $name . '</strong> because no user found');
                return false;
            }
            if ($newUser->sID != $sID)//Create a subcategory
                $idParent = new MongoId($newParentCategory->sID);
        }

        if ($Action=='update')
            $currentCategoryForUpdate = $this->SiteCategoriesModel->exists($sID);

        //echo $currentCategoryForUpdate->sName."<br/><br/>";

        $MongoData=array_merge($MongoData, array(
            "Name"=>$name,
            "URLName"=>$sURLName,
            "FullURLLink"=>$sFullURLLink,
            "Image"=>$sImage,
            "Description"=>$description,
            "NoForums"=>0,
            "NoTopics"=>0,
            "NoComments"=>0,
            "Parent"=>$idParent,
            "NoUsers"=>0,
            "CoverImage"=>$sCoverImage,
            "Importance"=>$iImportanceValue
        ));

        if ($bHideNameIconImage)
            $MongoData=array_merge($MongoData,array("HideNameIconImage"=>true));

        if ($currentCategoryForUpdate == null)
            $MongoData=array_merge($MongoData,array("Children"=>array()));

        //print_r($MongoData);

        $bSuccess=false;
        if ($currentCategoryForUpdate != null)
        {
            $findDocumentQuery = array ("_id"=>new MongoId($sID));
            if ($this->SiteCategoriesModel->update($findDocumentQuery ,$MongoData))
                $bSuccess=true;

            //echo 'Parent '.$newParentCategory->sID;
            //echo 'Current Category'.$currentCategoryForUpdate->Parent;
            if (($newParentCategory!=null)&&($newParentCategory->sID != $currentCategoryForUpdate->Parent))//Parent Changed in the last time
            {
                //echo'diferit';
                $newCategoryData = array(
                    "_id"=>new MongoId($sID),
                    "Name"=>$name,
                    "URLName"=>$sURLName,);

                //I have to delete from the previous category

                $this->SiteCategoriesModel->Update(array("_id"=>new MongoId($currentCategoryForUpdate->Parent)),array("Children"=>array("_id"=>new MongoId($sID))),'$pull');
                //add this new children
                if ($this->SiteCategoriesModel->insertDataInside(array("_id" => new MongoId($newParentCategory->sID)), "Children", $newCategoryData, $Action))
                    $bSuccess = true;
            }

        } else
        {
            $newCategoryId = $this->SiteCategoriesModel->insertData($MongoData);
            $newCategoryData = array(
                "_id"=>new MongoId($newCategoryId),
                "Name"=>$name,
                "URLName"=>$sURLName,);

            if ($newParentCategory!=null)
            {

                if ($this->SiteCategoriesModel->insertDataInside(array("_id" => new MongoId($newParentCategory->sID)), "Children", $newCategoryData, $Action))
                    $bSuccess = true;
            } else
                $bSuccess=true;
        }

        if ($bSuccess==true)
        {
            if ($newParentCategory != null) $this->AlertsContainer->addAlert('g_msgSendEmailsSuccess','success','Sub Category <strong>'.$name.'</strong> from <strong>'.$newParentCategory->sName.'</strong> has been added successfully');
            else $this->AlertsContainer->addAlert('g_msgSendEmailsSuccess','success','Category <strong>'.$name.'</strong> has been added successfully');
        }


        return true;

    }

    protected function processPage($sActionName, $sID)
    {
        $sFormAction = '/admin/site/emails/#SendEmail';
        if ($sActionName=='') $sActionName = 'send-email';

        if (($_POST)&&isset($_POST["val"]) && ($_POST["val"] == 'send_email'))
            $this->sendEmails('insert');

        $Category = null; $User=null;
        if (($sActionName!= '')&&($sID!=''))
        {

            switch ($sActionName)
            {
                case 'send-email':
                    break;
                case 'send-email-user':
                    $User = $this->UsersModel->exists($sID);
                    if ($User==null)
                    {
                        $this->AlertsContainer->addAlert('g_msgSendEmailsError','error','User not found: <strong>'.$sID.'</strong> ');
                        break;
                    }
                    $_POST['sendEmails-selectedUser'] = $User->sID;
                    $_POST['sendEmails-selectedCategory'] = '';
                    $_POST['sendEmails-name'] = $Category->sName.' => ';
                    $sFormAction = '/admin/site/emails/send-email/#SendEmail';
                    $Category=null;
                    break;
                case 'send-email-category':
                    $Category = $this->SiteCategoriesModel->exists($sID);
                    if ($Category==null)
                    {
                        $this->AlertsContainer->addAlert('g_msgSendEmailsError','error','Category not found: <strong>'.$sID.'</strong> ');
                        break;
                    }
                    $_POST['sendEmails-selectedUser'] = '';
                    $_POST['sendEmails-selectedCategory'] = $Category->sID;
                    $_POST['sendEmails-name'] = $Category->sName.' => ';
                    $sFormAction = '/admin/site/emails/send-email/#SendEmail';
                    $Category=null;
                    break;
            }
        }
        $this->data['sActionName'] = $sActionName;
        $this->data['sFormAction'] = $sFormAction;
        $this->data['dtSiteCurrentCategory']= $Category;
        $this->data['dtSiteUsers']=$this->UsersModel->findAll();
        $this->data['dtSiteCategories']=$this->SiteCategoriesModel->findAll();
        $this->ContentContainer->addObject($this->renderModuleView('send_emails_view',$this->data, TRUE),'<section class="col-lg-7 connectedSortable ui-sortable">');

    }



}