<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'modules/users/my_user/user/models/User_model.php';
require_once APPPATH.'modules/users/my_user/user/user_advanced_properties/user_activities/models/User_activities_model.php';

/*
 * MyUser class for the current logged in user. It is used to login the user with password and with cookies.
 * Also it stores the User Activities
 */

class My_user_model extends User_model
{

    public $UserActivities = null;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('auth_site/query_trials_blocked_login_incorrect','QueryTrialsBlockedLoginIncorrect');

        //print_r($_COOKIE);
        //Login with COOKIES
        if (isset($_COOKIE['id']) && isset($_COOKIE['credential']))
        {
            $sLoginId = $this->StringsAdvanced->processText($_COOKIE['id'],'html|xss|whitespaces');
            $sLoginCredential = $this->StringsAdvanced->processText($_COOKIE['credential'],'html|xss|whitespaces');
        } else
        {
            $sLoginId = "";
            $sLoginCredential = "";
        }

        //$this->Login("admin",md5("123456"),"");

        if ((!empty($sLoginCredential)) && (!empty($sLoginId)))
        {
            if ($this->QueryTrials->checkIPAddress($this->QueryTrialsBlockedLoginIncorrect))
                $this->Login($sLoginId,'', $sLoginCredential,"");
        }

    }

    protected function loginSuccessfully()
    {
        $this->bLogged=true;

        $this->ActivityInformation->updateOnlyLastActivity();
    }

    protected function loginSuccessfullyFirstTime()
    {
        modules::load('session_actions/process_session_actions')->solveSessionActions(false);
    }

    protected function logOutSuccessfully()
    {
        $this->bLogged = false;
    }

    public function logOut()
    {
        $this->clearCredentialUser();
        $this->logOutSuccessfully();
    }

    public function loginWithPassword($sUser, $sPass)
    {
        $this->bLogged=false;
        if ($this->QueryTrials->checkIPAddress($this->QueryTrialsBlockedLoginIncorrect))
        {

            if ($this->Login("",$sUser,"",$sPass))
            {
                $this->loginSuccessfully();
                $this->QueryTrials->removeOldAttempts();

                return true;
            } else
            {
                $this->QueryTrials->addAttempt();
                $this->AlertsContainer->addAlert('g_msgLoginError','error',$this->QueryTrials->sError);
            }
        }
        else
        {
            //$this->QueryTrials->addAttempt("login","rejected");
            $this->AlertsContainer->addAlert('g_msgLoginError','error',$this->QueryTrials->sError);
        }
        return false;
    }

    public function loginUserWithSocialNetwork($sSocialNetworkName, $sSocialNetworkId)
    {
        $this->bLogged=false;
        $MongoData = array("3rdPartiesSocialNet.Name" => ($sSocialNetworkName), "3rdPartiesSocialNet.Id" => $sSocialNetworkId);

        $cursor = $this->collection->find($MongoData);
        $count = $cursor->count();

        if ($count == 1)
        {
            $this->loadFromCursor($cursor);

            $sNewCredential = $this->CreateNewCredential(64);
            setcookie("id", $this->sID, time() + (2 * 365 * 24 * 60 * 60), "/");
            setcookie("credential", $sNewCredential, time() + (2 * 365 * 24 * 60 * 60), "/");

            $this->loginSuccessfully();
            $this->loginSuccessfullyFirstTime();

            $this->AlertsContainer->addAlert('g_msgLoginSuccess','success','Welcome back, '.$this->getFullName());
            return ['bResult'=>true,'id'=>$this->sID,'credential'=>$sNewCredential];

        } else
        {
            if ($count > 1)
            {
                $this->AlertsContainer->addAlert('g_msgGeneralError','error','More users found in the database');
                return ['bResult'=>false];
            } else
                if ($count < 1)
                {
                    unset($_COOKIE['id']);
                    setcookie('id', '', time() - 3600, '/'); // empty value and old timestamp

                    unset($_COOKIE['credential']);
                    setcookie('credential', '', time() - 3600, '/'); // empty value and old timestamp

                    //$this->AlertsContainer->addAlert('g_msgGeneralError','error','Invalid username/email or password');
                }
            return ['bResult'=>false];
        }
    }


    public function checkLoginWithPasswordSilent($sUser, $sPass)
    {
        return $this->checkUserPassword($sUser,$sPass);
    }


    //Try to log in and load the user credential
    private function Login($sLoginId, $sLoginUsername, $sLoginCredential, $sLoginPassword)
    {
        $this->bLogged=false;
        $bUsePassword = true;
        if ((empty($sLoginUsername)) || (empty($sLoginPassword)))
            $bUsePassword=false;

        //echo $sLoginId.' '.$sLoginCredential.' '; var_dump($bUsePassword);

        if ($bUsePassword)
        {
            $_COOKIE['id'] = "";
            $_COOKIE['credential'] = "";

            $UserNameData = array("Username" => $sLoginUsername);
            $EmailNameData = array("Email" => $sLoginUsername);

            if (!$this->checkUserPassword($sLoginUsername,$sLoginPassword))//Incorrect password
                return false;

            $MongoData = array('$or' => [$UserNameData, $EmailNameData]);

        } else //use credentials
        {
            if (strlen($sLoginCredential) < 10) return false;

            $MongoData = array("_id" => new MongoId($sLoginId), "Credential" => $sLoginCredential);
        }

        $cursor = $this->collection->find($MongoData);
        $count = $cursor->count();

        if ($count == 1)//successfully
        {
            $this->loadFromCursor($cursor);

            $this->loginSuccessfully();

            if ($bUsePassword == true)
            {
                $sNewCredential = $this->CreateNewCredential(64);

                setcookie("id", $this->sID, time() + (2 * 365 * 24 * 60 * 60), "/");
                setcookie("credential", $sNewCredential, time() + (2 * 365 * 24 * 60 * 60), "/");

                $this->loginSuccessfullyFirstTime();

                //$_SESSION['username'] = $sLoginUsername;
                //$_SESSION['credential'] = $sNewCredential;

            } else //logged successfully using the cookies
            {

            }

            $this->AlertsContainer->addAlert('g_msgLoginSuccess','success','Welcome back, '.$this->getFullName());
            return true;

        } else
        {
            if ($count > 1)
            {
                $this->AlertsContainer->addAlert('g_msgGeneralError','error','More users found in the database');
            } else
                if ($count < 1)
                {
                    unset($_COOKIE['id']);
                    setcookie('id', '', time() - 3600, '/'); // empty value and old timestamp
                    unset($_COOKIE['credential']);
                    setcookie('credential', '', time() - 3600, '/'); // empty value and old timestamp

                    //$this->AlertsContainer->addAlert('g_msgGeneralError','error','Invalid username/email or password');
                }
            return false;
        }

    }

    private function checkUserPassword($sLoginUsername,$sLoginPassword)
    {
        $UserNameData = array ("Username"=>$sLoginUsername);
        $EmailNameData = array ("Email"=>$sLoginUsername);

        $MongoData =  array ('$or'=>[$UserNameData,$EmailNameData]);

        $result = $this->collection->findOne($MongoData,array("Password"=>1));

        if (isset($result['Password']))
        {
            //echo '<br/>'.'<br/>'.$sLoginPassword;
            //echo 'DB Pass: '.$result['Password'];
            if (password_verify($sLoginPassword, $result['Password']))
            {
                //echo 'passwords match ';
                return true;
            }
        }

        //$this->AlertsContainer->addAlert('g_msgGeneralError','error','Invalid username/email or password');
        return false;
    }

    private function CreateNewCredential($iLength=64)
    {
        $sNewCredential = $this->StringsAdvanced->generateRandomString($iLength);
        $MongoData = array('$set'=>array("Credential"=>$sNewCredential));
        $this->collection->update(array ("_id"=>new MongoId($this->sID)),$MongoData);

        return $sNewCredential;
    }

    public function readCursor($p, $bEnableChildren = null)
    {
        parent::readCursor($p, $bEnableChildren);

        if ($this->bLogged)
            $this->UserActivities = new User_activities_model($this->sID);
    }

}