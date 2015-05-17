<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

//return a list of all pledges by sort type


$sortBy = $_POST['sortBy'];
//echo 'Please Sort by : ' . $value;

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

//sort by selection - default is just leave it as it = Region
switch($sortBy) {
	case "Total":
		for($a = 0; $a < 24; $a++) {
			for($b = $a+1; $b < 25; $b++){
				if( $theInfo[$a][1] < $theInfo[$b][1]) {
					//swap them
					$x1 = $theInfo[$a][0]; $x2 = $theInfo[$a][1]; $x3 = $theInfo[$a][2]; $x4 = $theInfo[$a][3];
					$theInfo[$a][0] = $theInfo[$b][0]; $theInfo[$a][1] = $theInfo[$b][1]; $theInfo[$a][2] = $theInfo[$b][2]; $theInfo[$a][3] = $theInfo[$b][3];
					$theInfo[$b][0] = $x1; $theInfo[$b][1] = $x2; $theInfo[$b][2] = $x3; $theInfo[$b][3] = $x4;
				}
			}
		}
		break;
	case "Average":
	for($a = 0; $a < 24; $a++) {
			for($b = $a+1; $b < 25; $b++){
				if( $theInfo[$a][3] < $theInfo[$b][3]) {
					//swap them
					$x1 = $theInfo[$a][0]; $x2 = $theInfo[$a][1]; $x3 = $theInfo[$a][2]; $x4 = $theInfo[$a][3];
					$theInfo[$a][0] = $theInfo[$b][0]; $theInfo[$a][1] = $theInfo[$b][1]; $theInfo[$a][2] = $theInfo[$b][2]; $theInfo[$a][3] = $theInfo[$b][3];
					$theInfo[$b][0] = $x1; $theInfo[$b][1] = $x2; $theInfo[$b][2] = $x3; $theInfo[$b][3] = $x4;
				}
			}
		}
		break;
	case "Pledge Count":
	for($a = 0; $a < 24; $a++) {
			for($b = $a+1; $b < 25; $b++){
				if( $theInfo[$a][2] < $theInfo[$b][2]) {
					//swap them
					$x1 = $theInfo[$a][0]; $x2 = $theInfo[$a][1]; $x3 = $theInfo[$a][2]; $x4 = $theInfo[$a][3];
					$theInfo[$a][0] = $theInfo[$b][0]; $theInfo[$a][1] = $theInfo[$b][1]; $theInfo[$a][2] = $theInfo[$b][2]; $theInfo[$a][3] = $theInfo[$b][3];
					$theInfo[$b][0] = $x1; $theInfo[$b][1] = $x2; $theInfo[$b][2] = $x3; $theInfo[$b][3] = $x4;
				}
			}
		}
		break;
}


$returnTable = "<table class='table' id='summaryTable'><thead><tr><th>By " . $sortBy . "</th><th>Region</th><th>Total</th><th>Average</th><th>Pledge Count</th></tr></thead><tbody>";
//output the table
for($c = 0; $c < 25; $c++) {
	$returnTable .= "<tr><th scope='row'>" . ($c+1) . "</th><td>" . ($theInfo[$c][0]) . "</td><td>$" . number_format($theInfo[$c][1], 2,'.', ',') . "</td><td>$" . number_format($theInfo[$c][3], 2,'.', ',') . "</td><td>" . ($theInfo[$c][2]) . "</td></tr>";		
}
$returnTable .= "</tbody></table>";

echo $returnTable;

?>

