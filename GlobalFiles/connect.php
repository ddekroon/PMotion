<?php
	$dbservertype='mysql';
	$servername='localhost';
	
	// username and password to log onto db server
	$dbusername='pmotiondata';
	$dbpassword='rememberHeavysalmon4';
	
	// name of database
	$dbname='data_perpetualmotion';
	
	////////////////////////////////////////
	////// DONOT EDIT BELOW  /////////
	///////////////////////////////////////
	
	//Connecting to Database
	connecttodb($servername,$dbname,$dbusername,$dbpassword);
	function connecttodb($servername,$dbname,$dbuser,$dbpassword){
		global $link;
		$link=mysql_connect ("$servername","$dbuser","$dbpassword");
		if(!$link){
			die("Could not connect to MySQL");
		}
		mysql_select_db("$dbname",$link) or die ("could not open db".mysql_error());
	}
	//////// End of connecting to database ////////
?>
