<?php

//This will check if there is an open batch connected to the current wordpress user id.  If so we get back a json object with batch number else batch number is null

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

//get the userid
$current_user = wp_get_current_user();

$theCurrentUserID = $current_user->ID;

//get a list of all pledges that have ok_to_publish as = 0 and match the current user id
//loop thru them and return

$result = $wpdb->get_results( "SELECT * FROM pledge_batch WHERE ok_to_publish=0 AND wp_userid=" . $theCurrentUserID, ARRAY_A );

//if we do not find any open batches for the current user then return batch_number as null
if(count($result) == 0) {
	// no open batches found
	$arr = array('batch_number' => 'null');
	echo json_encode($arr);
	return;
}

$batchID = $result[0]['id']; //need to get id from batch number

//there are some results so return the pledges to populate the table
$query = "SELECT COALESCE(SUM(amount),0) as pledgeTotal FROM pledge WHERE batch_id=" . $batchID;

$subtotalArray = $wpdb->get_results( $query, ARRAY_A );
$subtotal = $subtotalArray[0]['pledgeTotal'];


//return a json object with - 
// - batch_number
// - total 
// - subtotal

$arr = array('batch_number' => (string)$result[0]['batch_number'], 'total' => (string)$result[0]['amount_from_finance'], 'subtotal' => (string)$subtotal);
echo json_encode($arr);



?>

