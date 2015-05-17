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

//make sure the id does not exist then add the batch


//get a list of all pledges that have ok_to_publish as = 0 and match the current user id
//loop thru them and return
$theTimeStamp = date('Y-m-d H:i:s');
$query = "INSERT INTO pledge_batch (batch_number, amount_from_finance, start_time, ok_to_publish, pledge_batch_type, batch_date, wp_userid) VALUES ('$batch_ID', $amount_of_pledges, '$theTimeStamp', 0, 1, '$theTimeStamp',  $theCurrentUserID)";
$result = $wpdb->query($query);

if($result == 1) {
	echo 'Batch ' . $batch_ID . ' created successfully.  Please add pledges.';
} else {
	echo 'Batch could not be created. Contact Tech support';
}


?>

