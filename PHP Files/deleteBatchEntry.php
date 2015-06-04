<?php

//This will check if there is an open batch connected to the current wordpress user id.  If so we get back a json object with batch number else batch number is null

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

$batch_num = $_POST['batchNum'];


//need to get batch id from the batch number
$result = $wpdb->get_results( "SELECT * FROM pledge_batch WHERE batch_number='" . $batch_num . "'", ARRAY_A );
$batch_id = $result[0]['id'];

//get  all pledges with the batch_id
$query = "SELECT * FROM pledge WHERE batch_id=" . $batch_id;
$result = $wpdb->get_results( $query , ARRAY_A );

for($c = 0; $c < count($result); $c++) {
	//get constituent id
	$conID = $result[$c]['constituent_id'];
	//delete the constituent
	$query = "DELETE FROM constituent WHERE id=" . $conID;
	$wpdb->query($query);
}

//delete all pledges
$query = "DELETE FROM pledge WHERE batch_id=" . $batch_id;
$wpdb->query( $query );


//delete the batch
$query = "DELETE FROM pledge_batch WHERE batch_number='" . $batch_num ."'";
$wpdb->query( $query );

echo 'success';
return;


/*
//get the batch number and get pledges from that id
$result = $wpdb->get_results( "SELECT * FROM pledge_batch WHERE batch_number='" . $batch_num . "'", ARRAY_A);

//if we do not find any open batches for the current user then return batch_number as null
if(count($result) == 0) {
	// no batches found
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
*/


?>

