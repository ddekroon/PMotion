<?php //prints the top info of the score reporter, ie the game info and title
function printLeagueTopInfo($sportsDropDown, $leaguesDropDown) { ?>
            Sport
            <select id='userInput' name='sportID' onchange='reloadPageSport()'>
                <?php print $sportsDropDown; ?>
            </select><br /><br />

            League
            <select id='userInput' name='leagueID' onchange='reloadPage()'>
                <?php print $leaguesDropDown; ?>
            </select>
<?php }

function printTeamsHeader() { ?>
	<tr>
    	<th colspan=6>
        	League Teams
        </th>
    </tr><tr>
		<td>
        	#
        </td><td>
        	Team Name
        </td><td>
        	Del
        </td><td>
        	Conv
        </td><td>
        	Pd
        </td><td>
        	
        </td>
    </tr>
<?php }

function printTeamNode($i) {
	global $team, $teamFinalized, $regComment, $leagueID, $sportID; ?>

	<tr>
        <td>
        	<?php print $team[$i]->teamNumInLeague; ?>
        </td><td>
			<?php print '<a ';
			print $team[$i]->teamHasIndividuals == 1?"style='font-weight:700;'":'';
			print " href='/control/Teams/editTeam.php?sportID=$sportID&leagueID=$leagueID&teamID=".$team[$i]->teamID."'>".$team[$i]->teamName.'</a>'; 
			print $team[$i]->teamDroppedOut == 1? '(Dropout)':''?>
            <input type='hidden' name='teamID[]' value='<?php print $team[$i]->teamID ?>' />
		</td><td>
        	<input type='checkbox' name='delete[]' VALUE='<?php print $i ?>'>
        </td><td>
        	<input type="checkbox" name="isConvenor[]" <?php print $team[$i]->teamIsConvenor == 1?'checked':'' ?> value="<?php print $team[$i]->teamID?>"/>
        </td><td>
        	<input type="checkbox" name="teamPaid[]" <?php print $team[$i]->teamPaid == 1?'checked':'' ?> value="<?php print $team[$i]->teamID?>" />
        </td><td>
        	<input type="hidden" name="curTeamNum[]" value="<?php print $team[$i]->teamNumInLeague ?>" />
        	<select name='teamNum[]' onchange="updateNumbers(<?php print $i ?>)">
            <?php print $team[$i]->teamNumDropDown; ?>
            </select>
        </td>
    </tr>
<?php }

function printTeamsFooter($teamNum, $leagueID) { ?>
    <tr>
        <td colspan=2>
            <input type='submit' name='loadIndividuals' value='Individual Teams' onclick="return loadIndividualTeams(<?php print $leagueID ?>)" />
			<input type='submit' name='loadIndividuals' value='Excel Code' onclick="return loadTeamsPage(<?php print $leagueID ?>)" />
        </td><td colspan=3>
			<input type='submit' name='deleteTeams' value='Del Teams'>
        </td><td>

			<input type='submit' name='updateTeamNums' value='Update Data'>
        </td>
    </tr><tr>
        <td colspan=6>
            <input type='text' style='width:180px;' name='newTeamName' value='' style="border-color:#990066; width:200px;">
            <button name='addTeam'>Add Team</button>
        </td>
    </tr>
<?php }


function printAgentsHeader(){ ?>
	<tr>
		<th colspan=6>
			Free Agents
		</th>
	</tr>
	<tr>
    	<td>
        	#
        </td><td>
        	Name
        </td><td>
        	Gender
        </td><td>
        	Note
        </td><td>
        	Grp
        </td><td>
        	Del
        </td>
    </tr>                     
<?php }


function printAgentNode($i){
	global $playerName, $playerGender, $playerEmail, $playerGroupID, $playerNote, $playerID, $sportID, $leagueID; ?>

	<tr>
        <td>
        	<?php print $i +1; ?>
        </td>
        <td>
        	<?php print "<a href='/control/Players/updatePlayer.php?sportID=$sportID&leagueID=$leagueID&teamID=0&playerID=$playerID[$i]'>$playerName[$i]</a>" ?>
		</td>
        <td>
            <?php print $playerGender[$i]; ?>
		</td>
        <td >
        	<textarea id='userInput' name='agentNote'><?php print $playerNote[$i]; ?></textarea>
		</td>
        <td>
            <?php print $playerGroupID[$i]; ?>
		</td><td>
            <input type="checkbox" name="delAgent[]" value="<?php print $playerID[$i] ?>" />
		</td>
    </tr>
<?php }

function printAgentsFooter() { ?>
    <tr>
        <td colspan=6>
            <input type="submit" name='deleteAgents' value='Del Agents' 
            	onclick="return confirm('Are you sure you want to delete agent(s)?');">
        </td>
    </tr>
<?php }

function printUnregTeams($unregTeam) {
    global $sportID, $leagueID;
	if(count($unregTeam) > 0) {
		for($i=0; $i < count($unregTeam); $i++) { ?>
			<tr>
				<td>
					<?php print $i +1; ?>
				</td><td>
					<?php print "<a href='/control/Teams/editTeam.php?sportID=$sportID&leagueID=$leagueID&teamID=".$unregTeam[$i]->teamID."'>".$unregTeam[$i]->teamName."</a>" ?>
				</td><td>
					<?php print $unregTeam[$i]->teamCaptainFirstName; ?>
				</td><td >
					<?php print $unregTeam[$i]->teamCaptainLastName; ?>
				</td><td>
					<?php print "<a target='_blank' href='mailto:".$unregTeam[$i]->teamCaptainEmail."'>".$unregTeam[$i]->teamCaptainEmail.'</a>'; ?>
				</td><td>
					<input type='checkbox' name='delFence[]' value=<?php print $unregTeam[$i]->teamID?>>
				</td>
			</tr>
		<?php } 
	} else { ?>
		<tr>
			<td colspan=6>
				No teams in holding
			</td>
		</tr>
	<?php }?>
    <tr>
        <td colspan=3 align="center">
			<input type="submit" name='addFenceTeams' value='Add Checked Teams to League' onclick="return checkAdd()">
		</td><td colspan=3 align="center">
            <input type="submit" name='delFenceTeams' value='Delete Checked Fence Teams' onclick="return checkDelete()">
        </td>
    </tr>
<?php }