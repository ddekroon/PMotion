<?php 
function printTopInfo($sportsDD, $leaguesDD, $newLeagueOneName, $newLeagueTwoName) { ?>
	<table>
		<tr>
			<th colspan=2>
				Split League Form
			</th>
		</tr>
		<tr>
			<td>
				Sport
			</td><td>
				<select id='userInput' name='sportID' onchange='reloadPageSport()'>
					<?php print $sportsDD; ?>
				</select>
			</td>
		</tr><tr>
			<td>
				League to be split
			</td><td>
				<select id='userInput'name='leagueID' onchange='reloadPageLeague()'>
					<?php print $leaguesDD; ?>
				</select>
			</td>
		</tr><tr>
			<td>
				New league 1
			</td><td>
				<input id='userInput' type='text' name='newLeagueOneName' value="<?php print $newLeagueOneName; ?>" />
			</td>
		</tr><tr>
			<td>
				New league 2
			</td><td>
				<input id='userInput' type='text' name='newLeagueTwoName' value="<?php print $newLeagueTwoName; ?>" />
			</td>
		</tr>
	</table>
<?php }

function printTeamsHeader() { ?>
	<tr>
    	<th colspan=3>
        	Teams
        </th>
    </tr>
<?php } 

function printTeamNode($i, $numTeams, $curTeam) { ?>
	<tr>
    	<td>
        	<?php print $i+1?>
        </td><td>
        	<?php print $curTeam->teamName; ?>
            <input type="hidden" name="teamID[]" value=<?php print $curTeam->teamID?> />
            <input type="hidden" name="teamName[]" value="<?php print $curTeam->teamName?>" />
        </td><td>
        	<select id='userInput'name="teamNewLeague[]">
            	<option <?php print $i < floor($numTeams/2) ? 'selected':'' ?> value=1>1</option>
                <option <?php print $i >= floor($numTeams/2) ? 'selected':'' ?> value=2>2</option>
            </select>
        </td>
    </tr>
<?php } 

function printBottomButton() { ?>
	<tr>
		<td colspan=3>
			<Button style="font-size:16px; font-weight:300;" NAME='setup'>Setup</Button>
		</td>
	</tr>
<?php } ?>