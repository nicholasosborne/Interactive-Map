<?php
include('dbconnect.php');

if(isset($_GET['id'])){

	$id = mysql_real_escape_string($_GET['id']);
	$id = str_replace('p','',$id);
	$query = mysql_query("SELECT caption,image,user FROM dww_images WHERE event_id = '$id' AND approved=1");
	
	$output = array();
	
	while($row = mysql_fetch_assoc($query)) {
	$href = $row['image'];
	$title = "@".$row['user'].": ".$row['caption'];
	
	array_push($output,array('href'=>$href,'title'=>$title));
	
	}
	
	echo json_encode($output);
}

?>