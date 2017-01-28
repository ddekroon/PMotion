<?php //prints the top info of the score reporter, ie the game info and title
function printLeagueTopInfo($sportsDropDown, $leaguesDropDown, $datesDropDown) { 
	global $leagueWins, $leagueLosses, $leagueTies, $closedWins, $closedLosses, $closedTies ?>
   <div class='getIDs'>
		Sport
		<select id='userInput' name='sportID' onchange='reloadPageSport()'>
			<?php print $sportsDropDown; ?>
		</select><br /><br />
		League
		<select id='userInput' name='leagueID' onchange='reloadPage()'>
			<?php print $leaguesDropDown; ?>
		</select><br /><br />
		Date
		<select id='userInput' name='dateID' onchange='reloadPageWeek(this)'>
			<?php print $datesDropDown; ?>
		</select>
	</div>
	<div class='tableData'> 	
		<table>
			<tr>
				<th>
					Time
				</th><th>
					Wins
				</th><th>
					Losses
				</th><th>
					Ties
				</th><th>
					Total
				</th>
			</tr><tr>
				<td>
					Before Closing
				</td><td>
					<?php print $leagueWins; ?>
				</td><td>
					<?php print $leagueLosses; ?>
				</td><td>
					<?php print $leagueTies; ?>
				</td><td>
					<?php print $leagueWins + $leagueLosses + $leagueTies; ?>
				</td>
			</tr><tr>
				<td>
					After Closing
				</td><td>
					<label id='closedWins'><?php print $closedWins; ?></label>
				</td><td>
					<label id='closedLosses'><?php print $closedLosses; ?></label>
				</td><td>
					<label id='closedTies'><?php print $closedTies; ?></label>
				</td><td>
					<label id='closedTotal'><?php print $closedWins + $closedLosses + $closedTies ?></label>
				</td>
			</tr>
		</table>
	</div>
<?php }

function printTeamsHeader() { 
	global $numMatches, $numGames;?>
	<tr>
    	<th colspan=<?php print $numMatches * $numGames + 4 ?>>
        	League Teams
            <input type='hidden' name='numMatches' value="<?php print $numMatches ?>" />
            <input type='hidden' name='numGames' value="<?php print $numGames ?>" />
        </th>
    </tr>
	<tr>
		<td>
        	#
        </td><td>
        	Team Name
        </td>
        <?php for($i = 0; $i < $numMatches; $i++) { ?>
			<td>
                Opp <?php print $i+1; ?>
            </td>
			<?php for ($j = 0; $j < $numGames; $j++) { ?>
                <td>
                    Game <?php print $j+1; ?>
                </td>
            <?php }
		} ?>
    </tr>
<?php }

function printTeamNode($curTeam, $i) { 
	global $teamNames, $leagueID, $numMatches, $dropDownIndex;?>
	<tr>
        <td>
        	<?php print $i + 1; ?>
            <input type="hidden" name="teamID[]" value="<?php print $curTeam->teamID?>" />
        </td><td>
			<?php print "<a href='/control/Search/teamPage.php?teamID=$curTeam->teamID'>$curTeam->teamName</a>" ?>
		</td><td>
			<?php print '<select name="oppTeamID1[]">'.getOppTeamDD($leagueID, $curTeam->teamOppTeamID1).'</select>';?>
		</td>
        <?php foreach($curTeam->teamOppSubmission[$curTeam->teamOppTeamID1] as $oppSubmission)  {
			print '<td><select name="result['.$curTeam->teamID.'][]" onchange="changeClosed(this, '.$dropDownIndex.')">'.getresultDD($oppSubmission).'</select></td>'; ?>
			<script>
				loadValue(<?php print $oppSubmission.', '.$dropDownIndex++?>);
			</script>
		<?php } 
		if($numMatches == 2) {?>
            <td>
                <?php print '<select name="oppTeamID2[]">'.getOppTeamDD($leagueID, $curTeam->teamOppTeamID2).'</select>';?>
            </td>
            <?php foreach($curTeam->teamOppSubmission[$curTeam->teamOppTeamID2] as $oppSubmission)  {
                print '<td><select name="result['.$curTeam->teamID.'][]" onchange="changeClosed(this, '.$dropDownIndex.')">'.getresultDD($oppSubmission).'</select></td>'; ?>
				<script>
					loadValue(<?php print $oppSubmission.', '.$dropDownIndex++?>);
				</script>
            <?php } 
		}?>
    </tr>
<?php }

function printTeamsFooter() { ?>
	<tr>
		<td colspan=8>
			<input type='submit' name="closeWeek" onClick="return checkYesNoClose()" value='Close Week' />
		</td>
	</tr>
<?php }