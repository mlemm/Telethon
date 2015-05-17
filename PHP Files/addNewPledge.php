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


$amount = $_POST['amount'];
$title = $_POST['title'];
$first = $_POST['first_name'];
$last = $_POST['last_name'];
$org = $_POST['organization'];
$city = $_POST['city'];
$province = $_POST['province'];
$ok = $_POST['ok_to_display'];
$batch_number = $_POST['batch_num'];
$pledge_number = $_POST['pledge_num'];

//need to get batch id from the batch number
$result = $wpdb->get_results( "SELECT id,amount_from_finance FROM pledge_batch WHERE batch_number='" . $batch_number . "'", ARRAY_A );
$batch_id = $result[0]['id'];
$finance = $result[0]['amount_from_finance'];



//add constituent

$theTimeStamp = date('Y-m-d H:i:s');
$query = "INSERT INTO constituent (amount, title_id, first_name, last_name, organization_name, city, province, region_id, last_modified) VALUES ($amount, 1, '$first', '$last', '$org', '$city', '$province', 1, '$theTimeStamp')";
$result = $wpdb->query($query);
if($result != 1)  goto error;
$constituentID = $wpdb->insert_id;

//just get the data and add it to the pledges list
$query = "INSERT INTO pledge (pledge_number, amount, displayed, ok_to_publish, constituent_id, batch_id, pledge_date, last_modified) VALUES ('$pledge_number', $amount, 0, $ok, $constituentID, $batch_id, '$theTimeStamp', '$theTimeStamp')";
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


//pledge was added return json with new subtotal
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
$returnArr = array('error' => 'batchOverFull');
echo json_encode($returnArr);
return;



error:
$returnArr = array('error' => 'Other Error');
echo json_encode($returnArr);
return;


?>

