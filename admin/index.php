<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>MMVA Afterparty Approval</title>
    
	
   <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=1;" />
   <link rel="stylesheet" type="text/css" href="css/bootstrap.css" media="screen" />
   <link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css" media="screen" />
   <link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../js/jquery.migrate.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    
  </head>

<script>
$(document).ready(function() {
  update();
  setInterval(update,20000);

});

function update(){
$.ajax({
        type: 'POST',
        url: 'api/update/',
        dataType: 'json',
        success: function(json) {
 			if(json.status == "ok"){
 			
 			$('#content').html(json.content);
 				

 			}else if(json.status == "error") alert(json.error);
        }	
	});
}


function approve(id){
$.ajax({
        type: 'POST',
        url: 'api/approve/',
        data:{"id":id},
        dataType: 'json',
        success: function(json) {
 			if(json.status == "ok"){
 			update();
 			}else if(json.status == "error") alert(json.error);
        }	
	});
}

function reject(id){
$.ajax({
        type: 'POST',
        url: 'api/reject/',
        data:{"id":id},
        dataType: 'json',
        success: function(json) {
 			if(json.status == "ok"){
 			update();
 			}else if(json.status == "error") alert(json.error);
        }	
	});
}


</script>


  <body>
  
  <div class="row">
  	<div class="span12">
  	<ul id="content"></ul>
  	</div>
  </div>
  
  </body>
  </html>