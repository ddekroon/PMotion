<?php /*****************************************
File: editByLeague.php
Creator: Derek Dekroon
Created: August 5/2012
Allows a user to edit the teams/players registered for a tournament
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$container = new Container('Test MySQLi');

$dbservertype='mysql';
$servername='mysql.giantgoat.dreamhosters.com';
$database = 'data_perpetualmotion';

// username and password to log onto db server
$dbusername='pmotiondata';
$dbpassword='rememberHeavysalmon4';

// name of database
$dbname='data_perpetualmotion';


$dbConnection = new mysqli($servername, $dbusername, $dbpassword, $database);
if ($dbConnection->connect_errno) {
    $container->printError("Failed to connect to MySQL: " . $dbConnection->connect_error);
}

if(!$dbConnection->query("SELECT * FROM $teamsTable")) {
	$container->printError('Error getting teams - '.$dbConnection->error);
}

$result = $dbConnection->query("SELECT * FROM $teamsTable");
// Using iterators (support was added with PHP 5.4)
while ($row = $result->fetch_assoc()) {
    printf("'%s'@'%s'\n", $row['team_id'], $row['team_name']);
}

$dbConnection->close();

$container->printFooter(); ?>