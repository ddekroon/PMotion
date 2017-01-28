<?php
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once('includeFiles/prntPgFormFunctions.php');
require_once('includeFiles/prntPgVariableDeclarations.php');

if(($seasonID = $_GET['seasonID']) == '') {
    print 'Error seasonID is NULL';
    exit(0);
}
if(($sportID = $_GET['sportID']) == '') {
    print 'Error sportID is NULL';
    exit(0);
}

declareSportVariables();
$leagueNames = getLeagueData($seasonID, $sportID); ?>

<html>
    <head>
    	<title>Print Page</title>
        <link rel="stylesheet" href="includeFiles/regPrintStyle.css" type="text/css" />

    </head>
    <body>
        <table class='master' align=center>
            <?php printFormHeader($logo, $sportHeader);
            printLeagues($leagueNames);
            printPlayerForm();
            printFormFooter(); ?>
        </table>
    </body>
</html>