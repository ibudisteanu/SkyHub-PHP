<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Oauth2_controller extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->helper('url');
    }

    public function processOAuth2($sOAuth2Type = '', $sDoneOAuth1='')
    {
        $bStatus=false;
        $bGo=false;

        /*
        if ((isset($_GET['state']))&&($_GET['state']!='')) $bStatus=true;
        else
        if ((isset($_GET['go'])) && ($_GET['go'] == 'go')) $bGo=true;*/

        if ((!isset($_GET)) || (empty($_GET))) $bGo=true;

        //No status and no $bGo
        if (($bGo))
        {
            redirect(base_url('login/'.$sOAuth2Type.'?go=go'), 'refresh');
            return;
        }



        $bError = false;

        switch ($sOAuth2Type)
        {
            case 'google':
                //$this->load->library('../modules/users/authentication/oauth2/libraries/lusitanian/oauth/examples/Google_OAuth2',null,'GoogleOAuth2');
                require_once APPPATH.'/modules/users/authentication/oauth2/libraries/lusitanian/oauth/examples/Google_OAuth2.php';
                global $objGoogleOAuth2;
                if ($objGoogleOAuth2->bSuccess)
                {
                    //var_dump($objFacebookOAuth2->arrResult); //for debugging
                    if (!modules::load('oauth2/oauth2_process_data_controller')->processOAuth2($sOAuth2Type, $objGoogleOAuth2->arrResult) )
                        $bError=false;
                } else $bError=false;

                break;
            case 'facebook':
                require_once APPPATH.'/modules/users/authentication/oauth2/libraries/lusitanian/oauth/examples/Facebook_OAuth2.php';
                global $objFacebookOAuth2;
                $objFacebookOAuth2->login();
                if ($objFacebookOAuth2->bSuccess)
                {
                    //var_dump($objFacebookOAuth2->arrResult); //for debugging
                    if (!modules::load('oauth2/oauth2_process_data_controller')->processOAuth2($sOAuth2Type, $objFacebookOAuth2->arrResult, $objFacebookOAuth2->arrAccessToken)  == false)
                        $bError=false;
                } else $bError=false;

                break;
            case 'linkedin':
                require_once APPPATH.'/modules/users/authentication/oauth2/libraries/lusitanian/oauth/examples/Linkedin_OAuth2.php';
                global $objLinkedinOAuth2;
                if ($objLinkedinOAuth2->bSuccess)
                {
                    //var_dump($objLinkedinOAuth2->arrResult); //for debugging
                    if (!modules::load('oauth2/oauth2_process_data_controller')->processOAuth2($sOAuth2Type, $objLinkedinOAuth2->arrResult)  == false)
                      $bError=false;
                } else $bError=false;

                break;
            case 'yahoo':
                require_once APPPATH.'/modules/users/authentication/oauth2/libraries/lusitanian/oauth/examples/Yahoo_OAuth2.php';
                global $objYahooOAuth2;
                if ($objYahooOAuth2->bSuccess)
                {
                    var_dump($objYahooOAuth2->arrResult); //for debugging
                    //if (!modules::load('oauth2/oauth2_process_data_controller')->processOAuth2($sOAuth2Type, $objYahooOAuth2->arrResult)  == false)
                        //$bError=false;
                } else $bError=false;

                break;
            case 'twitter'://not working now...
                require_once APPPATH.'/modules/users/authentication/oauth2/libraries/lusitanian/oauth/examples/Twitter_OAuth2.php';
                global $objTwitterOAuth2;
                if ($objTwitterOAuth2->bSuccess)
                {
                    //var_dump($objTwitterOAuth1->arrResult);
                    if (!modules::load('oauth2/oauth2_process_data_controller')->processOAuth2($sOAuth2Type, $objTwitterOAuth2->arrResult)  == false)
                        $bError=false;
                } else $bError=false;
                break;
            case 'github':
                break;
            default:
                $bError=true;
                break;
        }

        //Showing a nice error
        if ($bError)
        {
            $this->Template->loadMeta('Signup to SkyHub using '.$sOAuth2Type,'Anyone can signup to SkyHub using multiple social networks and platforms including '.$sOAuth2Type , '', '');
            $this->Template->renderHeader('Signup to SkyHub using '.$sOAuth2Type);

            modules::load('auth_site/login')->index('box');
            modules::run('fluid_header/main_header/index');

            $this->Template->renderContainer();
            $this->Template->renderFooter();
        }

    }

    public function renderOAuth2Buttons($bHidden=false)
    {
        return $this->renderModuleView('oauth2_buttons_view.php', $this->data,$bHidden);
    }


}