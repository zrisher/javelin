<?php

/*
 * Takes search inputs from forms and delivers URL search strings to linkedin_helper
 * Zach Risher
 * v0.8 - 9/24/2012
 */

 //TODO: When you want to search for either of two terms, use facets. To find all people in the United States or France, you cannot do country-code=us,fr; instead, you need to do facet=location,us:0,fr:0.
 //TODO: what if posts are empty?  if(empty($person_attr_array))

//errors
error_reporting(E_ALL); 
ini_set( 'display_errors','1');

//requires
require 'config.php';

if ($_POST['searchtype']="custom"){


//encode search terms
$search_term_array = $_POST['searchAttr'];
foreach ($search_term_array as $key => $value){
    $search_term_array_parsed[] = rawurlencode($key).'='.rawurlencode($value);
}
$search_terms = implode("&", $search_term_array_parsed);

//encode field selectors
$person_attr_array = $_POST['personAttr'];
$return_fields = implode(",", $person_attr_array); 
  
//combine to form search url 
$search_url = $config['urls']['LI_people_search'].':(people('.$return_fields.'))?'.$search_terms.'&count=25';

echo '<p><pre>search url: '.$search_url."</pre></p>";

  

}



?>
