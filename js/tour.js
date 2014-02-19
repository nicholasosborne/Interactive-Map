//TOUR DATE SYSTEM VARIABLES
		var tweetURL = 'http://tour.downwithwebster.com/';
		var fb_appid = '239354029576629';
		var fb_pic = 'http://tour.downwithwebster.com/images/facebook.jpg';
		var fb_desc = 'Down With Webster is touring Canada and we want you to share your concert photos!';



$(document).ready(function() {
	
	$("#close-pop-up, #popup-dim, #closebtn").click(function(){
		$(this).parent().fadeOut();
		//$("#tour-pop-up, #popup-dim").fadeOut();
	});
	
	
});

function facebookShare(url) {
	window.open(url,"Tour","height=236, width=516");
}

	function fblogin(fbid) {
    	FB.login(function(response) {
            FB.api('/'+fbid+'/attending', 'post', function (data) {
            $('#rsvp-button').hide();
            $('#rsvp-message').show();
        });
   	}, {scope:'rsvp_event'});
};

function mapClick(id){
		var point = mypoints[id];
		
		if(point.type == "greenicon"){
		
			$('#rsvp-button').show();
			$('#rsvp-message').hide();
		
	
			$("#tour-pop-up, #popup-dim").fadeIn();
			$("#date").html('<span class="tourinfo-heading">Date:</span><span class="tourinfo"> '+ point.date +'</span>');
			$("#location").html('<span class="tourinfo-heading">Location:</span><span class="tourinfo"> '+ point.location +'</span>');
			$("#venue").html('<span class="tourinfo-heading">Venue:</span><span class="tourinfo"> '+ point.venue +'</span>');
			$("#title").html('#'+point.hashtag + " (Submit your photos to this hashtag)");
			//Tweet Button
			$("#tweet-button").html('<a href="https://twitter.com/share" class="twitter-share-button twitter-tour-button" data-count="none" data-url="'+tweetURL+'" data-text="Down With Webster is playing a show at '+ point.venue +' in '+ point.location + ' on '+  point.date +'!" data-hashtags="'+ point.hashtag +'">Tweet</a>');
			$.getScript('http://platform.twitter.com/widgets.js');		
		
			//Facebook Share Button
			$("#share-button").html('<a href="#" onclick="facebookShare(\'https://www.facebook.com/dialog/feed?%20%20app_id='+fb_appid+'&%20%20link='+tweetURL+'&%20%20picture='+fb_pic+'&name='+point.date+'&caption='+ point.location + ' - '+ point.venue + '&description='+fb_desc+' %23'+point.hashtag+'&display=popup&redirect_uri='+tweetURL+'\')"><img src="https://s3.amazonaws.com/cdn.universalmusic/universalmusic/lib/tour/share-button.png" alt="Share"/></a>');
		
				
			//Venue Map
			$("#venue-map").html('<iframe width="608" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.ca/maps?ie=UTF8&amp;q='+point.venue+'+'+point.map+'&amp;t=m&amp;z=16&amp;iwloc=A&amp;output=embed"></iframe>');
		
			//display ticket links unless they are sold out
			if (point.soldout == 0) {
				$("#tickets").html('<a href="'+point.tickets+'" target="_blank" id="ticket-link">Buy Tickets</a>');
			} else if ($(this).data('soldout') == 1){
				$("#tickets").html('<div id="sold-out">Sold Out</div>');
			}
		
			var fbid = point.fbid;
		
			//display rsvp button if there is event id
			if (point.fbid != "") {
				$("#rsvp-button").html('<a href="#"><img src="https://s3.amazonaws.com/cdn.universalmusic/universalmusic/lib/tour/rsvp-button.png" alt="Share"/></a>');
				$('#rsvp-button').click(function(){
    				fblogin(fbid);
				});
			} else {
				$("#rsvp-button").html('');
			}
		}else{
			//Show the fancy box with images
			$.getJSON('ajax.php?id='+id, function (data) {
				$.fancybox.open(data, {
    				padding : 0,
    				index: 0,
    				helpers : {
						thumbs : {
            				width: 100,
            				height: 100,
            				position: 'top'
        				},
        				title: {
							type: 'over'
						}
					}   
				});
			});	
		}	
}