<?php

/*
 * Logout page
 * Zach Risher
 * v1.0 - 9/24/2012
 */

//errors
error_reporting(E_ALL); 
ini_set( 'display_errors','1');

//requires
require 'config.php';

// Initialize the session.
// If you are using session_name("something"), don't forget it now!
session_start();

// Unset all of the session variables.
$_SESSION = array();
//session_unset();
/*
unset($_SESSION['oauth_token_secret']);
unset($_SESSION['lrequest_token_secret']);
unset($_SESSION['laccess_oauth_token']);
unset($_SESSION['laccess_oauth_token_secret']);
unset($_SESSION['loauth_verifier']);
*/   

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $cur_cookie = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $cur_cookie["path"], $cur_cookie["domain"],
        $cur_cookie["secure"], $cur_cookie["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

//redirect back to calling page, or base if none set
if (isSet($_POST['redirect_url'])){
    header("Location: ".$_POST['redirect_url']);
}   
elseif (isSet($config['urls']['recovery_page'])){
    header("Location: ".$config['urls']['recovery_page']);
}
else{
    echo '<p> Logout complete, but no redirect page was provided. </p>';
}

    
?>
