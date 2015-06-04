<?php

//Create a New Batch -> I assume that all fields have been validated before this is called

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

//get the userid
$current_user = wp_get_current_user();

$theCurrentUserID = $current_user->ID;

//get the batch ID to create
$batch_ID = $_POST['ID'];
$amount_of_pledges = intval($_POST['finance']);
$batch_type = intval($_POST['batchtype']);

//make sure the id does not exist then add the batch
//pledge_batch->batch_number must be unique
$query = "SELECT * FROM pledge_batch WHERE batch_number='" . $batch_ID . "'";
$pledgeQuery = $wpdb->get_results($query, ARRAY_A);

if(count($pledgeQuery)) {
	echo 'Not Unique';
	return;
}


//get a list of all pledges that have ok_to_publish as = 0 and match the current user id
//loop thru them and return
$theTimeStamp = date('Y-m-d H:i:s');
$query = "INSERT INTO pledge_batch (batch_number, amount_from_finance, start_time, ok_to_publish, pledge_batch_type, batch_date, wp_userid) VALUES ('$batch_ID', $amount_of_pledges, '$theTimeStamp', 0, $batch_type, '$theTimeStamp',  $theCurrentUserID)";
$result = $wpdb->query($query);

if($result == 1) {
	echo 'success';
} else {
	echo 'error';
}

return;

?>

