<?php

require_once  __DIR__.'/login_user_DB.php';
require_once  __DIR__.'/../skyhub_server/server_uri.php';

class User
{

    private $LoginUserDB;
    protected  $sLoggedUserName='';

    private $sCookieFile;
    private $sAgent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
    private $cURL;

    public function __construct()
    {
        $this->LoginUserDB = new LoginUserDB();

        $this->sCookieFile  =  dirname(__FILE__) . '/cookie23312xxxSSS255g$$.txt';
        $this->cURL = curl_init();
    }

    public function __destruct()
    {
        curl_close ($this->cURL);
    }

    public function login($sUsername)
    {
        //Already logged in
        if ($this->sLoggedUserName == $sUsername)
            return true;
        else
        {
            $User = $this->LoginUserDB->getUser($sUsername);

            if ($User != null) {

                global $sSkyHubServerUri;
                $data = $this->cURLDownloadCookie($sSkyHubServerUri. 'api/users/get/authentication/login/' . $User['id'].'/'.$User['pass']);
                $data = json_decode($data, true);
                //var_dump($data);
                if ($data != null)
                {
                    if ((isset($data['result'])) && ($data['result'] == true))
                        return true;
                }
            } else
            {
                echo 'User not found '.$sUsername;
            }
            return false;
        }
    }

    public function logout()
    {
        global $sSkyHubServerUri;
        $data = $this->cURLDownloadCookie($sSkyHubServerUri. 'api/users/get/authentication/logout');
        $data = json_decode($data, true);

        if ((isset($data['result'])) && ($data['result'] == true))  return true;

        return false;
    }

    protected  function getURL($sURL)
    {
        $sText = file_get_contents($sURL);
        $arrData = json_decode($sText, true);
        return $arrData;
    }

    protected  function cURLDownloadCookie($sURL)
    {
        //$USER  = 'user'; $PASS      = 'pass';
        //The API url, to do the login

        //Set all the various options
        curl_setopt($this->cURL, CURLOPT_URL, $sURL);
        curl_setopt($this->cURL, CURLOPT_USERAGENT, $this->sAgent);
        curl_setopt($this->cURL, CURLOPT_POST, 0); // set POST method
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, 1);
        //Set the cookie file you want to use
        curl_setopt($this->cURL, CURLOPT_COOKIEFILE, $this->sCookieFile);
        curl_setopt($this->cURL, CURLOPT_COOKIEJAR, $this->sCookieFile);
        //curl_setopt($this->cURL, CURLOPT_USERPWD, $USER.":".$PASS);
        curl_setopt($this->cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->cURL, CURLOPT_SSL_VERIFYHOST, 0);

        //execute the CURL
        $mRes = curl_exec($this->cURL);
        return $mRes;
    }

    public function cURLSendPost($sURL, $arrFields)
    {
        $sFields='';
        if ($arrFields != null) {
            foreach ($arrFields as $key => $value)
            {
                $value = urlencode($value);
                $sFields .= $key.'='.$value.'&';
            }
            rtrim($sFields, '&');
        }

                //We do not have to initialise CURL again
        curl_setopt($this->cURL, CURLOPT_URL, $sURL);
        curl_setopt($this->cURL, CURLOPT_USERAGENT, $this->sAgent);
        curl_setopt($this->cURL, CURLOPT_POST, 0); // set POST method
        curl_setopt($this->cURL, CURLOPT_POST, count($arrFields));
        if ($sFields != '')
            curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $sFields);
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, 1);
        //Remember to use the same cookiefile as above
        curl_setopt($this->cURL, CURLOPT_COOKIEFILE, $this->sCookieFile);
        curl_setopt($this->cURL, CURLOPT_COOKIEJAR, $this->sCookieFile);
        curl_setopt($this->cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->cURL, CURLOPT_SSL_VERIFYHOST, 2);
        //execute the CURL call

        //echo 'CURL_BEFORE '.$sURL.' <br/>';
        $mRes = curl_exec($this->cURL);
//        echo 'CURL_AFTER ###'.$mRes.'### <br/>';
//        echo 'CURL INFO ###'; print_r(curl_getinfo($this->cURL)); echo '### <br/>';

        if(curl_errno($this->cURL)){
            echo '<b>Curl error: </b> ' . curl_error($this->cURL) . "<br/>";
        }

        return $mRes;
    }

}