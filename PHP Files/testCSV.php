<!DOCTYPE html>
<html lang="en-CA">
<head><title>CHEO Telethon | CSV Results</title></head>
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

/*
echo 'Region 1 is ' . $regions[0]["name"] . "<p>";
echo 'Title 1 is ' . $titles[0]["title"] . "<p>";
echo 'City 1 is ' . $city_province[0]["city"] . " in " . $city_province[0]["province"] . "<p>";

*/


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
while( ($aLine = fgetcsv($pledgefileHandle)) !== FALSE ) {

	if(count($aLine) != 9) {
		fclose($pledgefileHandle);
		echo  "<p><font color='red'> not correct number of columns on line " . $c  . "</font></p>";
		goto error;
	}	

	//check regions
	$regionMatch = -1;
	if( strcmp($aLine[8], "" ) !== 0) { //make sure string is not null
		$matchFound = FALSE;
		for($i = 0; $i < $regionsCount; $i++) {
			if( strpos($aLine[8], $regions[$i]["name"]) !== FALSE ) { $matchFound = TRUE; $regionMatch = $i; }
		}

		if(!$matchFound) {
			echo "<p> Line number " . $c . " region does not match any type!  Region is " . $aLine[8] . " It will become 24 - Unknown</p>"; 
		}
	} else {
		echo "<p> Line number " . $c . " region is missing - it will become 24 - Unknown and you will be able to associate later.</p>";
	}


	//check Titles
	if( strcmp($aLine[2], "" ) !== 0) { //make sure string is not null
		$matchFound = FALSE;
		for($i = 0; $i < $titlesCount; $i++) {
			if( strpos($aLine[2], $titles[$i]["title"]) !== FALSE ) $matchFound = TRUE;
		}

		if(!$matchFound) {
			echo "<p> Line number " . $c . " title does not match any type!  Title is " . $aLine[2] . " It will become blank </p>"; 
		}
	}
	

	//check city & province
	if( strcmp($aLine[6], "" ) === 0 || strcmp($aLine[7], "" ) === 0) { //make sure string is not null
		echo "<p><font color='red'> Line number " . $c . " City or Province is blank - this will fail</font></p>";
	} else {
		$matchFound = FALSE;
		for($i = 0; $i < $city_provinceCount; $i++) {
			if( strcmp($aLine[6], $city_province[$i]["city"]) === 0 && strcmp($aLine[7], $city_province[$i]["province"]) === 0) $matchFound = TRUE;
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
				echo "<p><font color='orange'> City -" . $aLine[6] . " / " . $aLine[7] . "-  Mismatch on Line " . $c . " ! Could not find a good match -> will add to Database.</font></p>";
			} else {
				echo "<p> City/Province  -" . $aLine[6] . " / " . $aLine[7] .   "-  Mismatch on Line " . $c . " ! The most similar city is " . $city_province[$mostSimilar]["city"] . " / " . $city_province[$mostSimilar]["province"] .  " : This is what we will use.</p>";
			}

		}
	}

	$c++;
}


fclose($pledgefileHandle);

error:

?>

<p><a href="http://192.168.69.99/?page_id=390">Return to load telethon</a></p>

</body>
</html>

