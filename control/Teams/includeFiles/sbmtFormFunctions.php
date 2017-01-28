<?php 
function printInfoDDs($seasonsDD, $sportsDD, $leaguesDD) { ?>
	<tr>
		<td>
        	<select name="seasonID" onchange="reloadPageSeason()">
            	<option value=0>--SEASON--</option>
                <?php print $seasonsDD ?>
            </select>
        </td>
    </tr>
    <tr>
		<td>
        	<select name="sportID" onchange="reloadPageSport()">
            	<option value=0>--SPORT--</option>
                <?php print $sportsDD ?>
            </select>
        </td>
    </tr>
    <tr>
		<td>
        	<select name="leagueID" onchange="reloadPageLeague()">
            	<option value=0>--LEAGUE--</option>
                <?php print $leaguesDD ?>
            </select>
        </td>
    </tr>
<?php }

function printTeamInfo($teamName) { ?>
	<tr>
		<td>
        	Team Name: 
        	<input type="text" name="teamName" value="<?php print $teamName?>" />
        </td>
    </tr>
<?php }

function printPlayerHeader() { ?>
	<tr>
        <th align='center'>
            <br />
        </th><th align='center'>
            First Name
        </th><th align='center'>
            Last Name
        </th><th align='center'>
            Email
        </th><th align='center'>
            Sex
        </th><th align='center'>
            Phone Num
        </th>
    </tr>
    <tr>
    	<td colspan=6>
        	<font color="#FF0000">**Note: Player 1 will be inputted as captain</font>
        </td>
    </tr>
<?php }

function printPlayerNode($curPlayer, $curNum) { ?>
	<tr>
		<td>
			<?php print $curNum+1; ?>
		</td><td>
			<INPUT TYPE="text" NAME="playerFirst[]" VALUE="<?php print htmlentities($curPlayer->playerFirstName, ENT_QUOTES);?>" style="width:100px;">
		</td><td>
			<INPUT TYPE="text" NAME="playerLast[]" VALUE="<?php print htmlentities($curPlayer->playerLastName, ENT_QUOTES);?>" style="width:130px;">
		</td><td>
			<INPUT TYPE="text" NAME="playerEmail[]" VALUE="<?php print htmlentities($curPlayer->playerEmail, ENT_QUOTES);?>" style="width:200px;">
		</td><td align=center>
			<SELECT NAME="playerSex[]">
				<OPTION VALUE='M' <?php print $curPlayer->playerGender == 'M'? 'selected':''?>>Male</OPTION>
				<OPTION VALUE='F'<?php print $curPlayer->playerGender == 'F'? 'selected':''?>>Female</OPTION>
				<OPTION VALUE='O' <?php print $curPlayer->playerGender == 'O'? 'selected':'' ?>>Other</OPTION>
			</SELECT>
		</td><td>
			<INPUT TYPE="text" NAME="playerPhone[]" VALUE="<?php print preg_replace("/\D/",'',$curPlayer->playerPhone)?>" style="width:100px;">
		</td>
	</tr>
<?php }

function printComments($teamComments) { ?>
	<TR>
        <TD colspan=6 align='center'>
           	Comments:
        </TD>
    </TR>
    <TR>
        <TD colspan=6 align='center'>
            <TEXTAREA NAME='teamComments' COLS=80 ROWS=6><?php print $teamComments ?></TEXTAREA>
        </TD>
    </TR>
<?php }

function printButtons() { ?>
	<tr>
    	<td align="center">
        	<button name="submit" value=1>Submit Team</button>
        </td>
    </tr>
<?php }