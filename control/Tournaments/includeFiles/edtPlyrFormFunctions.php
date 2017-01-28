<?php //prints the top info of the score reporter, ie the game info and title
function printTopInfo($tourneysDropDown, $isLeagues, $leaguesDropDown, $isTeams, $teamsDropDown, $playersDropDown) { 
	global $tourneyID, $leagueID, $teamID?>
    <table>
		<tr>
			<th>
				Get IDs
			</th>
		</tr><tr>
			<td>
				Tournament
				<select id='userInput' name='tourneyID' onchange='reloadPageTourney()'>
					<option value=0>--TOURNAMENT NAME--</option>
					<?php print $tourneysDropDown; ?>
				</select>
			</td>
		</tr>
		<?php if($isLeagues != 0) { ?>
		<tr>
			<td>
				League
				<select  id='userInput' name='leagueID' onchange='reloadPageLeague()'>
					<option value=10000>--LEAGUE NAME--</option>
					<?php print $leaguesDropDown; ?>
				</select>
			</td>
		</tr> 
		<?php }
		if($isTeams != 0) { ?>
		<tr>
			<td>
				Team
				<select  id='userInput' name='teamID' onchange='reloadPageTeam()'>
					<option value=0>--TEAM NAME--</option>
					<?php print $teamsDropDown; ?>
				</select>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td>
				Player
				<select id='userInput' name='playerID' onchange='reloadPagePlayer()'>
					<option value=0>--PLAYER NAME--</option>
					<?php print $playersDropDown ?>
				</select>
			</td>
		</tr><tr>
			<td>
				<?php if($isTeams == 1) {
					print "<a href='editTeam.php?tournamentID=$tourneyID&leagueID=$leagueID&teamID=$teamID'>Team Page</a>";
				} else {
					print "<a href='editByLeague.php?tournamentID=$tourneyID&leagueID=$leagueID'>League Page</a>";
				}?>
			</td>
		</tr>
	</table>
<?php }

function printPlayerHeader($isLeagues, $leaguesDropDown, $isTeams, $teamsDropDown) { ?>
    <tr>
        <th colspan=2>
            Player Form
        </th>
    </tr>
<?php }

function printPlayerForm($playerObj){ ?>
	<tr>
        <td>
            First Name 
		</td><td>
        	<input id='userInput' type="text" value='<?php print stripslashes(htmlentities($playerObj->playerFirstName, ENT_QUOTES))?>' name="firstName" />
        </td>
    </tr><tr>
        <td>
            Last Name 
		</td><td>
        	<input id='userInput' type="text" value='<?php print stripslashes(htmlentities($playerObj->playerLastName, ENT_QUOTES))?>' name="lastName">
        </td>
    </tr><tr>
        <td>
            Email 
		</td><td>
        	<input id='userInput' type="text" value='<?php print stripslashes(htmlentities($playerObj->playerEmail, ENT_QUOTES))?>' name="email" />
        </td>
    </tr><tr>
        <td>
            Gender 
		</td><td>
         	<?php $letterArray = array('M', 'F',); ?>
        	<select id='userInput' name="gender">
            <?php foreach($letterArray as $curLetter) {
				if ($playerObj->playerGender == $curLetter) { ?>
					<option selected="selected" value=<?php print $curLetter ?>><?php print $curLetter?></option>
				<?php } else { ?>
					<option value=<?php print $curLetter ?>><?php print $curLetter?></option>
				<?php } 
			} ?>
            </select>
        </td>
    </tr><tr>
        <td>
            Note
		</td><td>
         	<textarea id='userInput' name="note"><?php print stripslashes(htmlentities($playerObj->playerNote, ENT_QUOTES)); ?></textarea>
        </td>
    </tr>
<?php }