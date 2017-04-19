<?php

/**
 * Example of retrieving an authentication token of the Facebook service
 *
 * PHP version 5.4
 *
 * @author     Benjamin Bender <bb@codepoet.de>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Facebook;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

class Facebook_OAuth2
{
    public $arrResult = array();
    public $arrAccessToken = [];

    public $bSuccess=false;

    public $servicesCredentials;
    public $serviceFactory;
    public $currentUri;

    public function __construct()
    {

    }

    public function login()
    {
        // Session storage
        $storage = new Session();

        // Setup the credentials for the requests
        $credentials = new Credentials(
            $this->servicesCredentials['facebook']['key'],
            $this->servicesCredentials['facebook']['secret'],
            $this->currentUri->getAbsoluteUri()
        );

        // Instantiate the Facebook service using the credentials, http client and storage mechanism for the token
        /** @var $facebookService Facebook */
        $facebookService = $this->serviceFactory->createService('facebook', $credentials, $storage, array('email'));

        if (!empty($_GET['code'])) {
            // retrieve the CSRF state parameter
            $state = isset($_GET['state']) ? $_GET['state'] : null;

            // This was a callback request from facebook, get the token
            $token = $facebookService->requestAccessToken($_GET['code'], $state);

            try {
                $this->arrAccessToken['sToken'] = $this->accessProtected($token,'accessToken');
                $this->arrAccessToken['sType'] = $this->accessProtected($token,'extraParams')['token_type'];
                $this->arrAccessToken['iExpiresIn'] = $this->accessProtected($token,'extraParams')['expires_in'];
            } catch (Exception $exception) {

            }

            // Send a request with it
            $this->arrResult = json_decode($facebookService->request('/me?scope=email&fields=id,email,name,first_name,last_name,age_range,link,locale,picture,timezone,updated_time,verified,friends'), true);
            $this->bSuccess=true;


            // Show some of the resultant data
            //echo 'Your unique facebook user id is: ' . $result['id'] . ' and your name is ' . $result['name'];

        } elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
            $url = $facebookService->getAuthorizationUri();
            header('Location: ' . $url);
        } else {
            $url = $this->currentUri->getRelativeUri() . '?go=go';
            echo "<a href='$url'>Login with Facebook!</a>";
        }

    }

    public function sendNotification()
    {

    }

    private function accessProtected($obj, $prop) {
        $reflection = new ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }

}

global $objFacebookOAuth2;
$objFacebookOAuth2 = new Facebook_OAuth2();
$objFacebookOAuth2->servicesCredentials = $servicesCredentials;
$objFacebookOAuth2->serviceFactory = $serviceFactory;
$objFacebookOAuth2->currentUri = $currentUri;


