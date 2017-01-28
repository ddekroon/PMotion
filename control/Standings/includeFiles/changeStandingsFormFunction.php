<?php

// Prints the sport, season and leauge drop down menu
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
	
<?php }

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
        	Leauge Position
        </td><td>
        	Spirit Position
        </td>
    </tr>
<?php }

// Prints teams in the leauge and the drop downs for changing the leauge and spirit position
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

// Prints the change standings button
function printTeamsFooter($leagueWeek) { ?>
    <tr>
		<td colspan=4>
			<input type='submit' name="saveStandings" onClick="return checkYesNoSave()" value='Change Standings' />
		</td>
    </tr>
<?php }