<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

//get all the city / provinces
$result = $wpdb->get_results("SELECT * FROM constituent WHERE region_id!=24 AND region_id!=6", ARRAY_A);

//loop thru all the city_provinces
for($c = 0; $c < count($result); $c++) {
	$city = $result[$c]['city'];
	$prov = $result[$c]['province'];
	$region = $result[$c]['region_id'];
	$contain = $wpdb->get_results("SELECT * FROM city_province WHERE city='$city' AND province='$prov'", ARRAY_A);

	if(count($contain) > 0) {
		//update the region code
		$wpdb->query("UPDATE city_province SET region_id=$region WHERE city='$city' AND province='$prov'");
		echo "Updated $city, $prov to region $region\n";
	} else {
		//does not contain so add to city_provice table
		$wpdb->query("INSERT INTO city_province (city, province, region_id) VALUES ('$city', '$prov', $region)");
		echo "Added $city, $prov with region $region\n";
	}
}


return;

?>

