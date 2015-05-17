<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

//get the total donated and return it
$totalDonated = $wpdb->get_results("SELECT SUM(amount) AS total_donated FROM pledge", ARRAY_A);

//var_dump($totalDonated);
//echo '10';
echo number_format($totalDonated[0]['total_donated'], 2,'.', ',');

?>

