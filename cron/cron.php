<?php
function removeEmoji($text) {
    $clean_text = "";
    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $text);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);

    return $clean_text;
}

function getInstagram($tag,$event_id,$min_id){

	global $instagram_token;
	$photos = json_decode(file_get_contents("https://api.instagram.com/v1/tags/$tag/media/recent?count=25&min_tag_id=$min_id&access_token=$instagram_token"));
	$list = $photos->data;

	foreach ($list as $post){

		$caption = mysql_real_escape_string($post->caption->text);
		$caption = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "$1", $caption);
		$caption = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "$1", $caption);
		$caption = removeEmoji($caption);
		$user = mysql_real_escape_string($post->user->username);
		$id = mysql_real_escape_string($post->id);
		$img_url = mysql_real_escape_string($post->images->standard_resolution->url);
	
		mysql_query("INSERT INTO dww_images (event_id,caption,image,user,type) VALUES ('$event_id','$caption','$img_url','$user','i')");
	}
	if(isset($photos->pagination->next_min_id)){
		$max_id = $photos->pagination->next_min_id;
	}else{
		$max_id = $min_id;
	}
	mysql_query("UPDATE dww_dates SET instagram_id = '$max_id' WHERE show_id='$event_id'");
}


function getTwitter($tag,$event_id,$min_id){

	global $twitteroauth;
	
	#GET TWEETS
	$tweets = $twitteroauth->get("search/tweets",array("q" => "#$tag", "rpp" => 100, "include_entities" => "true", "result_type" => "recent", "since_id" => "$min_id"));
	$list = $tweets->statuses;


	foreach ($list as $post){
	
		$caption = mysql_real_escape_string($post->text);
		$caption = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "$1", $caption);
		$caption = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "$1", $caption);
		$caption = removeEmoji($caption);
		$user = mysql_real_escape_string($post->user->screen_name);
		$id = mysql_real_escape_string($post->id_str);

		if(isset($post->entities->media)){
			#ADD IMAGES
	
			$media = $post->entities->media;
			foreach ($media as $image){
				//GET INFO
				$img_id = $image->id_str;
				$img_url = mysql_real_escape_string($image->media_url);
				mysql_query("INSERT INTO dww_images (event_id,caption,image,user,type) VALUES ('$event_id','$caption','$img_url','$user','t')");

			}
		}
		if(isset($tweets->search_metadata->max_id)){
			$max_id = $tweets->search_metadata->max_id;
		}else{
			$max_id = $min_id;
		}
		mysql_query("UPDATE dww_dates SET twitter_id = '$max_id' WHERE show_id='$event_id'");
	}
}

#GENERAL SETTINGS
chdir(dirname(__FILE__));
include('../dbconnect.php');
require("twitteroauth/twitteroauth.php");
$instagram_token = '';
$twitteroauth = new TwitterOAuth('', '', '512430723-', '');

#GET THE SINCE ID FROM THE DB
$query = mysql_query("SELECT show_id,hashtag,instagram_id,twitter_id from dww_dates");

while($row = mysql_fetch_assoc($query)) {
	getInstagram($row['hashtag'],$row['show_id'],$row['instagram_id']);
	getTwitter($row['hashtag'],$row['show_id'],$row['twitter_id']);
	
}
?>