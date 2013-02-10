<?php

/*
 * Direct users to this page to check they're logged into LinkedIn,
 * and provide the proper vars to SESSION to use oauth
 * Zach Risher, with a fine base from
 * http://hasin.me/2010/04/04/complete-oauth-script-for-twitter-and-linkedin-using-pecl-oauth-extension/
 * v2.0 - 9/24/2012
 */

/* Notes:
if(isset($_SESSION['oauth_laccess_token'])) {
    $access_token = $_SESSION['oauth_laccess_token'];
    $access_token_secret =$_SESSION['oauth_laccess_secret'];
    $oauth_verifier = $_SESSION['oauth_verifier'];
    $oauthc->setToken($access_token,$access_token_secret);
 * } 
 * 
 */

//errors
error_reporting(E_ALL); 
ini_set( 'display_errors','1');

//requires
require_once 'helper.php';
require 'config.php';
require 'linkedin_helper.php';

session_start();

//$callback_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];//better than PHP_SELF, no pathinfo
$callback_url = 'http://testredirect.com';

if (isset($_POST['redirect_url'])){
    $_SESSION['current_callback_url'] = $_POST['redirect_url'];
}

$oauthc = new OAuth(
    $config['oauth']['consumerkey'],
    $config['oauth']['consumersecret'],
    OAUTH_SIG_METHOD_HMACSHA1,
    OAUTH_AUTH_TYPE_AUTHORIZATION);

$oauthc->setNonce(rand());

if(empty($_SESSION['oauth_lrequest_secret'])) {
    //get the request token and store it
    try {
    $request_token_info = $oauthc->getRequestToken($config['oauth']['requesttokenurl'],$callback_url);}
    catch (exception $e){
        print_xml($oauthc->getLastResponse());
    }
    $_SESSION['oauth_lrequest_secret'] = $request_token_info['oauth_token_secret'];
    $_SESSION['oauth_lrequest_token'] = $request_token_info['oauth_token'];
    //forward user to authorize url
    header("Location: {$config['oauth']['authurl']}?oauth_token=".$request_token_info['oauth_token']);
}

if(empty($_SESSION['oauth_laccess_token'])) {
    //get the access token and store it
    $oauthc->setToken($_SESSION['oauth_lrequest_token'],$_SESSION['oauth_lrequest_secret']);
    //first used to be $_REQUEST['oauth_token'], any issues just using recently saved one instead of posted?
    $access_token_info = $oauthc->getAccessToken($config['oauth']['accesstokenurl']);
    $_SESSION['oauth_laccess_token']= $access_token_info['oauth_token'];
    $_SESSION['oauth_laccess_secret']= $access_token_info['oauth_token_secret'];
    $_SESSION['oauth_verifier'] = $_REQUEST['oauth_verifier'];
}

if (oauth_check_set() AND isset($_SESSION['current_callback_url'])){
    header("Location: ".$_SESSION['current_callback_url']);//forward user back to page that requested access
}
else{
    print_p('Error obtaining Oauth credentials.');
}    

?>
