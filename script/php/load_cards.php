<?php
  function clean($string) {
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
  }

	// Returns an addition to a where clause
	// Precondition: An empty string to start building the where clause or an
	//				 in process where string
	//  $new: The new boolean condition
	//  [$delimit]: The string to put in between the boolean conditions
	function WhereAdd( &$where, $new, $delimit = 'AND' )
	{
		// If the addition is empty then exit without any changes
		if( isset($new) && strlen($new) <= 0 )
		{
			return;
		}
	
		// If the string is not empty append a delimiter
		if( strlen( $where ) > 0 )
		{
			$where .= " {$delimit} ";
		}
		
		// If ther where clause is empty start the where statement
		if( strlen( $where ) <= 0 )
		{
			$where .= " WHERE";
		}
		
		// Add the new condition
		$where .= " {$new}";
	}
	
	function Set( $value )
	{
		if( $value == "true" )
		{
			return true;
		}
		
		return false;
	}

 	//Variables for connecting to your database.
	//These variable values come from your hosting account.
	$hostname = "localhost";
	$username = "arthurwut";
	$dbname = "visualguider";

	//These variable values need to be changed by you before deploying
	$password = "";
	$usertable = "cards";

	//Connecting to your database
	mysql_connect($hostname, $username, $password) OR DIE ("Unable to 
	connect to database! Please try again later.");
	mysql_select_db($dbname);
	
	// Build where statement
	$where = '';

	$search = mysql_real_escape_string(strip_tags( $_POST['q'] ));	
	WhereAdd( $where, "title like '%{$search}%'" );	
	
	if( Set( $_POST['m'] ) )
	{
		WhereAdd( $where, "rarity='M'" );
	}
	
	if( Set( $_POST['r'] ) )
	{
		WhereAdd( $where, "rarity='R'" );
	}
	
	if( Set( $_POST['u'] ) )
	{
		WhereAdd( $where, "rarity='U'" );
	}
	
	if( Set( $_POST['c'] ) )
	{
		WhereAdd( $where, "rarity='C'" );
	}	

  //WhereAdd( $where, "mana<>''" );
	
	$cards = mysql_query( "SELECT DISTINCT * from cards {$where} GROUP BY title LIMIT 10");
		
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
			
			$i = 0;
			
			while( $i < count( $cardData->responseData->results ) )
			{
			  $width = intval($cardData->responseData->results[$i]->width);
			  $height = intval($cardData->responseData->results[$i]->height);
			  if( abs($width-223) > 10 || abs($height-310) > 10 ) {
					$i++;
					continue;
				}
				
				$row['img'] = clean($row['title']);
				copy($cardData->responseData->results[$i]->unescapedUrl, "/home/webserver/www/visualguider/image/card/{$row['img']}.jpg");
				
				if( is_readable( "/home/webserver/www/visualguider/image/card/{$row['img']}.jpg" ) )
				{
					mysql_query( "UPDATE cards set img='{$row['img']}' WHERE title=\"{$row['title']}\"" );
				}
				break;
			}
		}

		
		$row["img"] = clean($row["img"]) . ".jpg";
		$output[] = $row;		
	}
	
	echo json_encode( $output );
?>
