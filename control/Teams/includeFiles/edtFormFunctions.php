<?php //prints the top info of the score reporter, ie the game info and title
function printTopInfo($sportsDropDown, $leaguesDropDown, $teamsDropDown) {
	global $sportID, $leagueID ?>

	<div class='getIDs'>
		Sport
		<select id='userInput' name='sportID' onchange='reloadPageSport()'>
			<?php print $sportsDropDown; ?>
		</select><br /><br />
		League
		<select id='userInput' name='leagueID' onchange='reloadPageLeague()'>
			<?php print $leaguesDropDown; ?>
		</select><br /><br />
		Team
		<select id='userInput' name='teamID' onchange='reloadPageTeam()'>
			<option value=0>-- Team Name --</option>
			<?php print $teamsDropDown; ?>
		</select><br /><br />
		<a href='/control/Registration/editByLeague.php?sportID=<?php print $sportID?>&leagueID=<?php print $leagueID?>'>
			League Page
		</a>
	</div>
<?php }

function printEditTeamForm($leaguesDropDown, $teamWeek, $teamPicLink, $teamDroppedOut, $teamName) { ?>
	<div class='tableData'>
		<table>
			<tr>
				<th colspan=2>
					Edit Team Parameters
				</th>
			</tr><tr>
				<td>
					Team Name
					<input id='userInput' type='text' name='newTeamName' value='<?php print htmlentities($teamName, ENT_QUOTES) ?>' />
				</td><td>
					Team League
					<select id='userInput' name='newLeagueID'>
						<?php print $leaguesDropDown; ?>
					</select>
				</td>
			</tr><tr>
				<td>
					Week In Score Reporter
					<input type='hidden' id='teamWeek' value=<?php print $teamWeek?> />
					<select id='userInput' name='newTeamWeek'>
						<?php for($i=0;$i<20;$i++) {
							print "<option value=$i ";
							print $i == $teamWeek?'selected':'';
							print ">$i</option>";
						}?>
					</select>
				</td><td>
					Team Dropped Out
					<select id='userInput' name='teamDropped'>
						<option value=1 <?php print $teamDroppedOut == 1?'selected':'' ?>>Yes</option>
						<option value=0 <?php print $teamDroppedOut == 0?'selected':'' ?>>No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php print $teamPicLink ?>
				</td><td>
					<input type='submit' name='updateTeamData' value='Update Team Data' />
				</td>
			</tr>
		</table>
	</div>
<?php }

function printTeamTopInfo() { ?>
    <tr>
        <th colspan=6>
            Teams Players
        </th>
    </tr>
<?php }

function printAgentsTopInfo(){ ?>
	<tr>
		<th colspan=6>
			Free Agents
		</th>
	</tr>
<?php }


function printPlayersHeader(){ ?>
	<tr>
   		<td>
        	<br />
        </td>
        <td>
        	S
        </td><td>
        	Name
        </td><td>
        	Note
        </td>
		<td>
			G
		</td>
		<td>
        	Sel
        </td>
    </tr>                     
<?php }


function printPlayerNode($i, $player, $groupID, $isAgent){ 
	global $sportID, $leagueID, $teamID; ?>
	<tr>
        <td>
            <?php print $i+1; ?>
		</td>
        <td>
            <?php print $player[$i]->playerGender; ?>
		</td>
        <td>
        	<?php if ($isAgent == 0) { ?>
             	<input type='hidden' name='playerID[<?php print $i ?>]' value=<?php print $player[$i]->playerID ?>>
                <?php $player[$i]->playerIsIndividual == 1 ? $fontFilter="style='font-weight:bold;'" : $fontFilter = '';
				print "<a $fontFilter href='/control/Players/updatePlayer.php?sportID=$sportID&leagueID=$leagueID&teamID=$teamID&playerID=".$player[$i]->playerID."'>".$player[$i]->playerName.'</a>';
				print $player[$i]->playerIsCaptain == 1?' (Cptn)':'';
            } else { ?>
             	<input type='hidden' name='agentID[<?php print $i ?>]' value=<?php print $player[$i]->playerID ?>>
                <?php print "<a href='/control/Players/updatePlayer.php?sportID=$sportID&leagueID=$leagueID&playerID=".$player[$i]->playerID."'>
				".$player[$i]->playerName.'</a>';
            } ?>
		</td><td>
        	<textarea style='width:200px' rows="3" name='noteField'><?php print 'Player skill: ' . $player[$i]->playerSkill . "\r\n"; print htmlentities($player[$i]->playerNote);?></textarea>
		</td>
		<td>
			<?php print $player[$i]->playerGroupID; ?>
			<input type='hidden' name='groupID[]' value='<?php print $player[$i]->playerGroupID?>' />
		</td>
        <td>
        	<?php if ($isAgent == 0) { ?>
             	<input type='checkbox' name='player[]' value=<?php print $i ?>><br />
            <?php } else { ?>
             	<input type='checkbox' name='agent[]' value=<?php print $i ?> onclick='checkGroups(<?php print $player[$i]->playerGroupID?>, this)'>
            <?php } ?>
		</td>
    </tr>
<?php }

function printPlayersFooter($teamNum, $isAgent) {
	if($isAgent == 1) {
		$colSpanNum = 5;
		$typeName = 'agent';
	} else {
		$colSpanNum = 4;
		$typeName = 'player';
	} ?>
    <tr>
		<?php if ($isAgent == 1) { ?>
			<td colspan=6>
				<input type='checkbox' checked='checked' value='Check' onClick='setAuto(this)'>Toggle Groups 
				<button name='addAgents' value=1>Add To Team</button>
				<button name='deleteAgents' value=1 onclick='return checkYesNo()'>Delete Agents</button>
			</td>
        <?php } else { ?>
            <td colspan=5>
				<button name='removePlayers' value=1>Move To FA List</button>
				<button name='deletePlayers' value=1 onclick='return checkYesNo()'>Delete Players</button>
			</td>
        <?php } ?>
    </tr>
<?php } ?>

<?php function printAddPlayers($isAgent) {
	if($isAgent == 1) {
		$colSpanNum = 5;
		$typeName = 'agent';
		$title = 'Add Agent';
	} else {
		$colSpanNum = 4;
		$typeName = 'player';
		$title = 'Add Player';
	} ?>
	
	<tr>
		<th colspan=5>
			<?php print $title ?>
		</th>
	</tr><tr>
		<td>
			Gender
		</td><td>
        	Name
        </td><td>
        	Email
        </td><td>
        	Note
        </td>
        <?php if ($isAgent == 1) { ?>
            <td>
                Group #
            </td>
        <?php } ?>
    </tr><tr>
    	<td>
            <select name='<?php print $typeName?>Gender'>
                <option selected='selected' value='M'>M</option>
                <option value='F'>F</option>
            </select>
        </td><td>
            <input type='text' style='width:50px;'  name='<?php print $typeName?>FirstName'>
            <input type='text' style='width:50px;' name='<?php print $typeName?>LastName'>
        </td><td>
            <input type='email' style='width:130px;' name='<?php print $typeName?>Email' >
        </td><td>
			<textarea style='width:150px;' name='<?php print $typeName?>Note' ></textarea>
        </td>
        <?php if ($isAgent == 1) { ?>
            <td width='10px'>
                <input type='text' name='groupNumber' style='width:20px;' size='1px'>
            </td>
        <?php } ?>
    </tr><tr>
        <td colspan=<?php print $colSpanNum?>>
            <button name='<?php print $typeName?>Add' value=1>Create Player</button>
        </td>
    </tr>
<?php }