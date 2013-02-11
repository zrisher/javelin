<?php

/*
 * Index for LinkedIn API Tool
 * Zach Risher
 * v1.0 9/24/2012
 */

error_reporting(E_ALL); 
ini_set( 'display_errors','1');

require_once 'helper.php';
require 'session_manager.php';
require 'linkedin_helper.php';
require 'config.php';

session_z_begin()
//session_start();


?>

<!DOCTYPE html>
<html>
    <head>
        
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        
    </head>
    
    <body>
        
        <?php 
        /*
        print_p('server name is '.$_SERVER['SERVER_NAME']);
        print_p('script name is '.$_SERVER['SCRIPT_NAME']); 
        print_p('file path name is '.dirname(__FILE__));
        print_p('http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
         */
        print_p($config['urls']['redirect']);
        
        ?>
        
        <h1>The Tool</h1>
        
        <div>
            <p>LinkedIn Session:</p>
            
            <?php 
            if(!oauth_check_set()){
                echo '
                <p>Please log in to LinkedIn to use this tool.</p>
                <form action="linkedin_login_page.php" method="post">
                <input type="hidden" name="redirect_url" value="" />
                <input type="submit" name="formSubmit" value="Log into LinkedIn" />
                </form>
                ';
            }
            else{
                echo '<p>You are logged into LinkedIn.</p>';  
                
                //logout button
                echo '<form action="';
                echo $config['urls']['logout_page'];
                echo '"method="post">
                <input type="submit" name="formSubmit" value="Logout" />
                </form>
                ';

                if (isset($_POST['searchtype'])){
                 $searchtype = $_POST['searchtype'];
                 $search_term_array = $_POST['searchAttr'];
                 $person_attr_array = $_POST['personAttr'];
                $search_url = oauth_form_search_url($searchtype, $search_term_array, $person_attr_array);
                echo 'Performing search.';//.$search_url;
                oauth_request_xml($search_url);
                }
                
                /*
                $search_url = 'http://api.linkedin.com/v1/people-search:(people:(id,first-name,last-name,picture-url,headline),num-results)';
                echo 'Performing search on '.$search_url;
                oauth_request_xml($search_url);
                
                $search_url = 'http://api.linkedin.com/v1/people-search?company-name=google';
                echo 'Performing search on '.$search_url;
                oauth_request_xml($search_url);
                */
                
                //search options
                echo file_get_contents('custom_search_form.html');
                

            }
            ?>
            
        </div>
        

    </body>
    
</html>
