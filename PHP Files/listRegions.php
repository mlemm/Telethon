<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );

global $wpdb;


//get all the regions that are 24 or 6
$result = $wpdb->get_results("SELECT * FROM constituent WHERE region_id=24", ARRAY_A);

for($c = 0; $c < count($result); $c++) {
	$city = $result[$c]['city'];
	$prov = $result[$c]['province'];
	$region = $result[$c]['id'];

	echo "<p>$city / $prov please select a region <select id='$region'><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='71'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option><option value='13'>13</option><option value='14'>14</option><option value='15'>15</option><option value='16'>16</option><option value='17'>17</option><option value='18'>18</option><option value='19'>19</option><option value='20'>20</option><option value='21'>21</option><option value='22'>22</option><option value='23'>23</option><option value='24' selected>24</option><option value='25'>25</option></select><button value='update' onClick='updateRegion($region)'>Update</button>\n";

}



return;


?>

