<?php

require_once APPPATH.'modules/widgets/widgets_graphical/toolbox/controllers/Tool_box.php';

class Header_tool_box extends Tool_box
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
    }

    public function createMainMenu()
    {
        if (TUserRole::checkUserRights(TUserRole::Admin))
        {
            $this->ToolBoxContainer->addElement('header','header-add-subcategory','category','fa fa-plus',base_url('admin/site/categories/add-category'));
        }
    }

    public function createSiteCategoryMenu($CategoryObject)
    {
        if (TUserRole::checkUserRights(TUserRole::Admin))
        {
            $this->ToolBoxContainer->addElement('header','header-edit','edit','fa fa-edit',base_url('admin/site/categories/edit-category/'.$CategoryObject->sID));
            $this->ToolBoxContainer->addElement('header','header-add-subcategory','subcategory','fa fa-plus',base_url('admin/site/categories/add-subcategory/'.$CategoryObject->sID));
            $this->ToolBoxContainer->addElement('header','header-delete','delete','fa fa-remove',base_url('admin/site/categories/delete-category/'.$CategoryObject->sID));
        }

        if (TUserRole::checkUserRights(TUserRole::User))
            $this->ToolBoxContainer->addElement('header','header-add-forum','forum','fa fa-plus',$CategoryObject->getFullURL().'/add-forum/#AddForum','secondary');

        $this->ToolBoxContainer->addElement('header','header-add-forum','topic','fa fa-plus',$CategoryObject->getFullURL().'/add-topic/#AddTopic','primary');

    }

    public function createSiteSubCategoryMenu($CategoryObject)
    {
        $this->createSiteCategoryMenu($CategoryObject);
    }

    public function createProfileMenu($UserObject)
    {
        if(((TUserRole::checkCompatibility($UserObject,TUserRole::User)&&($UserObject->sID==$this->MyUser->sID)))||
            (TUserRole::checkUserRights(TUserRole::SuperAdmin)))
        {
            if (TUserRole::checkUserRights(TUserRole::SuperAdmin))
                $sEditProfileURL = base_url('profile/edit/'.$UserObject->sID);
            else
                $sEditProfileURL = base_url('profile/edit');

            $this->ToolBoxContainer->addElement('header','header-edit','edit','fa fa-edit',$sEditProfileURL, 'primary');
            $this->ToolBoxContainer->addElement('header','header-upload-avatar','upload avatar','glyphicon glyphicon-user',$sEditProfileURL );
            $this->ToolBoxContainer->addElement('header','header-change-cover','change cover','glyphicon glyphicon-camera',$sEditProfileURL );
        }
    }

    public function createForumMenu($Forum)
    {
        if ($Forum->checkOwnership())
        {
            $this->ToolBoxContainer->addElement('header', 'header-edit', 'edit', 'fa fa-edit', $Forum->getFullURL() . '/edit-forum/#AddForum');
            $this->ToolBoxContainer->addElement('header', 'header-create-forum-category', 'add forum category', 'fa fa-plus', $Forum->getFullURL() . '/add-forum-category/#AddForumCategory','secondary');
        }

        $this->ToolBoxContainer->addElement('header','header-create-topic','create topic','glyphicon glyphicon-plus',$Forum->getFullURL() . '/add-topic/#AddTopic','primary');
    }

    public function createForumCategoryMenu($forumCategory)
    {
        if ($forumCategory->checkOwnership())
        {
            $this->ToolBoxContainer->addElement('header', 'header-edit', 'edit forum category', 'fa fa-edit', $forumCategory->getFullURL() . '/edit-forum-category/#AddForumCategory');
        }

        $object = $this->ToolBoxContainer->addElement('header','header-create-topic','create topic','glyphicon glyphicon-plus',$forumCategory->getFullURL() . '/add-topic/#AddTopic','primary');
        //$object->sOnClick = "addTopicFormInlineClick(".$topic->sTitle."','".$topic->sID."','".$topic->sID."','add-reply'));";

    }

    public function createTopicMenu($topic)
    {
        if ($topic->checkOwnership())
        {
            $object = $this->ToolBoxContainer->addElement('header', 'header-edit', 'edit', 'fa fa-edit', $topic->getUsedURL() . '/edit-topic/#AddTopic');
            $object->sOnClick = "editTopicFormInlineClick('".$topic->sParentId."','".$topic->sID."',".json_encode(strip_tags($topic->sTitle)).",'topic-preview')";
        }

        $object = $this->ToolBoxContainer->addElement('header', 'header-reply', 'reply', 'glyphicon glyphicon-plus','','primary');
        $object->sOnClick = "addReplyButtonClick(addReplyButtonClick(this,'".$topic->sTitle."','".$topic->sID."','".$topic->sID."','add-reply'));";

        $object = $this->ToolBoxContainer->addElement('header','header-create-topic','create topic','glyphicon glyphicon-plus',$topic->sParentId . '/add-topic/#AddTopic');

        //trb corectat sParentId

    }

}