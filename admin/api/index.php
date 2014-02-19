<?php


error_reporting(E_ALL);
ini_set('display_errors', '1');

#REQUIREMENTS
include("../../dbconnect.php");

#GET FUNCTION

$function = $_REQUEST['function'];

$return_data = array();

switch($function){

	case "update":
	$return_data = update();
	break;
	
	case "approve":
	$return_data = approve();
	break;
	
	case "reject":
	$return_data = reject();
	break;
	
	default:
	#$return_data = update();
    $return_data['status'] = "error";
    $return_data['error'] = "nofunction";
    break;
}

echo json_encode($return_data);

### FUNCTION TIME

function update(){

$data = array();
$data['content'] = "";


$query = mysql_query("SELECT * FROM dww_images where approved=0 order by id asc");

	while($row = mysql_fetch_assoc($query)){
	 

		$data['content'].= "<li><div class=\"content\">";
		
		if($row['image'] != ""){
		
		
			if($row['type'] == "t"){
		
				$data['content'].= "<img src=\"".$row['image'].":small\">";
		
			}else{
				$data['content'].= "<img src=\"".str_replace("_7", "_5", $row['image'])."\">";		
			}
		
		}
		
		$data['content'].= "<span class=\"user\">".$row['event_id']." | ".$row['user'].":</span> ".$row['caption'];
		
		$data['content'].= "</div>";
		
		$data['content'].= "<div class=\"controls\">";
		
		$data['content'].= '<button type="button" class="btn-large btn-success approve" onclick="approve(\''.$row['id'].'\')">Approve</button>';
		
		$data['content'].= '<button type="button" class="btn-large btn-danger reject" onclick="reject(\''.$row['id'].'\')">Reject</button>';
		
		$data['content'].= "</div>";
		
		$data['content'].= "</li>";
	}


$data['status'] = "ok";

return $data;

}

function approve(){

$id = mysql_real_escape_string($_REQUEST['id']);
    
mysql_query("UPDATE dww_images set approved=1 where id = '$id'");

$data['status'] = "ok";

return $data;

}


function reject(){

$id = mysql_real_escape_string($_REQUEST['id']);
mysql_query("UPDATE dww_images set approved=-1 where id = '$id'");

$data['status'] = "ok";

return $data;
}


?>