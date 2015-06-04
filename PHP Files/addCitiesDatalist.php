<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;
//get all the cites from the database

//need to get batch id from the batch number
$result = $wpdb->get_results( "SELECT city FROM city_province", ARRAY_A );

$returnList = "";

//loop thru cities and add to options list
for($c = 0; $c < count($result); $c++) {
	$returnList .= '<option value="' . $result[$c]['city'] .'"></option>';
}


echo $returnList;

?>

