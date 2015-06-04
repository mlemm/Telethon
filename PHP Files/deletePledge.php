<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

//get the userid
$current_user = wp_get_current_user();

$theCurrentUserID = $current_user->ID;

//we assume that all the data passed has been validated for by javascript

//collect data
//amount, title, first name, last name, organization, city, province, anonymous
//batch #, 

$batch_number = $_POST['batch_num'];
$pledge_number = $_POST['pledge_num'];

//need to get batch id from the batch number
$result = $wpdb->get_results( "SELECT id,amount_from_finance FROM pledge_batch WHERE batch_number='" . $batch_number . "'", ARRAY_A );
$batch_id = $result[0]['id'];
$finance = $result[0]['amount_from_finance'];

//need to check if pledge number already exists
$query = "SELECT * FROM pledge WHERE pledge_number='" . $pledge_number ."'";
$result = $wpdb->get_results( $query , ARRAY_A );

if(count($result) == 0) {
	// this pledge does not exists
	$arr = array('error' => 'notexist');
	echo json_encode($arr);
	return;
}

//first we need to get the constituent_id from the pledge
$conID = $result[0]['constituent_id'];

//consitiuent already exists so just delete

$query = "DELETE FROM constituent WHERE id=" . $conID;
$result = $wpdb->query($query);
if($result != 1) goto error;
	

//just delete the pledge
$query = "DELETE FROM pledge WHERE constituent_id=" . $conID;
$result = $wpdb->query($query);
if($result != 1) goto error;

//check if batch is now finished by comparing amount from finance to total donated
$query = $wpdb->get_results("SELECT SUM(amount) AS pledgeamount FROM pledge WHERE batch_id=" . $batch_id , ARRAY_A); //add all the pledges that have pledgebatch id
$donated_so_far = $query[0]['pledgeamount'];

if($donated_so_far == $finance) {
	goto batchFull;
}

if($donated_so_far > $finance) {
	goto batchOverFull;
}


//test
//pledge was updated return json with new subtotal
//make sure to update the batch as not being finished (ok_to_publish = 0)
$query = "UPDATE pledge_batch SET ok_to_publish=0 WHERE batch_number='" . $batch_number ."'";
$result = $wpdb->query($query);
$returnArr = array('error' => 'null', 'subtotal' => (string)$donated_so_far);
echo json_encode($returnArr);
return;



batchFull:

//close batch
$query = "UPDATE pledge_batch SET ok_to_publish=1 WHERE batch_number='" . $batch_number ."'";
$result = $wpdb->query($query);
$returnArr = array('error' => 'batchFull');
echo json_encode($returnArr);
return;

batchOverFull:
$query = "UPDATE pledge_batch SET ok_to_publish=0 WHERE batch_number='" . $batch_number ."'";
$result = $wpdb->query($query);
$returnArr = array('error' => 'batchOverFull');
echo json_encode($returnArr);
return;



error:
$returnArr = array('error' => 'Other Error');
echo json_encode($returnArr);
return;


?>

