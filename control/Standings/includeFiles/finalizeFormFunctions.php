<?php

//prints the top info of the score reporter, ie the game info and title
function printIDs($sportsDropDown, $seasonsDropDown, $leaguesDropDown) { 
	global $sportID, $seasonID, $leagueID ?>

	Sport
	<select id='userInput' name='sportID' onchange='reloadSport(this)'>
		<?php print $sportsDropDown; ?>
	</select><br /><br />
	Season
	<select id='userInput' name='seasonID' onchange='reloadSeason(this)'>
		<?php print $seasonsDropDown; ?>
	</select><br /><br />
	League
	<select id='userInput' name='leagueID' onchange='reloadLeague(this)'>
		<?php print $leaguesDropDown; ?>
	</select><br /><br />
	<?php print "<a target='_blank' href='../Email/emailCaptains.php?emailTarget=1&sportID=$sportID&seasonID=$seasonID&leagueID=$leagueID'>
	Email Captains
	</a>"; ?>
<?php }

//prints the matches sections of the score reporter
function printHelperHeader($matches, $games) { ?>
	<tr>	
		<th colspan=8>
			Submitted Scores
		</th>
	</tr><tr>
		<td>
			Position
		</td><td>
			Team Name
		</td>
		<?php for($j=0;$j < $matches;$j++) { ?>
			<td>
				Opponent #<?php print $j+1?>
			</td>
			<?php for($q=1;$q<= $games;$q++) { ?>
				<td>
					<?php print 'Game '.$q ?>
				</td>
			<?php }
		} ?>
	</tr>
<?php }

//prints the matches sections of the score reporter
function printHelperTeam($teamNode, $position) {
	global $games, $matches;
	$gameNum = 0; ?>
	<td>
    	<?php print $position + 1 ?>
    </td><td>
    	<?php print "<a href='../Search/teamPage.php?teamID=".$teamNode->teamID."'>".$teamNode->teamName.'</a>' ?>
    </td>
    <?php for($j=0;$j < $matches;$j++) { ?>
        <td>
        	<?php print $teamNode->oppTeamName[$gameNum]; ?>
        </td>
		<?php for($q=0;$q< $games;$q++) { ?>
		<td>
			<?php printGameResult($teamNode->gameResult[$gameNum], $teamNode->scoreUs[$gameNum], $teamNode->scoreThem[$gameNum]); 
			$gameNum++; ?>
		</td>
		<?php } ?>
    <?php }
}

function printGameResult($resultNum, $scoreUs, $scoreThem) {
	$resultString = '';
	if($resultNum == 1) $resultString .= 'Won';
	else if($resultNum == 2) $resultString .= 'Lost';
	else if($resultNum == 3) $resultString .= 'Tied';
	else if($resultNum == 4) $resultString .= 'Cancelled';
	else if($resultNum == 5) $resultString .= 'Practice';
	else {
		print 'No Idea';
		return;
	}
	$resultString .= ' ('.$scoreUs.'-'.$scoreThem.')';
	print $resultString;
}

function printTeamsHeader() { ?>
	<tr>
    	<th colspan=4>
        	League Teams
        </th>
    </tr><tr>
		<td>
        	#
        </td><td>
        	Team Name
        </td><td>
        	Position
        </td><td>
        	Spirit Position
        </td>
    </tr>
<?php }

function printTeamNode($curTeam, $i) { ?>
	<tr>
        <td>
        	<?php print $i + 1; ?>
            <input type="hidden" name="teamID[]" value="<?php print $curTeam->teamID?>" />
        </td><td>
			<?php print "<a href='/control/Search/teamPage.php?teamID=$curTeam->teamID'>$curTeam->teamName</a>" ?>
		</td><td>
        	<select name='teamNum[]'>
            <?php print $curTeam->teamNumDropDown; ?>
            </select>
        </td><td>
        	<select name='spiritNum[]'>
            <?php print $curTeam->teamSpiritDropDown; ?>
            </select>
        </td>
    </tr>
<?php }

function printTeamsFooter($leagueWeek) { ?>
    <tr>
		<td colspan=4>
			<input type='submit' name="closeStandings" onClick="return checkYesNoClose()" value='Close Standings' />
			<input type='submit' name="saveStandings" onClick="return checkYesNoSave()" value='Save Standings' />
			<input type='submit' name="undoStandings" <?php print $leagueWeek <= 50?'disabled':'' ?> onClick="return checkYesNoUndo()" value='Undo Closing' />
		</td>
    </tr>
<?php }