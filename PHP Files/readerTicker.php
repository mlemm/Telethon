<?php
header('Content-disposition: attachment; filename=readerticker.txt');
header('Content-type: text/plain');

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

$num_pledges = $_POST['readerPledges'];

//we will merge the tables so we don't have to look it up a second time - should be alot faster
$result = $wpdb->get_results("SELECT p.amount, p.constituent_id, p.ok_to_publish, c.id, c.title_id, c.first_name, c.last_name, c.organization_name, c.city, t.id, t.title FROM (pledge p LEFT JOIN constituent c ON (p.constituent_id = c.id)) LEFT JOIN title t ON (c.title_id = t.id) WHERE p.ok_to_publish=1 AND p.displayed=0 AND p.amount!=0", ARRAY_A);
//gets all possible values

if(count($result) < $num_pledges) $num_pledges = count($result); // make sure we have enough results to return

$rand_keys = array_rand($result, $num_pledges);	

echo "CHEO Telethon 2015\r\nTelevision Pledges\r\n-----------------------\r\n";


for($c = 0; $c < $num_pledges; $c++) {

	if($result[$rand_keys[$c]]['last_name'] == "") {
		//user organization only
		echo html_entity_decode($result[$rand_keys[$c]]['organization_name'],ENT_QUOTES) . "  " . html_entity_decode($result[$rand_keys[$c]]['city'],ENT_QUOTES) . "  $" . $result[$rand_keys[$c]]['amount'] . ".../\r\n";
	} else {
		//user title first last - no organization
		echo $result[$rand_keys[$c]]['title'] . " " . html_entity_decode($result[$rand_keys[$c]]['first_name'],ENT_QUOTES) . " " . html_entity_decode($result[$rand_keys[$c]]['last_name'],ENT_QUOTES) . "  " . html_entity_decode($result[$rand_keys[$c]]['city'],ENT_QUOTES) . " $" . $result[$rand_keys[$c]]['amount'] . ".../\r\n";
	}
	//set to ok_to_publish to false
	$wpdb->query("UPDATE pledge SET displayed=1 WHERE constituent_id=" . $result[$rand_keys[$c]]['constituent_id']);
	
}

//format is Title First Name Last Name Organization..City.. Amount.../0D0A
//If Last Name then Title First Last else Organization



?>
