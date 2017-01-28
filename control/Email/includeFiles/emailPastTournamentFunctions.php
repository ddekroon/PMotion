<?php

function getYearsDD($tourneyID, $selectedYear) {
	global $tournamentsTable, $tournamentPlayersTable, $dbConnection, $container;
	$yearsDropDown = '';
	if($tourneyID != 0) {
		$yearQuery = "SELECT tournament_start_year FROM $tournamentsTable WHERE tournament_id = $tourneyID";
		if(!($result = $dbConnection->query($yearQuery))) 
			$container->printError('ERROR getting tournament year - '.$dbConnection->error);
		$yearObj = $result->fetch_object();
		$year = $yearObj->tournament_start_year;
	}
	$curYear = date("Y");
	while($year <= $curYear) {
		if($year == $selectedYear) {
			$yearsDropDown.="<option selected value=$year>$year</option>";
		} else {
			$yearsDropDown.="<option value=$year>$year</option>";
		}
		$year++;
	}
	return $yearsDropDown;
}

function printPlayerHeader($tourneyID, $year, $direction) {

	$teamHead=makeLink('tournament_team_name', 'Team Name', $tourneyID, $year, $direction);
	$nameHead=makeLink('tournament_player_lastname', 'Player Name', $tourneyID, $year, $direction);
	$emailHead=makeLink('tournament_player_email', 'Email', $tourneyID, $year, $direction);
	$yearHead=makeLink('year', 'Year', $tourneyID, $year, $direction); ?>
    <tr>
		<th colspan=6>
			Past Captains
		</th>
	</tr><tr>
    	<td></td><td>
        	<?php print $teamHead ?>
        </td><td>
        	<?php print $nameHead ?>
        </td><td>
        	<?php print $emailHead ?>
        </td><td>
        	<?php print $yearHead ?>
        </td><td>
        	Del
        </td>
    </tr>
<?php }
 
function makeLink($searchTerm, $headerValue, $tourneyID, $year, $direction){
	global $emailTarget;
	if($tourneyID != 0){
		$sortBy='tourneyID';
		$sortvalue=$tourneyID;
	}

	return "<a href='emailPastTournaments.php?tourneyID=$tourneyID&year=$year&sortBy=$sortValue&orderBy=$searchTerm&direction=$direction'>$headerValue</a>";
} 

function printTeamHeader($tourneysDD, $yearsDD) { ?>
    <h1>Perpetual Motion's Email Database</h1>
	<div class='tableData'>
		<table>
			<tr>
				<th colspan=2>
					Edit Email Data
				</th>
			</tr><tr>
				<td>
					<select name='tourneyID' onChange="reloadPageTournament()">
						<option value=0>--Tournaments--</option>
						<?php print $tourneysDD; ?>
					</select>
				</td><td>
					<select name='year' onChange="reloadPageTournament()">
						<option value=0>--Years--</option>
						<?php print $yearsDD; ?>
					</select>
				</td>
			</tr><tr>
				<td>
					Subject <input type='text' name='subject' width='100'></input>
				</td><td>
					<select name='sender'>
						<option value='dave'>dave@perpetualmotion.org</option>
						<option value='derek'>derek@perpetualmotion.org</option>
						<option value='info'>info@perpetualmotion.org</option>
						<option value='scores'>scores@perpetualmotion.org</option>
						<option value='terry'>terry@perpetualmotion.org</option>
						<option value='nick'>nick@perpetualmotion.org</option>
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
			<tr><tr>
				<td colspan=2>
					<input type='button' name='Check_All' value='Check All' onClick='CheckAll()'>
					<input type='Submit' value='Next' name='goNextPage'>
					<input type='button' name='UnCheck_All' value='Uncheck All' onClick='UnCheckAll()'>
				</td>
			</tr>
		</table>
	</div>
    
<?php }

function printPlayerNode($curPlayer) {
	global $playerArray, $player;
	$sportID = $curPlayer->playerSportID;
	$leagueID = $curPlayer->playerLeagueID;
	$playerID = $curPlayer->playerID;
	if(filter_var($curPlayer->playerEmail, FILTER_VALIDATE_EMAIL)) { ?>
	<tr>
		<td>
			<?php print $player + 1?>
			<input type='checkbox' name='checkBox[]' value='<?php print $player;?>' />
            <input type='hidden' name='playerID[]' value='<?php print $curPlayer->playerID;?>' />
		</td><td>
			<?php print $curPlayer->playerTeamName?>
			<input type='hidden' name='teamName[]' value='<?php print $curPlayer->playerTeamName;?>' />
		</td><td>
			<?php print $curPlayer->playerFirstName.' '.$curPlayer->playerLastName;?>
			<input type='hidden' name='playerName[]' value='<?php print $curPlayer->playerFirstName.' '.$curPlayer->playerLastName;?>' />
		</td>
		<td>
			<a style="font-size:9px" href="mailto:<?php print $curPlayer->playerEmail;?>">
				<?php print $curPlayer->playerEmail;?>
			</a>
			<input type='hidden' name='playerEmail[]' value='<?php print $curPlayer->playerEmail;?>' />
		</td><td>
			<?php print $curPlayer->playerYear;?>
		</td><td>
			<input type='checkbox' name='deleteBox[]' value='<?php print $curPlayer->playerID;?>' />
		</td>
	</tr>
	<?php $player++; 
	}
} 

function printBottomButton($tourneyID) { ?>
	<tr>
        <td colspan=6>
			<input type="submit" value="Delete Checked Players" name="delPlayers" formtarget="_top" formaction="emailPastTournaments.php?tourneyID=<?php print $tourneyID;?>" />
        </td>
    </tr>
<?php }?>