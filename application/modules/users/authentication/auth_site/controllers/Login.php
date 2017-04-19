<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller
{
    static $iLoginNo=0;

    function __construct()
    {
        parent::__construct();

        $this->load->helper('url');
        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
    }

    public function index($sLoginPageType = 'page', $sSpecialStyle='')
    {
        return $this->processLoginForm($sLoginPageType, $sSpecialStyle);
    }

    protected function checkLogin()
    {
        if ($_POST)
        {
            $username = $this->StringsAdvanced->processText($_POST['username'], 'html|xss|whitespaces');
            $password = $this->StringsAdvanced->processText($_POST['password'], 'html|xss|whitespaces');

            $this->MyUser->loginWithPassword($username, $password);

            if ($this->MyUser->bLogged) {

                return true;
            } else {
                return false;
            }
        };
    }

    public function  logOut()
    {
        $this->MyUser->logOut();
        redirect(base_url(''), 'refresh');
    }

    protected function processLoginForm($sLoginPageType, $sSpecialStyle)
    {
        if (($_GET)&&(isset($_GET["val"]))&&($_GET["val"]=='logout'))
        {
            if ($this->MyUser->bLogged)
                $this->Logout();

            redirect(base_url(''), 'refresh');
            //$this->getLoginView($sLoginPageType);
        } else
        {
            if ($this->MyUser->bLogged)
            {
                //header("Location: "."../User/Profile/UserProfile.php");
                //die();
                if ($sLoginPageType=='box')
                    return;
                else
                    return;
            } else
            {
                if (isset($_POST["val"])&&($_POST["val"]=='checkin'))
                {
                    if ($this->CheckLogin())
                    {
                        redirect(base_url(''), 'refresh');
                    } else
                    {
                        //$this->data['g_sLoginErrorMessage']='Wrong <strong>username/email</strong> or <strong>password</strong>';
                        return $this->getLoginView($sLoginPageType, $sSpecialStyle);
                    }

                } else
                {
                    return $this->getLoginView($sLoginPageType, $sSpecialStyle);
                }
            }
        }
    }

    public function getLoginView($sLoginPageType, $sSpecialStyle)
    {
        $this->includeWebPageLibraries('tooltip');
        $this->includeWebPageLibraries('validation');

        $this->OAuth2 = modules::load('oauth2/Oauth2_controller', NULL);
        $this->data['OAuth2LoginButtons'] = $this->OAuth2->renderOAuth2Buttons(true);
        $this->data['sSpecialStyle'] = $sSpecialStyle;
        $this->data['iLoginNo'] = Login::$iLoginNo++;

        $sContent='';
        switch ($sLoginPageType )
        {
            case 'box':
                $sContent = $this->renderModuleView('login_box',$this->data,TRUE);
                $this->load->vars(array('g_dtLoginBox' => $sContent));
                break;
            case 'form':
                $sContent = $this->renderModuleView('login_form',$this->data,TRUE);
                $this->load->vars(array('g_dtLoginBox' => $sContent));
                break;
            case 'page':
                $sContent=$this->renderModuleView('login_container',$this->data,TRUE);
                $this->ContentContainer->addObject($sContent,'',10);
                break;
        }
        return $sContent;
    }


}