<?php

function printPlayerHeader($sportID, $leagueID, $dayNumber, $seasonID, $direction) {

	$teamHead=makeLink('team_name', 'Team Name', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$nameHead=makeLink('player_lastname', 'Player Name', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$emailHead=makeLink('player_email', 'Email', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$sportHead=makeLink('sport_name', 'Sport', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$leagueHead=makeLink('league_name', 'League', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$seasonHead=makeLink('season_id', 'Season', $sportID, $leagueID, $dayNumber, $seasonID, $direction);
	$dayHead=makeLink('league_day_number', 'Game Day', $sportID, $leagueID, $dayNumber, $seasonID, $direction);?>
    <tr>
		<th colspan=8>
			Perpetual Players
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
        </td><td>
        	Late Email
        </td>
    </tr>
<?php }
 
function makeLink($searchTerm, $headerValue, $sportID, $leagueID, $dayNumber, $seasonID, $direction){

	if($sportID != 0){
		$sortBy='sportID';
		$sortValue=$sportID;
	}
    if($leagueID != 0) {
		$sortBy='leagueID';
		$sortValue=$leagueID;
	}
    if($dayNumber != 0){
		$sortBy='dayNumber';
		$sortValue=$dayNumber;
	}
    if($seasonID != 0) {
		$sortBy='seasonID';
		$sortValue=$seasonID;
	}

	$reloadPage = 'removeLateEmail.php';

	return "<a href='$reloadPage?sportID=$sportID&seasonID=$seasonID&leagueID=$leagueID&dayNumber=$dayNumber&orderBy=$searchTerm&direction=$direction'>$headerValue</a>";
} 

function printTeamHeader($sportsDD, $leaguesDD, $daysDD, $seasonsDD, $linkedFrom) { ?>
    <h1>Remove Team From Late Email List</h1>
	
    <style>
	.Table
    {
        display: table;
		width:900px;
		margin:0px auto;
		text-align: center;
    }
    .Title
    {
        display: table-caption;
        text-align: center;
        font-weight: bold;
        font-size: larger;
    }
    .Heading
    {
        display: table-row;
        font-weight: bold;
        text-align: center;
		width:900px;
		margin:0px auto;
    }
    .Row
    {
        display: table-row;
		width:900px;
		margin:0px auto;
		text-align: center;
    }
	.Column
    {
		width:450px;
        display: table-cell;
		margin:0px auto;
		vertical-align: top;
		padding:5px;
    }
    .Cell
    {
		width:900px;
		height:25px;
        display: table-cell;
		text-align:center;
        border: solid;
		border-color:#CCC;
        border-width:thin;
		margin:0px auto;
    }
	</style>
    
    <p><div class='Table' style="width:90%; max-width:850px; min-width:250px;">
    	<div class="Heading" style="background:#09F">
            <div class="Cell">
                Choose League
            </div>
        </div>
        <div class="Cell" style="background:skyblue;">
                <select name='sportID' onChange="reloadPage(<?php print $linkedFrom ?>)">
                    <?php print $sportsDD; ?>
                </select>
                <select name='leagueID' onChange="reloadPage(<?php print $linkedFrom ?>)">
                    <?php print $leaguesDD; ?>
                </select>
                <select name='dayNumber' onChange="reloadPage(<?php print $linkedFrom ?>)">
                    <?php print $daysDD; ?>
                </select>
                <select name='seasonID' onChange="reloadPage(<?php print $linkedFrom ?>)">
                    <?php print $seasonsDD; ?>
                </select>
        </div>
        <div class="Row">
            <div class="Cell">
                <input type='button' name='Check_All' value='Check All' onClick='CheckAll()'>
                <input type='Submit' Name='Submit' Value='Toggle'>
                <input type='button' name='UnCheck_All' value='Uncheck All' onClick='UnCheckAll()'>
            </div>
        </div>
	</div>
<?php }

function printPlayerNode($playerNum) {
	global $playerArray, $player, $bccArray;
	$sportID = $playerArray[$playerNum]->playerSportID;
	$leagueID = $playerArray[$playerNum]->playerLeagueID;
	$playerID = $playerArray[$playerNum]->playerID;
	if(filter_var($playerArray[$playerNum]->playerEmail, FILTER_VALIDATE_EMAIL)) { ?>
	<tr>
		<td>
			<?php print $player + 1?>
			<INPUT TYPE='checkbox' NAME='checkBox[]' VALUE='<?php print $player;?>' />
            <INPUT TYPE='hidden' NAME='playerID[]' VALUE='<?php print $playerArray[$playerNum]->playerID;?>' />
		</td>
		<td>
			<?php print $playerArray[$playerNum]->playerTeamName?>
			<INPUT TYPE='hidden' NAME="teamName[]" VALUE="<?php print $playerArray[$playerNum]->playerTeamName;?>" />
            <INPUT TYPE='hidden' NAME="teamID[]" VALUE="<?php print $playerArray[$playerNum]->playerTeamID;?>" />
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
        <td><?php print $playerArray[$playerNum]->lateEmailAllowed;?></td>
	</tr>
	<?php $player++; 
	}
} ?>