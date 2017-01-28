<?php function printTeamsHeader($sportsDropDown, $leaguesDropDown) { ?>
	<tr>
		<th colspan=5>
			Available Prize Winners
		</th>
	</tr><tr>
		<td colspan=5>
			Sport
			<select id='userInput' name='sportID' onchange='reloadPageSport()'>
				<?php print $sportsDropDown; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan=5>
			League
			<select id='userInput' name='leagueID' onchange='reloadPageLeague()'>
				<?php print $leaguesDropDown; ?>
			</select>
		</td>
	</tr> 
<?php }

function printTeamsTop() { ?>
	<tr>
        <td style="width:20px;">
            Mv
        </td><td style="width:20px;">
        	#
        </td><td>
        	Team Name
        </td><td>
        	League
        </td><td>
        	Captain Name
        </td>
    </tr>
<?php }

function printTeamNode($count, $curTeam) { ?>
	<tr>
        <td style="vertical-align:middle; width:20px;">
        	<INPUT TYPE='checkbox' NAME='moveTeam[]' VALUE=<?php print $curTeam->teamID?>>
        </td><td style="width:20px;">
            <?php print $count; ?>
		</td><td>
            <?php print $curTeam->teamName ?>
		</td><td>
            <?php print $curTeam->teamLeagueName ?>
		</td><td>
            <?php print "<a href='mailto:$curTeam->teamCapEmail'>$curTeam->teamCapName</a>"; ?>
        </td>
	</tr>
<?php }

function printWinnersHeader($prizeTimesDropDown, $prizeTime) { ?>
	<tr>
		<th colspan=6>
			Current Winners
		</th>
	</tr><tr>
		<td colspan=6>
			Time Frame
			<select id='userInput' name='prizeTime' onchange='reloadPagePrize()'>
				<option value=0>--Prize Time--</option>
				<?php print $prizeTimesDropDown; ?>
			</select>
		</td>
	</tr><tr>
		<td colspan=6>
			<button name='manageTime' value=1 onclick="return manageTimes()">Manage Time Frames</button>
			<button name='printWinner' value=1 <?php print $prizeTime == 0?'disabled="disabled"':''?> onclick="return printWinners(<?php print $prizeTime?>)">Print Winners</button>
		</td>
	</tr>
<?php }


function printWinnersTop($sportID, $leagueID) { 
	global $prizeTime;?>
	<tr>
        <td style="width:20px;">
            Mv
        </td><td style="width:20px;">
        	#
        </td><td>
        	Team Name
        </td><td>
        	<a href='<?php print "prizeWinners.php?sportID=$sportID&leagueID=$leagueID&prizeTime=$prizeTime&sortBy=League" ?>'>League</a>
        </td><td>
        	Captain Name
        </td><td>
            <a href='<?php print "prizeWinners.php?sportID=$sportID&leagueID=$leagueID&prizeTime=$prizeTime&sortBy=Prize" ?>'>Prize</a>
            <button name="managePrizes" value=1 onclick="return openManagePrizes()">Manage Prizes</button>
        </td>
    </tr>
<?php }

function printWinnerNode($count, $curTeam) { ?>
	<tr>
        <td style="vertical-align:middle; width:20px;">
            <INPUT TYPE='checkbox' NAME='moveWinner[]' VALUE=<?php print $curTeam->teamID?>>
        </td><td style="width:20px;">
            <?php print $count; ?>
		</td><td>
            <?php if(strlen($curTeam->teamName) > 20) {
				print '<span style="font-size:10px">'.$curTeam->teamName.'</span>';
			} else {
				print $curTeam->teamName;
			}?>
		</td><td>
            <?php print $curTeam->teamLeagueName ?>
		</td><td>
            <?php print "<a href='mailto:$curTeam->teamCapEmail'>$curTeam->teamCapName</a>"; ?>
        </td><td>
        	<input type="hidden" name="winnerTeamID[]" value="<?php print $curTeam->teamID?>" />
            <select name="teamPrize[]">
            	<option value='N/A'>--Prize--</option>
				<?php print $curTeam->teamPrizeDD; ?>
            </select>
        </td>
	</tr>
<?php }

function printButtons($type) {
	if($type == 1) {?> 
        <tr>
            <td colspan=5>
                <input type='button' name='CheckAllTeams' value='Check All Teams' onClick="return checkAll('moveTeam[]')">
                <input type='submit' name='moveTeams' value='Move To Winners' onClick='return checkYesNo()'>
            </td>
        </tr>
    <?php } else { ?>
        <tr>
            <td colspan=6>
            	<input type='button' name='CheckAllWinners' value='Check All Winners' onClick="return checkAll('moveWinner[]')">
                <input type='submit' name='updatePrizes' value='Update Prizes' onClick='return checkYesNo()'>
                <input type='submit' name='moveWinners' value='Move To Teams' onClick='return checkYesNo()'>
            </td>
        </tr>
    <?php }
}

function printPrintableWinnersHeader($prizeTimesDropDown) { ?>
	<td>
        <table class="titleBox">
            <tr>
                <th>
                    Current Winners
                </th>
            </tr>
        </table><table class='getIDs'>
            <tr>
                <td>
                    Time Frame
				</td><td>
                    <select name='prizeTime' onchange='reloadPagePrize()'>
                        <option value=0>--Prize Time--</option>
                        <?php print $prizeTimesDropDown; ?>
                    </select>
                </td>
            </tr>
        </table>
    </td>
<?php }

function printPrintableWinnersTop($prizeTime) { ?>
	<tr>
		<th style="width:20px;">
        	#
        </th><th>
        	Team Name
        </th><th>
        	<a href='<?php print "printPrizeWinners.php?prizeTime=$prizeTime&sortBy=League" ?>'>League</a>
        </th><th>
        	Captain Name
        </th><th>
            <a href='<?php print "printPrizeWinners.php?prizeTime=$prizeTime&sortBy=Prize" ?>'>Prize</a>
        </th>
    </tr>
<?php }
function printPrintableWinnerNode($count, $curTeam) { ?>
	<tr>
		<td style="width:20px;">
            <?php print $count; ?>
		</td><td>
            <?php print $curTeam->teamName ?>
		</td><td>
            <?php print $curTeam->teamLeagueName ?>
		</td><td>
            <?php print "<a href='mailto:$curTeam->teamCapEmail'>$curTeam->teamCapName</a>"; ?>
        </td><td>
        	<?php print $curTeam->teamPrizeString; ?>
        </td>
	</tr>
<?php }