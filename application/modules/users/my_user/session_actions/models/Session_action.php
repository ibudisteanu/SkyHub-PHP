<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Session_action extends CI_Model
{
    static $SessionsCount = 0;

    public $iId=0;
    public $sName;
    public $sType; //reply, topic
    public $arrProperties = [];
    public $bSessionValid=false;

    protected  $sSessionName;

    public function __construct($iId = -1)
    {
        parent::__construct();

        if ($iId == -1) $this->iId = ++ Session_action::$SessionsCount;
        else{
            Session_action::$SessionsCount = $iId;
            $this->iId = $iId;
        }
        $this->sSessionName = 'sessionAction'.$this->iId;


        $this->readSession();

    }

    public function readSession()
    {
        //if (isset($_SESSION[$this->sSessionName]))
        if (isset($_COOKIE[$this->sSessionName]))
        {
            $sessionJSONData = $_COOKIE[$this->sSessionName];
            //$sessionJSONData = $_SESSION[$this->sSessionName];

            $arrData = json_decode($sessionJSONData,true);


            if (isset($arrData['name'])) $this->sName = $arrData['name'];
            if (isset($arrData['type'])) $this->sType = $arrData['type'];
            if (isset($arrData['properties'])) $this->arrProperties = $arrData['properties'];


            $this->bSessionValid = true;
        } else
            $this->bSessionValid=false;
    }

    public function writeSession($sName="", $sType="", $arrProperties=[])
    {
        if (($sName != '')&&($sType != '')&&(count($arrProperties) >= 0))//check if it is a new new session
        {
            $this->sName = $sName;
            $this->sType = $sType;
            $this->arrProperties = $arrProperties;

            $arrData = ['name'=>$this->sName, 'type'=>$this->sType, 'properties'=>$this->arrProperties];
            $sessionJSONData = json_encode($arrData);

            setcookie($this->sSessionName, $sessionJSONData, time() + (2 * 365 * 24 * 60 * 60), "/");

            $this->bSessionValid=true;

            return true;
        } else return false;
    }

    public function deleteSession()
    {
        //if (isset($_SESSION[$this->sSessionName]))
        if (isset($_COOKIE[$this->sSessionName]))
        {
            unset($_COOKIE[$this->sSessionName]);
            setcookie($this->sSessionName, '', time() - 3600, '/'); // empty value and old timestamp

            $this->bSessionValid=false;

            //echo 'deleted Action Session'.$this->sSessionName.'<br/>';

            return true;
        } else return false;
    }


    public function solveActionSession($bRedirectPage=false)
    {
        switch ($this->sType)
        {
            case 'newReplyPOST':
                if ($this->checkProperties(['POST']))
                {
                    $this->includePOSTFields($this->arrProperties['POST']);

                    $apiRepliesController = modules::load('api/api_replies');

                    if ($bRedirectPage)
                        $this->deleteSession();

                    //var_dump('new reply');

                    return $apiRepliesController->processReplySubmit(false);
                }
                return true;
                break;
            case 'newTopicPOST':
                if ($this->checkProperties(['POST','ActionName','FullURLLink','Parent','Topic']))
                {
                    $this->includePOSTFields($this->arrProperties['POST']);

                    $addForumTopicController = modules::load('add_topic/add_forum_topic');
                    $addForumTopicController->bRedirectSuccess=$bRedirectPage;
                    $addForumTopicController->bRenderForm=false;

                    if ($bRedirectPage)
                        $this->deleteSession();

                    //var_dump('new topic');

                    return $addForumTopicController->index($this->arrProperties['ActionName'],$this->arrProperties['FullURLLink'],$this->arrProperties['Parent'],$this->arrProperties['Topic']);

                }
                break;
        }

        return false;
    }

    private function checkProperties($array)
    {
        foreach ($array as $property)
            if (!array_key_exists($property,$this->arrProperties))
                return false;

        return true;
    }

    protected function includePOSTFields($includePOST)
    {
        foreach ($includePOST as $key=>$value)
        {
            {
                if (!isset($_POST[$key]))
                    $_POST[$key] = $value;
            }
        }
    }


}