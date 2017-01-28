<?php function printPlayerHeader($sportID, $leagueID, $dayNumber, $seasonID, $direction) {

	$teamHead=makeLink('team_name', 'Team Name', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$nameHead=makeLink('player_lastname', 'Player Name', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$emailHead=makeLink('player_email', 'Email', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$sportHead=makeLink('sport_name', 'Sport', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$leagueHead=makeLink('league_name', 'League', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$seasonHead=makeLink('season_id', 'Season', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$dayHead=makeLink('league_day_number', 'Game Day', $sportID, $leagueID, $dayNumber, $seasonID, $direction); ?>
    <tr>
		<th colspan=7>
			Perpetual Teams
		</th>
	</tr><tr>
    	<td></td><td>
        	<?php print $teamHead ?>
        </td><td>
        	<?php print $nameHead ?>
        </td><td>
        	<?php print $emailHead ?>
        </td><td>
        	<?php print $sportHead ?>
        </td><td>
        	<?php print $leagueHead ?>
        </td><td>
        	<?php print $dayHead ?>
        </td>
    </tr>
<?php }
 
function makeLink($searchTerm, $headerValue, $sportID, $leagueID, $dayNumber, $seasonID, $direction){
	global $emailTarget;

	return "<a href='emailTeams.php?emailTarget=$emailTarget&sportID=$sportID&leagueID=$leagueID&dayNumber=$dayNumber&seasonID=$seasonID&orderBy=$searchTerm&direction=$direction'>$headerValue</a>";
} 

function printTeamHeader($sportsDD, $leaguesDD, $daysDD, $seasonsDD) { ?>
    <h1>Perpetual Motion's Email Database</h1>
	<div class='tableData'>
		<table>
			<tr>
				<th colspan="2">
					Email Options
				</th>
			</tr><tr>
				<td colspan="2">
					<select name='sportID' onChange="reloadPage(2)">
						<?php print $sportsDD; ?>
					</select>
					<select name='leagueID' onChange="reloadPage(2)">
						<?php print $leaguesDD; ?>
					</select>
					<select name='dayNumber' onChange="reloadPage(2)">
						<?php print $daysDD; ?>
					</select>
					<select name='seasonID' onChange="reloadPage(2)">
						<?php print $seasonsDD; ?>
					</select>
				</td>
			</tr><tr>
				<td colspan=2>
					Subject <INPUT TYPE='text' NAME='subject' width='100'></input>
					<select name='sender'>
						<option value='dave'>dave@perpetualmotion.org</option>
						<option value='derek'>derek@perpetualmotion.org</option>
						<option value='info'>info@perpetualmotion.org</option>
						<option value='scores'>scores@perpetualmotion.org</option>
						<option value='zach'>zach@perpetualmotion.org</option>
					</select>
				</td>  
			</tr><tr>
				<td>
					<textarea name='message' style='height:150px;width:450px;'></textarea>
				</td><td style="vertical-align:top;">
					<fieldset>
						<legend>Key</legend>
							First Name - %first%<br/>
							Last Name - %last%<br />
							Mr./Mrs. - %gender%<br />
							Team - %team%<br />
							League - %league%<br />
							First, (Teamname Team Captain) - %introLine%<br />
					</fieldset>
				</td>
			</tr><tr>
				<td colspan=2>
					<input type='button' name='Check_All' value='Check All' onClick='CheckAll()'>
					<input type='Submit' Value='Next'>
					<input type='button' name='UnCheck_All' value='Uncheck All' onClick='UnCheckAll()'>
				</td>
			</tr>
		</table>
	</div>
<?php }

function printTeamEmails($teamName, $numPlayers) {
	global $playerArray, $teamNum, $player;
	
	for($i=0;$i<$numPlayers;$i++) {
		$newTeamName=$playerArray[$i]->playerTeamName;
		if($teamName==$newTeamName) {
			print ($playerArray[$i]->playerEmail."\r\n");
		}
	}
}

function printPlayerNode($playerNum) {
	global $playerArray, $teamNum, $player;
	$sportID = $playerArray[$playerNum]->playerSportID;
	$leagueID = $playerArray[$playerNum]->playerLeagueID;
	$playerID = $playerArray[$playerNum]->playerID;
	$teamName=$playerArray[$playerNum]->playerTeamName;
	if($playerNum == 0 || $playerArray[$playerNum]->playerTeamID != $playerArray[$playerNum -1]->playerTeamID){
		if(filter_var($playerArray[$playerNum]->playerEmail, FILTER_VALIDATE_EMAIL)) {
        $teamNum++;?>
		<tr>
			<td>
            	<?php print $teamNum?>
				<INPUT TYPE='checkbox' NAME='checkBox[]' onchange="checkTeams(<?php print $playerArray[$playerNum]->playerTeamID?>, this)" 
                	VALUE=<?php print $player ?> />
                <INPUT TYPE='hidden' NAME='playerID[]' VALUE='<?php print $playerArray[$playerNum]->playerID;?>' />
			</td>
			<td>
            	<a href='<?php print "/control/Email/emailTeams.php?sportID=$sportID&leagueID=$leagueID&playerID=$playerID&teamName=".urlencode($teamName).""?>'>
					<?php print  $playerArray[$playerNum]->playerTeamName?>
                </a>
              	<INPUT TYPE='hidden' NAME='teamName[]' VALUE='<?php print $playerArray[$playerNum]->playerTeamName;?>' />
                <INPUT TYPE='hidden' NAME='teamID[]' VALUE='<?php print $playerArray[$playerNum]->playerTeamID;?>' />
			</td>
			<td>
            	<a href='<?php print "../Players/updatePlayer.php?sportID=$sportID&leagueID=$leagueID&playerID=$playerID"?>'>
					<?php print $playerArray[$playerNum]->playerFirstName.' '.$playerArray[$playerNum]->playerLastName;?>
                </a>
                <INPUT TYPE='hidden' NAME='playerName[]' VALUE='<?php print $playerArray[$playerNum]->playerFirstName.' '.$playerArray[$playerNum]->playerLastName;?>' />
            </td>
			<td>
            	<font size=1>
                <a href="mailto:<?php print $playerArray[$playerNum]->playerEmail;?>">
					<?php print $playerArray[$playerNum]->playerEmail;?>
                </a>
                </font>
                <INPUT TYPE='hidden' NAME='playerEmail[]' VALUE='<?php print $playerArray[$playerNum]->playerEmail;?>' />
            </td>
			<td><?php print $playerArray[$playerNum]->getSportString();?></td>
			<td><?php print $playerArray[$playerNum]->playerLeagueName;?></td>
			<td><?php print $playerArray[$playerNum]->getdayString();?></td>
		</tr>
		<?php $player++; 
		}
	} else {
		if(filter_var($playerArray[$playerNum]->playerEmail, FILTER_VALIDATE_EMAIL)) { ?>
    	<tr>
        	<td>
            	<INPUT TYPE='checkbox' style="display:none;" NAME='checkBox[]' VALUE=<?php print $player ?>  />
                <INPUT TYPE='hidden' NAME='playerID[]' VALUE='<?php print $playerArray[$playerNum]->playerID;?>' />
			</td>
            <td>
            	<INPUT TYPE='hidden' NAME='teamName[]' VALUE="<?php print $playerArray[$playerNum]->playerTeamName;?>" />
                <INPUT TYPE='hidden' NAME='teamID[]' VALUE='<?php print $playerArray[$playerNum]->playerTeamID;?>' />
            </td>
			<td>
            	<a href='<?php print "../Players/updatePlayer.php?sportID=$sportID&leagueID=$leagueID&playerID=$playerID"?>'>
					<?php print $playerArray[$playerNum]->playerFirstName.' '.$playerArray[$playerNum]->playerLastName;?>
                </a>
                <INPUT TYPE='hidden' NAME='playerName[]' VALUE='<?php print $playerArray[$playerNum]->playerFirstName.' '.$playerArray[$playerNum]->playerLastName;?>' />
            </td>
			<td>
            	<font size=1>
                <a href="mailto:<?php print $playerArray[$playerNum]->playerEmail;?>">
					<?php print $playerArray[$playerNum]->playerEmail;?>
                </a>
                </font>
                <INPUT TYPE='hidden' NAME='playerEmail[]' VALUE='<?php print $playerArray[$playerNum]->playerEmail;?>' />
            </td>
            <td></td>
            <td></td>
            <td></td>
		</tr>
        <?php $player++;
		}
	}
} ?>