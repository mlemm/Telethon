<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

$region = intval($_POST['region']);
$pledgeNum = intval($_POST['pledge']);


//get all the regions that are 24 or 6
//echo "UPDATE constituent SET region_id=$region WHERE id=$pledgeNum";

$result = $wpdb->query("UPDATE constituent SET region_id=$region WHERE id=$pledgeNum");
if($result <= 0) {
	echo "Unable to update.";
} else {
	echo "Region association updated.";
}

return;


?>

