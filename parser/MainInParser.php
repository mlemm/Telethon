<?php

/*

	Markus Lemm - 260444 - November 29, 2014
	For diskdaddy.com - project is the CHEO Telethon

	Parser for the inital file to load all the mail in / online donations
	.csv file - first line is the names of all the categories seperated by commas
	all other lines are the values seperated by commas

*/

	/*
	Issues
	 - when number are > 999 there is a comma in the amound and is seperated by explode program
	*/

	//Sudo
	//1. Open file
	//2. Init the variables as arrays
	//3. Parse line by line filing arrays
	//4. Transfer to DB



	//Open file
	$inputFileHandle = fopen("TestLoadFile.CSV", "r");

	//Read the first line and get all the variables
	//All lines end with \n
	$firstString = fgets($inputFileHandle);

	//strip line feed off end
	$firstString = rtrim($firstString, "\n");	


	$variables = explode(",", $firstString);

	//confirm that we have the correct number of variables
	if(count($variables) === 9)  
		echo "correct number of vars\n";
	else {
		echo "Not the Correct number of Vars! - Exiting\n";
		fclose($inputFileHandle);
		exit(-1);
	}

	//now we want a list of all region codes

	$constituentID = array();
	$giftAmount = array();
	$title = array();
	$firstName = array();
	$lastName = array();
	$name = array();
	$preferredCity = array();
	$preferredProvince = array();
	$preferredRegion = array();
	$counter = 0;
	fclose($inputFileHandle);

	$csv = array_map('str_getcsv', file("TestLoadFile.CSV"));
	//print_r($csv);

	foreach($csv as $lineArray) {
		if($counter!=0) {
			list($constituentID[$counter], $giftAmount[$counter], $title[$counter],
				$firstName[$counter], $lastName[$counter], $name[$counter],
				$preferredCity[$counter], $preferredProvince[$counter], $preferredRegion[$counter] ) = array_pad($lineArray, 9, " ");
		}

		$counter++;
	}



	$sorted_Array = array_unique($preferredRegion);
	natcasesort($sorted_Array);

	$sorted_Array_Titles = array_unique($title);
	natcasesort($sorted_Array_Titles);

	$sorted_Array_City = array_unique($preferredCity);
	natcasesort($sorted_Array_City);

	$sorted_Array_Provence = array_unique($preferredProvince);
	natcasesort($sorted_Array_Provence);
/*
	print_r($sorted_Array_Titles);
	print_r($sorted_Array_City);
	print_r($sorted_Array_Provence);
	print_r($sorted_Array);
*/
	
	//similar_text("Barry's Bay", "Barrys Bay", $diff);
	//$diff = levenshtein("St.isidore", "St. Isidore");
	//echo "testing Val-Des-Monts vs Val-des-monts is " . $diff ."\n";
	//compare all the city name to each other and output if value is >= x
	foreach ($sorted_Array_City as $firstCity) {

		foreach ($sorted_Array_City as $secondCity) {

			similar_text($firstCity, $secondCity, $diff);
			if($diff >= 75.0 && $diff != 100.0) echo "These are very similar " . $firstCity . " and " . $secondCity. "\n";

		}
	}


	//create a new file with the unique Titles, Preferred City, Provence, Region
	$inputFileHandle = fopen("uniqueTitles.txt", "w");

	fwrite($inputFileHandle, "Unique Titles\n\n");

	foreach($sorted_Array_Titles as $theValue) {
		fwrite($inputFileHandle, $theValue . "\n" );
	}

	fwrite($inputFileHandle, "\nUnique Cities\n\n");

	foreach($sorted_Array_City as $theValue) {
		fwrite($inputFileHandle, $theValue . "\n" );
	}

	fwrite($inputFileHandle, "\nUnique Provences\n\n");

	foreach($sorted_Array_Provence as $theValue) {
		fwrite($inputFileHandle, $theValue . "\n" );
	}

	fwrite($inputFileHandle, "\nUnique Regions\n\n");

	foreach($sorted_Array as $theValue) {
		fwrite($inputFileHandle, $theValue . "\n" );
	}



	fclose($inputFileHandle);

?>