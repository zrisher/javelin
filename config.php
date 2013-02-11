<?php 

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$config['urls']['protocol'] = 'http://';
$config['urls']['server'] = $_SERVER['SERVER_NAME'];//'avarice.elasticbeanstalk.com';
$config['urls']['script_dir'] = dirname($_SERVER['PHP_SELF']);
$config['urls']['redirect'] = $config['urls']['protocol'].$config['urls']['server'].$config['urls']['script_dir']; 
$config['urls']['recovery_page'] = $config['urls']['redirect'];
$config['urls']['logout_page'] = $config['urls']['redirect'].'/logout.php';
$config['urls']['LI_people_search'] = 'http://api.linkedin.com/v1/people-search';

$config['oauth']['consumersecret']="ydLucJgDoiJp7owO";
$config['oauth']['consumerkey']="l7trmkggic9u";
//$config['oauth']['callbackurl']="http://avarice.elasticbeanstalk.com/avarice/linkedin_login_page.php";
$config['oauth']['requesttokenurl']="https://api.linkedin.com/uas/oauth/requestToken?scope=r_fullprofile+r_network";
$config['oauth']['accesstokenurl']="https://api.linkedin.com/uas/oauth/accessToken";
$config['oauth']['authurl']="https://api.linkedin.com/uas/oauth/authorize";

?>
