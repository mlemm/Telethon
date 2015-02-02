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

	$theLine = fgets($inputFileHandle);
	$theLine = rtrim($theLine, "\n");
	$theLine = rtrim($theLine, "\"");
	$theLine = trim($theLine, "\"");

	while($theLine) {

		list($constituentID[$counter], $giftAmount[$counter], $title[$counter],
			$firstName[$counter], $lastName[$counter], $name[$counter],
			$preferredCity[$counter], $preferredProvince[$counter], $preferredRegion[$counter] ) = array_pad(explode("\",\"", $theLine,9), 9, " ");

		$theLine = fgets($inputFileHandle);
		$theLine = rtrim($theLine, "\n");
		$theLine = rtrim($theLine, "\"");
		$theLine = trim($theLine, "\"");
		$counter++;

	}



	//echo $firstString . "\n";
	$sorted_Array = array_unique($preferredRegion);
	natcasesort($sorted_Array);

	print_r($sorted_Array);

	echo " size of array is " . count($sorted_Array) . "\n";


	fclose($inputFileHandle);

?>