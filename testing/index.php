<!DOCTYPE html>
<html>
<head>
<style>
*
{
	margin: 0px;
	padding: 0px;
}
body
{
	width: 1000000px;
	height: 1000000px;
	overflow:hidden;
	white-space: nowrap;
	background-image: url( "image/background.png" );
	position:relative;
	top:0px;
	left:0px;
}
.card
{
	position:absolute;
	background-color: #AAA;
	background-image: url( "image/aa.jpg" );
	background-size: 100% 100%;
	margin: 10px;
	float:left;
	box-shadow: 5px 5px 5px rgba( 0, 0, 0, 0.3 );
	border-radius: 5%;
}
</style>
<script type="text/javascript" src="jq.js"></script>
<script type="text/javascript">

function Init()
{
	// Global
	cardSize = [223,310];
	maxWeight = 0;
	cards = [];
	zoom = 1;
	lastCardTarget = null;
	options = { duration: 1000, queue: false, easing: "swing" };
	
	// Animation variables
	desiredZoom = 1;
	zoomDelta = 0;
	
	animationDuration = 0;
	
	// Get all of the weights of all of the cards on the page
	$(".card").each(function() {
		var weight = $(this).attr("weight");
		if( weight == undefined )
		{
			$(this).hide();
			return;
		}
		
		// Add the weight and a reference to the card
		cards.push( [ weight, this ] );
		
		// Update maxWeight
		if (weight > maxWeight) maxWeight = weight;
	});
		
	/* Size all of the cards
	for( c in cards )
	{
	
		// Get the precentage
		var p = ( cards[c][0] / maxWeight );	
		
		$(cards[c][1]).animate( { width: (cardSize[0]*p*zoom), height: (cardSize[1]*p*zoom) },
								 options );	
	}
	*/
	SizeCards();
		
	// Give all cards an on click event to resize on click
	$(".card").click(function() {
		// Stop the current animation
		$("body").stop();
		zoomDelta = 0;
		
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
	
	$('body').click( function (e) { 
		if ( e.target == this )
		{
			zoom = 1;
			SizeCards();
		}
	});	
	//	Prevent mouse wheel scrolling
	$(document).bind("mousewheel", function(e){
	
		// Zoom scales 
		if( e.originalEvent.deltaY > 0 )
		{
			zoom -= 0.2;
			if( zoom <= 0 )
			{
				zoom = 0.2;
			}
		}
		else
		{
			zoom += 0.2;
		}
		
		SizeCards( lastCardTarget );
		e.preventDefault();
		e.stopPropagation();
	});
	
	//	Prevent mouse wheel middle button scrolling
	$(document).mousedown(function(e){
		if( e.which = 2 )
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
	if( target != undefined )
	{
		
		// Get the current positions of the body and the card to center on
		var currentLeft = parseInt($("body").css("left"));
		var currentTop = parseInt($("body").css("top"));
		var newTargetLeft = -$(target).attr("left")*zoom;
		var newTargetTop = -$(target).attr("top")*zoom;
		var targetWeight = $(target).attr("weight") / maxWeight;

		$(target).css("z-index", 1);
		CenterViewport( newTargetLeft, newTargetTop, cardSize[0]*targetWeight*zoom, cardSize[1]*targetWeight*zoom );
		
	}
	else
	{
		CenterViewport( 100, 100 );
	}	
}
$(document).ready(function(){
	Init();
});
</script>
</head>
<body>
<div id="cards">
<?php

	$x = 0;
	while( $x < 2*pi() )
	{
		$posX = ( cos($x) * 1000 ) - 1920/2;
		$posY = ( sin($x) * 1000 ) - 1200/2;
		$size = rand( 1, 10 ) / 10;
		echo "<div class=\"card\" weight=\"{$size}\" top=\"{$posY}\" left=\"{$posX}\"></div>";
		$x += pi()/10;
	}


?>
</div>
</body>
</html>
