<?php

//This will find all open pledges and return them as a table

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;


$theBatchID = $_POST['ID'];

//get all the batches that are associated with that batchID then return

//get batch number from batch id
$result = $wpdb->get_results("SELECT * FROM pledge_batch WHERE batch_number='" . $theBatchID . "'", ARRAY_A);
$batchNumber = $result[0]['id'];

//count how many
$result = $wpdb->get_results("SELECT * FROM pledge WHERE batch_id=" . $batchNumber, ARRAY_A);
$numPledges = count($result);

//now we know how many pledges we have to loop thru

$returnPledges = '<div id="pledge_list">';


for($a = 0; $a < $numPledges; $a++) {
	$returnPledges .= "<a class='list-group-item' onClick='loadPledgeInfo(\"" . $result[$a]['pledge_number'] . "\"); return false;' href='#'>Pledge " . $result[$a]['pledge_number'] . ' for $' . $result[$a]['amount'] . ' </a>';
}
   
$returnPledges .= '</div>';

echo $returnPledges;

?>
