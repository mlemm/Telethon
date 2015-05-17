<!DOCTYPE html>
<html lang="en-CA">
<head><title>CHEO Telethon | CSV Load Results</title></head>
<body>

<?php

ini_set('display_errors', 'On');
ini_set('html_errors', 0);

// ----------------------------------------------------------------------------------------------------
// - Error Reporting
// ----------------------------------------------------------------------------------------------------
error_reporting(-1);

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

$wpdb->query( "TRUNCATE TABLE constituent");
$wpdb->query( "TRUNCATE TABLE pledge");
$wpdb->query( "TRUNCATE TABLE pledge_batch");
$wpdb->query( "TRUNCATE TABLE ticker_item");
$wpdb->query( "TRUNCATE TABLE ticker_batch");

echo 'All Database info has beeen cleared!';


?>


<p><a href="http://184.175.49.94/?page_id=390">Return to load telethon</a></p>

</body>
</html>
