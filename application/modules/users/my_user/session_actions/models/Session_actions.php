<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'modules/users/my_user/session_actions/models/Session_action.php';

class Session_actions extends CI_Model
{
    public $arrSessionActions = [];

    public function __construct()
    {
        parent::__construct();

        $this->readSessionActions();
    }

    protected function readSessionActions()
    {
        $bSessionActionFound=true;

        foreach ($_COOKIE as $cookieName => $cookieValue)
        {
            if (0 === strpos($cookieName, 'sessionAction'))
            {
                $iIndex = (int) substr($cookieName, strlen('sessionAction') );

                $newSessionAction = new Session_action($iIndex);

                if ($newSessionAction->bSessionValid)
                    array_push($this->arrSessionActions, $newSessionAction);
                else
                {
                    unset($newSessionAction);
                    Session_action::$SessionsCount--;
                }

            }
        }
        /*
        while ($bSessionActionFound)
        {
            $bSessionActionFound=false;
            $newSessionAction = new Session_action();

            if ($newSessionAction->bSessionValid) {
                $bSessionActionFound = true;
                array_push($this->arrSessionActions, $newSessionAction);
            }
            else
            {
                unset($newSessionAction);
                Session_action::$SessionsCount--;
            }
        }
        */
        //echo 'arrSessionActions: '.count($this->arrSessionActions);
    }

    public function createSessionAction($sName, $sType, $arrProperties)
    {
        if (($sName == '') || ($sType == '')) return false;

        if ($this->checkSessionActionIdentical($sName, $sType, $arrProperties)) return false;

        //var_dump($_COOKIE);

        $newSessionAction = new Session_action();
        $newSessionAction->writeSession($sName, $sType, $arrProperties);

        array_push($this->arrSessionActions,$newSessionAction);

        //var_dump($newSessionAction);
        //var_dump($_COOKIE);

        return $newSessionAction;
    }

    protected function checkSessionActionIdentical($sName, $sType, $arrProperties)
    {
        foreach ($this->arrSessionActions as $sessionAction)
            if (($sessionAction->sName == $sName)&&($sessionAction->sType == $sType) && ($sessionAction->arrProperties == $arrProperties))
                return true;

        return false;
    }

    public function getSessionActions()
    {
        return $this->arrSessionActions;
    }

    public function solveSessionActions($bRedirectPage=false)
    {
        /*for ($index=0; $index < count($this->arrSessionActions); $index++)
            if (isset($this->arrSessionActions[$index]))
            {
                $sessionAction = $this->arrSessionActions[$index];

                if ($sessionAction->solveActionSession($bRedirectPage))
                {
                    $this->arrSessionActions[$index]->deleteSession();
                    unset($this->arrSessionActions[$index]);
                    $index--;
                }
            }*/
        foreach ($this->arrSessionActions as $sessionAction) {

            if ($sessionAction->solveActionSession($bRedirectPage))
            {
                $sessionAction->deleteSession();
                unset($sessionAction);
            }
        }
    }


}