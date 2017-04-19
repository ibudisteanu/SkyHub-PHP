<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'modules/users/my_user/user/user_advanced_properties/third_parties_social_networks/models/Third_party_social_network.php';

class Third_parties_social_networks extends CI_Model
{
    public $arrThirdParties = array();

    public $sUserParentId;
    private $UserCollection;

    public function __construct($sUserParentId, $collection)
    {
        $this->sUserParentId=$sUserParentId;
        $this->UserCollection = $collection;
    }

    public function readCursor($p, $bEnableChildren=null)
    {
        foreach ($p as $element)
        {
            $obj = new Third_party_social_network($this->sUserParentId, $this->UserCollection);
            $obj->readCursor($element, $bEnableChildren);
            array_push($this->arrThirdParties, $obj);
        }
    }

    public function pushThirdParties($object)
    {
        array_push($this->arrThirdParties, $object);
        $object->sUserParentId = $this->sUserParentId;
        $object->UserCollection = $this->UserCollection;
    }

    public function serializeProperties()
    {
        $arrResult = array();

        $arrElements=array();
        foreach ($this->arrThirdParties as $element)
        {
            array_push($arrElements, $element->serializeProperties());
        }
        if (!empty($arrElements))
            $arrResult = array_merge($arrResult, $arrElements);

        return $arrResult;
    }

    public function getSocialIDFromSocialNetworkName($sName)
    {
        foreach ($this->arrThirdParties as $thirdParty)
            if (strtolower($thirdParty->sSocialName) == strtolower($sName))
                return $thirdParty->sSocialId;

        return null;
    }

    public function getSocialAccessTokenFromSocialNetworkName($sName)
    {
        foreach ($this->arrThirdParties as $thirdParty)
            if (strtolower($thirdParty->sSocialName) == strtolower($sName))
                return $thirdParty->arrSocialAccessToken;

        return null;
    }

    public function setSocialAccessTokenFromSocialNetworkName($sName, $arrSocialAccessToken)
    {
        for ($i=0; $i<count($this->arrThirdParties); $i++)
            if (strtolower($this->arrThirdParties[$i]->sSocialName) == strtolower($sName))
                $this->arrThirdParties[$i]->arrSocialAccessToken = $arrSocialAccessToken;
    }


}
