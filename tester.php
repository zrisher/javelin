<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL); 
ini_set( 'display_errors','1');
require 'helper.php';

 // Tests for XML aggregation
function test_xmlagg(){
$xmlstr1 = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<people-search>
  <people total="3" count="25" start="25">
    <person>
      <id>RHrEnHsSqb1</id>
      <first-name>Vinay1</first-name>
      <last-name>Shah1</last-name>
    </person>
    <person>
      <id>_gotZuABGK1</id>
      <first-name>Teresa1</first-name>
      <last-name>Fok1</last-name>
    </person>
    <person>
      <id>YBnQaTIug_1</id>
      <first-name>Lisa1</first-name>
      <last-name>Steckmest1</last-name>
    </person>
  </people>
  <num-results>195</num-results>
</people-search>
XML;

$xmlstr2 = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<people-search>
  <people total="3" count="25" start="25">
    <person>
      <id>RHrEnHsSqb2</id>
      <first-name>Vinay2</first-name>
      <last-name>Shah2</last-name>
    </person>
    <person>
      <id>_gotZuABGK2</id>
      <first-name>Teresa2</first-name>
      <last-name>Fok2</last-name>
    </person>
    <person>
      <id>YBnQaTIug_2</id>
      <first-name>Lisa2</first-name>
      <last-name>Steckmest2</last-name>
    </person>
  </people>
  <num-results>195</num-results>
</people-search>
XML;



print_p('original strings:');
$sxmlstr1 = simplexml_load_string($xmlstr1);
$sxmlstr2 = simplexml_load_string($xmlstr2);
print_xml($sxmlstr1->asxml());
print_xml($sxmlstr2->asxml());
print_xml($sxmlstr1->people->asxml());
print_xml($sxmlstr2->people->asxml());
foreach ($sxmlstr1->people->person as $person){
    print_p(get_object_vars($person));
}

print_p('merginged1 strings:');
Foreach ($sxmlstr2->people->person as $person){
mergexml1($sxmlstr1->people,$person);
}
print_xml($sxmlstr1->asxml());
print_xml($sxmlstr2->asxml());
foreach ($sxmlstr1->people->person as $person){
    print_p( print_r(get_object_vars($person) ));
}
}

?>
