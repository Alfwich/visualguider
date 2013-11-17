<!DOCTYPE html>
<html>
<head>
<style>
*
{
	margin: 0px;
	padding: 0px;
	-webkit-touch-callout: none;
	-webkit-user-select: none;
	-khtml-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;	
}
body
{
	overflow:hidden;
	white-space: nowrap;
	background-image: url( "image/bg.jpg" );
	position:relative;
	top:0px;
	left:0px;
}
.card
{
	position:absolute;
	background-color: #AAA;
	background-size: 100% 100%;
	box-shadow: 5px 5px 5px rgba( 0, 0, 0, 0.3 );
	border-radius: 5%;
}
#center_top
{
	overflow:hidden;
	white-space: nowrap;
	width:188px;
	background-color: rgba( 0, 0, 0, 0.2 );
	padding: 3px;
	border-radius: 5px;
}
.search_input
{
	height: 25px;
	float: left;
}
.search_button
{
	width: 25px;
	height: 25px;
	margin-left:2px;
	border-radius: 3px;
	background-image: url( "image/magnifyingglass.png" );
	background-size: 100% 100%;	
	float: left;	
}
</style>
<script type="text/javascript" src="jq.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>
<script type="text/javascript">

function Init()
{
	// Global
	screenXOffset = $(window).width()/2;
	screenYOffset = $(window).height()/2;
	cardSize = [223,310];
	maxWeight = 0;
	cards = [];
	zoom = 1;
	lastCardTarget = null;
	options = { duration: 500, queue: false, easing: "swing" };
	
	// Animation variables
	desiredZoom = 1;
		
	// Get all of the weights of all of the cards on the page
	$(".card").each(function() {
		var weight = $(this).attr("weight");
		var left = $(this).attr("left");
		var top = $(this).attr("top");
		if( weight == undefined )
		{
			$(this).hide();
			return;
		}
		
		$(this).css("background-image", "url('image/Image ("+(Math.floor(Math.random()*64)+1)+").jpg')" );
		
		// Add the weight and a reference to the card
		cards.push( [ weight, this ] );
				
		// Update maxWeight
		if (weight > maxWeight)
		{
			maxWeight = weight;
		}
	});
	
	// Move all elements into position for inital rendering
	var oldAnimationDuration = options.duration;
	options.duration = 0;
	SizeCards();
	options.duration = oldAnimationDuration;
	
	// Give all cards an on click event to resize on click
	$(".card").click(function() {
		// Stop the current animation
		$("body").stop();
				
		var weight = $(this).attr("weight");
		if( weight == undefined )
		{
			$(this).hide();
			return;
		}
				
		// Make the size the desired card size
		var p = ( weight / maxWeight );
		zoom = 1 / p;
		
		lastCardTarget = this;
		SizeCards( this, 1 );
	});
	$(document).click( function (e) {
		if( !$(e.target).hasClass("card") &&
			!$(e.target).hasClass("search_input") &&
			!$(e.target).hasClass("search_button")			)
		{
			zoom = 1;
			lastCardTarget = null;
			SizeCards();
		}
	});
	
	//	Prevent mouse wheel scrolling
	$(document).bind("mousewheel", function(e){
	
		// Zoom scales 
		if( e.originalEvent.deltaY > 0 )
		{
			zoom *= .80;
			if( zoom <= 0.2 )
			{
				zoom = 0.2;
			}
		}
		else
		{
			zoom /= .80;
		}
		
		SizeCards( lastCardTarget );
		e.preventDefault();
		e.stopPropagation();
	});
	
	//	Prevent mouse wheel middle button scrolling
	$(document).mousedown(function(e){
		if( e.which == 2 )
		{
			e.preventDefault();
			e.stopPropagation();
		}
		
	});
}

// Will center the viewport at x, y
function CenterViewport( x, y, w, h )
{
	if( w == undefined )
	{
		w = 0;
	}
	
	if( h == undefined )
	{
		h = 0;
	}	
	
	// Move the coords to the center of the object
	x -= w/2;
	y -= h/2;
	
	// Move the coords to the center of the screen
	x += $(window).width()/2;
	y += $(window).height()/2;
	
	// Move the body
	$("body").animate( { left: x, top: y }, options );
	
	// Top center offset
	x -= ( $(window).width()/2 - $("#center_top").width()/2.0 );
	y -= 5;
	$("#center_top").animate( { left: -x, top: -y }, options );
}

function SizeCards( target, tweens )
{
	// Scale all of the cards width, height, and set the positions relative to the new zoom level
	for( c in cards )
	{
		var p = cards[c][0] / maxWeight;
		var left = $(cards[c][1]).attr("left");
		var top = $(cards[c][1]).attr("top");
		$(cards[c][1]).css("z-index", 0 );
		$(cards[c][1]).animate({ "width":cardSize[0]*p*zoom,"height":cardSize[1]*p*zoom,
								 "left":(left*zoom)+"px", "top":(top*zoom)+"px"},options)
	}
		
	// Move the viewport to center on a card if we have a target card
	if( target != undefined && target != null )
	{
		
		// Get the current positions of the body and the card to center on
		var newTargetLeft = -$(target).attr("left")*zoom;
		var newTargetTop = -$(target).attr("top")*zoom;
		var targetWeight = $(target).attr("weight") / maxWeight;

		$(target).css("z-index", 1);
		CenterViewport( newTargetLeft, newTargetTop, cardSize[0]*targetWeight*zoom, cardSize[1]*targetWeight*zoom );
		
	}
	else
	{
		// Center to middle of screen
		CenterViewport((screenXOffset)*zoom,(screenYOffset)*zoom);
	}	
}
$(document).ready(function(){
	Init();
});
</script>
</head>
<body>
<div id="center_top" style="position:absolute; z-index:2;">
	<input type="text" class="search_input" name="search" value="search" />
	<button name="search_button" class="search_button"></button>
</div>
<?php
	$cards = 40;
	$x = 0;
	while( $x < 2*pi() )
	{
		$posX = ( cos($x) * rand( 1, 4000 ) ) - 1920/2;
		$posY = ( sin($x) * rand( 1, 4000 ) ) - 1200/2;
		$size = rand( 1, 1000 ) / 1000;
		echo "<div class=\"card\" weight=\"{$size}\" top=\"{$posY}\" left=\"{$posX}\"></div>";
		$x += pi()/($cards/2);
	}


?>
</body>
</html>
