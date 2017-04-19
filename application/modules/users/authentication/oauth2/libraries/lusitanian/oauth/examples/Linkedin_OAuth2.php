<?php

/**
 * Example of retrieving an authentication token of the Linkedin service
 *
 * PHP version 5.4
 *
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @author     Antoine Corcy <contact@sbin.dk>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Linkedin;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

class Linkedin_OAuth2
{
    public $arrResult = array();
    public $bSuccess=false;

    public function __construct()
    {

    }
}

global $objLinkedinOAuth2;
$objLinkedinOAuth2 = new Linkedin_OAuth2();


// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['linkedin']['key'],
    $servicesCredentials['linkedin']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Linkedin service using the credentials, http client and storage mechanism for the token
/** @var $linkedinService Linkedin */
$linkedinService = $serviceFactory->createService('linkedin', $credentials, $storage, array('r_basicprofile','r_emailaddress'));

if (!empty($_GET['code'])) {
    // retrieve the CSRF state parameter
    $state = isset($_GET['state']) ? $_GET['state'] : null;

    // This was a callback request from linkedin, get the token
    $token = $linkedinService->requestAccessToken($_GET['code'], $state);

    // Send a request with it. Please note that XML is the default format.
    $objLinkedinOAuth2->arrResult = json_decode($linkedinService->request('/people/~?format=json'), true);
    $objLinkedinOAuth2->bSuccess=true;

    $objLinkedinOAuth2->arrResult = array_merge($objLinkedinOAuth2->arrResult, array("picture"=>json_decode($linkedinService->request('/people/~/picture-urls::(original)?format=json'),true)));
    //$objLinkedinOAuth2->arrResult = array_merge($objLinkedinOAuth2->arrResult, array("email"=>$linkedinService->request('/people/~:(email-address,location)?format=json')));
    $objLinkedinOAuth2->arrResult = array_merge($objLinkedinOAuth2->arrResult, json_decode($linkedinService->request('/people/~:(email-address,location)?format=json'),true));
    //$objLinkedinOAuth2->arrResult = array_merge($objLinkedinOAuth2->arrResult, array("location"=>$linkedinService->request('/people/~:(location)')));

    // Show some of the resultant data
    //echo 'Your linkedin first name is ' . $result['firstName'] . ' and your last name is ' . $result['lastName'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $linkedinService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Linkedin!</a>";
}
