 <?php
require_once('class_week.php');
require_once('includeFiles/dateClass.php');
require_once('includeFiles/class_schedule_variables.php');

class Schedule{
	
	public  $league_id,
			$schedule_title, $schedule_html, $scheduleWeeks, $playoffStartWeek, $sportID, $schedVarsObj, $venueNames, 
			$leagueIsSplit, $leagueSplitWeek, $maxGameTimes, $maxGameTimesPlayoffs, $topNotes, $bottomNotes, $numColumns,
			$venueStars, $scheduleLink, $inlineNotes, $distributionTable;
			
	private $maxGamesWeekNum, $maxGamesWeekNumPlayoffs, $teamNamesArray;

	function __construct($lg_id, $type){
		$this->league_id = $lg_id;
		$this->maxGameTimes = 0;
		$this->maxGameTimesPlayoffs = 0;
		$this->scheduleWeeks = array();
		$this->distributionTable = '';
		$this->setScheduleTitle();
		$this->setTeamListTable();
		$this->setTeamsArray();
		$this->setLeagueData();
		$this->schedVarsObj = new ScheduleVariables();
		
		for($i = 1; $i <= $this->getNumberOfWeeksInLeague(); $i++){
			$this->setWeekScheduleArray($i); //gets values for fields dates and times	
		}
		if($lg_id == 500) { //dont think this is ever used anymore, was to check excel code but I changed how that works
			$this->checkForMatchErrors();
		} else {
			if($type == 'edit') {
				$this->checkForMatchErrors();
				$this->printEditableSchedule();
			} else if ($type == 'store') {
				$this->checkForMatchErrors();
				$this->makeScheduleHTML(1);
				$this->loadToFile();
			} else if($type=='playoff') {
				$this->makeScheduleHTML($this->playoffStartWeek);
				$this->printHTML();
			}
		}
	}
	
	//getBaseVariables
	private function getBaseVariables() {
		$this->schedVarsObj->numWeeks = $_POST['weeksNum'];
		$this->schedVarsObj->numTimes = $_POST['numTimes'];
		$this->schedVarsObj->sameTimes = $_POST['sameTimes'];
		$this->schedVarsObj->numVenues = $_POST['numVenues'];
		$this->schedVarsObj->sameVenues = $_POST['sameVenues'];
		$this->schedVarsObj->startDay = $_POST['startDay'];
		$this->schedVarsObj->startMonth = $_POST['startMonth'];
		$this->schedVarsObj->startYear = $_POST['startYear'];
		for($i = 1, $j = 0; $i <= $this->schedVarsObj->numWeeks; $i++, $j++) {
			$this->schedVarsObj->timesArray[$i] = $_POST["timesDD"][$j];
			$this->schedVarsObj->venuesArray[$i] = $_POST["venuesDD"][$j];
		}		
	}
	
	//setLeagueData
	//Query scheduled_matches table and find all unique values of date_id where the league_id is the desired league
	public function setLeagueData(){
		global $datesTable, $scheduledMatchesTable, $teamsTable, $leaguesTable, $venuesTable, $scheduleVariablesTable;
		$leagueQuery = mysql_query("SELECT * FROM $datesTable
			INNER JOIN $leaguesTable ON $leaguesTable.league_day_number = $datesTable.date_day_number 
			AND league_season_id = date_season_id WHERE league_id = ".$this->league_id." ORDER BY date_week_number")
				or die('ERROR getting number of weeks - '.mysql_error());
		$weekNum = 1;		
		$lastWeekNum = 0;
		while($leagueArray = mysql_fetch_array($leagueQuery)) {
			if($leagueArray['date_week_number'] != $lastWeekNum) {
				$lastWeekNum = $leagueArray['date_week_number'];
				$this->scheduleWeeks[$weekNum] = new Week();
				$this->scheduleWeeks[$weekNum]->setWeekDescription($leagueArray['date_id'], $leagueArray['date_description']);
				$this->playoffStartWeek = $leagueArray['league_playoff_week'];
				$this->sportID = $leagueArray['league_sport_id'];
				$this->leagueIsSplit = $leagueArray['league_is_split'];
				$this->leagueSplitWeek = $leagueArray['league_split_week'];
				$weekNum++;
			}
		}
		$this->number_of_weeks_in_league = $weekNum - 1;
		
		//set Dynamic Schedule Link
		if($this->sportID == 1) {
			$scheduleLink = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GuelphUltimate'.DIRECTORY_SEPARATOR.'Schedules'.DIRECTORY_SEPARATOR;
		} else if($this->sportID == 2) {
			$scheduleLink = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'BeachVolleyball'.DIRECTORY_SEPARATOR.'Schedules'.DIRECTORY_SEPARATOR;
		} else if($this->sportID == 3) {
			$scheduleLink = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'FlagFootball'.DIRECTORY_SEPARATOR.'Schedules'.DIRECTORY_SEPARATOR;
		} else if($this->sportID == 4) {
			$scheduleLink = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'Soccer'.DIRECTORY_SEPARATOR.'Schedules'.DIRECTORY_SEPARATOR;
		}
	
		$this->scheduleLink = $scheduleLink.'schedule-'.$this->league_id.'.htm';
		
		//Get all venues for the sport
		$lastVenueName = '';
		$venuesQuery = mysql_query("SELECT * FROM $venuesTable WHERE venue_sport_id = ".$this->sportID." 
			ORDER BY venue_short_show_name") or die('ERROR getting venues - '.mysql_error());
		while($venueArray = mysql_fetch_array($venuesQuery)) {
			if($venueArray['venue_short_show_name'] != $lastVenueName) {
				$this->venueNames[$venueArray['venue_id']] = $venueArray['venue_name'];
			}
		}
		
		$notesQuery = mysql_query("SELECT * FROM $scheduleVariablesTable WHERE schedule_variables_league_id = ".$this->league_id)
			or die('ERROR getting notes - '.mysql_error());
		$leagueArray = mysql_fetch_array($notesQuery);
		$this->numColumns = $leagueArray['schedule_variables_num_columns'];
		$this->topNotes = explode('%', $leagueArray['schedule_variables_top_note']);
		$this->bottomNotes = explode('%',$leagueArray['schedule_variables_bottom_note']);
		$stars = explode('%', $leagueArray['schedule_variables_playoff_venue_stars']);
		$counter = 0;
		foreach($stars as $starsNode) {
			$this->venueStars[$this->playoffStartWeek + $counter++] = explode('-', $starsNode);
		}
		$inline = explode('%', $leagueArray['schedule_variables_inline_notes']);
		foreach($inline as $inlineNode) {
			$nodeNotes = explode('$', $inlineNode);
			$counter = 0;
			foreach($nodeNotes as $timeRowNote) {
				$nodeTokens = explode('@', $timeRowNote);
				$this->inlineNotes[$nodeTokens[0]][$counter++] = $nodeTokens[1];
			}
		}
	}
	
	//getNumberOfWeeksInLeague
	public function getNumberOfWeeksInLeague(){
		return $this->number_of_weeks_in_league;
	}
	
	//setWeekScheduleArray
	public function setWeekScheduleArray($weekNum){
		global $datesTable, $venuesTable, $scheduledMatchesTable, $teamsTable, $leaguesTable;
		
		$teamQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = ".$this->league_id." AND team_num_in_league > 0
			ORDER BY team_num_in_league ASC") or die('ERROR getting team data - '.mysql_error());
		while($teamArray = mysql_fetch_array($teamQuery)) {
			$teamNumsArray[$teamArray['team_id']] = $teamArray['team_num_in_league'];
		}
		
		$query = "SELECT * FROM $scheduledMatchesTable
			Inner Join $teamsTable ON $teamsTable.team_id = $scheduledMatchesTable.scheduled_match_team_id_2
			Inner Join $venuesTable ON $venuesTable.venue_id = $scheduledMatchesTable.scheduled_match_field_id
			Inner Join $leaguesTable ON $leaguesTable.league_id = $scheduledMatchesTable.scheduled_match_league_id
			Inner Join $datesTable ON $datesTable.date_id = $scheduledMatchesTable.scheduled_match_date_id
			WHERE 
			$leaguesTable.league_id = ".$this->league_id." AND $datesTable.date_week_number = $weekNum 
			ORDER BY date_id ASC, scheduled_match_venue_num_in_week ASC, scheduled_match_time ASC, scheduled_match_id ASC";
		
		$result = mysql_query($query) or die('ERROR getting schedules data - '.mysql_error());
		if(mysql_num_rows($result) == 0) {
			$query = "SELECT * FROM $datesTable 
				INNER JOIN $leaguesTable ON $leaguesTable.league_day_number = $datesTable.date_day_number
				WHERE league_id = ".$this->league_id." AND date_week_number = $weekNum AND date_sport_id = league_sport_id
				AND date_season_id = league_season_id";
			$result = mysql_query($query) or die('error getting no games week data - '.mysql_error());
			$key = mysql_fetch_array($result);
			$this->scheduleWeeks[$weekNum]->setWeekDescription($key['date_id'], $key['date_description']);
			return 0;
		}
		$times = array();
		$dateID = 0;
		$venueID = 0;
		$maxGamesAtVenue = 0;
		$curVenueGames = 0;
		while ($key = mysql_fetch_assoc($result)){
			if($dateID == 0) { //This info will be the same for every iteration, set the values the first time
				$dateID = $key['date_id'];
				$this->scheduleWeeks[$weekNum]->setWeekDescription($key['date_id'], $key['date_description']);
			}
			
			$vnu = $key['venue_id'];
			$vnuName = $key['venue_short_show_name'];
			//adds a new venue
			if($venueID == 0 OR $venueName != $vnuName){ //It's a new venue
				$venueID = $vnu;
				$venueName = $vnuName;
				$curVenueGames = 0;
				$this->scheduleWeeks[$weekNum]->addVenue($venueID, $key['venue_name'], $key['venue_link'], $this->venueNames, $this->numColumns);
			}
			
			//This block creates a list of all the times for the week
			$timeNode = $key['scheduled_match_time'];
			$this->scheduleWeeks[$weekNum]->setTime($vnu, $timeNode);
					
			//Adds the match to the array
			$team_id_1 = $key['scheduled_match_team_id_1'];
			$team_id_2 = $key['scheduled_match_team_id_2'];
			$teamNumOne = $teamNumsArray[$team_id_1];
			$teamNumTwo = $teamNumsArray[$team_id_2];
			$teamOneString = $key['scheduled_match_playoff_team_1'];
			$teamTwoString = $key['scheduled_match_playoff_team_2'];
			if($team_id_1 == 1 && $team_id_2 == 1 && strlen($teamOneString) > 0 && strlen($teamTwoString) > 0 
				&& $weekNum < $this->playoffStartWeek) {
				$this->playoffStartWeek = $weekNum;
			}
				
			if($weekNum < $this->playoffStartWeek) {
				$this->scheduleWeeks[$weekNum]->setVenueMatch($team_id_1, $team_id_2, $teamNumOne, $teamNumTwo, $timeNode);
			} else {
				$this->scheduleWeeks[$weekNum]->setVenueMatchPlayoff($teamOneString, $teamTwoString, $timeNode);
			}
		}
		//$this->scheduleWeeks[$weekNum]->setNumTimes($maxGamesAtVenue);
		//$this->setColumns($weekNum, $maxGamesAtVenue);
	}
	
	//checkForMatchErrors
	public function checkForMatchErrors() {
		$teamOpps = array(); //Going to be used to hold which teams any specific team has played for.
		$count = 1;
		foreach($this->teamNamesArray as $team) {
			$teamOpps[$count] = array();
			for($i = 1; $i <= count($this->teamNamesArray); $i++) {
				$teamOpps[$count][$i] = 0;
			}
			$count++;
		}
		for($week_number = 1; $week_number <= $this->getNumberOfWeeksInLeague(); $week_number++){
			$weeks = $this->scheduleWeeks[$week_number];
			if(!(($this->leagueIsSplit == 1 && $week_number > $this->leagueSplitWeek) || $this->leagueIsSplit == 0) 
				|| $weeks->getNumVenues() == 0) {
				
				continue;
			}
			
			$weekMatches = array();
			$counter = 0;
			foreach($weeks->venuesArray as $week_venue){
				$venueMatches = array();
				## Loop through as many time slots as there are at the venue in the week ##
				for($timeSlot=0; $timeSlot < $week_venue->getNumTimeSlots(); $timeSlot++){
					if($week_venue->getMatchTeams($timeSlot) != new Match()) {	
						$weekMatches[$counter] = $week_venue->getMatchTeams($timeSlot);
						$teamOpps[$weekMatches[$counter]->getTeamOneNum()][$weekMatches[$counter]->getTeamTwoNum()]++;
						$teamOpps[$weekMatches[$counter]->getTeamTwoNum()][$weekMatches[$counter]->getTeamOneNum()]++;
					
						if(preg_replace('/[^0-9]/', '', $weekMatches[$counter]->getTeamOneString()) != '') {
							$isInt = 1;
						} else if (preg_replace('/[^0-9]/', '', $weekMatches[$counter]->getTeamTwoString()) != '') {
							$inInt = 1;
						} else {
							$isInt = 0;
						}
						for($i = 0; $i < $counter; $i++) {
							if($week_number < $this->playoffStartWeek 
								&& $weekMatches[$i]->getTeamOneNum() == $weekMatches[$counter]->getTeamOneNum()
								&& $weekMatches[$i]->getTeamTwoNum() == $weekMatches[$counter]->getTeamTwoNum()) {
								
								print '<span style="color:red">Warning teams '.	$weekMatches[$counter]->getTeamOneNum().' and '.$weekMatches[$counter]->getTeamTwoNum().' play eachother twice in week '.$week_number.'</span><br />';
							} else if($week_number >= $this->playoffStartWeek 
								&& $weekMatches[$i]->getTeamOneString() == $weekMatches[$counter]->getTeamOneString()
								&& $weekMatches[$i]->getTeamTwoString() == $weekMatches[$counter]->getTeamTwoString()
								&& $isInt == 1) {
								
								print '<span style="color:red">Warning teams '.	$weekMatches[$i]->getTeamOneString().' and '.$weekMatches[$i]->getTeamTwoString().' play eachother twice in week '.$week_number.'</span><br />';
							}
						}
						$counter++;
					}
				}
			}
		}//end foreach (weeks in the league)
		
		$this->distributionTable .= '<table class="showDistribution"><tr><th>Tm</th>';
		for($i = 1; $i <= count($this->teamNamesArray); $i++) {
			$this->distributionTable .= '<th>'.$i.'</th>';
		}
		$this->distributionTable .= '</tr>';
		$count = 1;
		foreach($this->teamNamesArray as $team) {
			$count % 2 == 0?$colourFilter= 'style="background-color:#ccc;"':$colourFilter = '';
			$this->distributionTable .= '<tr><th '.$colourFilter.'>'.$count.'</th>';
			for($i = 1; $i <= count($this->teamNamesArray); $i++) {
				if($i == $count) {
					$this->distributionTable .= '<td '.$colourFilter.'>x</td>';
				} else {
					$this->distributionTable .= '<td '.$colourFilter.'>'.$teamOpps[$count][$i].'</td>';
				}
			}
			$this->distributionTable .= '</tr>';
			$count++;
		}
		$this->distributionTable .= '</table>';
	}
	
	//setColumns
	function setColumns($weekNum, $maxGamesAtVenue) {
		if($weekNum < $this->playoffStartWeek) {
			if($maxGamesAtVenue == $this->numColumns) {
				print 'k';
				$this->maxGameTimes = $maxGamesAtVenue;
				$this->maxGamesWeekNum = $weekNum;
				for($i = 1; $i <= $weekNum; $i++) {
					for($j = $this->scheduleWeeks[$i]->getNumTimes(); $j < $this->numColumns; $j++) {
						$this->scheduleWeeks[$i]->setTime($this->scheduleWeeks[$this->maxGamesWeekNum]->getTime($j));
					}
				}
			}
		} else {
			if($maxGamesAtVenue > $this->maxGameTimesPlayoffs) {
				$this->maxGameTimesPlayoffs = $maxGamesAtVenue;
				$this->maxGamesWeekNumPlayoffs = $weekNum;
			}
			for($i = $this->playoffStartWeek; $i <= $weekNum; $i++) {
				if($this->scheduleWeeks[$i]->getNumTimes() < $this->maxGameTimesPlayoffs) {
					for($j = $this->scheduleWeeks[$i]->getNumTimes(); $j < $this->maxGameTimesPlayoffs; $j++) {
						$this->scheduleWeeks[$i]->setTime($this->scheduleWeeks[$this->maxGamesWeekNumPlayoffs]->getTime($j));
					}
				}
			}
		}
	}
	
	//getDay
	public function getday($day_number){
		if($day_number == 1) $day = 'Monday';
		else if($day_number == 2) $day = 'Tuesday';
		else if($day_number == 3) $day = 'Wednesday';
		else if($day_number == 4) $day = 'Thursday';
		else if($day_number == 5) $day = 'Friday';
		else if($day_number == 6) $day = 'Saturday';
		else if($day_number == 7) $day = 'Sunday';
		else $day = '';
		return $day;
	}
	
	//setScheduleTitle
	public function setScheduleTitle(){
		global $seasonsTable, $leaguesTable, $sportsTable;
		$query = "SELECT * FROM $leaguesTable
			Inner Join $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
			Inner Join $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id
			WHERE $leaguesTable.league_id = ".$this->league_id;
		
		$result = mysql_query($query) or die('ERROR getting title info - '.mysql_error());
		$array = mysql_fetch_array($result);
		$dayString = $this->getday($array['league_day_number']);
		
		$this->schedule_title = 'Guelph '.$array['sport_name'].'<br />'.$array['league_name'].
			' - '.$dayString.' - '.$array['season_name'].' '.$array['season_year'];
	}//end getScheduleTitle
	
	//getScheduleTitle
	public function getScheduleTitle(){
		return $this->schedule_title;
	}
	
	//setTeamListTable
	//Makes a 2-column table of all teams in the league. Takes a count of how many are in the league and divides by 2.
	//If there is an odd number of teams, the left column will have one more team than the right column
	public function setTeamListTable(){
		global $teamsTable;
		$query = "SELECT * FROM $teamsTable WHERE $teamsTable.team_league_id =  ".$this->league_id." 
			AND team_num_in_league > 0 ORDER BY $teamsTable.team_num_in_league";
		$result = mysql_query($query) or die('ERROR setting team list - '.mysql_error());
		while($key = mysql_fetch_assoc($result)){
			if(strlen($key['team_name']) > 1){
				$team_array[] = array($key['team_num_in_league'],$key['team_name'],$key['team_id']);
			}
		}
		
		## The halfway point of the list ##
		$break_team_list_point = round(count($team_array)/2);
		
		$this->team_list_table = "<TABLE  align='center' class='teamlist' style='width:95%;max-width:500px;min-width:100px;'>";
		
		for($x = 0; $x < $break_team_list_point; $x++){
			$y = $x + $break_team_list_point;
			
			## If the team name is longer than 24 characters, override the font to make it smaller ##
			$resize_left="";
			$resize_right="";
			
			if(strlen($team_array[$x][1])>=25){
				$resize_left="style=\"font-size:80%\"";
			}elseif(strlen($team_array[$y][1])>=25){
				$resize_right="style=\"font-size:80%\"";
			}
			
			$team_array[$x][0] > 0?$teamOneFilter = 'Team':$teamOneFilter = '';
			$team_array[$y][0] > 0?$teamTwoFilter = 'Team':$teamTwoFilter = '';
			$this->team_list_table .= "<TR><td>$teamOneFilter</td><td id='team_num_cell'>".$team_array[$x][0]."</td>";
			$this->team_list_table .= "<td id='team_name_left' $resize_left><a href='/allSports/teamPage.php?teamID=".$team_array[$x][2]."'>";
			$this->team_list_table .= $team_array[$x][1].'</a></td>';
			$this->team_list_table .= "<td>$teamTwoFilter</td><td id='team_num_cell'>".$team_array[$y][0]."</td>";
			$this->team_list_table .= "<td id='team_name_right' $resize_right><a href='/allSports/teamPage.php?teamID=".$team_array[$y][2]."'>";
			$this->team_list_table .= $team_array[$y][1]."</td></TR>";
		}//end for
		
		$this->team_list_table .= "</TABLE>";
	}//end setTeamList	
	
	//getTeamListTable
	public function getTeamListTable(){
		return $this->team_list_table;
	}

	//getWeekScheduleArray
	function getWeekScheduleArray(){
		return $this->week_schedule_array;
	}
	
	//setTeamsArray
	function setTeamsArray(){
		global $teamsTable;
		unset ($this->teamsNamesArray);
		$query = mysql_query("SELECT* FROM $teamsTable WHERE team_league_id = ".$this->league_id) 
			or die('ERROR getting teams - '.mysql_error());
		while($array = mysql_fetch_array($query)) {
			$this->teamNamesArray[$array['team_id']] = $array['team_name'];
		}
	}
	
	//getTeamsArray
	function getTeamsArray(){
		return $this->teamsArray;
	}
	
	//printHeaders
	function printHeaders($ttl, $tmlst){
		$headers ="<TABLE class='schedule' style='width:95%;max-width:825px;min-width:100px;'><TR><tr><th align='right'><button type='Submit' class='no-print' style='width:30px;height:25px;' name='printit' value='Print Form' onclick='javascript:window.print();'><img src='print.png' style='width:15px;height:15px;'></button></th></tr><td id='title'>$ttl</td></TR>";
		$headers.="<TR><td align='center' id='teamsCell'>$tmlst</td></TR>";
		if(count($this->topNotes) > 0) {
			$headers.="<TR><td align='center' id='notesCell'><table class='notesTable'><tr><td><textarea name='topNote' style='width:500px; height:100px;'>";
			for($i = 0; $i < count($this->topNotes); $i++) {
				if(strlen($this->topNotes[$i]) > 2) {
					$headers.= $this->topNotes[$i]."\n";
				}
			}
			$headers.='</textarea></td></tr></table></td></tr>';
		}
		print $headers;
	}
	
	//makeHeaders
	function makeHeaders($ttl, $tmlst){
		$headers  = "<html><head><title>".$this->leagueTitle.'</title>';
		$headers .= "<link type='text/css' rel='stylesheet' href='Schedule/scheduleStyle.css' /></head><body>";
		$headers .= "<TABLE align='center' class='schedule'>
  <TR>
  <tr>
    <th align='right'> <button type='Submit' class='no-print' name='printit' value='Print Form' onclick='javascript:window.print();'><img src='printButton.png' style='width:30px;height:25px;'></button>
    </th>
  </tr>
    <td id='title'>$ttl</td></TR>";
		$headers .= "<TR><td align='center' id='teamsCell'>$tmlst</td></TR>";
		if(strlen($this->topNotes[0]) > 0) {
			$headers.="<TR><td align='center' id='notesCell'><table class='notesTable'>";
			$headers.='<tr><td>'.$this->topNotes[0].'</td><td>'.$this->topNotes[1].'</td></tr>';
			for($i = 2; $i < count($this->topNotes); $i++) {
				$headers.='<tr><td></td><td>'.$this->topNotes[$i].'</td></tr>';
			}
			$headers.='</table></td></tr>';
		}
		return $headers;
	}
	
	//printBottomNote
	function printBottomNote() {
		if(count($this->bottomNotes) > 0) {
			print "<TR><td align='center' id='notesCell'><table class='notesTable' style='width:95%;max-width:825px;min-width:100px;'><tr><td>";
			print '<textarea name="bottomNote" style="height:100px; width:400px;">';
			for($i = 0; $i < count($this->bottomNotes); $i++) {
				if(strlen($this->bottomNotes[$i]) > 2) {
					print $this->bottomNotes[$i]."\n";
				}
			}
			
			print '</textarea></td></tr></table></td></tr>';
		}
	}
	
	//makeBottomNote
	function makeBottomNote() {
		if(count($this->bottomNotes) > 0) {
			$headers.="<TR><td align='center' id='notesCell'><table class='notesTable' style='width:95%;max-width:825px;min-width:100px;'>";
			for($i = 0; $i < count($this->bottomNotes); $i++) {
				if(strlen($this->bottomNotes[$i]) > 2) {
					$headers.='<tr><td>'.$this->bottomNotes[$i].'</td></tr>';
				}
			}
			$headers.='</table></td></tr>';
		}
		return $headers;
	}
	
	//makeDateLine
	public function makeDateLine($dtvl){
		$date_value="<TR><td colspan=4>$dtvl</td></TR>";
		return $date_value; 
	}
	
	//makeTeam
	public function makeTeam($tmnm){
		$team=$tmnm;
		if(strlen($team)>=18){
			$team=substr($tmnm,0,17)."...";
		}
		return $team;
	}
	
	//makeFieldCell
	public function makeFieldCell($weekNum,$field, $fieldNum, $starsNum){
		$starsArray = array('', '*', '**', '***');
		$fieldNum% 2 == 0?$colourFilter = 'background-color:#ddd;':$colourFilter = '';
		strlen($field->getVenueName()) > 20?$fontFilter = 'font-size:10px;':$fontFilter = '';
		$this->schedule_html[$weekNum].= "<tr><td style='$colourFilter' id='field_name'><a target='_blank' style='$fontFilter' href='".$field->getVenueLink()."'>".$field->getVenueName();
		if($weekNum >= $this->playoffStartWeek) {
			$this->schedule_html[$weekNum].= $starsArray[$this->venueStars[$weekNum][$starsNum]];
		}
		$this->schedule_html[$weekNum].= '</td>';
	}
	
	//printFieldCell
	public function printFieldCell($weekNum,$field, $fieldNum, $timeRow, $starsNum){
		$fieldNum % 2 == 0?$colourFilter = 'background-color:#ddd;':$colourFilter = '';
		print "<TR><td style='$colourFilter' id='field_name'><select name='fieldID[$weekNum][$timeRow][]'>".$field->getVenueDD().'</select>';
		if($weekNum >= $this->playoffStartWeek) {
			print $this->makeStarsSelect($weekNum, $starsNum, $this->venueStars[$weekNum][$starsNum]);
		}
		print "</td>";
	}
	
	//makeGame
	public function makeGame($x,$teamOneNum,$teamTwoNum, $teamOneID, $teamTwoID, $fieldNum){
		$fieldNum % 2 == 0?$colourFilter = 'style="background-color:#ddd;"':$colourFilter = '';
		$teamOneID <= 1?$teamOne='':$teamOne=$teamOneNum;
		$teamTwoID <= 1?$teamTwo='':$teamTwo=$teamTwoNum;
		if($teamOneID <=1 || $teamTwoID <= 1) {
			$versus = '';
		} else {
			$versus = 'vs';
		}
		$teamNameOne = htmlentities($this->teamNamesArray[$teamOneID], ENT_QUOTES);
		$teamNameTwo = htmlentities($this->teamNamesArray[$teamTwoID], ENT_QUOTES);
		
		$this->schedule_html[$x].= "<td $colourFilter id='team_left'>";
		$this->schedule_html[$x].= "<a class='gameLink' title='$teamNameOne'href='/allSports/teamPage.php?teamID=$teamOneID'>$teamOne</a>";
		$this->schedule_html[$x].= "</td><td $colourFilter id='versus'>$versus</td><td $colourFilter id='team_right'>";
		$this->schedule_html[$x].= "<a class='gameLink' title='$teamNameTwo' href='/allSports/teamPage.php?teamID=$teamTwoID'>$teamTwo</a>";
		$this->schedule_html[$x].= '</td>';
	}
	
	//makePlayoffGame
	public function makePlayoffGame($x,$teamOneString,$teamTwoString, $fieldNum){
		$fieldNum % 2 == 0 ? $colourFilter = 'style="background-color:#ddd;"':$colourFilter = '';
		$teamOneString == '1'?$teamOne='':$teamOne=$teamOneString;
		$teamTwoString == '1'?$teamTwo='':$teamTwo=$teamTwoString;
		if($teamOneString == '1' || $teamTwoString == '1' || ($teamOneString == '' && $teamTwoString == '')) {
			$versus = '';
		} else {
			$versus = 'vs';
		}
		
		$this->schedule_html[$x].="<td $colourFilter id='team_left'>$teamOne</td><td $colourFilter id='versus'>$versus</td><td $colourFilter id='team_right'>$teamTwo</td>";
	}
	
	//printGame
	public function printGame($weekNum, $teamOneNum, $teamTwoNum, $teamOneID, $teamTwoID, $fieldNum){
		$fieldNum % 2 == 0?$colourFilter = 'style="background-color:#ddd;"':$colourFilter = '';
		$teamOneID <= 1?$teamOne='':$teamOne=$teamOneNum;
		$teamTwoID <= 1?$teamTwo='':$teamTwo=$teamTwoNum;
		if($teamOneID <=1 || $teamTwoID <= 1) {
			$versus = '';
		} else {
			$versus = 'vs';
		}
		
		print "<td $colourFilter id='team_left'><input id='gameTeam' type='text' name='teamOne[$weekNum][$fieldNum][]' value='$teamOne'></td>";
		print "<td $colourFilter id='versus'>$versus</td>";
		print "<td $colourFilter id='team_right'><input id='gameTeam' type='text' name='teamTwo[$weekNum][$fieldNum][]' value='$teamTwo'></td>";
	}
	
	//printPlayoffGame
	public function printPlayoffGame($weekNum, $teamOneString, $teamTwoString, $fieldNum){
		$fieldNum % 2 == 0 ? $colourFilter = 'style="background-color:#ddd;"':$colourFilter = '';
		$teamOneString == '1'?$teamOne='':$teamOne=$teamOneString;
		$teamTwoString == '1'?$teamTwo='':$teamTwo=$teamTwoString;
		if($teamOneString == '1' || $teamTwoString == '1' || ($teamOneString == '' && $teamTwoString == '')) {
			$versus = '';
		} else {
			$versus = 'vs';
		}
		
		print "<td $colourFilter id='team_left'><input style='width:40px;' type='text' name='teamOne[$weekNum][$fieldNum][]' value='".htmlentities($teamOne, ENT_QUOTES)."' /></td>";
		print "<td $colourFilter id='versus'>$versus</td><td $colourFilter id='team_right'><input type='text' style='width:40px;' name='teamTwo[$weekNum][$fieldNum][]' value='".htmlentities($teamTwo, ENT_QUOTES)."' /></td>";
	}

	//makeSpacerCell
	function makeSpacerCell($x, $fieldNum){
		$fieldNum % 2== 0 ? $colourFilter = 'style="background-color:#ddd;"':$colourFilter = '';
		$this->schedule_html[$x].="<td $colourFilter id='space_between_games'>&nbsp;</td>";
	}
	
	//printSpacerCell
	function printSpacerCell($x, $fieldNum){
		$fieldNum % 2 == 0 ? $colourFilter = 'style="background-color:#ddd;"':$colourFilter = '';
		print "<td $colourFilter id='space_between_games'>&nbsp;</td>";
	}
	
	//getVenueName
	function getVenueName($venueID) {
		return $this->venueNames[$venueID];
	}
	
	//makeStarsSelect
	private function makeStarsSelect($weekNum, $fieldNum, $numStars) {
		$weeksDropDown = "<select name='numStars[$weekNum][]'>";
		$starsArray = array('-- Stars --', '*', '**', '***', );
		for($i = 0; $i < 4; $i++) {
			$weeksDropDown .= "<option ";
			$weeksDropDown .= intval($numStars) == $i ? 'selected' : '';
			$weeksDropDown .= " value=$i>$starsArray[$i]</option>";
		}
		return $weeksDropDown;
	}

	//printEditableSchedule
	function printEditableSchedule() {
		## Print the title and  list of teams (static at the top of the schedule ##
		$this->printHeaders($this->getScheduleTitle(),$this->getTeamListTable(), $this->topNotes);
		print "<input type='hidden' name='numColumns' value=".$this->numColumns.' />';
		
		## Loop through as many weeks as there are in the schedule ##
		for($week_number = 1; $week_number <= $this->getNumberOfWeeksInLeague(); $week_number++){
			$inlineNoteNum = 0;
			if(!(($this->leagueIsSplit == 1 && $week_number > $this->leagueSplitWeek) || $this->leagueIsSplit == 0)) {
				continue;
			}
			
			$weeks = $this->scheduleWeeks[$week_number];
			
			if($week_number < $this->playoffStartWeek && $weeks->getNumVenues() == 0) { //not playoffs, no games
				$holidayCheck = 0;
				
				for($j = $week_number + 1; $j <= $this->getNumberOfWeeksInLeague(); $j++) {
					if($this->scheduleWeeks[$j]->getNumVenues() != 0) {
						$holidayCheck = 1;
					}
				}
				if($holidayCheck == 1) {
					print "<input type='hidden' name='timeRows[]' value=1 />";
					print "<tr><td id='weekCell'><table class='holiday_week'><tr><td id='week_header'>";
					print $weeks->getWeekDateDescription().' **No Game - Holiday</td>';
					print '</tr></table></td></tr>';
				}
				continue;
			} else if ($week_number >= $this->playoffStartWeek && $weeks->getNumVenues() == 0) {
				continue;
			}
			
			if($weeks->getNumTimes() > $this->numColumns && $this->numColumns > 1) {
				$totalGameTimes = ceil($weeks->getNumTimes() / $this->numColumns);
				$numGamesPerRow = $this->numColumns;
			} else {
				$totalGameTimes = 1;
				$numGamesPerRow = $weeks->getNumTimes();
			}
			
			if($this->numColumns == 1) {
				$columnsForInlineNote = 2;
			} else {
				$columnsForInlineNote = 4;
			}
			
			$gameColumns = $numGamesPerRow * 4 - 1 - $columnsForInlineNote;
			
			print "<TR><td id='weekCell'><table align='center' class='schedWeek' style='width:95%;max-width:600px;'>";
			print "<TR><th id='week_header'>".$weeks->getWeekDateDescription();
			print "<input type='hidden' name='dateID[$week_number]' value=".$weeks->getWeekDateID().' /></th>';
			print "<td colspan=$columnsForInlineNote id='week_header'>".$this->inlineNotes[$weeks->getWeekDateID()][$inlineNoteNum++].'</td>';
			print "<td colspan=$gameColumns id='week_number'>Week $week_number</td></TR>";
			## The way and place that the date line is shown varies depending on the schedule format ##
			if($this->sportID == 2) {
				print "<td id='field_name'>Court</td>";
			} else {
				print "<td id='field_name'>Field</td>";
			}
			
			print "<input type='hidden' name='timeRows[]' value=$totalGameTimes />";
			
			$fieldNum = 0;
			for($timeRows = 0; $timeRows < $totalGameTimes; $timeRows++) { //amount of times rows there will be
				$skipColumn = array(0, 0, 0);
				if($timeRows > 0) {
					print '</tr><tr><td></td>';
					if(strlen($this->inlineNotes[$weeks->getWeekDateID()][$inlineNoteNum]) > 5) {
						$colspan = $numGamesPerRow * 4 - 1;
						print "<td colspan=$colspan id='week_header'>".$this->inlineNotes[$weeks->getWeekDateID()]
							[$inlineNoteNum++].'</td></tr><tr><td></td>';	
					}
				}
				
				##print time rows and dark/light rows
				for($i = $timeRows * $this->numColumns; $i < ($timeRows * $this->numColumns) + $numGamesPerRow; $i++) {
					//print $i.'-'.$weeks->getTime($i).'<br />';
					if($i % $this->numColumns == 0 || $weeks->getTime($i) >  $weeks->getTime($i - 1)) {
						print "<td colspan='3' id='game_header'>";
						print "<input type='hidden' name='time[$week_number][]' value=".$weeks->getTime($i).' />';
						print $weeks->getFormattedTime($i).'</td>';
						$i < (($timeRows * $this->numColumns) + $numGamesPerRow - 1)?$this->printSpacerCell($week_number, 1):'';
					} else {
						print "<td colspan='3'></td>";
						$i < (($timeRows * $this->numColumns) + $numGamesPerRow - 1)?$this->printSpacerCell($week_number, 1):'';
						$skipColumn[$i] = 1;
					}
				}
				if($this->sportID!=2 && $timeRows == 0){
					print  '</tr><tr><td></td>';
					for($i = 0; $i < $numGamesPerRow; $i++) {
						print "<td id='game_column_header'>Dark</td><td></td><td id='game_column_header'>White</td>";
						$i < ($numGamesPerRow - 1)?$this->printSpacerCell($week_number, 1):'';
					}
				}
				
				## Loop through all scheduled venues in the week ##
				$fieldNum = 0;
				$starsNum = 0;
				foreach($weeks->venuesArray as $week_venue){
					$venueHasGameCheck = 0; //by default assumes a venue has no games and shouldn't be printed
					for($time=$timeRows * $this->numColumns; $time < ($timeRows * $this->numColumns) + $numGamesPerRow; $time++){
						if($week_venue->getMatchTeams($time) != new Match()) {
							$venueHasGameCheck = 1;
							break;
						}
					}
					if($venueHasGameCheck == 1) {
						$this->printFieldCell($week_number, $week_venue, $fieldNum, $timeRows, $starsNum);
					} else {
						$starsNum++;
						continue;
					}
					
					## Loop through as many time slots as there are at the venue in the week ##
					for($timeSlot=$timeRows * $this->numColumns; $timeSlot < ($timeRows * $this->numColumns) + 
						$numGamesPerRow; $timeSlot++){
							
						$match = $week_venue->getMatchTeams($timeSlot);
						if($week_number < $this->playoffStartWeek) {
							$teamOneNum = $match->getTeamOneNum();
							$teamTwoNum = $match->getTeamTwoNum();
							$teamOneID = $match->getTeamOneID();
							$teamTwoID = $match->getTeamTwoID();
							$this->printGame($week_number, $teamOneNum, $teamTwoNum, $teamOneID, $teamTwoID, $fieldNum);
						} else {
							$teamOneString = $match->getTeamOneString();
							$teamTwoString = $match->getTeamTwoString();
							$this->printPlayoffGame($week_number, $teamOneString, $teamTwoString, $fieldNum);
						}
						
						
						## Add a 20px blank cell (margin) between the space for the first and second, and second
						## and third set of games ##
						if(($timeSlot + 1) % $numGamesPerRow !=0){
							$this->printSpacerCell($week_number, $fieldNum);
						}
					}
					$starsNum++;
					$fieldNum++;
				}
			}
			print "</table></td></tr>";
		}//end foreach (weeks in the league)
		$this->printBottomNote();
		print '</table>';
	}//end function drawSchedule		
	
	//datePicker
	function date_picker($date) {
		
		$day = date('j', $date);
		$month = date('n', $date);
		$year = date('Y', $date);

		$months=array('','January','February','March','April','May',
		'June','July','August', 'September','October','November','December');
	
		// Month dropdown
		$html="<select name='startMonth[]'>";
	
		for($i=1;$i<=12;$i++)
		{
		   $html .= "<option ";
		   $html .= $i==$month?'selected':'';
		   $html .= " value='$i'>$months[$i]</option>";
		}
		$html.="</select> ";
	   
		// Day dropdown
		$html.="<select name='startDay[]'>";
		for($i=1;$i<=31;$i++)
		{
		   $html .= "<option ";
		   $html .= $i==$day?'selected':'';
		   $html .= " value='$i'>$i</option>";
		}
		$html.="</select> ";
	
		// Year dropdown
		$html.="<select name='startYear[]'>";
	
		for($i=$year;$i<=$year + 1;$i++)
		{      
			$html .= "<option ";
			$html .= $i==$year?'selected':'';
			$html .= " value='$i'>$i</option>";
		}
		$html.="</select> ";
	
		return $html;
}
	
	//storeScheduleHTML
	function makeScheduleHTML($startWeek){
		
		## Print the title and  list of teams (static at the top of the schedule ##
		$this->schedule_html = array();
		if($startWeek != $this->playoffStartWeek) {
			$this->schedule_html[0] = $this->makeHeaders($this->getScheduleTitle(),$this->getTeamListTable());
		} else {
			$this->schedule_html[0] = '';
		}
		
		## Loop through as many weeks as there are in the schedule ##
		for($week_number = $startWeek; $week_number <= $this->getNumberOfWeeksInLeague(); $week_number++){
			$inlineNoteNum = 0;
			if(!(($this->leagueIsSplit == 1 && $week_number > $this->leagueSplitWeek) || $this->leagueIsSplit == 0)) {
				continue;
			}
			
			$weeks = $this->scheduleWeeks[$week_number];
			$this->schedule_html[$week_number] = '';
			
			if($week_number < $this->playoffStartWeek && $weeks->getNumVenues() == 0) { //not playoffs, no games
				if(strlen($this->inlineNotes[$weeks->getWeekDateID()][$inlineNoteNum]) > 5) {
					$this->schedule_html[$week_number] .= "<tr><td id='weekCell'><table class='holiday_week'><tr><td id='week_header'>";
					$this->schedule_html[$week_number] .= $weeks->getWeekDateDescription().' '.$this->inlineNotes[$weeks->getWeekDateID()][$inlineNoteNum].'</td>';
					$this->schedule_html[$week_number] .= '</tr></table></td></tr>';
					continue;
				} else {
					$holidayCheck = 0;
					for($j = $week_number + 1; $j < $this->getNumberOfWeeksInLeague(); $j++) {
						if($this->scheduleWeeks[$j]->getNumVenues() != 0) {
							$holidayCheck = 1;
							break;
						}
					}
					if($holidayCheck == 1) {
						$this->schedule_html[$week_number] .= "<tr><td id='weekCell'><table class='holiday_week'><tr><td id='week_header'>";
						$this->schedule_html[$week_number] .= $weeks->getWeekDateDescription().' **No Game - Holiday';
						$this->schedule_html[$week_number] .= '</td></tr></table></td></tr>';
					}
					continue;
				}
			} else if ($week_number >= $this->playoffStartWeek && $weeks->getNumVenues() == 0) {
				continue;
			}
			
			if($weeks->getNumTimes() > $this->numColumns) {
				$totalGameTimes = ceil($weeks->getNumTimes() / $this->numColumns);
				$numGamesPerRow = $this->numColumns;
			} else {
				$totalGameTimes = 1;
				$numGamesPerRow = $weeks->getNumTimes();
			}
			
			if($this->numColumns == 1) {
				$columnsForInlineNote = 2;
			} else {
				$columnsForInlineNote = 4;
			}
			
			$gameColumns = $numGamesPerRow * 4 - 1 - $columnsForInlineNote;
			
			$this->schedule_html[$week_number] .= "<TR><td id='weekCell'><table align='center' class='schedWeek' style='width:95%;max-width:600px;'>";
			$this->schedule_html[$week_number] .= "<TR><th id='week_header'>".$weeks->getWeekDateDescription().'</th>';
			$this->schedule_html[$week_number] .= "<td colspan=$columnsForInlineNote id='week_header'>".$this->inlineNotes[$weeks->getWeekDateID()][$inlineNoteNum++].'</td>';
			$this->schedule_html[$week_number] .= "<td colspan=$gameColumns id='week_number'>Week $week_number</td></TR>";
			## The way and place that the date line is shown varies depending on the schedule format ##
			if($this->sportID == 2) {
				$this->schedule_html[$week_number].="<td id='field_name'>Court</td>";
			} else {
				$this->schedule_html[$week_number].="<td id='field_name'>Field</td>";
			}
			
			for($timeRows = 0; $timeRows < $totalGameTimes; $timeRows++) {
				
				if($timeRows > 0) {
					$this->schedule_html[$week_number] .= '</tr><tr><td></td>';
					if(strlen($this->inlineNotes[$weeks->getWeekDateID()][$inlineNoteNum]) > 5) {
						$colspan = $numGamesPerRow * 4 - 1;
						$this->schedule_html[$week_number] .= "<td colspan=$colspan id='week_header'>".
							$this->inlineNotes[$weeks->getWeekDateID()][$inlineNoteNum++].'</td></tr><tr><td></td>';	
					}
				}
				
				##print time rows and dark/light rows
				for($i = $timeRows * $this->numColumns; $i < ($timeRows * $this->numColumns) + $numGamesPerRow; $i++) {
					if($weeks->getFormattedTime($i) != '') {
						$this->schedule_html[$week_number] .= "<td colspan='3' id='game_header'>".$weeks->getFormattedTime($i)."</td>";
						$i < (($timeRows * $this->numColumns) + $numGamesPerRow - 1)?$this->makeSpacerCell($week_number, 1):'';
					} else {
						$this->schedule_html[$week_number] .= "<td colspan='3'></td>";
						$i < (($timeRows * $this->numColumns) + $numGamesPerRow - 1)?$this->makeSpacerCell($week_number, 1):'';
					}
				}
				if($this->sportID!=2 && $timeRows == 0){
					$this->schedule_html[$week_number] .= '</tr><tr><td></td>';
					for($i = 0; $i < $numGamesPerRow; $i++) {
						$this->schedule_html[$week_number] .= "<td id='game_column_header'>Dark</td><td></td><td id='game_column_header'>White</td>";
						$i < ($numGamesPerRow - 1)?$this->makeSpacerCell($week_number, 1):'';
					}
				}
				
				## Loop through all scheduled venues in the week ##
				$fieldNum = 0;
				$starsNum = 0;
				foreach($weeks->venuesArray as $week_venue){
					$venueHasGameCheck = 0; //by default assumes a venue has no games and shouldn't be printed
					for($time=$timeRows * $this->numColumns; $time < ($timeRows * $this->numColumns) + $numGamesPerRow; $time++){
						if($week_venue->getMatchTeams($time) != new Match()) {
							$venueHasGameCheck = 1;
							break;
						}
					}
					if($venueHasGameCheck == 1) {
						$this->makeFieldCell($week_number, $week_venue, $fieldNum, $starsNum);
					} else {
						$starsNum++;
						continue;
					}
					
					## Loop through as many time slots as there are at the venue in the week ##
					for($timeSlot=$timeRows * $this->numColumns; $timeSlot < ($timeRows * $this->numColumns) + $numGamesPerRow; 
						$timeSlot++){
						$match = $week_venue->getMatchTeams($timeSlot);
						if($week_number < $this->playoffStartWeek) {
							$teamOneNum = $match->getTeamOneNum();
							$teamTwoNum = $match->getTeamTwoNum();
							$teamOneID = $match->getTeamOneID();
							$teamTwoID = $match->getTeamTwoID();
							$this->makeGame($week_number, $teamOneNum, $teamTwoNum, $teamOneID, $teamTwoID, $fieldNum);
						} else {
							$teamOneString = $match->getTeamOneString();
							$teamTwoString = $match->getTeamTwoString();
							$this->makePlayoffGame($week_number, $teamOneString, $teamTwoString, $fieldNum);
						}
						
						
						## Add a 20px blank cell (margin) between the space for the first and second, and second
						## and third set of games ##
						if(($timeSlot + 1) % $numGamesPerRow !=0){
							$this->makeSpacerCell($week_number, $fieldNum % 2);
						}
					}
					$starsNum++;
					$fieldNum++;
				}
			}
			$this->schedule_html[$week_number] .= "</table></td></tr>";
		}//end foreach (weeks in the league)
		$this->schedule_html[$this->getNumberOfWeeksInLeague()] .=$this->makeBottomNote().'</table></body></html>';
		
		
	}	
	
	private function loadToFile() {
		$file_ptr = fopen($this->scheduleLink, 'w');
		if($file_ptr != false) {
			foreach ($this->schedule_html as $sched){
				fwrite($file_ptr, $sched);
			}
			fclose($file_ptr);
		} else {
			print 'Couldn\'t open file stream - '.$this->scheduleLink.'<br />';
		}
	}
	
	private function printHTML() {
		foreach ($this->schedule_html as $sched){
			print $sched;
		}
	}
	
}//end Schedule ?>