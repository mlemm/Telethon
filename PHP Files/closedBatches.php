<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

//get a list of all pledges that have ok_to_publish as = 0
//loop thru them and return

$result = $wpdb->get_results( "SELECT * FROM pledge_batch WHERE ok_to_publish=1", ARRAY_A );

if(count($result) > 0) {
	
	$returnTable = "<table class='table' id='closedBatches'><thead><tr><th>Count</th><th>Batch #</th><th>Total</th><th>Pledges</th><th>Author</th></tr></thead><tbody>";
	for($c = 0; $c < count($result); $c++) {

		$theBatchID = $result[$c]['id']; //get the id of the pledgebatch
		$pledgeQuery = $wpdb->get_results("SELECT COUNT(*) AS pledgeCount FROM pledge WHERE batch_id=" . $theBatchID , ARRAY_A); //count all the pledges that have pledgebatch id
		$pledge_count = $pledgeQuery[0]['pledgeCount'];
		$returnTable .= "<tr><th scope='row'>" . ($c+1) ."</th><td>" . $result[$c]['batch_number']. "</td><td>$" . number_format($result[$c]['amount_from_finance'], 2,'.', ',') . "</td><td>" . $pledge_count . "</td><td>" . get_userdata($result[$c]['wp_userid'])->user_login . "</td></tr>";
	
	}
	$returnTable .= "</tbody></table>";
} else {
	$returnTable = "Nothing here";
}

echo $returnTable;

?>

