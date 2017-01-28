<?php /*****************************************
File: confirmSend.php
Creator: Derek Dekroon
Created: July 7/2012
Page to let a user make any final edits on an email before they get sent out.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$container = new Container('Confirm Send Email');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'whoGetsEmailed.php');

if(($emailTarget = $_GET['emailTarget']) == '') {
	$emailTarget = 0;
}

$mailMessage = stripslashes($_POST['message']);
$mailSubject = stripslashes($_POST['subject']);
$mailSender = stripslashes($_POST['sender']);
if(($checkBoxes = $_POST['checkBox']) != '') {
	$numEmails = 0;
	foreach($_POST['checkBox'] as $checkBox) {
		$playerID[$numEmails] = $_POST['playerID'][$checkBox];
		$playerName[$numEmails] = $_POST['playerName'][$checkBox];
		$playerEmail[$numEmails] = $_POST['playerEmail'][$checkBox];
		$playerLeagueID[$numEmails] = $_POST['playerLeagueID'][$checkBox];
		$teamName[$numEmails] = $_POST['teamName'][$checkBox];
		$numEmails++;
	}
}

if(isset($_POST['send'])){
	$sentAddresses = array();
    $sender = $_POST['sender'].'@perpetualmotion.org';
    $subject=nl2br(stripslashes($_POST['subj']));
    $message=nl2br(stripslashes($_POST['mess']));
	$emailsSentString='';
	$playerEmail = $_POST['playerEmail'];
	$playerName = $_POST['playerName'];
	$playerTeamName = $_POST['playerTeamName'];
	$playerLeagueID = $_POST['playerLeagueID'];
	$file = fopen('whoGotEmailed.txt', "a");
	if($file) {
		print '<u>Messages Sent To:</u><br /><br />';
		for($i=0;$i<count($playerEmail);$i++) {
			$checkString = $playerName[$i].' '.$playerEmail[$i].' '.$playerTeamName[$i].' '.$playerLeagueID[$i];
			if(!in_array($checkString, $sentAddresses)) {
				$newMessage = replaceKeys($message, $_POST['playerID'][$i]);
				try {
				sendEmails(array($playerEmail[$i]), $sender, $subject, $newMessage);
				} catch (phpmailerException $e) {
				  echo $e->errorMessage(); //error messages from PHPMailer
				} catch (Exception $e) {
				  echo $e->getMessage();
				}
				fwrite($file, $playerEmail[$i].' - '.$subject.' '.date('F jS h:i:s A')."\n");
				array_push($sentAddresses, $checkString);
				print($playerEmail[$i]);
			}
		}
		fclose($file);
	}
	//sendAdminEmails(controlPanelMailer(), $sender, $subject, $message);	
} elseif(isset($_POST['cancel'])) { //if cancel selected, close tab
	echo "<script>window.close();</script>";
}else{ //if neither selected, show page

	$sendAddresses = array();?>
	
	<form name='confirmEmails' action="<?php echo $_SERVER['PHP_SELF']."?emailTarget=$emailTarget"?>" method='post'>
	<input type='hidden' value="<?php print htmlentities($mailMessage, ENT_QUOTES); ?>" name="message">
	<input type='hidden' value="<?php print htmlentities($mailSubject, ENT_QUOTES); ?>" name="subject">
	<input type='hidden' value="<?php print htmlentities($mailSender , ENT_QUOTES); ?>" name='sender'>
	<h1>Message Confirmation</h1>
	<div class='tableData'>
		<table>
			<tr>
				<th>
					Message Info
				</th>
			</tr><tr>
				<td>
					Subject: 
					<input type='text' name='subj' value="<?php print htmlentities($mailSubject, ENT_QUOTES);?>">
				</td>
			</tr><tr>
				<td>
					<textarea name='mess' COLS=60 ROWS=8><?php print htmlentities($mailMessage, ENT_QUOTES); ?></textarea>
				</td>
			</tr><tr>
				<td>
					<button style="font-size:18px; font-weight:700;" type='submit' name='send'>Send</button>
					<button style="font-size:18px; font-weight:700;" type='SUBMIT' name='cancel'>Cancel</button>
				</td>
			</tr>
		</table>
	</div><div class='tableData'>
		<table>
			<tr>
				<th colspan=3>
					Recipients
				</th>
			</tr><tr>
				<td>
					Name
				</td><td>
					Email
				</td><td>
					Team
				</td>
			</tr>         
		<?php for($i=0;$i<$numEmails;$i++) { 
			$checkString = $playerName[$i].' '.$playerEmail[$i].' '.$teamName[$i].' '.$playerLeagueID[$i];
			if(!in_array($checkString, $sendAddresses)) {?>
				<tr>
					<td>
						<?php print $playerName[$i] ?>
						<input type="hidden" value="<?php print $playerID[$i]?>" name="playerID[]">
					</td>
					<td>
						<?php print $playerEmail[$i];?>
						<input type="hidden" value="<?php print $playerName[$i]?>" name="playerName[]">
						<input type="hidden" value="<?php print $playerEmail[$i]?>" name="playerEmail[]">
					</td>
					<td>
						<?php print $teamName[$i];?>
						<input type="hidden" value="<?php print $teamName[$i]?>" name="playerTeamName[]">
						<input type="hidden" value="<?php print $playerLeagueID[$i]?>" name="playerLeagueID[]">
					</td>
				</tr>
				<?php array_push($sendAddresses, $checkString);
			}
		}?>
		</table>
	</div>
	</form>
    <?php 
		/* variable to keep track of number of emails printed*/
		$emailCount = "1";
		for($i=0;$i<$numEmails;$i++) {
			/*prints 49 emails then prints || to show the division*/
			if($emailCount != "49"){
				print $playerEmail[$i].",";?>
				<input type="hidden" value="<?php print $playerName[$i]?>" name="playerName[]">
				<input type="hidden" value="<?php print $playerEmail[$i]?>" name="playerEmail[]">
                <?php $emailCount++;
            }else{
				/* dividing every 49th email with two beraks*/ ?>
				<?php print $playerEmail[$i];?>
				<input type="hidden" value="<?php print $playerName[$i]?>" name="playerName[]">
				<input type="hidden" value="<?php print $playerEmail[$i]?>" name="playerEmail[]">
                <br/><br/>
                <?php $emailCount++;
				$emailCount = "1";
				} ?>
    <?php } ?>
<?php }// end else, no buttons were selected ?>

<?php $container->printFooter(); ?>

<?php function replaceKeys($message, $playerID) {
	global $playersTable, $teamsTable, $leaguesTable, $emailTarget, $subject;
	global $tournamentPlayersTable, $tournamentTeamsTable, $tournamentsTable, $dbConnection;
	$leagueName = '';
	$teamName = '';
	if($emailTarget != 5) { //5 represents the old tournaments
		
		$playerQuery = "SELECT * FROM $playersTable 
			INNER JOIN $teamsTable ON $playersTable.player_team_id = $teamsTable.team_id 
			INNER JOIN $leaguesTable ON $teamsTable.team_league_id = $leaguesTable.league_id WHERE player_id = $playerID";
		if(!($result = $dbConnection->query($playerQuery))) print 'ERROR getting player data - '.$dbConnection->error;
		if($result->num_rows == 0) {
			$playerQuery = "SELECT * FROM $playersTable WHERE player_id = $playerID";
				if(!($result = $dbConnection->query($playerQuery))) print 'ERROR getting player data - '.$dbConnection->error;
		}
		$playerObj = $result->fetch_object();
		$playerFirst = ucfirst($playerObj->player_firstname);
		$playerLast = ucfirst($playerObj->player_lastname);
		$playerObj->player_sex == 'M'?$playerGender = 'Mr.':$playerGender = 'Ms.';
		$teamName = $playerObj->team_name;
		if(strlen($playerObj->league_name) > 2) {
			$leagueName = $playerObj->league_name.' - '.dayString($playerObj->league_day_number);
		} else {
			$leagueName = '';
		}
		$introLine = $playerFirst.', ('.$teamName.' Team Captain)';
	} else {
		$playerQuery = "SELECT * FROM $tournamentPlayersTable INNER JOIN $tournamentTeamsTable 
			ON $tournamentTeamsTable.tournament_team_id = $tournamentPlayersTable.tournament_player_team_id 
			INNER JOIN $tournamentsTable ON $tournamentsTable.tournament_id = 
			$tournamentPlayersTable.tournament_player_tournament_id
			WHERE tournament_player_id = $playerID";
		if(!($result = $dbConnection->query($playerQuery))) print 'ERROR getting player data - '.mysql_error();
		if($result->num_rows == 0) {
			$playerQuery = "SELECT * FROM $tournamentPlayersTable INNER JOIN $tournamentsTable ON 
				$tournamentsTable.tournament_id = $tournamentPlayersTable.tournament_player_tournament_id 
				WHERE tournament_player_id = $playerID";
			if(!($result = $dbConnection->query($playerQuery))) print 'ERROR getting player data - '.mysql_error();
		}
		$playerObj = $result->fetch_object();
		$playerFirst = ucfirst($playerObj->tournament_player_firstname);
		$playerLast = ucfirst($playerObj->tournament_player_lastname);
		$playerObj->tournament_player_gender == 'M'?$playerGender = 'Mr.':$playerGender = 'Ms.';
		$teamName = $playerObj->tournament_team_name;
		if($playerObj->tournament_is_leagues == 1) {
			$leagueNamesArray = explode('%', $playerObj->tournament_league_names);
			$leagueName = $leagueNamesArray[$playerObj->tournament_player_league_id];
		} else {
			$leagueName = $playerObj->tournament_name;
		}
	}
	
	$newMessage = preg_replace('/\%first\%/', $playerFirst, $message);
	$newMessage = preg_replace('/\%last\%/', $playerLast, $newMessage);
	$newMessage = preg_replace('/\%gender\%/', $playerGender, $newMessage);
	$newMessage = preg_replace('/\%team\%/', $teamName, $newMessage);
	$newMessage = preg_replace('/\%league\%/', $leagueName, $newMessage);
	$newMessage = preg_replace('/\%introLine\%/', $introLine, $newMessage);
	
	$subject = preg_replace('/\%first\%/', $playerFirst, $subject);
	$subject = preg_replace('/\%last\%/', $playerLast, $subject);
	$subject = preg_replace('/\%gender\%/', $playerGender, $subject);
	$subject = preg_replace('/\%team\%/', $teamName, $subject);
	$subject = preg_replace('/\%league\%/', $leagueName, $subject);
	$subject = preg_replace('/\%introLine\%/', $introLine, $subject);
	return $newMessage;

} ?>