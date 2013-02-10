<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*request_token_secret27c390fe-efb4-4220-8fd3-4eb50eb3ee4d
laccess_oauth_tokene3d8962e-853f-452f-a477-f52df9ad4bc7
laccess_oauth_token_secret4a46a7b2-44cc-49f3-9b15-9f440c377407
loauth_verifier81211
*/


function session_z_begin(){
    /* session_cache_limiter('private'); 
    $cache_limiter = session_cache_limiter();
    would be nice to have this, but have to look into conflicts per manual page with cache limitations
    echo 'session status:'.session_status(); unfortunately =>php5.4
    */

    ini_set('session.use_trans_sid', false);
    ////trans_sid sends session variables as url parameters when cookies disabled, 
    //so copied URLs can include session parameters, bad for security
    session_cache_expire(10);

    $session_start = session_start();
    $_SESSION["Z authd"] = TRUE;
    return $session_start;

}






?>
