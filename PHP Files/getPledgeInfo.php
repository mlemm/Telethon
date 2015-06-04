<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;



$pledge_number = $_POST['pledge_num'];

//get pledge info
//if 0 returned then could not be found else return info


//need to check if pledge number is unique
$query = "SELECT * FROM pledge WHERE pledge_number='" . $pledge_number ."'";
$result = $wpdb->get_results( $query , ARRAY_A );
//if we do not find any open batches for the current user then return batch_number as null
if(count($result) == 0) {
	// there are no pledges with this number
	$arr = array('error' => 'notexist');
	echo json_encode($arr);
	return;
} 
//get consituent id so we get the info about the user

$consituentInfo = $wpdb->get_results( "SELECT * FROM constituent c LEFT JOIN title t ON (c.title_id=t.id) WHERE c.id=" . $result[0]['constituent_id'], ARRAY_A);

//if this fails then we have a db problem
if(count($consituentInfo) == 0) {
	$arr = array('error' => 'dberror');
	echo json_encode($arr);
	return;
}

//return the info
$arr = array('error' => 'null', 'amount' => $result[0]['amount'], 'first' => $consituentInfo[0]['first_name'], 'last' => $consituentInfo[0]['last_name'], 'org' => $consituentInfo[0]['organization'], 'city' => $consituentInfo[0]['city'], 'prov' => $consituentInfo[0]['province'], 'theTitle' => $consituentInfo[0]['title'], 'anon' => $result[0]['ok_to_publish']);
echo json_encode($arr);
return;


?>

