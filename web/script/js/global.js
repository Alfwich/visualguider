
// Global
screenXOffset = $(window).width()/2;
screenYOffset = $(window).height()/2;
cardSize = [223,310];
maxWeight = 0;
cards = [];

// Zoom
zoom = 1;
zoomTarget = null;
options = { duration: 500, queue: false, easing: "swing" };

// Animation variables
desiredZoom = 1;

// Searching
isSearching = false;
queries = 0;

function Init()
{
	// When the document is clicked default the zoom and return to center and reset ui
	$(document).click( function (e) {
		if( !$(e.target).hasClass("card") 				&&
			!$(e.target).hasClass("search_input") 		&&
			!$(e.target).hasClass("search_button")		&&
			!$(e.target).hasClass("query")				&&
			!$(e.target).hasClass("query_label")		&&			
			!$(e.target).hasClass("query_title")		&&					
			!$(e.target).hasClass("clear_button")	&&		
			!$(e.target).hasClass("query_button")	  )
		{
			zoom = 1;
			zoomTarget = null;
			
			$(".card").each( function(){
				$(this).css( "z-index", "0" );
				$(this).css( "border", "0" );			
			});
			
			SizeCards();
			FocusViewport( null );
		}
	});
	
	//	Prevent mouse window scrolling and turn mouse wheen into zoom
	$(document).bind("mousewheel DOMMouseScroll MozMousePixelScroll", function(e){
	
		// Zoom scales 
		var e = window.event || e.originalEvent; // old IE support
		var delta = Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail)));

		if( delta < 0 )
		{
			zoom *= .80;
			if( zoom <= 0.3 )
			{
				zoom = 0.3;
			}
		}
		else
		{
			zoom /= .80;
			
			if( zoom >= 5 )
			{
				zoom = 5;
			}			
		}
		
		
		SizeCards();
		FocusViewport( zoomTarget );
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
	
	// Enter key will submit 
	$(document).keypress(function(e)
	{
		if( e.keyCode == 13 )
		{
			$('#search_button').click();
		}
	});	
	
	FocusViewport( null, 0 );		
}

function RemoveCard( card )
{
	// If this card is the 'weightiest' card then recalc the max weight
	if( $(card).attr("weight") == maxWeight )
	{
		maxWeight = 0;
		$(".card").each(function(){
			if( this == card)
			{
				return;
			}
			
			weight = $(this).attr("weight");
			if( weight > maxWeight )
			{
				maxWeight = weight;
			}
		});
	}
	
	// Remove this card
	$(card).fadeOut( 400, function(){
		$(card).remove();
		SizeCards();
	});
}

function AddCard( card, x, y )
{
	// Temp testing variables
	weight = 5;
	x += ( Math.random() * 1000 ) - 500;
	y += ( Math.random() * 1000 ) - 500;
	
	// Create a new card object
	var $div = $("<div>", 
	{	
		class: "card",
		name: card.title,
		left: x,
		top: y,
		weight: weight,
		q:queries,
	});
	
	if( weight > maxWeight )
	{
		maxWeight = weight;
	}
	
	// Move the card to the correct spot to make it appear to 
	// come out of nothing
	$div.css("left", ( x*zoom )+( cardSize[0]*zoom)/2 );
	$div.css("top", ( y*zoom )+(cardSize[1]*zoom)/2 );
	
	// Set the background of the card
	$div.css("background-image", "url('image/card/"+card.img+"')" );
	
	// When the card gets clicked focus on it
	$div.click(function(e) {
		
		var card = e.target;
		// Stop the current animation
		$("body").stop();
						
		SizeCards( card );
		FocusViewport( card );
		
	});
	
	$("body").append($div);
	
	SizeCards();
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
		
	// Move UI Elements
	$("#center_top").animate( { left: -x + ( $(window).width()/2 - $("#center_top").width()/2.0 ), top: -y+5 }, options );
	$("#right").animate( { left: -x + $(window).width() - ($("#right").width()), top: -y }, options );

}

function FocusViewport( target, duration )
{
	// If a custom duration is specified switch for this animation
	var oldDuration = options.duration;
	if( duration != undefined )
	{
		options.duration = duration;
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
		CenterViewport(-( cardSize[0] / 2 ) * zoom,0);
	}
	
	// Reset the speed
	options.duration = oldDuration;	
	
}
function SizeCards( card )
{
	// If defined then consider this card as the target
	if( card != undefined )
	{
		var weight = $(card).attr("weight");
		
		// Make the zoom the desired size to zoom into card
		zoom = ( 1 / ( weight / maxWeight ) ) * 2;
		
		// Set the zoom target
		zoomTarget = card;	
	}

	// Scale all of the cards width, height, and set the positions relative to the new zoom level
	$(".card").each( function()
	{
		var weight = $(this).attr("weight");
		
		var p = weight / maxWeight;
		var left = $(this).attr("left");
		var top = $(this).attr("top");
		
		$(this).css("z-index", 0 );
		$(this).animate({	"width":cardSize[0]*p*zoom,"height":cardSize[1]*p*zoom,
							"left":(left*zoom)+"px", "top":(top*zoom)+"px"}, options)
	});
}

// Will append a query button to right panel
function AddQuery( search, data )
{

	// If this is the first query button then add a button to clear everything
	if( $("#right").is(':empty') )
	{
		// Remove everything button
		var $remove_button = $("<button>", 
		{	
			class: "clear_button",
			onclick: "Clear();",
			text: "Clear All",
		});
		$("#right").append($remove_button)
	}
	
	// Create a query button
	var $query = $("<div>", 
	{	
		class: "query",
		q:queries,
	});
	
	// Title
	var $title = $("<div>", 
	{	
		class: "query_title",
		onclick: "HighlightQuery( "+queries+")",
		text: "'" + search + "' " + data.length + " results",
	});
	$query.append($title)
	
	// Remove query button
	var $remove_button = $("<button>", 
	{	
		class: "query_remove_button",
		onclick: "RemoveQuery( "+queries+")",
		text: "x",
	});
	$query.append($remove_button)
	
	// Create labels for each card returned
	for( c in data )
	{
		var $label = $("<div>",
		{
			class:"query_label",
			onclick:"HighlightQuery("+queries+",\""+data[c].title+"\")",
			text:data[c].title,
		});
		$query.append($label);
	}
	
	// Increase the query counter
	queries++;
	
	$query.fadeIn();
	$("#right").append($query);	
}

// Removes all of the objects that are a part of the specified query
function RemoveQuery( query_id )
{
	// Remove each card generated with this query
	$(".card[q="+query_id+"]").each( function(e){
		RemoveCard( this );
	});
	
	// Remove button
	$(".query[q="+query_id+"]").fadeOut( 400, function(){
		$(this).remove();
	});
}

// Removes all of the objects that are a part of the specified query
function HighlightQuery( query_id, title )
{
	// Unhighlight all cards
	$(".card").each( function(e){
		$(this).css( "z-index", "0" );
		$(this).css( "border", "" );
	});	
	
	var jq = "[q="+query_id+"]";
	
	// If a title is specified then alter the jquery
	if( title != undefined )
	{
		jq += "[name='"+title+"']";
	}
	
	// Remove each card generated with this query
	$(".card"+jq).each( function(e){
		$(this).css( "z-index", "1" );
		$(this).css( "border", "3px solid #0F0" );
		
		// If a title was specified then focus on that card
		if( title != undefined )
		{
			SizeCards( this );
			FocusViewport( this );
		}
	});
}

// Will http request the php image loading script
function LoadCards( q )
{
	var m = $("input.mythic").prop('checked');
	var r = $("input.rare").prop('checked');
	var u = $("input.uncommon").prop('checked');
	var c = $("input.common").prop('checked');
	var post = { 
			"q" : q,
			"m" : m,			
			"r" : r,
			"u" : u,
			"c" : c,			
	};
	$.post(
		"script/php/load_cards.php",
		post,
		function(data) {
		
			// Reset ui
			$("#search_button").css("background-image", "url('image/magnifyingglass.png')");
			$("#search_button").removeAttr( "disabled" );		
		
			cards = JSON.parse(data);
			var x = ( Math.random() * 3000 ) - 1500;
			var y = ( Math.random() * 3000 ) - 1500;
			
			// Add each card to the screen
			for ( c in cards )
			{
				AddCard( cards[c], x, y );
			}
						
			// Add a remove button to the right panel
			AddQuery( q, cards );
		}
	);
}

// Search for cards button function
function SearchCards(e)
{
	// Don't allow searching for the default value
	if( $("#search_input").val() == $("#search_input").prop("defaultValue") )
	{
		return;
	}
	
	// Don't search if there is a request pending
	if( $("#search_button").attr("disabled") == "disabled" )
	{
		return;
	}
	
	// Load the cards
	LoadCards( $("#search_input").val() );
	
	// Alter UI state
	$("#search_input").val( $("#search_input").prop("defaultValue") );
	$("#search_button").attr("disabled", "disabled");
	$("#search_button").css("background-image", "url('image/processing.gif')");
}

// Removes all querys and all cards from the page
function Clear()
{
	// Clear all dynamically generated objects
	$(".card, .query, .clear_button").fadeOut( 400, function(){
		$(this).remove();
	});		
}

$(document).ready(function(){
	Init();
});