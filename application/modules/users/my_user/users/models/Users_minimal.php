<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'core/models/MY_Hierarchy_page_cached_model.php';

class Users_minimal extends MY_Hierarchy_page_cached_model
{
    public $sClassName = 'User_model';
    protected $arrFieldsMinimal = array("_id","Username","URLName","FullURLLink","FullURLDomains","FullURLName","First Name","Last Name","Name","AvatarPicture","Biography","Country","City","Role","LastLogin","LastActivity");

    public function __construct()
    {
        //parent::__construct(array("_id","Username","First Name","Last Name","Name","AvatarPicture","Biography","Country","City","Role","LastLogin","LastActivity"));
        parent::__construct();
        $this->initDB('users',TUserRole::notLogged,TUserRole::User,TUserRole::User,TUserRole::User);
    }


    public function userByMongoId($sID, $arrIncludeFields=[])
    {
        $arrFieldsMinimal = $this->arrFieldsMinimal ;
        if ($arrIncludeFields != null) $arrFieldsMinimal = array_merge($arrIncludeFields, $arrIncludeFields);

        $sCacheId = 'userByMongoId_minimal_'.$sID. ($arrIncludeFields != [] ? implode("_", $arrIncludeFields) : '');

        return $this->loadContainerByIdCached($sCacheId,$sID,$arrFieldsMinimal,true);
    }

    public function findUserByFullURL($sFullURL='')
    {
        return $this->loadContainerByIdOrFullURL('', $sFullURL);
    }

    public function userByUsername($sUsername)
    {
        $sCacheId= 'userByUsername_minimal_'.$sUsername;
        return $this->loadContainerByFieldNameCached($sCacheId,"Username",$sUsername,$this->arrFieldsMinimal,true);
    }

    public function userByEmail($sEmail)
    {
        $sCacheId= 'userByEmail_minimal_'.$sEmail;
        return $this->loadContainerByFieldNameCached($sCacheId, "Email",$sEmail,$this->arrFieldsMinimal,true);
    }

    /* USED FOR API */
    public function findAllUsers()
    {
        return $this->findAllCached('findAllUsersMinimal');
    }

}