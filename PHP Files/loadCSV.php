<!DOCTYPE html>
<html lang="en-CA">
<head><title>CHEO Telethon | CSV Load Results</title></head>
<body>

<?php

//ini_set('display_errors', 'off');
//ini_set('html_errors', 0);
define('WP_MEMORY_LIMIT', '512M');

// ----------------------------------------------------------------------------------------------------
// - Error Reporting
// ----------------------------------------------------------------------------------------------------
//error_reporting(-1);

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

/*
Markus Lemm - diskdaddy.com - 260444 - April 28/2015

Var to keep track of errors

Error s
 

*/

//keep regions/titles/city/provinces
$regions = $wpdb->get_results( "SELECT name FROM region", ARRAY_A );
$regionsCount = count($regions);

$titles = $wpdb->get_results( "SELECT title FROM title", ARRAY_A );
$titlesCount = count($titles);

$city_province = $wpdb->get_results( "SELECT city,province FROM city_province", ARRAY_A );
$city_provinceCount = count($city_province);

//create a batch for all the inital information
$total_donate = 0.0;
$current_user = wp_get_current_user();
$theTimeStamp = date('Y-m-d H:i:s');
$createBatch = "INSERT INTO pledge_batch (batch_number, amount_from_finance, start_time, finish_time, ok_to_publish, pledge_batch_type, batch_date, wp_userid) VALUES ('10001', $total_donate, '$theTimeStamp', '$theTimeStamp', 1, 0, '$theTimeStamp',  $current_user->ID)";
$wpdb->query( $createBatch);
$pledge_batch_id = $wpdb->insert_id;



 //load file
 //loop thru file with array

$filename = basename($_FILES["pledgefile"]["name"]);

//check that file is csv
$fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
if( !($fileExtension == 'csv' || $fileExtension == 'CSV')) {
	echo "<p> not a csv file </p>";
	goto error;
}


$pledgefileHandle = fopen($_FILES["pledgefile"]["tmp_name"], 'r');

$aLine = fgetcsv($pledgefileHandle);


if(count($aLine) != 9) {
	fclose($pledgefileHandle);
	echo  "<p><font color='red'> not correct number of columns</font></p>";
	goto error;
}

//loop thru the array checking vars
$c = 2;
$uniquePledgeNumber = 1000000;
while( ($aLine = fgetcsv($pledgefileHandle)) !== FALSE ) {

	if(count($aLine) != 9) {
		fclose($pledgefileHandle);
		echo  "<p><font color='red'> not correct number of columns on line " . $c  . "</font></p>";
		goto error;
	}	

	//check regions
	$regionMatch = 24;
	if( strcmp($aLine[8], "" ) !== 0) { //make sure string is not null
		$matchFound = FALSE;
		for($i = 0; $i < $regionsCount; $i++) {
			if( strpos($aLine[8], $regions[$i]["name"]) !== FALSE ) { $matchFound = TRUE; $regionMatch = $i+1; }
		}

		if(!$matchFound) {
			//echo "<p> Line number " . $c . " region does not match any type!  Region is " . $aLine[8] . " It will become 24 - Unknown</p>";
			//this is dealt with later by searching for matching city/province
			$regionMatch = 24;
		}
	} else {
		//echo "<p> Line number " . $c . " region is missing - it will become 24 - Unknown and you will be able to associate later.</p>";
		$regionMatch = 24;
	}


	//check Titles
	$titleMatch = 11;
	if( strcmp($aLine[2], "" ) !== 0) { //make sure string is not null
		$matchFound = FALSE;
		for($i = 0; $i < $titlesCount; $i++) {
			if( strpos($aLine[2], $titles[$i]["title"]) !== FALSE ) { $matchFound = TRUE; $titleMatch = $i; }
		}

		if(!$matchFound) {
			//echo "<p> Line number " . $c . " title does not match any type!  Title is " . $aLine[2] . " It will become blank </p>"; 
		}
	}
	

	//check city & province
	$city_provinceMatch = -1;
	if( strcmp($aLine[6], "" ) === 0 || strcmp($aLine[7], "" ) === 0) { //make sure string is not null
		//echo "<p><font color='red'> Line number " . $c . " City or Province is blank - this will fail</font></p>";
	} else {
		$matchFound = FALSE;
		for($i = 0; $i < $city_provinceCount; $i++) {
			if( strcmp($aLine[6], $city_province[$i]["city"]) === 0 && strcmp($aLine[7], $city_province[$i]["province"]) === 0) { $matchFound = TRUE; $city_provinceMatch = $i; }
		}

		//if we didn't find an exact match then look for a similar match
		if(!$matchFound) {
			//attempt to find a similar city
			//loop thru the list and find the most similar
			$howSimilar = 0.0;
			$mostSimilar = -1;
			$diff = 0.1;
			$diff2 = 0.0;
			for($i = 0; $i < $city_provinceCount; $i++) {
				similar_text( $city_province[$i]["city"], $aLine[6], $diff);
				similar_text( $city_province[$i]["province"], $aLine[7], $diff2);
				if($diff > $howSimilar && $diff2 >= 81.0) {
					$howSimilar = $diff;
					$mostSimilar = $i;
				}
			}

			//now we have the most likely city check if province also matches
			
			if($howSimilar < 81.0) {
				//not a good match found -> add to db
				//echo "<p><font color='orange'> City -" . $aLine[6] . " / " . $aLine[7] . "-  Mismatch on Line " . $c . " ! Could not find a good match -> will add to Database.</font></p>";

			} else {
				//echo "<p> City/Province  -" . $aLine[6] . " / " . $aLine[7] .   "-  Mismatch on Line " . $c . " ! The most similar city is " . $city_province[$mostSimilar]["city"] . " / " . $city_province[$mostSimilar]["province"] .  " : This is what we will use.</p>";
				$aLine[6] = $city_province[$mostSimilar]["city"];
				$aLine[7] = $city_province[$mostSimilar]["province"];
				$city_provinceMatch = $mostSimilar;
			}


		}
	}

	// now lets add the info to the database
	//strip any $ or chars of numbers
	$theAmount = preg_replace("/[^0-9.]/", "",$aLine[1]);
	$aLine[3] = htmlspecialchars($aLine[3], ENT_QUOTES);
	$aLine[4] = htmlspecialchars($aLine[4], ENT_QUOTES);
	$aLine[5] = htmlspecialchars($aLine[5], ENT_QUOTES);
	$aLine[6] = htmlspecialchars($aLine[6], ENT_QUOTES);
	$aLine[7] = htmlspecialchars($aLine[7], ENT_QUOTES);

	//if lastname and organization name are blank then it will be anonymous

	if( ($aLine[4] == "" || $aLine[4] == " ") && ($aLine[5] == "" || $aLine[5] == " ") ) $anony = 0; else $anony = 1;
	$addToConstituent = "INSERT INTO constituent (amount, title_id, first_name, last_name, organization_name, city, province, region_id) VALUES ($theAmount, $titleMatch, '$aLine[3]', '$aLine[4]', '$aLine[5]', '$aLine[6]' , '$aLine[7]', $regionMatch )";
	$wpdb->query( $addToConstituent);
	$unique_ID = $wpdb->insert_id;
	if($wpdb->show_errors() === 0 || $wpdb->show_errors() === FALSE) echo "<p>" . $wpdb->show_errors();

	//now add the info to the pledge table - this will be
	$theTimeStamp = date('Y-m-d H:i:s');
	//we should have a unique pledge number for each pledge we add
	$addToPledge = "INSERT INTO pledge (pledge_number, amount, displayed, ok_to_publish, constituent_id, batch_id, pledge_date) VALUES ('$uniquePledgeNumber', $theAmount, 0, $anony, $unique_ID, $pledge_batch_id, '$theTimeStamp')";
	$uniquePledgeNumber++;
	$wpdb->query( $addToPledge);
	if($wpdb->show_errors() === 0 || $wpdb->show_errors() === FALSE) echo "<p>" . $wpdb->show_errors();

	//keep track of how much is donated
	$total_donate += $theAmount;

	$c++;
}

//update the total amount that was donated in the pledge batch
$updateBatch = "UPDATE pledge_batch SET amount_from_finance=$total_donate WHERE batch_number='10001'";
$wpdb->query( $updateBatch);

//need to go thru all the city/provinces and try to associate with a region
//first get all constituents that are 24
$result = $wpdb->get_results("SELECT * FROM constituent WHERE region_id=24", ARRAY_A);

//find an entry
for($d = 0; $d < count($result); $d++) {
	$theTestCity = $result[$d]['city'];
	$theTestProv = $result[$d]['province'];
	$returned = $wpdb->get_results("SELECT * FROM city_province WHERE city='$theTestCity' AND province='$theTestProv'", ARRAY_A);
	if(count($returned) > 0) {
		//we found another city/province that is not unknown
		$currentID = $result[$d]['id'];
		$newRegion = $returned[0]['region_id'];
		$wpdb->query("UPDATE constituent SET region_id=$newRegion WHERE id=$currentID");
		//echo "Pledge region was added - "
	}		
}


fclose($pledgefileHandle);

error:

?>

<p><a href="http://192.168.69.99/?page_id=390">Return to load telethon</a></p>

</body>
</html>

