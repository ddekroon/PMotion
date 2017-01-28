<?php //prints the top info of the score reporter, ie the game info and title
function printTopInfo($tourneyID, $isLeagues, $leagueID, $tourneysDropDown, $leaguesDropDown, $teamsDropDown) { ?>
	<div class='tableData'>
		<table>
			<tr>
				<th>
					Select Team
				</th>
			</tr><tr>
				<td>
					Tournament
					<select name='tourneyID' onchange='reloadPageTourney()'>
						<option value=0>--Tournaments--</option>
						<?php print $tourneysDropDown; ?>
					</select>
				</TD>
			</TR>
			<TR <?php print $isLeagues == 0?'style="display:none"':'' ?>>
				<TD>
					League
						<select name='leagueID' onchange='reloadPageLeague()'>
							<option value=10000>-- Leagues --</option>
							<?php print $leaguesDropDown; ?>
						</select>
				</TD>
			</TR> 
			<TR>
				<TD>
					Team
					<select name='teamID' onchange='reloadPageTeam()'>
						<option value=0>--TEAM NAME--</option>
						<?php print $teamsDropDown; ?>
					</select>
				</TD>
			</TR> 
			<tr>
				<td>
					<a href='editByLeague.php?tournamentID=<?php print $tourneyID?>&leagueID=<?php print $leagueID?>'>
						League Page
					</a>
				</td>
			</tr>
		</table>
    </div>
<?php }

function printEditTeamForm($leaguesDropDown, $isWaiting) { ?>
	<table>
		<tr>
			<th>
				Edit Team Data
			</th>
		</tr><tr>
			<td>
				<input type="text" name="newTeamName" />
				<button name="changeTeamName" value=1 onclick="return checkTeamName()">Change Team Name</button>
			</td>
		</tr><tr>
			<td>
				<select name='newLeagueID'>
						<option value=0>-- Leagues --</option>
						<?php print $leaguesDropDown; ?>
					</select>
				<button name="changeLeague" value=1 onclick="return checkLeague()">Change League</button>
			</td>
		</tr><tr>
			<td>
				<select name='isWaiting'>
						<option <?php print $isWaiting==1?'selected':'' ?> value=1>Yes (is waiting)</option>
						<option <?php print $isWaiting==0?'selected':'' ?> value=0>No (is not waiting)</option>
					</select>
				<button name="changeWaiting" value=1>Change Is On Waiting List</button>
			</td>
		</tr>
	</table>
<?php }

function printTeamTopInfo() { ?>
    <TR>
        <th colspan=6>
            Teams Players
        </th>
    </TR>
<?php }


function printPlayersHeader(){ ?>
	<tr>
   		<td>
        	Num
        </td><td>
        	Name
        </td><td>
        	Email
        </td><td>
        	Gender
        </td><td>
        	Note
        </td><td>
            Del
        </td>
    </tr>                     
<?php }


function printPlayerNode($i, $curPlayer){  ?>
	<tr>
        <td>
           	<?php print $i+1; ?>
		</td><td>
            <?php print "<a href='editPlayer.php?tournamentID=$curPlayer->playerTourneyID&leagueID=$curPlayer->playerLeagueID&teamID=$curPlayer->playerTeamID&playerID=$curPlayer->playerID'>
            $curPlayer->playerFirstName $curPlayer->playerLastName</a>"; ?>
		</td><td>
            <font size=1><?php print "<a target='_blank' href='mailto:$curPlayer->playerEmail'>$curPlayer->playerEmail</a>" ?></font>
		</td><td>
            <?php print $curPlayer->playerGender ?>
		</td><td>
            <?php print $curPlayer->playerNote; ?>
		</td><td style="vertical-align:middle;">
             <input type="checkbox" name='playerCheck[]' value=<?php print $curPlayer->playerID ?>>
		</td>
    </tr>
<?php }

function printPlayersFooter() { ?>
    <tr>
    	<td>
            <select name="newPlayerGender">
                <option value='M'>M</option>
                <option value='F'>F</option>
            </select>
        </td><td colspan=2>
            Name
            <input type="text" style="width:60px;" name="newPlayerFirstName">
            <input type="text" style="width:80px;" name="newPlayerLastName">
        </td><td colspan=2 align=left>
            Email <input type="text" style="width:150px;" name="newPlayerEmail" >
        </td><td rowspan="2" style="vertical-align:middle;">
			<input type='button' name='CheckAll'value='Check' onClick="checkAllPlayers()">
            <input type='button' name='UncheckAll' value='Uncheck' onClick="uncheckAllPlayers()">
        </td>
    </tr><tr>
    	<td colspan=5>
            Note <input type="text" style="width:300px;" name="newPlayerNote" >
        </td>
    </tr><tr>
        <td colspan=3>
            <input type='submit' name='playerAdd' value='Create Player' />
        </td><td colspan=3>
            <input type='submit' name='playerDelete' value='Delete Players' onclick="checkYesNo()">
        </td>
    </tr>
<?php } ?>