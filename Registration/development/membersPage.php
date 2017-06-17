<?php
session_start ();

require(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'security.php');
date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once('includeFiles/teamMailFunctions.php');
require_once('includeFiles/teamClass.php');

//Start actual code
if(isset($_SESSION['username'])){
	$username = $_SESSION['username'];
	$userID = $_SESSION['userID'];
}else{
	$username = 'unknown';
	$userID = 0;
}

//automatically sets what leagues/seasons are/aren't available based on dates in the database
$currentDate = date('Y-m-d');
$seasonsQuery = "SELECT * FROM $seasonsTable";
$seasonsArray = mysql_query($seasonsQuery) or die ('Date error'.mysql_error());
while($seasonNode = mysql_fetch_array($seasonsArray)) {
	$openDate=$seasonNode['season_registration_opens_date'];
	$closeDate=$seasonNode['season_registration_up_until'];
	$seasonID = $seasonNode['season_id'];

	if(secondIsLater($openDate, $currentDate) == 1 AND secondIsLater($currentDate, $closeDate) ==1){ //within registration time
		mysql_query("UPDATE $seasonsTable SET season_available_registration = 1 WHERE season_id = $seasonID");
		mysql_query("UPDATE $leaguesTable SET league_available_for_registration = 1 WHERE league_season_id = $seasonID");
	} else { //not as much
		mysql_query("UPDATE $seasonsTable SET season_available_registration = 0 WHERE season_id = $seasonID");
		mysql_query("UPDATE $leaguesTable SET league_available_for_registration = 0 WHERE league_season_id = $seasonID");
	}
}
	
//Get current season data
$seasonQuery=mysql_query("SELECT * FROM $seasonsTable WHERE season_available_registration = 1");
$seasonArray=mysql_fetch_array($seasonQuery);
$seasonObj['registerByDate'] = date('F j, Y', strtotime($seasonArray['season_registration_due_by']));
$seasonObj['confirmationDate'] = date('F j, Y', strtotime($seasonArray['season_confirmation_due_by']));
$seasonObj['seasonName'] = $seasonArray['season_name'];
$seasonObj['seasonID'] = $seasonArray['season_id'];
$curSeason = explode(' ', $seasonName);
$seasonObj['regBySport'] = $seasonArray['season_registration_by_sport'];
$lastSeasonID = $seasonObj['seasonID'] - 1;
if($seasonObj['regBySport'] == 1) {
	$sportsQuery = mysql_query("SELECT * FROM $sportsTable 
		INNER JOIN $leaguesTable ON $leaguesTable.league_sport_id = $sportsTable.sport_id
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id WHERE sport_id > 0 
		AND season_available_registration = 1 ORDER BY sport_registration_due_date ASC, sport_id ASC") 
		or die('ERROR getting sport data - '.mysql_error());
	$numSports = 1;
	$sportsObj = array();
	$lastSportID = 0;
	while($sportArray = mysql_fetch_array($sportsQuery)) {
		if($sportArray['sport_id'] != $lastSportID) {
			$lastSportID = $sportArray['sport_id'];
			$sportsObj[$numSports]['sportName'] = $sportArray['sport_name'];
			$sportsObj[$numSports++]['sportRegDate'] = date('F j, Y', strtotime($sportArray['sport_registration_due_date']));
		}
	}
}

//Get past season data
$pastSeasonQuery = mysql_query("SELECT * FROM $seasonsTable WHERE season_id = $lastSeasonID");
$pastSeasonArray = mysql_fetch_array($pastSeasonQuery);
$pastSeasonName = $pastSeasonArray['season_name'];

//if delete was pressed, delete the old teams before loading all the users teams
if(isset($_POST['delete'])) {
	if(isset($_POST['team'])) {
		foreach($_POST['team'] as $teamID) {
			mysql_query("UPDATE $teamsTable SET team_deleted = 1 WHERE team_id = $teamID");
			//If the team being deleted is in the season users are currently able to register for, set its number in league to 0.
			$seasonQuery=mysql_query("SELECT * FROM $seasonsTable 
				INNER JOIN $leaguesTable ON $leaguesTable.league_season_id = $seasonsTable.season_id
				INNER JOIN $teamsTable ON $teamsTable.team_league_id = $leaguesTable.league_id 
				INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id 
				WHERE team_id = $teamID") or die('ERROR getting season/league data for team '.$teamID.' - '.mysql_error());
			$seasonArray=mysql_fetch_array($seasonQuery);
			$delLeagueID = $seasonArray['league_id'];
			$seasonAvailableForReg = $seasonArray['season_available_registration'];
			$teamNumInLeague = $seasonArray['team_num_in_league'];
			$teamObj = new Team();
			$teamObj->teamName = $seasonArray['team_name'];
			$teamObj->teamLeagueName = $seasonArray['league_name'];
			$teamObj->teamSportName = $seasonArray['sport_name'];
			$teamObj->teamID = $seasonArray['team_id'];
			if ($seasonAvailableForReg == 1 && $teamNumInLeague > 0) {
				fixTeamNumbers($delLeagueID, $teamID);
				mysql_query("UPDATE $teamsTable SET team_num_in_league = 0 WHERE team_id = $teamID");
				mailTeamUnregistered(0);
			}
		}
	} else {
		print 'Error deleting teams - No teams selected<br/>';
	}
}

if($seasonObj['regBySport'] == 1) {
	$regDueLine =  '<h3>Registration Due By</h3>';
	$lastDate = '';
	$printArray = array();
	for($i = 1; $i <= count($sportsObj); $i++) {
		if($lastDate == '') {
			$lastDate = $sportsObj[$i]['sportRegDate'];
		}	
		if($sportsObj[$i]['sportRegDate'] == $lastDate) {
			$printArray[] = $sportsObj[$i]['sportName'];
		} else {
			$regDueLine .= '<h3>'.join(', ', $printArray).' - '.$lastDate.'</h3>';
			$printArray = array();
			$printArray[] = $sportsObj[$i]['sportName'];
		}
		$lastDate = $sportsObj[$i]['sportRegDate'];
		if($i == count($sportsObj)) {
			$regDueLine .= '<h3>'.join(', ', $printArray).' - '.$sportsObj[$i]['sportRegDate'].'</h3>';
		}
	}
} else {
	$regDueLine .= '<h3>Registration due date: '.$seasonObj['registerByDate'].'</h3>';
}

//get users past team(s) data
$teamsQueryString = "SELECT * FROM $teamsTable
	Inner Join $leaguesTable ON $teamsTable.team_league_id = $leaguesTable.league_id
	Inner Join $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id
	Inner Join $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
	WHERE team_managed_by_user_id = $userID AND team_deleted = 0";
$teamsQuery=mysql_query($teamsQueryString); 
$count=0;
while ($teamsArray=mysql_fetch_array($teamsQuery)){
	$pastTeams[$count]['registered'] = $teamsArray['team_finalized'];
	$pastTeams[$count]['sportName'] = $teamsArray['sport_name'];
	$pastTeams[$count]['sportID'] = $teamsArray['sport_id'];
	$pastTeams[$count]['teamName'] = stripslashes($teamsArray['team_name']);
	$pastTeams[$count]['leagueName'] = preg_replace("/\*(.*)\*/",'',$teamsArray['league_name']);
	$pastTeams[$count]['day'] = dayString($teamsArray['league_day_number']);
	$pastTeams[$count]['seasonName'] = $teamsArray['season_name'].' '.$teamsArray['season_year'];
	$pastTeams[$count]['teamID'] = $teamsArray['team_id'];
	$count++; 
} ?>


<html>
	<head>
    	<title>
        	Register for Perpetual Motion
        </title>
		<link type="text/css" rel="stylesheet" href="includeFiles/registrationStyle.css" media="all" />
        <script type="text/javascript">
			function confirm_delete() {
				return confirm('are you sure you wan to delete team(s)? Please note if you\'re deleting any currently registered teams they will be unregistered for the coming season');
			}
		</script>
    </head>
    <body>		
        <form name='members' method='post' action='membersPage.php' onsubmit="return confirm_delete()">
        <span class="login">
			Welcome, <?php print $username?> (<a href=/Login/logout.php>Logout</a>)
		</span>
        <div class='container'>
			<h1>Perpetual Motion's Online Registration System</h1>
			<div class='pastTeams'>
			<?php if(mysql_num_rows($teamsQuery) > 0){ ?>
			<table>
				<tr>
					<th colspan=7>
						Previous Teams Registered<br /><span>
						<?php if($curSeason[0] == 'Summer'){ 
						print "<font size=2 color=red>$pastSeasonName teams have first priority for $seasonName but must confirm registration by: $confirmationDate
							<br /><br />";
						}
						print "Click on your team's name to re-register a previous team for the $seasonName league";?></span>
					</td>
				</tr><tr>
					<th>
						<input type='submit' name='delete' value='Delete'>
					</th><th>
						Team Name
					</th><th>
						Sport
					</th><th>
						League
					</th><th>
						Day
					</th><th>
						Season
					</th><th>
						Registered
					</th>
				</tr>	
				<?php for($i = 0; $i < count($pastTeams); $i++) { ?>
					<tr>
						<td>
							<input type='checkbox' name='team[]' value=<?php print $pastTeams[$i]['teamID']?>></input>
						</td><td>
							<a href='signupTeam.php?sportID=<?php print $pastTeams[$i]['sportID']?>&teamID=<?php print $pastTeams[$i]['teamID']?>'><?php print $pastTeams[$i]['teamName']?></a>
						</td><td>
							<?php print $pastTeams[$i]['sportName'] ?>
						</td><td>
							<?php print $pastTeams[$i]['leagueName'] ?>
						</td><td>
							<?php print $pastTeams[$i]['day'] ?>
						</td><td>
							<?php print $pastTeams[$i]['seasonName'] ?>
						</td><td>
							<?php print $pastTeams[$i]['registered'] == '0' ? $registeredYN = 'No' : $registeredYN='Yes';?>
						</td>
					</tr>
				<?php } ?>
			</table>
        	<?php }// end if strlen ?>
			</div><div class='registrationDate'>
				<?php print $regDueLine ?>
                
                
			</div><div class='bottomButtons'>
				<table align="center" class="logosTable">
					<tr>
						<th colspan=4> 
							Select a league logo below to start a new registration
						</th>
					</tr><tr>
						<td>
							<a href=signupTeam.php?sportID=1><div id='ultimateLogo'>
								<img style="vertical-align:middle;" src='/Logos/ultimate_0.png'>
							</div></a>
						</td><td>
							<a href=signupTeam.php?sportID=2><div id='volleyballLogo'>
								<img style="vertical-align:middle;" src='/Logos/volleyball_0.png'>
							</div></a>
						</td><td>
							<a href=signupTeam.php?sportID=3><div id='footballLogo'>
								<img style="vertical-align:middle;" src='/Logos/football_0.png'>
							</div></a>
						</td><td>
							<a href=signupTeam.php?sportID=4><div id='soccerLogo'>								
								<img style="vertical-align:middle;" src='/Logos/soccer_0.png'>
							</div></a>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>

<?php //takes in two dates in form yyyy-mm-dd and compares them, returns 0 on the first being bigger, 1 otherwise
function secondIsLater($earlyDate, $lateDate) {
	$earlierDate=explode("-", $earlyDate);
	$laterDate=explode("-", $lateDate);
	if($earlierDate[0] > $laterDate[0]) {
		return 0;
	} else if ($earlierDate[0] == $laterDate[0]) {
		if ($earlierDate[1] > $laterDate[1]) {
			return 0;
		} else if ($earlierDate[1] == $laterDate[1]) {
			if ($earlierDate[2] > $laterDate[2]) {
				return 0;
			}
		}
	}
	return 1;
}

//figures out which team is being deleted, sets all the teams with num in league higher than deleted team to -1.
function fixTeamNumbers($leagueID, $teamID) {
	global $teamsTable;
	$teamNum = 0;
	$deleteNum = 0;
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 ORDER BY team_num_in_league ASC")
		or die('ERROR getting teams to change #s'.mysql_error());
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$teamIDArray[$teamNum] = $teamArray['team_id'];
		$teamNumInLeague[$teamNum] = $teamArray['team_num_in_league'];
		if($teamIDArray[$teamNum] == $teamID) {
			$deleteNum = $teamNumInLeague[$teamNum];
		}
		$teamNum++;
	}
	
	if($deleteNum != 0) {
		for($i=0; $i< $teamNum; $i++) {
			if($i > $deleteNum) {
				mysql_query("UPDATE $teamsTable SET team_num_in_league = team_num_in_league-1 WHERE team_id = $teamID[$i]") 
					or die('ERROR updating team numbers '.mysql_error());
			}
		}
	} else {
		print 'Error changing team nums in league, current team to deleteID not in teams database';
	}
}

function dayString($dayNum) {
	if($dayNum ==1) {
		return 'Monday';
	} else if($dayNum ==2) {
		return 'Tuesday';
	} else if($dayNum ==3) {
		return 'Wednesday';
	} else if($dayNum ==4) {
		return 'Thursday';
	} else if($dayNum ==5) {
		return 'Friday';
	} else if($dayNum ==6) {
		return 'Saturday';
	} else if($dayNum ==7) {
		return 'Sunday';
	}
} ?>
