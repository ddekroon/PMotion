<?php 
ob_start();
date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'whoGetsEmailed.php');
require_once('includeFiles/groupMailFunctions.php');
require_once('includeFiles/groupSqlFunctions.php');
require_once('includeFiles/groupVariableDeclarations.php');
require_once('includeFiles/groupErrorChecking.php');
require_once('includeFiles/groupFormFunctions.php');
require_once('includeFiles/playerClass.php');

global $addressesTable;
$sportID=1;
$sportDropDown = getSportDD($sportID);
declareSportVariables();
for($i=0;$i<3;$i++) {
	$leagueDropDowns[$i] = getLeagueDD($leagueID[$i], $sportID);
}

if (isset($_POST['register'])) {	
	$playersArray = getSubmittedValues($teamID);
	$leagueNames = getLeagueNames($leagueID);
	for($i=0;$i<3;$i++) {
		$leagueDropDowns[$i] = getLeagueDD($leagueID[$i], $sportID);
	}
	
	if(checkErrors($leagueID[0], $playersArray, $payMethod) == 1) {
		insertInfo($playersArray, $groupComments, $leagueID, $leagueNames, $payMethod, $aboutUsMethod, $aboutUsText);
		mailForm($playersArray, $groupComments, $sportName, $leagueID, $leagueNames, $payMethod);   //Send confirmation emails to the convenor and the user who registered
		mailWaivers($playersArray, $sportName, $leagueNames, $sportID);
		header("Location: thankyouGroup.htm");  //show the thank you for registering page
	}
	
	$array = $_POST['checkBox'];
	for($i=0;$i<count($playersArray);$i++) {
		if(in_array($i, $array)) {
			$addEmail=$playersArray[$i]->playerEmail;
			$addQuery = "INSERT INTO $addressesTable (EmailAddress)  VALUES ('$addEmail');";
			mysql_query($addQuery); 
		}
		else{
			$deleteEmail=$playersArray[$i]->playerEmail;
			$delQuery = "DELETE FROM $addressesTable WHERE EmailAddress = '$deleteEmail'";	
			mysql_query($delQuery);
		} 	
	}
}
$payInfoDropDown = getPayInfoDD($payMethod,$seas_name);
$aboutUsDropDown = getAboutUsDD($aboutUsMethod); ?>

<html>
	<head>
    	<title><?php print $titleHeader?></title>
        <link rel="stylesheet" type="text/css" href="includeFiles/design.css"/>
        <script type="text/javascript" src="includeFiles/groupJavaFunctions.js"></script>
    </head>
    <body>
        <form id='devRegGroup' METHOD='POST' action=<?php print $_SERVER['PHP_SELF'].'?sportID='.$sportID?>  />
        	<table class='master' align=center>
            	<?php printJavaScript();
				printFormHeader($logo, $sportHeader);
				printDropDowns($sportDropDown, $registration_due, $leagueDropDowns);
				printPlayerForm($playersArray, $people);
				printFormCommentsAndButtons($groupComments, $payInfoDropDown, $registration_due, $aboutUsDropDown, $aboutUsText); ?>
            </table>
        </form>
    </body>
</html>
<?php ob_flush();?>