<?php 
function checkSameGames($curTeamOne, $curTeamTwo) {
	global $schedVars;
	
	for($i = 0; $i < count($schedVars->gamesThisWeek); $i++) {
		if($schedVars->weekNum < $schedVars->leaguePlayoffWeek && $curTeamOne == $schedVars->gamesThisWeek[$i][0]
			&& $curTeamTwo == $schedVars->gamesThisWeek[$i][1]) {
			
			print '<span style="color:red">Warning teams '.$curTeamOne.' and '.$curTeamTwo.' play eachother twice in week '.$schedVars->weekNum.'</span><br />';
		}
	}	
}

function printScheduleForm($seasonID, $sportID, $leagueID){ 
	global $leaguesDropDown, $sportsDropDown, $seasonsDropDown, $schedVars, $weeksDropDown; ?>

	<table>
		<tr>
			<th>
				League Form
			</th>
		</tr><tr>
			<td>
				Sport
				<select id='userInput' name='sportID' onchange='reloadUpdatePage()'>
					<?php print $sportsDropDown ?>
				</select>
			</td>
		</tr><tr>
			<td>
				Season
				<select id='userInput' name='seasonID' onchange='reloadUpdatePage()'>
					<?php print $seasonsDropDown ?>
				</select>
			</td>
		</tr><tr>
			<td>
				League
				<select id='userInput' name='leagueID' onchange='reloadUpdatePage()'>
					<?php print $leaguesDropDown ?>
				</select>
			</td>
		</tr><tr>
			<td>
				Playoff Start Week
				<select id='userInput' name='playoffWeek'>
					<?php print $weeksDropDown ?>
				</select>
			</td>
		</tr><tr>
			<td>
				<?php print "<a target='_blank'
					href='submitLeague.php?sportID=$sportID&seasonID=$seasonID&leagueID=$leagueID&update=1'>
					Edit League Parameters</a>"; ?>
			</td>
		</tr><tr>
			<td>
				Schedule URL http://data.perpetualmotion.org/
				<input type="text" name="url" style="width:600px" 
					value='<?php print $schedVars->leagueScheduleLink ?>'>
			</td>
		</tr><tr>
			<td>
				<input type='button' style="font-size:12px;" name="textFieldButton" id='toggleTextFieldButton'
					value='Check Excel Code'/>
				<input type='submit' name="makeSchedule" value='Make Schedule' />
				<input type='submit' style="font-size:12px;" name="addVenue" value='Venues Control Panel' 
					onclick='return openAddVenue();'/>
			</td>
		</tr>
	</table>
<?php }

function printWeeksHead() { ?>
	<tr>
		<th colspan=7>
			League Weeks
		</th>
	</tr><tr>
        <td>
            <br />
        </td><td>
            Date ID
        </td><td>
            Week
        </td><td>
            Calendar Day
        </td><td>
            Year
        </td><td>
            League Matches
        </td><td>
            Delete
        </td>
    </tr>
<?php }


function printWeekNodes($weeksObj) {
	for($i = 0; $i < count($weeksObj); $i++) { ?>
        <tr>
            <td>
                <?php print $i+1; ?>
            </td><td>
                <?php print $weeksObj[$i]->dateID; ?>
                <input type="hidden" name="dateID[]" value="<?php print $weeksObj[$i]->dateID; ?>">
            </td><td>
                <select name="dateWeek[]">
                    <?php print $weeksObj[$i]->getWeekDropDown(); ?>
                </select>
            </td><td>
                <?php print $weeksObj[$i]->dateActualWeek; ?>
            </td><td>
                <?php print $weeksObj[$i]->dateYear; ?>
            </td><td>
                <?php print $weeksObj[$i]->dateNumLeagueMatches; ?>
            </td><td>
                <input type='checkbox' name="delDate[]" value="<?php print $weeksObj[$i]->dateID; ?>" />
            </td>
        </tr>
    <?php }
} 

function printBottomButton() { ?>
    <tr>
		<td colspan=7>
			<input type='submit' name="fixNumbers" value='Update Weeks' />
		</td>
    </tr>
<?php }