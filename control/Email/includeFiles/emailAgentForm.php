<?php function printAgentHeader($sportID, $leagueID, $dayNumber, $seasonID, $direction) {

	$nameHead=makeLink('player_lastname', 'Player Name', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$emailHead=makeLink('player_email', 'Email', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$sportHead=makeLink('sport_name', 'Sport', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$leagueHead=makeLink('league_name', 'League', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$seasonHead=makeLink('season_id', 'Season', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$dayHead=makeLink('league_day_number', 'Game Day', $sportID, $leagueID, $dayNumber, $seasonID, $direction); 
	$groupHead=makeLink('individual_small_group_id', 'Group Num', $sportID, $leagueID, $dayNumber, $seasonID, $direction); ?>
    <TR>
    	<td>
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
        </td><td>
        	<?php print $groupHead ?>
        </td>
    </tr>
<?php }

function printAgentNode($playerNum) {
	global $playerArray, $player;
	$sportID = $playerArray[$playerNum]->playerSportID;
	$leagueID = $playerArray[$playerNum]->playerLeagueID;
	$playerID = $playerArray[$playerNum]->playerID;
	 // if(filter_var($playerArray[$playerNum]->playerEmail, FILTER_VALIDATE_EMAIL)) { ?>
	<tr>
		<td>
			<?php print $player + 1?>
			<input type='checkbox' NAME='checkBox[]' VALUE='<?php print $player;?>' />
            <input type='hidden' NAME='playerID[]' VALUE='<?php print $playerID;?>' />
		</td><td>
			<a href='<?php print "../Players/updatePlayer.php?sportID=$sportID&leagueID=$leagueID&playerID=$playerID"?>'>
				<?php print $playerArray[$playerNum]->playerFirstName.' '.$playerArray[$playerNum]->playerLastName;?>
			</a>
			<input type='hidden' NAME='playerName[]' VALUE='<?php print $playerArray[$playerNum]->playerFirstName.' '.$playerArray[$playerNum]->playerLastName;?>' />
		</td><td>
			<font size=1>
			<a href="mailto:<?php print $playerArray[$playerNum]->playerEmail;?>">
				<?php print $playerArray[$playerNum]->playerEmail;?>
			</a>
			</font>
			<input type='hidden' NAME='playerEmail[]' VALUE='<?php print $playerArray[$playerNum]->playerEmail;?>' />
		</td>
		<td><?php print $playerArray[$playerNum]->getSportString();?></td>
		<td><?php print $playerArray[$playerNum]->playerLeagueName;?>
        	<input type='hidden' NAME='playerLeagueID[]' VALUE='<?php print $playerArray[$playerNum]->playerLeagueID;?>' />
        </td>
		<td><?php print $playerArray[$playerNum]->getdayString();?></td>
        <td><?php print $playerArray[$playerNum]->playerGroupNum == 0?'None':$playerArray[$playerNum]->playerGroupNum ?></td>
	</tr>
	<?php $player++; 
	//}
} ?>