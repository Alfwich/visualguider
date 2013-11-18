<?php
return;

$cards = file_get_contents( "cards.txt" );
$cards = explode( PHP_EOL, $cards );
$inBody = false;
$lastLine = '';

$bodyLine = 0;
$title = '';
$cost = '';
$set = '';
$rarity = '';

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

foreach( $cards as $line )
{
	if( isset( $line ) && strlen( $line ) > 0 )
	{
		if( !$inBody ){
			$inBody = true;
		}
		$bodyLine++;
		
		switch( $bodyLine )
		{
			case 1:
				$title = $line;
			break;
			case 2:
				$cost = strtoupper ($line);
			break;			
		}
		
		$lastLine = $line;
	}
	else
	{
		
		$sets = explode( ',', $lastLine );
		foreach( $sets as $set )
		{
			if( strlen( $set ) <= 0 )
			{
				continue;
			}
			
			$setrare = explode( '-', str_replace( ' ', '', $set ) );
			mysql_query( "INSERT INTO `cards` (`title`,`type`,`mana`,`set`,`rarity`)VALUES('{$title}','MTG','{$cost}', '{$setrare[0]}', '{$setrare[1]}')" );
			echo "INSERT INTO `cards` (`title`,`type`,`mana`,`set`,`rarity`)VALUES('{$title}','MTG','{$cost}', '{$setrare[0]}', '{$setrare[1]}')";
		}
						
		$bodyLine = 0;
		$title = '';
		$cost = '';		
		$inBody = false;
	}
}
?>