<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'modules/users/my_user/user/user_advanced_properties/third_parties_social_networks/models/Third_party_social_network.php';
require_once APPPATH.'core/controllers/MY_AdvancedController.php';

class Oauth2_process_data_controller extends MY_AdvancedController
{
    function __construct()
    {
        parent::__construct();
    }

    function getAddress() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
        return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }

    public function processOAuth2($sOAuth2Type, $arrJSON, $arrAccessToken=[])
    {
        $bSuccess=false;
        if (empty($arrJSON)) return false;

        //var_dump($arrJSON);


        /*$sURL = $_SERVER['REQUEST_URI'];
        var_dump($this->getAddress());

        if (strpos($sURL,'http://myskyhub.ddns.net/') !== false) {
            $sURL = str_replace("http://myskyhub.ddns.net/","http://skyhub.me",$sURL);
            var_dump($sURL);
            die();
        }*/

        try
        {
            switch ($sOAuth2Type)
            {
                case 'google':

                    if ((!isset($arrJSON['id'])) || (!isset($arrJSON['name'])))
                        return false;

                    $result = $this->MyUser->loginUserWithSocialNetwork('google',$arrJSON['id']);
                    if (!$result['bResult'])
                    {
                        $UserNew = new User_model();

                        if (isset($arrJSON['email']))  $UserNew->sEmail = $arrJSON['email'];
                        if (isset($arrJSON['given_name'])) $UserNew->sFirstName = $arrJSON['given_name'];
                        if (isset($arrJSON['family_name'])) $UserNew->sLastName = $arrJSON['family_name'];
                        if (isset($arrJSON['name'])) $UserNew->sName = $arrJSON['name'];

                        if (isset($arrJSON['username'])) $UserNew->generateUserNameFromEmail($arrJSON['username']);
                        else $UserNew->generateUserNameFromEmail();

                        if (isset($arrJSON['verified']))  $UserNew->bVerified=$arrJSON['verified'];
                        if (isset($arrJSON['locale']))  $UserNew->sCountry=$arrJSON['locale'];
                        if (isset($arrJSON['gender']))
                        {
                            if ($arrJSON['gender']=='male') $UserNew->iGender=1;
                            else
                                if ($arrJSON['gender']=='female') $UserNew->iGender=0;
                        }

                        if (isset($arrJSON['picture']))
                        {
                            $UserNew->sAvatarPicture = $arrJSON['picture'];
                            $UserNew->sAvatarPicture  = $this->getAvatarOnlineAndResize($UserNew->sAvatarPicture,$UserNew->sID,'uploads/images/','icon|ico|png|jpg');
                            if ($UserNew->sAvatarPicture == false) $UserNew->sAvatarPicture='';
                        }

                        $objThirdPartySocialNetwork = new Third_party_social_network($UserNew->sID, null);
                        $objThirdPartySocialNetwork->sSocialName="google";
                        if (isset($arrJSON['id'])) $objThirdPartySocialNetwork->sSocialId = $arrJSON['id'];
                        if (isset($arrJSON['link'])) $objThirdPartySocialNetwork->sSocialLink = $arrJSON['link'];

                        $UserNew->ThirdPartiesSocialNetworks->pushThirdParties($objThirdPartySocialNetwork);
                        $UserNew->storeUpdate();

                        $result = $this->MyUser->loginUserWithSocialNetwork('google',$arrJSON['id']);
                        if ($result['bResult'])
                            $this->registeredSuccessfully();
                        else
                            echo 'There has been a problem loggin you in';
                    } else
                    {
                        //echo 'You have been logged in successfully';
                    }

                    redirect(base_url('oauth-homepage/'.$result['id'].'/'.$result['credential']), 'refresh');

                    $bSuccess=true;

                    break;
                case 'facebook':

                    if (!isset($arrJSON['id']))
                        return false;

                    $result = $this->MyUser->loginUserWithSocialNetwork('facebook',$arrJSON['id']);
                    if (!$result['bResult'])
                    {
                        $UserNew = new User_model();
                        if (isset($arrJSON['email']))  $UserNew->sEmail = $arrJSON['email'];
                        if (isset($arrJSON['first_name'])) $UserNew->sFirstName = $arrJSON['first_name'];
                        if (isset($arrJSON['last_name'])) $UserNew->sLastName = $arrJSON['last_name'];
                        if (isset($arrJSON['name'])) $UserNew->sName = $arrJSON['name'];

                        if (isset($arrJSON['username'])) $UserNew->generateUserNameFromEmail($arrJSON['username']);
                        else $UserNew->generateUserNameFromEmail();

                        // there is this objThirdPartySocialNetwork["age_range"]=> array(1) { ["min"]=> int(21) }

                        if (isset($arrJSON['locale']))  $UserNew->sCountry=$arrJSON['locale'];
                        if (isset($arrJSON['timezone']))  $UserNew->sTimeZone=$arrJSON['timezone'];
                        if (isset($arrJSON['verified']))  $UserNew->bVerified=$arrJSON['verified'];
                        if (isset($arrJSON['gender']))//not checked!!!
                        {
                            if ($arrJSON['gender']=='male') $UserNew->iGender=1;
                            else
                                if ($arrJSON['gender']=='female') $UserNew->iGender=0;
                        }

                        $objThirdPartySocialNetwork = new Third_party_social_network($UserNew->sID, null);
                        $objThirdPartySocialNetwork->sSocialName="facebook";
                        if (isset($arrJSON['id'])) $objThirdPartySocialNetwork->sSocialId = $arrJSON['id'];
                        if (isset($arrJSON['link'])) $objThirdPartySocialNetwork->sSocialLink = $arrJSON['link'];

                        if (isset($arrJSON['picture']))
                        {
                            $UserNew->sAvatarPicture = "http://graph.facebook.com/".$arrJSON['id']."/picture?type=large";
                            $UserNew->sAvatarPicture  = $this->getAvatarOnlineAndResize($UserNew->sAvatarPicture,$UserNew->sID,'uploads/images/','');
                            if ($UserNew->sAvatarPicture == false) $UserNew->sAvatarPicture='';

                            /*if (!is_array($data))
                            {
                                $UserNew->sAvatarPicture=$arrJSON['picture'];
                                $UserNew->sAvatarPicture = "http://graph.facebook.com/".$arrJSON['id']."/picture?type=large";
                                $UserNew->sAvatarPicture  = $this->getAvatarOnlineAndResize($UserNew->sAvatarPicture,$UserNew->sID,'uploads/images/','icon|ico|png');
                                if ($UserNew->sAvatarPicture == false) $UserNew->sAvatarPicture='';
                            }
                            else
                            {
                                foreach ($data as $element)
                                {
                                    if (isset($element['url']))
                                    {
                                        $UserNew->sAvatarPicture=$element['url'];

                                        $UserNew->sAvatarPicture  = $this->getAvatarOnlineAndResize($UserNew->sAvatarPicture,$UserNew->sID,'uploads/images/','icon|ico|png|jpg|jpeg');
                                        if ($UserNew->sAvatarPicture == false) $UserNew->sAvatarPicture='';
                                    }
                                    if (isset($element['is_silhouette'])) $objThirdPartySocialNetwork->addAdditionalProperties("is_silhouette",$element['is_silhouette']);
                                }
                            }*/
                        }
                        if (isset($arrJSON['updated_time']))  $objThirdPartySocialNetwork->addAdditionalProperties("updated_time",$arrJSON['updated_time']);

                        $UserNew->ThirdPartiesSocialNetworks->pushThirdParties($objThirdPartySocialNetwork);
                        $UserNew->ThirdPartiesSocialNetworks->arrSocialAccessToken = $arrAccessToken;

                        $UserNew->storeUpdate();

                        $result = $this->MyUser->loginUserWithSocialNetwork('facebook',$arrJSON['id']);
                        if ($result['bResult'])
                            $this->registeredSuccessfully();
                        else
                            echo 'There has been a problem loggin you in';
                    } else
                    {
                        //echo 'You have been logged in successfully';

                        $this->load->model('users/users', 'Users');
                        $User = $this->Users->userByMongoId($this->MyUser->sID);
                        $User->ThirdPartiesSocialNetworks->setSocialAccessTokenFromSocialNetworkName('facebook',$arrAccessToken);
                        $User->storeUpdate();
                    }

                    redirect(base_url('oauth-homepage/'.$result['id'].'/'.$result['credential']), 'refresh'); //echo 'You have been logged in successfully';
                    //header("Location: ".base_url(''));

                    $bSuccess=true;

                    break;
                case 'linkedin':
                    if (!isset($arrJSON['id']))
                        return false;

                  /*  var_dump($arrJSON);
                    die();*/

                    $result = $this->MyUser->loginUserWithSocialNetwork('linkedin',$arrJSON['id']);
                    if (!$result['bResult'])
                    {
                        $UserNew = new User_model();
                        if (isset($arrJSON['emailAddress'])) $UserNew->sEmail = $arrJSON['emailAddress'];
                        if (isset($arrJSON['firstName'])) $UserNew->sFirstName = $arrJSON['firstName'];
                        if (isset($arrJSON['lastName'])) $UserNew->sLastName = $arrJSON['lastName'];
                        if (isset($arrJSON['headline'])) $UserNew->sBiography = $arrJSON['headline'];

                        if (isset($arrJSON['userName'])) $UserNew->generateUserNameFromEmail($arrJSON['userName']);
                        else $UserNew->generateUserNameFromEmail();

                        // there is this objThirdPartySocialNetwork["age_range"]=> array(1) { ["min"]=> int(21) }

                        if (isset($arrJSON['picture']))
                        {
                            $data = $arrJSON['picture'];
                            if (isset($data['_total']))
                                $iPicturesCount = (int) $data['_total'];

                            if (isset($data['values']))
                            {
                                $data = $data['values'];

                                if ((is_array($data))&&(count($data) >= 1)) {

                                    $UserNew->sAvatarPicture = $this->getAvatarOnlineAndResize($data[0], $UserNew->sID, 'uploads/images/', '');
                                    if ($UserNew->sAvatarPicture == false) $UserNew->sAvatarPicture = '';
                                }
                            }
                        }

                        $objThirdPartySocialNetwork = new Third_party_social_network($UserNew->sID, null);
                        $objThirdPartySocialNetwork->sSocialName = "linkedin";
                        if (isset($arrJSON['id'])) $objThirdPartySocialNetwork->sSocialId = $arrJSON['id'];
                        if (isset($arrJSON['siteStandardProfileRequest'])) {
                            $data = $arrJSON['siteStandardProfileRequest'];
                            if (isset($data['url']))
                                $objThirdPartySocialNetwork->sSocialLink = $data['url'];
                        }

                        if (isset($arrJSON['location']))
                        {
                            $data = $arrJSON['location'];
                            if (isset($data['country']))
                            {
                                $country = $data['country'];
                                if (isset($country['code'])) $UserNew->sCountry = $country['code'];
                            }
                            if (isset($data['name']))
                            {
                                $UserNew->sCity = $data['name'];
                            }
                        }

                        $UserNew->ThirdPartiesSocialNetworks->pushThirdParties($objThirdPartySocialNetwork);
                        $UserNew->storeUpdate();

                        $result = $this->MyUser->loginUserWithSocialNetwork('linkedin',$arrJSON['id']);
                        if ($result['bResult'])
                            $this->registeredSuccessfully();
                        else
                            echo 'There has been a problem loggin you in';
                    } else
                    {
                        //echo 'You have been logged in successfully';
                    }


                    redirect(base_url('oauth-homepage/'.$result['id'].'/'.$result['credential']), 'refresh'); //echo 'You have been logged in successfully';
                    //header("Location: ".base_url(''));

                    $bSuccess=true;

                    break;
                case 'twitter':
                    if (!isset($arrJSON['id_str']))
                        return false;

                    $result = $this->MyUser->loginUserWithSocialNetwork('twitter',$arrJSON['id_str']);
                    if (!$result['bResult']){
                        $UserNew = new User_model();

                        //Email address is not done
                        if (isset($arrJSON['email_address'])) $UserNew->sEmail = $arrJSON['email_address'];
                        if (isset($arrJSON['name']))
                        {
                            $UserNew->sName = $arrJSON['name'];
                            $UserNew->generateFirstAndLastNamesFromName();
                        }

                        if (isset($arrJSON['screen_name'])) $UserNew->generateUserNameFromEmail($arrJSON['screen_name']);
                        else $UserNew->generateUserNameFromEmail();

                        if (isset($arrJSON['location']))
                        {
                            $sLocation = $arrJSON['location'];
                            //$data = str_split($sLocation,strpos($sLocation,','));
                            $data = explode(',',$sLocation);
                            $UserNew->sCity = '';
                            for ($index=0; $index<count($data)-1; $index++)
                                $UserNew->sCity .= $data[$index];

                            $UserNew->sCountry = $data[count($data)-1];
                        }
                        if (isset($arrJSON['description'])) $UserNew->sBiography = $arrJSON['description'];

                        if (isset($arrJSON['entities']))
                        {
                            $data = $arrJSON['entities'];
                            if (isset($data['url']))
                            {
                                $data = $data['url'];
                                if (isset($data['urls']))
                                {
                                    $data = $data['urls'];
                                    if (isset($data[0]))
                                    {
                                        $data = $data[0];
                                        if (isset($data['expanded_url']))
                                            $UserNew->sWebsite = $data['expanded_url'];
                                    }
                                }
                            }
                        }

                        if (isset($arrJSON['profile_image_url']))
                        {
                            $UserNew->sAvatarPicture = $arrJSON['profile_image_url'];
                            $UserNew->sAvatarPicture = str_replace("_normal","",$UserNew->sAvatarPicture);
                            $UserNew->sAvatarPicture  = $this->getAvatarOnlineAndResize($UserNew->sAvatarPicture,$UserNew->sID,'uploads/images/','icon|ico|png');
                            if ($UserNew->sAvatarPicture == false) $UserNew->sAvatarPicture='';
                        }
                        if (isset($arrJSON['profile_banner_url'])) $UserNew->sBackgroundImageLink = $arrJSON['profile_banner_url'];

                        if ($arrJSON['time_zone'])  $UserNew->sTimeZone = $arrJSON['time_zone'];
                        if ($arrJSON['lang'])  $UserNew->sLanguage = $arrJSON['lang'];

                        $objThirdPartySocialNetwork = new Third_party_social_network($UserNew->sID, null);
                        $objThirdPartySocialNetwork->sSocialName = "twitter";
                        if (isset($arrJSON['id_str'])) $objThirdPartySocialNetwork->sSocialId = $arrJSON['id_str'];
                        if (isset($data['url']))  $objThirdPartySocialNetwork->sSocialLink = $data['url'];

                        if (isset($arrJSON['followers_count']))
                            $objThirdPartySocialNetwork->addAdditionalProperties('followers_count',$arrJSON['followers_count']);

                        if (isset($arrJSON['friends_count']))
                            $objThirdPartySocialNetwork->addAdditionalProperties('friends_count',$arrJSON['friends_count']);

                        if (isset($arrJSON['created_at']))
                            $objThirdPartySocialNetwork->addAdditionalProperties('created_at',$arrJSON['created_at']);

                        if (isset($arrJSON['lang']))
                            $objThirdPartySocialNetwork->addAdditionalProperties('created_at',$arrJSON['created_at']);

                        if (isset($arrJSON['status']))
                        {
                            $data = $arrJSON['status'];
                            if (isset($data['text']))
                                $objThirdPartySocialNetwork->addAdditionalProperties('post_text',$data['text']);
                        }

                        $UserNew->ThirdPartiesSocialNetworks->pushThirdParties($objThirdPartySocialNetwork);
                        $UserNew->storeUpdate();

                        $result = $this->MyUser->loginUserWithSocialNetwork('twitter',$arrJSON['id_str']);
                        if ($result['bResult'])
                            $this->registeredSuccessfully();
                        else
                            echo 'There has been a problem loggin you in';
                    } else
                    {
                        //echo 'You have been logged in successfully';
                    }

                    redirect(base_url('oauth-homepage/'.$result['id'].'/'.$result['credential']), 'refresh'); //echo 'You have been logged in successfully';
                    //header("Location: ".base_url(''));

                    $bSuccess=true;

                    break;
                    break;
                case 'yahoo':
                    break;
            }
        }
        catch (Exception $ex)
        {
            var_dump($ex->getMessage());
            $bSuccess=false;
        }

        return $bSuccess;
    }

    protected function registeredSuccessfully()
    {
        $this->load->model('counter/counter_statistics','CounterStatistics');
        $this->CounterStatistics->increaseUsers(1);

        //send email
        modules::load('emails/email_controller')->sendActionEmail('registration');
        /*
        $this->load->model('emails/emails_model','Emails');
        modules::load('emails/email_controller')->sendEmail($this->Emails->insertActionEmail('registration'));
        */
    }


}