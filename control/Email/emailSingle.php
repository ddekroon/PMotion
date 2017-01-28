<?php /*****************************************
File: addToMailList.php
Creator: Alex Eckensweiler
Created: May 23rd 2014
Adds an email to the addressdb
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
	require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');
$javaScript = "<script type='text/javascript'>
			function checkYesNo() {
				return confirm('Are you sure you want to add this email?');
			}
		</script>";
$container = new Container('Add Email Addresses', '', $javaScript);


require_once('includeFiles/emailPlayerForm.php');
require_once('includeFiles/emailSQLFunctions.php');
require_once('includeFiles/playerClass.php');

function Email($addEmail) {
	print($addEmail);
	$To[0]=$addEmail;
	sendEmails($To,'alex@perpetualmotion.org', "Test", "Test");
}
if(isset($_POST['submitButton'])) {
	Email($_POST['addEmail']);
}

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($dayNumber = $_GET['dayNumber']) == '') {
	$dayNumber = 0;
}
if(($seasonID = $_GET['seasonID']) == '') {
	$seasonID = 0;
}
if(($orderBy = $_GET['orderBy']) == '') {
	$orderBy = '';
}
if(($direction = $_GET['direction']) == '') {
	$direction = 'ASC';
}
if(($emailTarget = $_GET['emailTarget']) == '') {
	$emailTarget = 0;
}

$sportsDD = getSportDD($sportID);
$seasonsDD = getSeasonDD($seasonID);
$leaguesDD = getLeaguesDD($sportID, $seasonID, $leagueID);
$daysDD = getDayNumDD($dayNumber);

$numPlayers = getCaptainData($sportID, $leagueID, $dayNumber, $seasonID, $orderBy, $direction); 
$emailTarget = 1; ?>

<form action=<?php print $_SERVER['PHP_SELF']?> method='post' id='Schedule'>
<div class='tableData'>
	<table class='nostyle'>
		<tr>
			<td>Email address</td>
			<td>
				<input type='text' size='40' name='addEmail' value='<?php print $addEmail ?>' class='input-text'>
			</td>
		</tr><tr>
			<td colspan='2' class='t-right'>
				<input type='submit' class='input-submit' name='submitButton' value='Email' onClick='return checkYesNo()' />
			</td>
		</tr>
	</table>
</div>
</form>

<?php $container->printFooter(); ?>