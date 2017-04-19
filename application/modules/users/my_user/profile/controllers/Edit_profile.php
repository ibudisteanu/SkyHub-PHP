<?php

require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Edit_profile extends MY_AdvancedController
{
    function __construct()
    {
        parent::__construct();

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
        $this->Template = modules::load('pages/Template');
        $this->load->model('users/Users','UsersModel');

        $this->includeWebpageLibraries('file-style');
    }

    public function index($sUserToEdit='')
    {
        $user = null;
        if (!$this->MyUser->bLogged)
        {
            $sUserToEdit='';
            show_404();
            return false;
        } else
        {//logged in

            //Super admin has rights to change other people accounts
            if (TUserRole::checkUserRights(TUserRole::SuperAdmin)&&($sUserToEdit!=''))
                $user  = $this->UsersModel->userByMongoId($sUserToEdit);

            if (($sUserToEdit=='')||($sUserToEdit==$this->MyUser->sID))
                $user = $this->MyUser;
        }

        $this->processLoginForm($user);
        return true;
    }

    protected function editProfile($user)
    {
        $this->load->model('profile/query_trials_blocked_edit_profile_too_many','QueryTrialsTooMany');
        $this->load->library('../modules/users/my_user/profile/libraries/Edit_profile_validator',$this->UsersModel,'EditProfileValidator');

        if ($user==null)
        {
            //$this->QueryTrials->removeOldAttempts();
            $this->AlertsContainer->addAlert('g_msgEditProfileError','error','You are not registered');
            return false;
        }

        if (! $this->EditProfileValidator->CheckPosts())
        {
            $this->AlertsContainer->addAlert('g_msgEditProfileError','error',$this->EditProfileValidator->sError);
            return false;
        }

        if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsTooMany))
        {
            $this->AlertsContainer->addAlert('g_msgEditProfileError','error',$this->QueryTrials->sError);
            return false;
        }

        if (!$user->checkOwnership())
        {
            $this->QueryTrials->addAttempt();
            $this->AlertsContainer->addAlert('g_msgEditProfileError','error','You don\'t have rights to edit this profile');
            return false;
        }

        $bio = $this->StringsAdvanced->processText($_POST['editProfile-bio'], 'html|xss|whitespaces');
        $firstName = $this->StringsAdvanced->processText($_POST['editProfile-firstName'], 'html|xss|whitespaces');
        $lastName = $this->StringsAdvanced->processText($_POST['editProfile-lastName'], 'html|xss|whitespaces');
        $company = $this->StringsAdvanced->processText($_POST['editProfile-company'], 'html|xss|whitespaces');
        $webSite = $this->StringsAdvanced->processText($_POST['editProfile-webSite'], 'html|xss|whitespaces');
        $timeZone = $this->StringsAdvanced->processText($_POST['editProfile-timeZone'], 'html|xss|whitespaces');
        $sCity = $this->StringsAdvanced->processText($_POST['editProfile-city'], 'html|xss|whitespaces');
        $sCountry = $this->StringsAdvanced->processText($_POST['editProfile-country'], 'html|xss|whitespaces');

        if (($firstName!='')&&($user->sFirstName != $firstName)) $user->sFirstName = $firstName;
        if (($lastName != '')&&($user->sLastName != $lastName)) $user->sLastName = $lastName;
        if (($company != '')&&($user->sCompany != $company)) $user->sCompany = $company;
        if (($webSite != '') && ($user->sWebsite != $webSite)) $user->sWebsite = $webSite;
        if (($bio != '') && ($user->sBiography != $bio)) $user->sBiography = $bio;
        if (($timeZone != '') && ($user->sTimeZone != $timeZone)) $user->sTimeZone = $timeZone;
        if (($sCity != '') && ($user->sCity != $sCity)) $user->sCity = $sCity;
        if (($sCountry != '') && ($user->sCountry != $sCountry)) $user->sCountry = $sCountry;

        //$user->sURLName = $user->sUsername;
        //$user->sFullURLLink = "profile/".$user->sUsername;
        //$user->sFullURLDomains = "page/profile";
        $user->sFullURLName = "profile/".$user->sFirstName.' '.$user->sLastName;

        if (($firstName != '') || ($lastName!='') || ($company !='') || ($webSite  != '') || ($bio != '') || ($timeZone != '') || ($sCity != '') || ($sCountry != ''))
        {
            $user->storeUpdate();
        }

        $this->QueryTrials->addAttempt();
        return true;
    }

    protected function editPassword($user)
    {
        $this->load->model('profile/query_trials_blocked_change_password_too_many','QueryTrialsTooMany');
        $this->load->library('../modules/users/my_user/profile/libraries/Change_password_validator',$this->UsersModel,'ChangePasswordValidator');

        if ($user==null)
        {
            //$this->QueryTrials->removeOldAttempts();
            $this->AlertsContainer->addAlert('g_msgChangePasswordError','error','You are not registered');
            return false;
        }

        if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsTooMany))
        {
            $this->AlertsContainer->addAlert('g_msgChangePasswordError','error',$this->QueryTrials->sError);
            return false;
        }

        if (!$user->checkOwnership())
        {
            $this->QueryTrials->addAttempt();
            $this->AlertsContainer->addAlert('g_msgEditProfileError','error','You don\'t have rights to edit this profile');
            return false;
        }

        if (! $this->ChangePasswordValidator->CheckPosts($user))
        {
            $this->AlertsContainer->addAlert('g_msgChangePasswordError','error',$this->ChangePasswordValidator->sError);
            return false;
        }

        $password=password_hash($this->StringsAdvanced->processText($_POST['changePassword-password'], 'html|xss|whitespaces'), PASSWORD_DEFAULT, ['cost' => 12]);

        $newEmail = $this->StringsAdvanced->processText($_POST['changePassword-newEmail'], 'html|xss|whitespaces');
        $newPassword = $this->StringsAdvanced->processText($_POST['changePassword-newPassword'], 'html|xss|whitespaces');
        $retypeNewPassword = $this->StringsAdvanced->processText($_POST['changePassword-retypeNewPassword'], 'html|xss|whitespaces');

        if ($this->MyUser->checkLoginWithPasswordSilent($user->sUserName,$password))
        {

            $sMessage='';
            if (($newEmail != '')&&($newEmail != $user->sEmail))
            {
                $user->sEmail = $newEmail;

                $this->QueryTrials->addAttempt();
                $user->storeUpdate();
                $sMessage .= 'Your email address has been changed successfully <br/>';
            }

            if (($newPassword==$retypeNewPassword) &&(strlen($newPassword)>0))
            {
                $user->setNewPassword($newPassword);
                $this->QueryTrials->addAttempt();
                $user->storeUpdate();

                $sMessage .= 'You password has been changed successfully <br/>';
            }

            if ($sMessage != '')
            {
                $this->StringsAdvanced->removeLastBRTag($sMessage);
                $this->AlertsContainer->addAlert('g_msgChangePasswordSuccess','success',$sMessage);
                return true;
            }

            $this->AlertsContainer->addAlert('g_msgChangePasswordError','error','Nothing to change');
            return false;

        } else
        {
            $this->AlertsContainer->addAlert('g_msgChangePasswordError','error','Incorrect password');
            return false;
        }

    }

    public function avatarUpload($user)
    {
        $this->load->model('profile/query_trials_blocked_edit_profile_too_many','QueryTrialsTooMany');

        if ($user==null)
        {
            //$this->QueryTrials->removeOldAttempts();
            $this->AlertsContainer->addAlert('g_msgChangePasswordError','error','You are not registered');
            return false;
        }

        if (!$this->QueryTrials->checkIPAddress($this->QueryTrialsTooMany))
        {
            $this->AlertsContainer->addAlert('g_msgChangePasswordError','error',$this->QueryTrials->sError);
            return false;
        }

        if (!$user->checkOwnership())
        {
            $this->QueryTrials->addAttempt();
            $this->AlertsContainer->addAlert('g_msgEditProfileError','error','You don\'t have rights to edit this profile');
            return false;
        }

        $sUploadedFileLocation = $this->uploadFileForm($user->sID,'avatarUpload-AvatarImageFile',"uploads/images/avatars/",
                                    "jpg|jpeg|gif|png",'g_msgAvatarUploadError','Avatar Profile Image','avatar_upload',false);

        if ($sUploadedFileLocation != FALSE)
        {
            try{
                $this->avatarImageResize($sUploadedFileLocation);
                $user->sAvatarPicture = base_url($sUploadedFileLocation);
            }
            catch (Exception $exception)
            {
                $this->AlertsContainer->addAlert('g_msgAvatarUploadError','error',$exception->getMessage());
            }

            $this->QueryTrials->addAttempt();
            $user->storeUpdate();

            return true;

            //$data['img'] = base_url('images/'.$file_data['file_name']);
        }
    }

    protected function processLoginForm($user)
    {
        $this->includeWebPageLibraries('country-select');

        if ($user==null)
        {
            show_404();
            return;
        }

        if (($_POST)&&isset($_POST["val"]) && ($_POST["val"] == 'change_password'))
        {
            if ($this->editPassword($user)) ;
                //$this->AlertsContainer->addAlert('g_msgChangePasswordSuccess','success','Your data has been updated');
        } else
        if (($_POST)&&isset($_POST["val"]) && ($_POST["val"] == 'edit_profile'))
        {
            if ($this->editProfile($user))
                $this->AlertsContainer->addAlert('g_msgEditProfileSuccess','success','Your profile has been updated');
        } else
        if (($_POST)&&isset($_POST["val"]) && ($_POST["val"] == 'upload_avatar'))
        {
            if ($this->avatarUpload($user))
                $this->AlertsContainer->addAlert('g_msgAvatarUploadSuccess','success','Your avatar has been updated');
        }

        $this->renderEditProfile($user);

    }

    public function renderEditProfile($user)
    {
        $this->load->vars(array('g_User' =>$user));
        $this->Template->loadMeta(WEBSITE_TITLE);
        $this->Template->renderHeader('home');

        $this->ContentContainer->addObject($this->renderModuleView('edit_profile',null,true));

        $this->Template->renderContainer();

        $this->Template->renderFooter();
    }

}