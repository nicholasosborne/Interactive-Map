<?
include("../dbconnect.php");

function get_umc($api, $vars = array()){
	$url = 'http://api.umusic.ca/'.$api.'.json';
	$i = 0;
	foreach($vars as $key => $value) {
   		if($i == 0){
   			$url.="?$key=$value";
   		}else{	
   			$url.="&$key=$value";
   		}
   		$i++;
	}	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$result = json_decode(curl_exec($ch));
	curl_close($ch);
	return $result;
}

?>




var RedIcon = L.Icon.extend({
    iconUrl: 'images/marker-red.png',
    shadowUrl: 'css/leaflet/images/marker-shadow.png',
    iconSize: new L.Point(25, 41),
    shadowSize: new L.Point(41, 41),
    iconAnchor: new L.Point(13, 41),
    popupAnchor: new L.Point(0, -33)
});


var GreenIcon = L.Icon.extend({
    iconUrl: 'images/marker-green.png',
    shadowUrl: 'css/leaflet/images/marker-shadow.png',
    iconSize: new L.Point(25, 41),
    shadowSize: new L.Point(41, 41),
    iconAnchor: new L.Point(13, 41),
    popupAnchor: new L.Point(0, -33)
});

var redicon = new RedIcon();
var greenicon = new GreenIcon();

var citiesLayer = new L.LayerGroup();


var mypoints = {};


<?php

$tour = get_umc('tour/v1/dates/downwithwebster');

$nextdate = get_umc('tour/v1/dates/downwithwebster',array('after'=>date('Y-m-d'),'rpp'=>'1'));


$nextdate = $nextdate->content[0]->uuid;

#var_dump($nextdate);


$tomorrow =  date("Y-m-d", time()+86400);
$today =  date("Y-m-d", time());
$data = array();
$hashtags = array();
if($tour->status == "ok"){
	$tour_data = $tour->content; 
	
	
	#GET THE HASHTAGS
	$htq = mysql_query("SELECT show_id,hashtag from dww_dates");
	
	while($row = mysql_fetch_assoc($htq)) {
	
	$hashtags["p".$row['show_id'].""] = $row['hashtag'];
	
	}
	
	#var_dump($hashtags);
	
	 
	foreach($tour_data as $point){
	
	#DEBUG DATE
	
	#if($point->date == "2014-01-30"){
	
	#$point->date = "2014-01-01";
	
	#}
	
	#END DEBUG
	
	if($point->date < $today){
		
		$type = "redicon";
	}else{
		$type = "greenicon";
	}
	
	echo "//".$point->date." |  ".$tomorrow." | ".(strtotime($point->date) < strtotime($tomorrow))." | $type \n";
	
	$address = $point->address;
	$address = urlencode($address);
	
	$temp = "";
	if($point->uuid != $nextdate){
	$temp = ",{icon: $type}";
	}
	
	$id = 'p'.$point->uuid;
	$lat = $point->lat;
	$lng = $point->long;
	

	echo("mypoints.$id = {type:'$type',
	'lat':'$lat',
	'long':'$lng',
	'point': new L.Marker(new L.LatLng($lat, $lng)$temp).on('click', function(e){mapClick('$id');}),
	'hashtag': '".$hashtags[$id]."',
	'date': '".date('m.d.Y', strtotime($point->date))."',
	'location': '".$point->city.", ".$point->region."',
	'venue': \"".$point->venue."\",
	'ticket': '".$point->ticket_url."',
	'map': '".$address."',
	'fbid': '".$point->fb_id."',
	'soldout': '".$point->soldout."'
	};
	");
	

	}
}


?>



$.each(mypoints, function(uuid, point) {
  citiesLayer.addLayer(point.point);
});


var cloudmadeAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade',
cloudmadeOptions = {attribution: cloudmadeAttribution },
cloudmadeUrl = 'http://{s}.tile.cloudmade.com/c23b7e00c0c242268382b71bd22e4600/82102/256/{z}/{x}/{y}.png';
var minimal = new L.TileLayer(cloudmadeUrl, cloudmadeOptions, {styleId: 26167}),
midnightCommander = new L.TileLayer(cloudmadeUrl, cloudmadeOptions, {styleId: 999}),
motorways = new L.TileLayer(cloudmadeUrl, cloudmadeOptions, {styleId: 46561});
var map = new L.Map('map', {center: new L.LatLng(50, -90), zoom: 5, maxZoom: 18, minZoom: 5, scrollWheelZoom: false, layers: [minimal, motorways, citiesLayer]});
var baseMaps = {
	"Minimal": minimalgrey,
	"Night View": midnightCommander
};
var overlayMaps = {
	"Motorways": motorways,
	"Cities": citiesLayer
	};
		
var circleLocation = new L.LatLng(48, -123),
circleOptions = {color: '#f03', opacity: 0.7},
circle = new L.Circle(circleLocation, 500, circleOptions);
layersControl = new L.Control.Layers(baseMaps, overlayMaps);
map.addControl(layersControl);