<?php

//This will find all open pledges and return them as a table

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;

$whereDoWeLook = $_POST['searchType'];
$whatWeLookFor = $_POST['searching'];

//lookup the info and return lastname, organization, pledge number, amount, batch number
//search by lastname, organization, pledge number amount

switch($whereDoWeLook) {
	case 'lastname':
		//need to do some fancy joins and get the info we need
		$result = $wpdb->get_results("SELECT c.id, c.last_name, c.organization_name, p.constituent_id, p.pledge_number, p.amount, p.batch_id, b.id, b.batch_number FROM (constituent c LEFT JOIN pledge p ON (c.id = p.constituent_id)) LEFT JOIN pledge_batch b ON (b.id = p.batch_id) WHERE c.last_name LIKE '%" . $whatWeLookFor . "%'", ARRAY_A);
		$tableReturn = '<div id="search_results"><table class="table"><thead><tr><th>By Last Name</th><th>Last Name</th><th>Organization</th><th>Pledge Number</th><th>Amount</th><th>Batch Number</th></tr></thead><tbody>';
	break;

	case 'organization':
		//need to do some fancy joins and get the info we need
		$result = $wpdb->get_results("SELECT c.id, c.last_name, c.organization_name, p.constituent_id, p.pledge_number, p.amount, p.batch_id, b.id, b.batch_number FROM (constituent c LEFT JOIN pledge p ON (c.id = p.constituent_id)) LEFT JOIN pledge_batch b ON (b.id = p.batch_id) WHERE c.organization_name LIKE '%" . $whatWeLookFor . "%'", ARRAY_A);
		$tableReturn = '<div id="search_results"><table class="table"><thead><tr><th>By Organization</th><th>Last Name</th><th>Organization</th><th>Pledge Number</th><th>Amount</th><th>Batch Number</th></tr></thead><tbody>';
	break;

	case 'pledgeNumber':
		//need to do some fancy joins and get the info we need
		$result = $wpdb->get_results("SELECT c.id, c.last_name, c.organization_name, p.constituent_id, p.pledge_number, p.amount, p.batch_id, b.id, b.batch_number FROM (constituent c LEFT JOIN pledge p ON (c.id = p.constituent_id)) LEFT JOIN pledge_batch b ON (b.id = p.batch_id) WHERE p.pledge_number LIKE '%" . $whatWeLookFor . "%'", ARRAY_A);
		$tableReturn = '<div id="search_results"><table class="table"><thead><tr><th>By Pledge Number</th><th>Last Name</th><th>Organization</th><th>Pledge Number</th><th>Amount</th><th>Batch Number</th></tr></thead><tbody>';
	break;

	case 'amount':
		//need to do some fancy joins and get the info we need
		$result = $wpdb->get_results("SELECT c.id, c.last_name, c.organization_name, p.constituent_id, p.pledge_number, p.amount, p.batch_id, b.id, b.batch_number FROM (constituent c LEFT JOIN pledge p ON (c.id = p.constituent_id)) LEFT JOIN pledge_batch b ON (b.id = p.batch_id) WHERE p.amount>" . intval($whatWeLookFor) , ARRAY_A);
		$tableReturn = '<div id="search_results"><table class="table"><thead><tr><th>By Amount</th><th>Last Name</th><th>Organization</th><th>Pledge Number</th><th>Amount</th><th>Batch Number</th></tr></thead><tbody>';
	break;

	default:
		$tableReturn = "Error";
}

for($c = 0; $c < count($result); $c++) {
	$tableReturn .= '<tr><th scope="row">' . ($c+1) . '</th><td>' . $result[$c]['last_name'] . '</td><td>' . $result[$c]['organization_name'] . '</td><td>' . $result[$c]['pledge_number'] . '</td><td>$' . $result[$c]['amount'] . '</td><td>' . $result[$c]['batch_number'] .'</td></tr>';
}

$tableReturn .= '</tbody></table></div>';

echo $tableReturn;


?>
