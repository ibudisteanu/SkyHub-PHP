<?php

/**
 * Example of making API calls for the Yahoo service
 *
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2014 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\Yahoo;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__.'/bootstrap.php';


class Yahoo_OAuth2
{
    public $arrResult = array();
    public $bSuccess=false;

    public function __construct()
    {

    }
}

global $objYahooOAuth2;
$objYahooOAuth2 = new Yahoo_OAuth2();

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
	$servicesCredentials['yahoo']['key'],
	$servicesCredentials['yahoo']['secret'],
	$currentUri->getAbsoluteUri()
);

// Instantiate the Yahoo service using the credentials, http client and storage mechanism for the token
$yahooService = $serviceFactory->createService('Yahoo', $credentials, $storage);
//var_dump($yahooService);
var_dump($_GET);
if (!empty($_GET['code'])) {
    // retrieve the CSRF state parameter


    $state = isset($_GET['state']) ? $_GET['state'] : null;

    // This was a callback request from facebook, get the token
    $token = $yahooService->requestAccessToken($_GET['code'], $state);

    // Send a request with it
    $objYahooOAuth2->arrResult = json_decode($yahooService->request('profile'), true);
    $objYahooOAuth2->bSuccess=true;

    /*
    $token = $storage->retrieveAccessToken('Yahoo');

    // This was a callback request from Yahoo, get the token
    $yahooService->requestAccessToken(
        $_GET['oauth_token'],
        $_GET['oauth_verifier'],
        $token->getRequestTokenSecret()
    );

    // Send a request now that we have access token
    $objYahooOAuth2->arrResult = json_decode($yahooService->request('profile'));
    $objYahooOAuth2->bSuccess=true;
    */
    //echo 'result: <pre>' . print_r($result, true) . '</pre>';

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $yahooService->getAuthorizationUri();
    header('Location: ' . (string)$url);
    /*
    // extra request needed for oauth1 to request a request token :-)
    $token = $yahooService->requestRequestToken();

    $url = $yahooService->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
    header('Location: ' . $url);*/
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Yahoo!</a>";
}
