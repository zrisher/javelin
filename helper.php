<?php

/*
 * General Helper functions
 * Zach Risher 9/14/2012
 */

error_reporting(E_ALL); 
ini_set( 'display_errors','1');

function print_xml($xml){
    echo '<pre>'; 
    print_r(htmlspecialchars($xml));
    echo '</pre>';  
}

function print_p($string){
    echo '<p>'.$string.'</p>';
}

function print_p_array($args){
    foreach ($args as $key => $value){
        try{
            print_p($key.' '.$value);
        }
        catch(Exception $e){
            print_p("Print_p_array: Couldn't print key or value.");
        }
    }
}

function mergeXML1(&$base, $add) { 
    
    //Take $add node, add as child to current $base node
    //If no further children add content too
    $add_name = $add->getName();
    if ( $add->count() != 0 ) {
        $new = $base->addChild($add_name); 
    }
    else{
        $new = $base->$add_name = $add; // escaping &'s
        //$new = $base->addChild($add_name, $add); 
    }
    
    //copy all attributes for the node too
    foreach ($add->attributes() as $a => $b){
        if (isset($new[$a])){ 
                if ($a == 'total' AND $b == '0')
                {
                    //seems like simplXML includes this by default
                }
                else{
                    print_p('Unexpected SimpleXML merging error.');
                    //print_p('attribute '.$a.' => '.$b.' added to base');
                    //print_xml($base->asxml());
                    //print_p('from add');
                    //print_xml($add->asxml());
                }
        }
        else{
        $new->addAttribute($a, $b); 
        }
    } 
    
    if ( $add->count() != 0 ) //neccesary?
    { 
        foreach ($add->children() as $child) 
        { 
            mergeXML1($new, $child); 
        } 
    } 
} 



