<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//require_once APPPATH.'core/models/MY_Advanced_model.php';

class Third_party_social_network extends CI_Model
{
    public $sSocialId;
    public $sSocialLink;
    public $sSocialName;
    public $arrSocialAccessToken = [];
    public $arrAdditionalProperties = array();

    public $sUserParentId;
    public $UserCollection;

    public function __construct($sUserParentId, $collection)
    {
        $this->sUserParentId=$sUserParentId;
        $this->UserCollection = $collection;
    }

    public function readCursor($p, $bEnableChildren=null)
    {

        if (isset($p["Id"])) $this->sSocialId = $p['Id'];
        if (isset($p["Name"])) $this->sSocialName = $p['Name'];

        if (isset($p["Link"])) $this->sSocialLink = $p['Link'];

        if (isset($p["AdditionalProperties"]))
            $this->arrAdditionalProperties = $p['AdditionalProp'];

        if (isset($p["AccessToken"]))
            $this->arrSocialAccessToken = $p['AccessToken'];
    }

    public function serializeProperties()
    {
        $arrResult = array();

        if (isset($this->sSocialId)) $arrResult = array_merge($arrResult, array("Id"=>$this->sSocialId));
        if (isset($this->sSocialName)) $arrResult = array_merge($arrResult, array("Name"=>$this->sSocialName));
        if (isset($this->sSocialLink)) $arrResult = array_merge($arrResult, array("Link"=>$this->sSocialLink));

        if ($this->arrSocialAccessToken != [])
            $arrResult = array_merge($arrResult, array("AccessToken"=>$this->arrSocialAccessToken));

        if (isset($this->arrAdditionalProperties))
        {
            if (count($this->arrAdditionalProperties) > 0)
                $arrResult = array_merge($arrResult, array("AdditionalProp"=>$this->arrAdditionalProperties));
        }

        return $arrResult;
    }

    public function addAdditionalProperties($name, $value)
    {
        $this->arrAdditionalProperties = array_merge($this->arrAdditionalProperties,array($name => $value));
    }

}
