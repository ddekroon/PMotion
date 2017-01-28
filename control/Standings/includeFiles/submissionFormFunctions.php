<?php

function printJavaScript() { ?>
	<noscript>
		<font face='verdana' size=2 color=red><b>
			For full functionality of this site it is necessary to enable JavaScript.
		</b></font><br />
		Here are the <a href='http://www.enable-javascript.com/' target='_blank'>
		instructions how to enable JavaScript in your web browser</a>.
	</noscript>
<?php }


//prints the top info of the score reporter, ie the game info and title
function printTopInfo($sportsDropDown, $leaguesDropDown, $weeksDropDown) { ?>
	Sport
	<select id='userInput' name='sportID' onchange='reloadPageSport()'>
		<?php print $sportsDropDown; ?>
	</select><br /><br />
	League
	<select id='userInput' name='leagueID' onchange='reloadPage()'>
		<?php print $leaguesDropDown; ?>
	</select><br /><br />
	Week
	<select id='userInput' name='dateID' onchange='secondReload()'>
		<option value=0>--SELECT WEEK--</option>
		<?php print $weeksDropDown;?>
	</select>
<?php }

function printError($error) {?>
    <font color="#FF0000" size=3>
        <?php print $error?>
    </font>
<?php }

//prints the matches sections of the score reporter
function printHeaderInfo($matches, $games) { ?>
	<th colspan=9>
		Score Submissions
	</th>
	</tr><tr>
	<td>
    	Team Name
    </td>
    <?php for($j=0;$j < $matches;$j++) { ?>
        <td>
            Opponent #<?php print $j+1?>
        </td>
        <?php for($q=1;$q<= $games;$q++) { ?>
            <td>
            	<?php print 'Game '.$q ?>
            </td>
            <?php } ?>
        <td>
            Spirit
        </td>
    <?php } ?>
        <td>
            Notes
        </td>
<?php }

//prints the matches sections of the score reporter
function printTeamInfo($teamNum) {
	global $games, $matches, $teamName, $teamID,  $oppTeamsDropDown, $teamSpiritSubmission, $teamGameResult, $dbTeamNote;
	$gameNum = 0; ?>
	
	<td>
    	<?php print "<a href='../Search/teamPage.php?teamID=$teamID[$teamNum]'>$teamName[$teamNum]</a>" ?>
    </td>
    <?php for($j=0;$j < $matches;$j++) { ?>
        <td align='center'>
            <select name='oppID[<?php print $teamNum ?>][<?php print $j?>]'>
                <option value=0>Select Opponent # <?php print $j+1?></option>
                <?php print $oppTeamsDropDown[$teamNum][$j]; ?>
            </select>
        </td><?php
        for($q=0;$q< $games;$q++) { ?>
            <td>
                <select name='gameResult[<?php print $teamNum ?>][<?php print $gameNum ?>]'>
                	<option VALUE=0>Result</option>
                    <?php if ($teamGameResult[$teamNum][$gameNum] == 1) {?>
                        <option selected VALUE=1>Won</option>
                    <?php } else { ?>
                        <option VALUE=1>Won</option>
                    <?php }
                    if ($teamGameResult[$teamNum][$gameNum] == 2) {?>
                        <option selected VALUE=2>Lost</option>
                    <?php } else { ?>
                        <option VALUE=2>Lost</option>
                    <?php }
                    if ($teamGameResult[$teamNum][$gameNum] == 3) {?>
                        <option selected VALUE=3>Tied</option>
                    <?php } else { ?>
                        <option VALUE=3>Tied</option>
                    <?php }
                    if ($teamGameResult[$teamNum][$gameNum] == 4) {?>
                        <option selected VALUE=4>Cancel</option>
                    <?php } else { ?>
                        <option VALUE=4>Cancel</option>
                    <?php }
                    if ($teamGameResult[$teamNum][$gameNum] == 5) {?>
                        <option selected VALUE=5>Practy</option>
                    <?php } else { ?>
                        <option VALUE=5>Practy</option>
                    <?php } ?>
                </select>
            </td>
            <?php $gameNum++;
        } ?>
        <td>
        	<select name='spirit[<?php print $teamNum ?>][<?php print $j ?>]'>
            	<option value=0>0</option>
            <?php for($k = 5; $k >=1; $k-=.5) {
                if ($k == $teamSpiritSubmission[$teamNum][$j]) {
                    print "<option selected VALUE=$k >$k</INPUT>";
                } else {
                    print "<option VALUE=$k >$k</INPUT>";
                }
            }?>
        </td>
    <?php } ?>
		<td><INPUT TYPE='text' NAME='note[<?php $teamNum ?>]' value='<?php print $dbTeamNote[$teamNum] ?>' SIZE=20></td>
<?php } ?>