<?php $dbservertype='mysql';
$servername='localhost';
$database = 'data_perpetualmotion';

// username and password to log onto db server
$dbusername='pmotiondata';
$dbpassword='rememberHeavysalmon4';

// name of database
$dbname='data_perpetualmotion';


$dbConnection = new mysqli($servername, $dbusername, $dbpassword, $database);
if ($dbConnection->connect_errno) {
    print "Failed to connect to MySQL: " . $dbConnection->connect_error;
} ?>