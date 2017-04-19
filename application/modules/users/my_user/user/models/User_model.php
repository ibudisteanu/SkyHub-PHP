<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Hierarchy_page_cached_model.php';
require_once APPPATH.'modules/users/my_user/user/models/UserStatus.php';
require_once APPPATH.'modules/users/my_user/user/user_advanced_properties/activity_information/models/User_activity_information.php';
require_once APPPATH.'modules/users/my_user/user/user_advanced_properties/third_parties_social_networks/models/Third_parties_social_networks.php';

CONST DEFAULT_AVATAR = 'gravatar_default';

class User_model extends MY_Hierarchy_page_cached_model
{
    public $sClassName = 'User_model';

    public $arrChildrenDefinition = array();

    public $ActivityInformation;
    public $ThirdPartiesSocialNetworks;

    public $bLogged;
    public $sID;
    public $sUserName;
    public $sEmail;
    public $sWebsite;
    public $bVerified;
    public $sTelephone;
    public $sName;
    public $sCompany;
    public $sCountry;
    public $sCity;
    public $iGender = -666;
    public $sBackgroundImageLink;
    public $sFirstName;
    public $sLastName;
    public $sBiography;
    public $sTimeZone;
    public $sLanguage;
    public $sAvatarPicture;

    private $sNewPassword;//the password is private

    private $enUserRole;

    public function __construct()
    {
        parent::__construct(true,null,null,false);

        $this->initDB('users',TUserRole::notLogged,TUserRole::notLogged,TUserRole::User,TUserRole::User);
        if (!isset($this->bLogged)) $this->bLogged=false;
        if (!isset($this->enUserRole)) $this->enUserRole = TUserRole::notLogged;

        if (!isset($this->sAvatarPicture)) $this->sAvatarPicture=DEFAULT_AVATAR;
        if (!isset($this->sBackgroundImageLink)) $this->sBackgroundImageLink='';

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
        $this->load->library('TimeLibrary',null,'TimeLibrary');

        if (!isset($this->ActivityInformation)) $this->ActivityInformation = new User_activity_information($this->sID, $this->collection);
        if (!isset($this->ThirdPartiesSocialNetworks)) $this->ThirdPartiesSocialNetworks = new Third_parties_social_networks($this->sID, $this->collection);
    }

    public function setNewPassword($sNewPassword='')
    {
        $this->sNewPassword = password_hash($sNewPassword, PASSWORD_DEFAULT, ['cost' => 12]);
    }

    public function getUserLink()
    {
        return ($this->sUserName);
    }

    public function getFullName()
    {
        return ($this->sFirstName.' '.$this->sLastName);
    }

    public function getUserRole()
    {
        return $this->enUserRole;
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p,$bEnableChildren);

        $this->sAuthorId = $this->sID;

        if (isset($p["Username"])) $this->sUserName = $p["Username"];
        if (isset($p["Email"])) $this->sEmail = $p["Email"];

        if (isset($p['Verified'])) $this->bVerified=$p['Verified'];
        if (isset($p["Telephone"])) $this->sTelephone = $p["Telephone"];

        if (isset($p["First Name"])) $this->sFirstName = $p["First Name"];
        if (isset($p["Last Name"])) $this->sLastName = $p["Last Name"];

        if (isset($p["Name"])) $this->sName = $p["Name"];
        else
        {
            if (isset($this->sFirstName) || ($this->sLastName))
                $this->sName = $this->sFirstName . " " . $this->sLastName;
        }

        if (isset($p["Company"]))  $this->sCompany = $p["Company"];
        if (isset($p["Website"]))  $this->sWebsite = $p["Website"];
        if (isset($p["Biography"]))  $this->sBiography = $p["Biography"];
        if (isset($p['TimeZone']))  $this->sTimeZone = $p['TimeZone'];
        if (isset($p['Country']))  $this->sCountry = $p['Country'];
        if (isset($p['City']))  $this->sCity = $p['City'];
        if (isset($p["Lang"])) $this->sLanguage = $p["Lang"];
        if (isset($p['Gender'])) $this->iGender = (int)$p['Gender'];

        if (isset($p["Role"])) $iRole = $p["Role"];
        else $iRole=0;

        switch ($iRole)
        {
            case TUserRole::Admin :
                $this->enUserRole = TUserRole::Admin;
                break;
            case TUserRole::SuperAdmin :
                $this->enUserRole = TUserRole::SuperAdmin;
                break;
            case TUserRole::BotUser:
                $this->enUserRole = TUserRole::BotUser;
                break;
            default:
                $this->enUserRole= TUserRole::User;
        }
        if (isset($p["AvatarPicture"]))  $this->sAvatarPicture = $p["AvatarPicture"];
        else $this->sAvatarPicture = $this->getGravatar();

        $this->ActivityInformation->readCursor($p, $bEnableChildren);
        $this->ActivityInformation->sUserParentId = $this->sID;

        //reading social networks which where linked in
        if (isset($p['3rdPartiesSocialNet']))
            $this->ThirdPartiesSocialNetworks->readCursor($p['3rdPartiesSocialNet'], $bEnableChildren);

        $this->bLogged=true;

        //$this->AlertsContainer->addAlert('g_msgGeneralSuccess','success','Logged in successfully');
    }

    protected function serializeProperties()
    {
        $this->sAuthorId = '';

        $arrResult = parent::serializeProperties();

        if (isset($this->sUserName)) $arrResult = array_merge($arrResult, array("Username"=>strtolower($this->sUserName)));
        if (isset($this->sEmail)) $arrResult = array_merge($arrResult, array("Email"=>$this->sEmail));

        if (isset($this->sTelephone)) $arrResult = array_merge($arrResult, array("Telephone"=>$this->sTelephone));

        if (isset($this->bVerified)) $arrResult = array_merge($arrResult, array("Verified"=>$this->bVerified));
        if (isset($this->sFirstName)) $arrResult = array_merge($arrResult, array("First Name"=>$this->sFirstName));
        if (isset($this->sLastName)) $arrResult = array_merge($arrResult, array("Last Name"=>$this->sLastName));

        if (isset($this->sName)) $arrResult = array_merge($arrResult, array("Name"=>$this->sName));
        if (isset($this->sNewPassword)) $arrResult = array_merge($arrResult, array("Password"=>$this->sNewPassword));

        if ((isset($this->sAvatarPicture)) && ($this->sAvatarPicture != DEFAULT_AVATAR) && (!$this->isGravatar($this->sAvatarPicture))) $arrResult = array_merge($arrResult, array("AvatarPicture"=>$this->sAvatarPicture));
        if (isset($this->iGender)) $arrResult = array_merge($arrResult, array("Gender"=>(boolean)$this->iGender));

        if (isset($this->sCompany)) $arrResult = array_merge($arrResult, array("Company"=>$this->sCompany));
        if (isset($this->sWebsite)) $arrResult = array_merge($arrResult, array("Website"=>$this->sWebsite));
        if (isset($this->sBiography)) $arrResult = array_merge($arrResult, array("Biography"=>$this->sBiography));
        if (isset($this->sTimeZone)) $arrResult = array_merge($arrResult, array("TimeZone"=>$this->sTimeZone));
        if (isset($this->sCountry)) $arrResult = array_merge($arrResult, array("Country"=>$this->sCountry));
        if (isset($this->sCity)) $arrResult = array_merge($arrResult, array("City"=>$this->sCity));
        if (isset($this->sLanguage)) $arrResult = array_merge($arrResult, array("Lang"=>$this->sLanguage));

        $arrResult = array_merge($arrResult, $this->ActivityInformation->serializeProperties());
        $arrSocialNetworks = $this->ThirdPartiesSocialNetworks->serializeProperties();
        if (!empty($arrSocialNetworks))
        {
            $arrResult = array_merge($arrResult, array("3rdPartiesSocialNet"=>$arrSocialNetworks));
        }

        return $arrResult;
    }

    public function generateFirstAndLastNamesFromName($sInitialName='')
    {
        if ($sInitialName == '') $sInitialName = $this->sName;
        if (strpos($sInitialName,' ') > 0 )
        {
            $array = explode(" ", $sInitialName);
            for ($index=0; $index < count($array)-1; $index++)
                $this->sFirstName = $array[$index];

            $this->sLastName = $array[count($array)-1];
        } else
        {
            $this->sFirstName = $sInitialName;
        }
    }

    public function generateUserNameFromEmail($sInitialUserName='')
    {
        $sUserName = '';
        if ($sInitialUserName != '')
        {
            $sOriginalUserName=strtolower($sInitialUserName);
        } else
        {
            if (strpos($this->sEmail,'@') > 0 ) $sOriginalUserName = explode("@", $this->sEmail)[0];
            else
                if (($this->sFirstName!='') || ($this->sLastName!='')) $sOriginalUserName = ($this->sFirstName != '' ? $this->sFirstName.'.' : '') .$this->sLastName;
                else $sOriginalUserName='user';

            $sOriginalUserName=strtolower($sOriginalUserName);
        }
        if ($sOriginalUserName != '')
        {
            $sUserName = $sOriginalUserName;
            //starting with a random number after the username $this->sUserName = $sOriginalUserName . rand(0,1000);
            while ($this->loadContainerByFieldName("Username",$sUserName,array("_id"),true) != null )
            {
                $sUserName = $sOriginalUserName . rand(0,10000);
            }
        }
        if (strlen($sUserName) > 2) {
            $sUserName = str_replace([";",'!','?',"&",'<','>',"-", "_","$",":",",","(",")","/","'",'"'], '', $sUserName );
            $sUserName = str_replace(" ", '.', $sUserName );
            $this->sUserName = $sUserName;
        }
    }

    //$iResolution can be '',0, 30, 50, 100, 300
    public function getCustomAvatarImage($iResolution)
    {
        $path_info = pathinfo($this->sAvatarPicture);

        $sImageFileExtension = (isset($path_info['extension']) ? $path_info['extension'] : '');
        $sImageFileName =  (isset($path_info['filename']) ? $path_info['filename'] : '');
        $sImageFileDirectory =  (isset($path_info['dirname']) ? $path_info['dirname'] : '');

        if (($sImageFileName=='') || ($sImageFileExtension=='') || ($sImageFileDirectory==''))
            return $this->sAvatarPicture;

        if (parse_url($sImageFileDirectory,PHP_URL_HOST) != 'skyhub.me')
            return $this->sAvatarPicture;

        if ( (string) $iResolution == '0') $iResolution='';

        $sImagePath = rtrim($sImageFileDirectory,'/').'/'.$sImageFileName.($iResolution != '' ? '_'.$iResolution : '').'.'.$sImageFileExtension;

        /*if (filter_var($sImageFileDirectory, FILTER_VALIDATE_URL))
        {
            $this->load->library('WebLibrary',null,'WebLibrary');

            if ($this->WebLibrary->URLExists($sImagePath))
                return $sImagePath;
        } else
        {
            if (file_exists($sImagePath))
                return $sImagePath;
        }

        return $this->sAvatarPicture;*/

        return $sImagePath;

    }

    public function clearCredentialUser()
    {
        if ($this->bLogged == false)
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','You are not logged in');
            return;
        }

        setcookie("id", "", time() + (86400 * 30), "/");
        setcookie("credential", "", time() + (86400 * 30), "/");

        $MongoData =array('$set'=>array("Credential"=>''));
        $this->collection->update(array ("_id"=>new MongoId($this->sID)),$MongoData);

        $this->AlertsContainer->addAlert('g_msgGeneralSuccess','success','Logged out successfully');
    }

    public function isGravatar($sAvatar='')
    {
        if ($sAvatar == '') $sAvatar = $this->sAvatarPicture;

        if (strpos($sAvatar,'https://www.gravatar.com/avatar/') !== false) return true;
        return false;
    }

    private function getGravatar( $s = 80, $d = 'wavatar', $r = 'g', $img = false, $atts = array() ) {
        $url = 'https://www.gravatar.com/avatar/';
        //$url .= md5( strtolower( trim( $this->sEmail != '' ? $this->sEmail : $this->sUserName ) ));
        $url .= md5( strtolower( trim( $this->sID ) ));
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }

    public function resetCache()
    {
        parent::resetCache();

        $this->AdvancedCache->delete('userByMongoId_minimal_'.$this->sID);
        $this->AdvancedCache->delete('userByUsername_minimal_'.$this->sUserName);
        $this->AdvancedCache->delete('userByEmail_minimal_'.$this->sEmail);
        $this->AdvancedCache->delete('userByMongoId_'.$this->sID);
        $this->AdvancedCache->delete('userByUsername_'.$this->sUserName);
        $this->AdvancedCache->delete('userByEmail_'.$this->sEmail);
        $this->AdvancedCache->delete('checkUserEmailUsed_'.$this->sEmail);
        $this->AdvancedCache->delete('checkUserUsernameUsed_'.$this->sUserName);
        $this->AdvancedCache->delete('findAllUsers');
        $this->AdvancedCache->delete('findAllUsersMinimal');
    }

    public function getURL()
    {
        return base_url('user/'.rtrim($this->sUserName,'/'));
    }

    public function getUsedURL()
    {
        return $this->getURL();
    }

}
