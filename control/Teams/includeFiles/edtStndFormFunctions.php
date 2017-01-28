<?php //prints the top info of the score reporter, ie the game info and title
function printTopInfo($sportsDropDown, $leaguesDropDown) {
	global $sportID, $leagueID ?>
	<tr>
    	<td colspan = 2>
            <TABLE class='titleBox'>
                <tr>
                	<th style="font-size:24px; font-weight:bold;" colspan=2>
                    	Team Editor
                    </th>
                </tr>
            </TABLE>
        </td>
    </tr>
    <tr>
    	<td colspan=2>
        	<table class='getIDs'>
                <TR>
                    <TD align=center>
                        Sport:
                        <select name='sportID' onchange='reloadPageSport()'>
                            <option value=0>--SPORT NAME--</option>
                            <?php print $sportsDropDown; ?>
                        </select>
                    </TD>
                </TR>
                <TR>
                    <TD align=center>
                        League:
                        <select name='leagueID' onchange='reloadPageLeague()'>
                            <option value=0>--LEAGUE NAME--</option>
                            <?php print $leaguesDropDown; ?>
                        </select>
                    </TD>
                </TR> 
                <tr>
                	<td>
                    	<br />
                    </td>
                </tr>
            </TABLE>
    	</td>
    </tr>
<?php }


function printStandingsHeader(){ ?>
    <tr>
	<td align=center>
	    Team
	</td>
	<td align=center>
	    Wins
	</td><td align=center>
	    Losses
	</td><td align=center>
	    Ties
	</td>
    </tr>                     
<?php }


function printTeamNode($curTeam){ ?>
    <tr>
        <td align=center>
            <?php print $curTeam->teamName; ?>
            <input type='hidden' name='teamID[]' value=<?php print $curTeam->teamID ?> />
	</td>
        <td align=center>
	    <select name='teamWins[]'>
		<?php for($i=0;$i<30;$i++) {
		    print "<option ";
            print $i==$curTeam->teamWins?'selected':'';
            print " value=$i>$i</option>";
		} ?>
	    </select>
	</td>
        <td align=center>
	    <select name='teamLosses[]'>
		<?php for($i=0;$i<30;$i++) {
		    print "<option ";
            print $i==$curTeam->teamLosses?'selected':'';
            print " value=$i>$i</option>";
		} ?>
	    </select>
	</td>
        <td align=center>
	    <select name='teamTies[]'>
		<?php for($i=0;$i<30;$i++) {
		    print "<option ";
            print $i==$curTeam->teamTies?'selected':'';
            print " value=$i>$i</option>";
		} ?>
	    </select>
	</td>
    </tr>
<?php }

function printButtons() { ?>
    <tr>
	<td>
	    <button name='submitTeams' value=1 onclick="checkYesNo()">Submit Teams</button>
	</td>
    </tr>
<?php } ?>