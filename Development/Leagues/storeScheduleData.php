<?php /*****************************************
File: storeScheduleData.php
Creator: Derek Dekroon
Created: June 5/2012
This file is used to enter scheduled matches and dates into their respective databases. It takes in the sport, season, and league as inputs, along with the URL
for the schedule to be added. Make sure to get the 'www.perpetualmotion.org/....' but NOT 'http://' as it gets added automatically.

Known Errors: Holidays will screw up the week that the scheduled matches are on... might not matter but pending how playoffs work.
			  In order for a match to be submitted a date, venue, and 2 teams are needed. After all this data is collected, whether it's the right match or not it will be submitted
			  		NOTE: which team # the program is on is set back to 0 on each venue, so if venues are right, this program SHOULDN'T crash.
			  Practise cannot be the venue, This is something Dave will need to make sure of, if practise is a venue, switch it to a venue in the database in order for correct results.

******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/scheduleJSFunctions.js'/></script>
	<script src='$jQueryPage' ></script>
	<script>
		$(document).ready(function(){
		  $('#toggleTextFieldButton').click(function(){
			$('#checkDataRow').fadeToggle(250);
			$('#checkDataButtonRow').fadeToggle(250);
		  });
		});
	</script>";
$container = new Container('Store Schedule Data', '../includeFiles/leagueStyle.css', $javaScript);

require_once('../includeFiles/scheduleDeclarations.php');
require_once('../includeFiles/scheduleFormFunctions.php');
require_once('../includeFiles/scheduleSQLFunctions.php');
require_once('../includeFiles/dateClass.php');
require_once('..'.DIRECTORY_SEPARATOR.'Schedule'.DIRECTORY_SEPARATOR.'class_lib.php');
require_once('../includeFiles/class_store_schedule.php');
//echo '-'.ini_get('allow_url_fopen').'-';

$schedVars = new StoreScheduleVars();
if(($schedVars->sportID = $_GET['sportID']) == '') {
	$schedVars->sportID = 0;
}
if(($schedVars->seasonID = $_GET['seasonID']) == '') {
	$schedVars->seasonID = 0;
}
if(($schedVars->leagueID = $_GET['leagueID']) == '') {
	$schedVars->leagueID = 0;
}


isset($_POST['makeSchedule'])? $makeSchedule = 1:$makeSchedule = 0;
isset($_POST['checkData'])? $checkData = 1:$checkData = 0;


if($schedVars->leagueID != 0) {
	declareLeagueVariables($schedVars->leagueID);
} else {
    $schedVars->leagueScheduleLink = '';
}
$toPrint = '';

if($makeSchedule == 1 || $checkData == 1) {
	
	if ($schedVars->leagueID == 0) {
		print 'ERROR - league = 0';
		exit(0);
	} else{
		if($makeSchedule == 1) {
			mysql_query("DELETE FROM $scheduledMatchesTable WHERE scheduled_match_league_id = $schedVars->leagueID") 
				or die('ERROR deleting old values - '.mysql_error());
			$toPrint .= 'Records deleted: '.mysql_affected_rows().'<br />';
		}
	}
	getVenues($schedVars->sportID);
	
	if($makeSchedule == 1) {
		$schedVars->leagueScheduleLink = $_POST['url'];
		setScheduleLink($schedVars->leagueID, $schedVars->leagueScheduleLink);
		$schedVars->fileName = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$schedVars->leagueScheduleLink;
		//print $schedVars->fileName.'<br />';
		$fh = fopen($schedVars->fileName, 'r') or die('cant open file '.$schedVars->fileName);
		$schedules = fread($fh, filesize($schedVars->fileName));
		fclose($fh);
		$doc = new DOMDocument();
	
		if($schedules != '') {
			if (!(@$doc->loadHTML($schedules))) {
				libxml_clear_errors();
			}
		}
		
		$sections = $doc->getElementsByTagName('table');
		$nodeNo = $sections->length;
		$schedVars->venueTeamNumber = 0;
		for($k=0; $k<$nodeNo; $k++) {
			$sec = $sections->item($k);
			$links = $sec->getElementsByTagName('td');
			for ($j=0; $j< $links->length ; $j++) {
				$cellValues[] = $links->item($j)->nodeValue;
			}
		}
	} else {
		$cellValues = preg_split("/(\t|\n|\r)/", $_POST['checkTextField']);
	}
	
	for ($j=0; $j<count($cellValues); $j++) {
		$schedVars->isDate = 0;
		if($cellValues[$j] != '') { //this gets the value from a td cell and stores it for processing.
			$schedVars->nodeFullText = preg_replace('/\s\s+/', ' ', $cellValues[$j]); //strips excess whitespace
			$schedVars->nodeString = preg_replace('/\*+/', '', $schedVars->nodeFullText); //Gets rid of stars
			if($schedVars->nodeIsTeam == 1) {
			$schedVars->teamNamesArray[intval($schedVars->nodeTeamNum)] = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($schedVars->nodeString, ENT_QUOTES));
				$schedVars->nodeIsTeam = 0;
				$schedVars->nodeFullText = '';
				$schedVars->nodeString = '';
			}
		} else {
			$schedVars->nodeString = '';
			$schedVars->nodeFullText = '';
		}
		//print $schedVars->nodeString.'<br />';
		
		
		//The way Dave makes his html files sometimes there's some junk values (ie \n) within crutial cells, this takes that junk out
		//Get all dates and store them accordingly
		//checks for a date, I was having a problem with 'Eat a Bag of Discs' being counted as a date, hench the second part of the statement
		if (preg_match("/[0-9]{1,}/", $schedVars->nodeString) && !($schedVars->curDate = strtotime($schedVars->nodeString)) === false) {
			if (preg_match("/^((([0]?[1-9]|1[0-2])(:|\.)[0-5][0-9]((:|\.)[0-5][0-9])?( )?(AM|am|aM|Am|PM|pm|pM|Pm))|(([0]?[0-9]|1[0-9]|2[0-3])(:|\.)[0-5][0-9]((:|\.)[0-5][0-9])?))$/"
				, $schedVars->nodeString) == 0) { //ISNT a time (eg 6:30pm)
				if($schedVars->getGames == 0) {
					$schedVars->getGames = 1;
					if($schedVars->leagueIsSplit == 0 && $makeSchedule == 1) {
						setTeamNums($schedVars->leagueID, $schedVars->teamNamesArray);
					}
					getTeamIDArray($schedVars->leagueID);
					$schedVars->declareTeamOpps();
				}
				$schedVars->gamesThisWeek = array();
				$count = 0; //used to keep track of when games are being played so they can be checked for errors properly
				$schedVars->weekNum++;
				$schedVars->dayOfYear = date('z', $schedVars->curDate) ;
				$schedVars->newGameTime = 0;
				if($makeSchedule == 1) {
					$schedVars->curDateID = createDate($schedVars->sportID, $schedVars->leagueDayOfWeek, $schedVars->nodeString, $schedVars->weekNum, $schedVars->dayOfYear, $schedVars->seasonID);
				}
				if(strlen($cellValues[++$j]) > 10) {
					$schedVars->inlineNotes[$schedVars->weekNum] = $schedVars->curDateID.'@'.$cellValues[$j];
				} else {
					$schedVars->inlineNotes[$schedVars->weekNum] = $schedVars->curDateID.'@ ';
				}
				$schedVars->venueRow = 0;
				$schedVars->maxVenueRow = 0;
				$schedVars->isDate = 1;
				$schedVars->practiseRow = 0;
				$schedVars->numVenuesByDate = 0;
				$schedVars->usedVenueIDs = array();
			} else { // A time is found, apparently this does need to be stored... as an int
				//get the numbers
				$timeString = '';
				$PM = 1; //by default games are in das PM
				for ($i=0;$i<strlen($schedVars->nodeString);$i++) {
					if (is_numeric($schedVars->nodeString[$i])) {
						$timeString.=$schedVars->nodeString[$i];
					} else if (strcasecmp($schedVars->nodeString[$i], 'p') == 0) {
						$PM = 1;
					} else if (strcasecmp($schedVars->nodeString[$i], 'a') == 0) {
						$PM = 0;
					}
				}
				if($schedVars->venueTeamNumber == 0 && $schedVars->curVenueID != 0) {
					$schedVars->newGameTime = 0;
				}
				$schedVars->matchTimes[$schedVars->newGameTime++] = intval($timeString) + 1200*$PM;
			}
		} //end of if found a date
		
		if($schedVars->numTeamsFound >= $schedVars->numTeamsDB && $schedVars->getGames == 0 && $schedVars->nodeString != '') {
			$schedVars->noteTop .= $schedVars->nodeString.'%';
		}
		if(strlen($schedVars->nodeFullText) > 10 && strlen($schedVars->nodeFullText) < 30 && substr($schedVars->nodeFullText, 0, 1) == '*') {
			$schedVars->inlineNotes[$schedVars->weekNum] .= '$'.$schedVars->curDateID.'@'.$schedVars->nodeFullText;
		}
		
		$schedVars->venueFound = getCurVenueID($schedVars->nodeString);
		
		if($schedVars->venueFound == 1 && $schedVars->weekNum >= $schedVars->leaguePlayoffWeek) {
			if(strpos($schedVars->nodeFullText, '***') !== false) {
				$schedVars->venueStars[$schedVars->weekNum] .= '3-';
			} else if(strpos($schedVars->nodeFullText, '**') !== false) {
				$schedVars->venueStars[$schedVars->weekNum] .= '2-';
			} else if(strpos($schedVars->nodeFullText, '*') !== false) {
				$schedVars->venueStars[$schedVars->weekNum] .= '1-';
			} else {
				$schedVars->venueStars[$schedVars->weekNum] .= '0-';
			}
		}
		
		if($schedVars->weekNum < $schedVars->leaguePlayoffWeek) {
			$isTeam = checkTeamNum($schedVars->nodeString, $schedVars->practiseRow, $schedVars->getGames, $schedVars->teamIDArray);
			if($isTeam == 1) {
				if($makeSchedule == 1) {
					createMatch($schedVars->leagueID, $schedVars->teamToBeStored, $schedVars->curVenueID, 
						$schedVars->matchTimes[$schedVars->timeSlotNum], $schedVars->curDateID, $schedVars->venueRow);
				}
				$schedVars->teamOpps[$schedVars->teamNums[$schedVars->teamToBeStored[0]]][$schedVars->teamNums[$schedVars->teamToBeStored[1]]]++;
				$schedVars->teamOpps[$schedVars->teamNums[$schedVars->teamToBeStored[1]]][$schedVars->teamNums[$schedVars->teamToBeStored[0]]]++;
				checkSameGames($schedVars->teamNums[$schedVars->teamToBeStored[0]], $schedVars->teamNums[$schedVars->teamToBeStored[1]]);
				$schedVars->gamesThisWeek[$count][0] = $schedVars->teamNums[$schedVars->teamToBeStored[0]];
				$schedVars->gamesThisWeek[$count++][1] = $schedVars->teamNums[$schedVars->teamToBeStored[1]];
				
				$schedVars->timeSlotNum++;
				if($schedVars->timeSlotNum > $schedVars->numColumns) {
					$schedVars->numColumns = $schedVars->timeSlotNum;
				}
			} else if($isTeam > 1) {
				$schedVars->nodeIsTeam = 1;
				$schedVars->numTeamsFound++;
				$schedVars->nodeTeamNum = $schedVars->teamNumInLeague; //value obtained from checkTeamNum
			}
		} else if($schedVars->weekNum >= $schedVars->leaguePlayoffWeek) {
			$schedVars->potentialTeamNum = preg_replace('/[^0-9]/', '', $schedVars->nodeString);
			if(strcmp('vs', $schedVars->nodeString) == 0) {
				$schedVars->venueTeamNumber += 2;
				while($cellValues[++$j] =='') {}
				$schedVars->teamToBeStored[0] = $schedVars->lastNode;
				$schedVars->teamToBeStored[1] = $cellValues[$j];
				if($makeSchedule == 1) {
					createPlayoffMatch($schedVars->leagueID, $schedVars->teamToBeStored, $schedVars->curVenueID, $schedVars->matchTimes[$schedVars->timeSlotNum], 
						$schedVars->curDateID, $schedVars->venueRow);
				}
				$schedVars->timeSlotNum++;
			} else if($practiseRow == 1 && is_numeric($potentialTeamNum)) {
				$schedVars->venueTeamNumber += 2;
				$schedVars->teamToBeStored[0] = 1;
				$schedVars->teamToBeStored[1] = $schedVars->nodeString;
				if($schedVars->makeSchedule == 1) {
					createPlayoffMatch($schedVars->leagueID, $schedVars->teamToBeStored, $schedVars->curVenueID, $schedVars->matchTimes[$schedVars->timeSlotNum], 
						$schedVars->curDateID, $schedVars->venueRow);
				}
				$schedVars->timeSlotNum++;
			}
			$schedVars->lastNode = $schedVars->nodeString;
		}
		
		if(strlen($schedVars->nodeFullText) > 30 && substr($schedVars->nodeFullText, 0, 1) == '*') {
			$schedVars->noteBottom.= $schedVars->nodeFullText.'%';
		}
		
	}
	if($makeSchedule == 1) {
		setScheduleVariables($schedVars->leagueID, $schedVars->numColumns, $schedVars->inlineNotes, $schedVars->noteTop,
			 $schedVars->noteBottom, $schedVars->venueStars);
		$schedule = new Schedule($schedVars->leagueID, 'store');
	}
} 

if(isset($_POST['fixNumbers'])) {
	fixWeeks();
}
$sportsDropDown = getSportDD($schedVars->sportID);
$seasonsDropDown = getSeasonDD($schedVars->seasonID);
$leaguesDropDown = getLeaguesDD($schedVars->sportID, $schedVars->seasonID, $schedVars->leagueID);
$weeksDropDown = getWeeksDD($schedVars->leagueID);

if($schedVars->leagueID != 0) {
	$weeksObj = getLeagueWeekData($schedVars->leagueID);
} ?>
<form action='<?php print $_SERVER['PHP_SELF'].'?sportID='.$schedVars->sportID.'&seasonID='.$schedVars->seasonID.'&leagueID='.$schedVars->leagueID ?>' method='post' id='Schedule'>
	<h1>Input a Schedule into the Database</h1>
	<div class='tableData'>
		<?php printScheduleForm($schedVars->seasonID, $schedVars->sportID, $schedVars->leagueID); ?>
	</div><div id='checkDataRow' class='tableData' style='display:none;'>
		<textarea id="checkInput" name="checkTextField"><?php print htmlentities($_POST['checkTextField'], ENT_QUOTES) ?></textarea><br />
		<input type='submit' name="checkData" value='Check Data' />
	</div><div class='tableData'>
		<?php if($schedVars->leagueID != 0 && count($weeksObj) > 0) { ?>
			<table>
				<?php printWeeksHead();
				printWeekNodes($weeksObj); 
				printBottomButton(); ?>
			</table>
		<?php } ?>
	</div>
</form>

<?php if($makeSchedule == 1 || $checkData == 1) {
	$schedVars->printDistribution();
	print $toPrint;
}

$container->printFooter();?>