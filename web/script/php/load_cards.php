<?php
 	//Variables for connecting to your database.
	//These variable values come from your hosting account.
	$hostname = "visualguider.db.11456014.hostedresource.com";
	$username = "visualguider";
	$dbname = "visualguider";

	//These variable values need to be changed by you before deploying
	$password = "guidEr11!";
	$usertable = "cards";

	//Connecting to your database
	mysql_connect($hostname, $username, $password) OR DIE ("Unable to 
	connect to database! Please try again later.");
	mysql_select_db($dbname);
	
	$search = mysql_real_escape_string(strip_tags( $_POST['q'] ));
	$cards = mysql_query( "SELECT DISTINCT * from cards WHERE title like '%{$search}%' GROUP BY title LIMIT 10 ");
	
	function get_images( $query, $start )
	{
		$url = 'http://ajax.googleapis.com/ajax/services/search/images?v=1.0';
		$url .= '&q=' . urlencode( $query );
		$url .= '&start=' . urlencode( $start );
		$c = curl_init();
		curl_setopt( $c, CURLOPT_URL, $url );
		curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
		$data = curl_exec( $c );
		return json_decode( $data );
	}	
	
	$output = array();	
	while( $row = mysql_fetch_assoc( $cards ) )
	{
		if( strlen( $row['title'] ) <= 0 )
		{
			continue;

		}
		
		if( strlen( $row['img'] ) <= 0 )
		{
			// For new sources if found
			// Sources:
			//		'tcg player' *** has no borders ***
			$source = rand( 1, 1 );
			switch( $source )
			{
				case 1:
					$cardData = get_images( "gatherer.wizards.com {$row['title']}", 0);
				break;		
				
			}
			
			copy($cardData->responseData->results[0]->unescapedUrl, "/home/content/14/11456014/html/misc/visualguider/image/card/{$row['title']}.jpg");
			$row['img'] = "{$row['title']}.jpg";
			
			if( is_readable( "/home/content/14/11456014/html/misc/visualguider/image/card/{$row['title']}.jpg" ) )
			{
				mysql_query( "UPDATE cards set img='{$row['img']}' WHERE title=\"{$row['title']}\"" );
			}
		
		}
		
		$output[] = $row;		
	}
	
	echo json_encode( $output );
?>