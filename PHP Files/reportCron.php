<?php

require_once  $_SERVER["DOCUMENT_ROOT"]."/wp-load.php";
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

$actions = $_POST['actions'];





function Disk_Daddy_Report() {

	//Either enable the cron or disable cron or generate report

   	global $wpdb;
    date_default_timezone_set("EST5EDT");
    $reportName = "Report " . date("Y-m-d Hi");
            
    //get total pledges
    $totalDonated = $wpdb->get_results("SELECT SUM(amount) AS total_donated FROM pledge", ARRAY_A);
            	$reportContent = "<h2>Pledge Summary Report</h2>";
            	$reportContent .= "<h1>Total Pledged : $" . number_format($totalDonated[0]['total_donated'], 2,'.', ',') . "</h1>";
            

//return a list of all pledges by region
$theInfo = array();

//get all the data and put it into an array
for($c = 1; $c <= 25; $c++) {

	$result = $wpdb->get_results( "SELECT name FROM region WHERE id=$c", ARRAY_A );
	$regionName = $result[0]['name'];

	$result = $wpdb->get_results( "SELECT SUM(amount) AS total_donated FROM constituent WHERE region_id=$c", ARRAY_A );
	$TotalDonated = $result[0]['total_donated'];

	$result = $wpdb->get_results( "SELECT COUNT(amount) FROM constituent WHERE region_id=$c", ARRAY_A );
	$TotalDonations = $result[0]["COUNT(amount)"];

	array_push($theInfo, array($regionName, $TotalDonated, $TotalDonations, ($TotalDonated/$TotalDonations)));

}


$reportContent .= "<table class='table' id='summaryTable'><thead><tr><th>By Region</th><th>Region</th><th>Total</th><th>Average</th><th>Pledge Count</th></tr></thead><tbody>";
//output the table
for($c = 0; $c < 25; $c++) {
	$reportContent .= "<tr><th scope='row'>" . ($c+1) . "</th><td>" . ($theInfo[$c][0]) . "</td><td>$" . number_format($theInfo[$c][1], 2,'.', ',') . "</td><td>$" . number_format($theInfo[$c][3], 2,'.', ',') . "</td><td>" . ($theInfo[$c][2]) . "</td></tr>";		
}
$reportContent .= "</tbody></table>";
            
            //open pledge summery
            $reportContent .= "<p><h2>Open Batch Summary</h2><p>";
            
            
            $result = $wpdb->get_results( "SELECT * FROM pledge_batch WHERE ok_to_publish=0", ARRAY_A );

if(count($result) > 0) {
	$returnTable = "<table class='table' id='openBatches'><thead><tr><th>Count</th><th>Batch #</th><th>Sub-Total</th><th>Total</th><th>Pledges</th><th>Author</th></tr></thead><tbody>";
	for($c = 0; $c < count($result); $c++) {
		$theBatchID = $result[$c]['id']; //get the id of the pledgebatch
		$pledgeQuery = $wpdb->get_results("SELECT COUNT(*) AS pledgeCount FROM pledge WHERE batch_id=" . $theBatchID , ARRAY_A); //count all the pledges that have pledgebatch id
		$pledge_count = $pledgeQuery[0]['pledgeCount'];
		$pledgeQuery = $wpdb->get_results("SELECT SUM(amount) AS pledgeamount FROM pledge WHERE batch_id=" . $theBatchID , ARRAY_A); //add all the pledges that have pledgebatch id
		$pledge_so_far = $pledgeQuery[0]['pledgeamount'];
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

$reportContent .= $returnTable;
            
            //closed pledge summary
            $reportContent .= "<p><h2>Closed Batch Summary</h2><p>";
            
            $result = $wpdb->get_results( "SELECT * FROM pledge_batch WHERE ok_to_publish=1", ARRAY_A );

if(count($result) > 0) {
	
	$returnTable = "<table class='table' id='closedBatches'><thead><tr><th>Count</th><th>Batch #</th><th>Total</th><th>Pledges</th><th>Author</th><th>Batch</th></tr></thead><tbody>";
	for($c = 0; $c < count($result); $c++) {

		$theBatchID = $result[$c]['id']; //get the id of the pledgebatch
		$pledgeQuery = $wpdb->get_results("SELECT COUNT(*) AS pledgeCount FROM pledge WHERE batch_id=" . $theBatchID , ARRAY_A); //count all the pledges that have pledgebatch id
		$pledge_count = $pledgeQuery[0]['pledgeCount'];
		$batch_type = $result[$c]['pledge_batch_type'];
		switch($batch_type) {
			case 0:
			$batch_name = "mail";
			break;
			case 1:
			$batch_name = "phone";
			break;
			case 2:
			$batch_name = "special";
			break;
			default:
			$batch_name = "online";
		}
		
		$returnTable .= "<tr><th scope='row'>" . ($c+1) ."</th><td>" . $result[$c]['batch_number']. "</td><td>$" . number_format($result[$c]['amount_from_finance'], 2,'.', ',') . "</td><td>" . $pledge_count . "</td><td>" . get_userdata($result[$c]['wp_userid'])->user_login . "</td><td>" . $batch_name . "</td></tr>";
	
	}
	$returnTable .= "</tbody></table>";
} else {
	$returnTable = "Nothing here";
}

$reportContent .= $returnTable;
            


	
	
            //generate the content
            
            $my_post = array(
  						'post_title'    => $reportName,
  						'post_content'  => $reportContent,
  						'post_status'   => 'publish',
  						'post_author'   => 4  						
						);

						// Insert the post into the database
						wp_insert_post( $my_post );
}


//we will always generate a report
Disk_Daddy_Report();

if($actions == "report") { echo "Report Generated"; }
/*
if($actions == "enable") { if (!wp_next_scheduled('Disk_Daddy_Report_hook')) { wp_schedule_event( time(), 'hourly', 'Disk_Daddy_Report_hook' );} echo "Auto Generation Enabled"; }
if($actions == "disable") { wp_clear_scheduled_hook('Disk_Daddy_Report_hook'); echo "Auto Generation Disabled"; }

add_action ( 'Disk_Daddy_Report_hook', 'Disk_Daddy_Report' );
*/

return;

?>

