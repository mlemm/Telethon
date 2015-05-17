<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

//get a list of all pledges that have ok_to_publish as = 0
//loop thru them and return

$result = $wpdb->get_results( "SELECT * FROM pledge_batch WHERE ok_to_publish=0", ARRAY_A );

if(count($result) > 0) {
	$returnTable = "<table class='table' id='openBatches'><thead><tr><th>Count</th><th>Batch #</th><th>Sub-Total</th><th>Total</th><th>Pledges</th><th>Author</th></tr></thead><tbody>";
	for($c = 0; $c < count($result); $c++) {
		$theBatchID = $result[$c]['id']; //get the id of the pledgebatch
		$pledgeQuery = $wpdb->get_results("SELECT COUNT(*) AS pledgeCount FROM pledge WHERE batch_id=" . $theBatchID , ARRAY_A); //count all the pledges that have pledgebatch id
		$pledge_count = $pledgeQuery[$c]['pledgeCount'];
		$pledgeQuery = $wpdb->get_results("SELECT SUM(amount) AS pledgeamount FROM pledge WHERE batch_id=" . $theBatchID , ARRAY_A); //add all the pledges that have pledgebatch id
		$pledge_so_far = $pledgeQuery[$c]['pledgeamount'];
		if($pledge_so_far > $result[$c]['amount_from_finance']) {
			//over the limit
			$returnTable .= "<tr bgcolor='#FF0000'><th scope='row'>" . ($c+1) ."</th><td>" . $result[$c]['batch_number']. "</td><td>$" . number_format($pledge_so_far, 2,'.', ',') . "</td><td>$" . number_format($result[$c]['amount_from_finance'], 2,'.', ',') . "</td><td>" . $pledge_count . "</td><td>" . get_userdata($result[$c]['wp_userid'])->user_login . "</td></tr>";
		} else {
			$returnTable .= "<tr><th scope='row'>" . ($c+1) ."</th><td>" . $result[$c]['batch_number']. "</td><td>$" . number_format($pledge_so_far, 2,'.', ',') . "</td><td>$" . number_format($result[$c]['amount_from_finance'], 2,'.', ',') . "</td><td>" . $pledge_count . "</td><td>" . get_userdata($result[$c]['wp_userid'])->user_login . "</td></tr>";
		}
	}
	$returnTable .= "</tbody></table>";
} else {
	$returnTable = "Nothing here";
}

echo $returnTable;

?>

