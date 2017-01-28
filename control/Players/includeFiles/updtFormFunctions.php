<?php //prints the top info of the score reporter, ie the game info and title
function printTopInfo($sportsDropDown, $leaguesDropDown, $teamsDropDown, $playersDropDown) { 
	global $sportID, $leagueID, $teamID; ?>
    <tr>
		<th colspan=2>
			Select Player
		</th>
	</tr><tr>
        <td>
            Sport
        </td><td>
            <select id='userInput' name='sportID' onchange='reloadPageSport()'>
                <?php print $sportsDropDown; ?>
            </select>
        </td>
    </tr><tr>
        <td>
            League
        </td><td>
            <select id='userInput' name='leagueID' onchange='reloadPageLeague()'>
                <?php print $leaguesDropDown; ?>
            </select>
        </td>
    </tr><tr>
        <td>
        <?php if($isIndividual == 0) {
        } ?>
            Team
        </td><td>
            <select id='userInput' name='teamID' onchange='reloadPageTeam()'>
                <option value=0>--Free Agency--</option>
                <?php print $teamsDropDown; ?>
            </select>
        </td>
    </tr><tr>
        <td>
            Player
        </td><td>
            <select id='userInput' name='playerID' onchange='reloadPagePlayer()'>
                <option value=0>--Players--</option>
                <?php print $playersDropDown ?>
            </select>
        </td>
    </tr><tr>
        <td colspan=2>
            <?php print "<a href=/control/Teams/editTeam.php?sportID=$sportID&leagueID=$leagueID&teamID=$teamID>Team Page</a>"; ?>
        </td>
    </tr>
<?php }

function printPlayerHeader($newLeaguesDropDown, $newTeamsDropDown) { ?>
    <tr>
        <th colspan=2>
            Player Form
        </th>
    </tr><tr>
        <td>
            New League<br />(Optional)
        </td><td>
            <select id='userInput' name='newLeagueID'>
                <?php print $newLeaguesDropDown; ?>
            </select>
        </td>
    </tr><tr>
        <td>
            New Team<br />(optional)
        </td><td>
            <select id='userInput' name='newTeamID'>
                <option value=0>--Free Agency--</option>
                <?php print $newTeamsDropDown; ?>
            </select>
        </td>
    </tr>
<?php }

function printPlayerForm(){
	global $player; ?>
	<tr>
        <td>
            First Name
		</td><td>
        	<input type='text' id='userInput' value='<?php print $player->playerFirstName?>' name="firstName" />
        </td>
    </tr><tr>
        <td>
            Last Name
		</td><td>
        	<input type='text' id='userInput' value='<?php print $player->playerLastName?>' name="lastName">
        </td>
    </tr><tr>
        <td>
            Email
		</td><td>
        	<input type='text' id='userInput' value='<?php print $player->playerEmail?>' name="email" />
        </td>
    </tr>
    <?php if($player->playerIsCaptain == 1) { ?>
		<tr>
            <td>
                User Email
            </td><td>
           		<input type="hidden" value='<?php print $player->playerUserID?>' name="userID" />
                <input type='text' id='userInput' value='<?php print $player->playerUserEmail?>' name="userEmail" />
            </td>
        </tr>
	<?php } ?>
    <tr>
        <td>
            Phone Number
		</td><td>
            <a href = '<?php print "tel:".$player->playerPhone?>'><?php print $player->playerPhone?></a>
            <br /><br />
            <input type='text' id='userInput' value='<?php print $player->playerPhone?>' name="phone" />
        </td>
    </tr><tr>
        <td>
            Gender
		</td><td>
         	<?php $letterArray = array('M', 'F', ); ?>
        	<select id='userInput' name="playerGender">
            <?php foreach($letterArray as $curLetter) {
				if ($player->playerGender == $curLetter) { ?>
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
         	<textarea rows="6" style="width:300px" name="note"><?php print 'Player skill: ' . $player->playerSkill . "\r\n\r\n"; print $player->playerNote; ?></textarea>
        </td>
    </tr><tr>
        <td>
            Captain
		</td><td>
        	<select id='userInput' name="isCaptain">
            	<?php for($i=1;$i>=0;$i--) { 
					if($i == $player->playerIsCaptain) { ?>
                		<option selected="selected" value=<?php print $i ?>><?php print $i >0?'Yes':'No'?></option>
					<?php } else { ?>
                		<option  value=<?php print $i ?>><?php print $i >0?'Yes':'No'?></option>
                	<?php } 
				}?>
            </select>
        </td>
    </tr>
    <?php if($player->playerIsIndividual == 1 && $player->playerIndividualGroup != 0) { ?>
		<tr>
            <td>
                Move Group
            </td><td>
                <select id='userInput' name="moveGroup">
                    <option selected="selected" value=<?php print $player->playerIndividualGroup?>>Yes</option>
                    <option  value=0>No</option>
                </select>
                <input type="hidden" name="isIndividual" value="<?php print $player->playerIsIndividual ?>" />
            </td>
        </tr>
    <?php }
}

function printFooter() { ?>
        <tr>
            <td colspan=2>
                <input type="submit" name="update" value="Update Player" />
            </td>
        </tr>
<?php } ?>