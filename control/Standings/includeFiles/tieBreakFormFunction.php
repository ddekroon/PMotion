<?php

// Prints the sport, season, leauge and teams drop down menu
function printIDs($sportsDropDown, $seasonsDropDown, $leaguesDropDown, $teamsDropDown1, $teamsDropDown2) 
{ 
	global $sportID, $seasonID, $leagueID;
	global $team1ID, $team2ID; ?>

	Sport
	<select id='userInput' name='sportID' onchange='reloadSport()'>
		<?php print $sportsDropDown; ?>
	</select><br /><br />
	Season
	<select id='userInput' name='seasonID' onchange='reloadPage()'>
		<?php print $seasonsDropDown; ?>
	</select><br /><br />
	League
	<select id='userInput' name='leagueID' onchange='reloadPage()'>
		<?php print $leaguesDropDown; ?>
	</select><br /><br />
	
    <table>
    	<tr>
    		<th colspan=2>
        		Choose Two Teams to Switch
       	 	</th>
   		 </tr> 
    	<tr>
			<td>
        		To Be Top
        	</td>
        	<td>
        		To Be Bottom
        	</td>
    	</tr>
    	<tr>
    		<td>
        		<select id='userInput' name='team1ID' onchange='reloadPage()'>
       				<?php print $teamsDropDown1; ?>
        		</select>
        	</td>
        	<td>
        		<select id='userInput' name='team2ID' onchange='reloadPage()'>
       				<?php print $teamsDropDown2; ?>
       	 		</select>      
       	 	</td>
    	</tr>
    </table>
	
<?php }

// Prints the change standings button
function printTeamsFooter($leagueWeek) 
{ ?>
    <tr>
		<td colspan=4>
			<input type='submit' name="saveStandings" onClick="return checkYesNoSave()" value='Change Standings' />
		</td>
    </tr>
<?php } ?>