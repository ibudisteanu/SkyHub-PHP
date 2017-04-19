<?php

require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Api_signin  extends MY_AdvancedController
{
    public function loginFastPOST()
    {
        if ( !isset($_POST) )
        {
            $arrResult = ["result"=>false,"message"=>"No POST found"];
            echo json_encode($arrResult);
            return false;
        }

        if (!isset($_POST['Id'])) {
            $arrResult = ["result"=>false,"message"=>'<strong>Id Post</strong> not presented <br/>'];
            echo json_encode($arrResult);
            return false;
        }

        if (!isset($_POST['Pass'])) {
            $arrResult = ["result"=>false,"message"=>'<strong>Pass Post</strong> not presented <br/>'];
            echo json_encode($arrResult);
            return false;
        }

        return $this->loginFast($_POST['Id'],$_POST['Pass']);
    }

    public function loginFast($sUsername='', $sPassword='')
    {
        $arrResult = array();
        if (!$this->MyUser->bLogged)
        {
            $bResult = $this->MyUser->LoginWithPassword($sUsername, $sPassword);
            if ($bResult)
                $arrResult = array_merge($arrResult, ["message"=>$this->AlertsContainer->renderViewByName('g_msgLoginSuccess','none',true,true)]);
            else
                $arrResult = array_merge($arrResult, ["message"=>$this->AlertsContainer->renderViewByName('g_msgLoginError','none',true, true)]);
        } else
        {
            $bResult =false;
            $arrResult = array_merge($arrResult, ["message"=>"You are already logged in"]);
        }
        $arrResult = array_merge($arrResult, ["result"=>$bResult,"loggedIn"=>$this->MyUser->bLogged] );
        echo json_encode($arrResult);
        return $bResult;
    }

    public function logoutFast()
    {
        $arrResult = [];
        if ($this->MyUser->bLogged)
        {
            $this->MyUser->logOut();
            $arrResult = array_merge($arrResult, ["result"=>true]);
            $arrResult = array_merge($arrResult, ["message"=>"You have been logged out"]);
        } else
        {
            $arrResult = array_merge($arrResult, ["result"=>false]);
            $arrResult = array_merge($arrResult, ["message"=>"You are not logged in"]);
        }
        $arrResult = array_merge($arrResult, ["loggedIn"=>$this->MyUser->bLogged]);
        echo json_encode($arrResult);
    }
}