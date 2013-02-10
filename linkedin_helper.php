<?php

/*
 * Helper functions for LinkedIn API communication
 * Zach Risher
 * v1.0 - 9/24/2012
 */

//errors
error_reporting(E_ALL); 
ini_set( 'display_errors','1');

//requires
require_once 'helper.php';
require 'config.php';

function oauth_check_set(){
    if (    isset($_SESSION['oauth_lrequest_token']) AND
            isset($_SESSION['oauth_lrequest_secret']) AND
            isset($_SESSION['oauth_laccess_token']) AND
            isset($_SESSION['oauth_laccess_secret']) AND
            isset($_SESSION['oauth_verifier'])
            ){
        return TRUE;
    }
    else{
        return FALSE;
    }
}

function oauth_form_search_url($searchtype, $search_term_array, $person_attr_array){
    require 'config.php';
    
    //$searchtype = $_POST['searchtype'];
    if ($searchtype="custom"){
        //encode search terms
        //$search_term_array = $_POST['searchAttr'];
        foreach ($search_term_array as $key => $value){
            $search_term_array_parsed[] = rawurlencode($key).'='.rawurlencode($value);
        }
        $search_terms = implode("&", $search_term_array_parsed);

        //encode field selectors
        //$person_attr_array = $_POST['personAttr'];
        $return_fields = implode(",", $person_attr_array); 

        //combine to form search url 
        $search_url = $config['urls']['LI_people_search'].':(people:('.$return_fields.'),facets:(code,name,buckets:(code,name,count)),num-results)?'.$search_terms.
                '&facets=location,industry,network,language,current-company,past-company,school';

        //echo '<p><pre>search url: '.$search_url."</pre></p>";
        return $search_url;
    }

}

function oauth_request_xml($url){
    
    if (!oauth_check_set()){
        exit('oauth_request_xml: Oauth details were not set, exiting.');
    }
       
    $oauth_vars['consumersecret']="ydLucJgDoiJp7owO";
    $oauth_vars['consumerkey']="l7trmkggic9u";

    $oauthc = new OAuth(
        $oauth_vars['consumerkey'],
        $oauth_vars['consumersecret'],
        OAUTH_SIG_METHOD_HMACSHA1,
        OAUTH_AUTH_TYPE_AUTHORIZATION); //initiate
    $oauthc->setNonce(rand());
    $oauthc->setToken($_SESSION['oauth_laccess_token'],
        $_SESSION['oauth_laccess_secret']);

    $api_url = $url;//.'&facet=network,F';//
    echo '<p> Oauth info set. Requesting initial info from '. $api_url .'</p>' ;
    try{
    $oauthc->fetch($api_url,null, OAUTH_HTTP_METHOD_GET);
    $cur_response = $oauthc->getLastResponse();
    $cur_response_sxml = simplexml_load_string($cur_response);
    $total_people = $cur_response_sxml->people['total'];
    $num_results = $cur_response_sxml->{'num-results'};
    echo '<p> LinkedIn responded to query with '.$num_results.' total results, iterating over the '.$total_people.' available. </p>';
    //print_xml($cur_response);

    $iteration_size=25; //this is the max, unfortunately
    $cur_lbound = 0;
    $cur_ubound = $iteration_size - 1;


    while ($cur_lbound<=$total_people){

        $api_url = $url.'&start='.$cur_lbound.'&count='.$iteration_size;
        //echo '<p> Iterating from '.$cur_lbound.' to '.$cur_ubound;//.'. URL '.$api_url.'</p>';
        $oauthc->fetch($api_url,null, OAUTH_HTTP_METHOD_GET);
        $cur_response= $oauthc->getLastResponse();
        $cur_response_sxml = simplexml_load_string($cur_response);

        if ($cur_lbound == 0) {
            $aggregate_response_sxml = $cur_response_sxml;
            }
        else {
            Foreach ($cur_response_sxml->people->person as $person){
                mergexml1($aggregate_response_sxml->people,$person);
                }      
            }


        $cur_lbound += $iteration_size;
        $cur_ubound += $iteration_size;
    }
    print_p('Iteration complete');
    }
    catch (Exception $e)
    {
        echo 'Error retrieving info from LinkedIn. Oauth response was:';
        print_xml($oauthc->getLastResponse());
    }



    $output = fopen("output.csv", "w");
    if ( $output ) {
        $first_person = $aggregate_response_sxml->people->person[0];
        if (isset($first_person)){
            //print_p('$Aggregate response:');
            //print_xml($aggregate_response_sxml->asXML());
            $c=fputcsv($output,array_keys(
                    sxml_person_to_array($aggregate_response_sxml->people->person[0])
                    ));
                foreach ($aggregate_response_sxml->people->person as $person) {
                    //prep output
                   $output_to_write = array(
                       //'algovalue' => 'algo value',
                       //'company' => 'company',
                   );
                   $output_line = array_merge($output_to_write,
                           sxml_person_to_array($person)
                           );
                   $c = $c + fputcsv($output, $output_line,',','"');
                }
            echo '<p> <a href=output.csv >Download Output File</a> </p>';
            echo $c, ' bytes written'; 
        }
        else {echo "No data returned.";}
        }
    else{        echo "Failed to open output.csv";    }
    fclose($output);
    /*
    global $encountered_attrs;
    print_p('Encountered attributes:');
    print_p_array($encountered_attrs);
    */
    
    

    
    
}

$single_line_attrs = array(
    'first-name'    => 1,
    'last-name'     => 1,
    'headline'      => 1,
    'industry'      => 1,
    'num-connections' => 1,
    'summary' => 1,
    'picture-url'   => 1,
    'public-profile-url' => 1,
    'num-recommenders' => 0,
    /*
    'locastion'     => 2,
    'relation to viewer',
    'positions',
    'three-current-positions'
     */
);

$encountered_attrs = array();

function sxml_person_to_array($person){
    global $single_line_attrs;
    global $encountered_attrs;
    
    $return_values['first-name'] = '';
    $return_values['last-name'] = '';
    $return_values['headline'] = '';
    $return_values['location'] = '';
    $return_values['industry'] = '';
    $return_values['relation-to-viewer'] = '';
    $return_values['num-connections'] = '';
    $return_values['summary'] = '';
    $return_values['positions'] = '';
    $return_values['picture-url'] = '';
    $return_values['public-profile-url'] = '';
    
    foreach($person->children() as $attr){
        
        $attr_name = $attr->getName();
        
        
        if (isset($encountered_attrs[$attr_name]))
            {
            $encountered_attrs[$attr_name] = $encountered_attrs[$attr_name] + 1;
            }
        else
        {
            $encountered_attrs[$attr_name] = 1;
        }
        
       

        //echo ' attr_name is '.$attr_name;
        //if ($attr_name = 'summary') {echo 'Encountered summary.';}
        if (isset($single_line_attrs[$attr_name]))
            {
            //echo 'added single line attr';
            $return_values[$attr_name] = $attr;
            //if ($attr_name = 'summary') {echo 'summary written.';}
            }
        elseif ($attr_name == 'location')
            {
            //echo 'added location';
            $return_values[$attr_name] = $attr->country->code.': '.$attr->name;
            }
        elseif ($attr_name == 'relation-to-viewer')
            {
            //echo 'added relation';
            $return_values[$attr_name] = $attr->distance;
            }
        elseif ($attr_name == 'positions' OR $attr_name=='three-current-positions')
            {
            //echo 'added positions';
            $return_values[$attr_name] = $attr['total'].' positions:';
            foreach ($attr->position as $position)
                {
                    $return_values[$attr_name] = $return_values[$attr_name].'('.
                            $position->title.' @ '.$position->company->name.'),';
                }
            }
        else{
            print_p('Encountered attribute for which we have no handling: '.$attr_name);
        }
            
        }
            
    return $return_values;
}

function xml_peoplesearch_to_element_array($person_search_sxml){
    
    //parse num results
    
    //parse people search
        //parse people
        //parse facets
    
    //return ((numresults, facets)
}

?>


