<?php

class View_profile extends MY_Controller
{
    public $User = null;

    function __construct()
    {
        parent::__construct();

        $this->load->model('users/users_minimal', 'Users');
    }

    public function index($sUserName='', $sID='')
    {
        if ($sID != '')
        {
            $this->User = $this->Users->userByMongoId($sID);

            if ($sUserName != $this->User->sUserName)
                $this->User=null;
        } else
        {

            if (($sUserName == '')&&($this->MyUser->bLogged))
                $this->User = $this->MyUser;
            else
                $this->User = $this->Users->userByUsername($sUserName);
        }



        $this->renderProfile($this->User);
    }

    protected function renderProfile($UserObject)
    {

        if ($this->User != null)
        {
            $this->Template->loadMeta($UserObject->getFullName(),$UserObject->sBiography, $UserObject->sAvatarPicture, base_url('profile/'.ltrim($UserObject->getUserLink(),'profile/')));
            $this->Template->renderHeader('home');

            modules::load('fluid_header/profile_header')->index($UserObject);

            $this->renderProfileBreadcrumbView($UserObject);

            $this->Template->renderContainer();
            $this->Template->renderFooter();
        } else
        {
            $this->showErrorPage('Profile not found','error','profile','Profile not found');
        }
    }

    protected function renderProfileBreadcrumbView($Profile)
    {
        $arrData = $Profile->getBreadCrumbArray();

        $arrData[1]['url'] = str_replace("profile/profile","profile",$arrData[1]['url']);

        modules::load('breadcrumb/breadcrumb')->addBreadcrumbObject($arrData,2);
    }


}