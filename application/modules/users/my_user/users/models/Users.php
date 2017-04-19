<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'core/models/MY_Hierarchy_page_cached_model.php';

class Users extends MY_Hierarchy_page_cached_model
{
    public $sClassName = 'User_model';

    public function __construct()
    {
        parent::__construct(array());
        $this->initDB('users',TUserRole::notLogged,TUserRole::User,TUserRole::User,TUserRole::User);
    }

    public function userByMongoId($sID)
    {
        $sCacheId = 'userByMongoId_'.$sID;
        return $this->loadContainerByIdCached($sCacheId, $sID,array(),true);
    }

    public function userByUsername($sUsername)
    {
        $sCacheId = 'userByUsername_'.$sUsername;
        return $this->loadContainerByFieldNameCached($sCacheId, "Username",$sUsername,array(),true);
    }

    public function userByEmail($sEmail)
    {
        $sCacheId = 'userByEmail_'.$sEmail;
        return $this->loadContainerByFieldNameCached($sCacheId,"Email",$sEmail,array(),true);
    }

    public function checkEmailUsed($sEmail)
    {
        $sCacheId = 'checkUserEmailUsed_'.$sEmail;

        if (!$result = $this->AdvancedCache->get($sCacheId))
        {
            $cursor = $this->find(["Email"=>$sEmail],["Email"]);

            $count = $cursor->count();
            if ($count > 0) $result =  true;
            else $result = false;

            $this->AdvancedCache->save($sCacheId, $result, 2678400);
        }

        return $result;
    }

    public function checkUsernameUsed($sUsername)
    {
        $sCacheId = 'checkUserUsernameUsed_'.$sUsername;

        if (!$result = $this->AdvancedCache->get($sCacheId))
        {
            $cursor = $this->find(["Username"=>$sUsername],["Email"]);

            $count = $cursor->count();
            if ($count > 0) $result = true;
            else $result = false;

            $this->AdvancedCache->save($sCacheId, $result, 2678400);
        }

        return $result;
    }

    public function userCount()
    {
        return $this->dataCount();
    }

    /* USED FOR API */
    public function findAllUsers()
    {
        return $this->findAllCached('findAllUsers');
    }

}