<?php 
function printJavaScript() { ?>
    <tr>
    	<td colspan=4>
            <table>
                <tr>
                    <td>
                        <noscript>
                            <font face='verdana' size=2 color=red><b>
                                For full functionality of this site it is necessary to enable JavaScript.
                            </b></font><br />
                            Here are the <a href='http://www.enable-javascript.com/' target='_blank'>
                            instructions how to enable JavaScript in your web browser</a>.<br /><br />
                        </noscript>
                    </td>
                </tr>
            </table>
    	</td>
    </tr>
<?php }

function printFormHeader($logo, $sportHeader) { ?>
	<tr>
        <td colspan=4>
            <TABLE class="logo" align="center">
                <tr>
                    <td><img src=<?php print $logo?>></td>
                </tr>
            </TABLE>
        </td>
    </tr>
    <tr>
        <td colspan=4><font face='verdana' size=4>
            <B><?php print $sportHeader?></B></font>
        </td>
    </tr>
<?php }

function printOldTeamList($oldTeams, $seas_name) {
	if(count($oldTeams) > 0 && !isset($_POST['register'])){ ?>		
        <tr>
            <td colspan=3 align=center>
                <table class="previousTeams">
                    <tr>
                        <td colspan=3 align=center>
                            <font face='verdana' size="3" color=red><B>
                            	Do you want to register one of these previous teams for <? print $seas_name?>?
                            </B></font>
                        </td>
                    <tr>
                        <th align="center">
                            Team Name
                        </th><th align="center">
                            League
                        </th><th align="center">
                            Season
                        </th>
                    </tr>
                    <?php for($i=0;$i<count($oldTeams); $i++) { ?>
						<tr>
                            <td align="center">
                                <?php print '<a href="signupTeam.php?sportID='.$oldTeams[$i]->teamSportID.'&teamID='.$oldTeams[$i]->teamID.'">'.$oldTeams[$i]->teamName.'</a>' ?>
                            </td><td align="center">
                                <?php print $oldTeams[$i]->teamLeagueName ?>
                            </td><td align="center">
                                <?php print $oldTeams[$i]->teamSeasonName ?>
                            </td>
                        </tr> 
                    <?php } ?>
                </table>
            </td>
        </tr>
	<?php }
}

function printLeagueAndTeam($teamObj, $leaguesDropDown) { 
	global $seasonData; ?>
	<tr>
        <td colspan=5 align=center>
            <font face='verdana' color=red><b>Registration due date<br />
			<?php foreach($seasonData as $season) {
				print $season['name'].' - '.$season['regDue'].'<br />';
            } ?></b></font>
        </td>
    </tr>
    <tr BGCOLOR="#CCCCCC">
        <td colspan=4 align="center">
            <b>1. Select Your Division and Choose Team Name</b>
            <br>
            <font face='verdana' size=1>Please choose a division.</font>
        </td>
    </tr>
    <tr>
        <td colspan=4>
            <TABLE align='center' class="leagueTeamName">
                <tr>
                    <th>
                        Preferred League
                    </th><td align="left">
                        <select id='changeLeague' class='userInput' name='leagueID'>
                            <?php print $leaguesDropDown;?>
                        </select>
                    </td>
                </tr><tr>
                    <th>
                        Team Name
                    </th><td align="left">
                        <input class='userInput' type='text' name='teamName' VALUE="<?php print htmlentities($teamObj->teamName, ENT_QUOTES);?>">
                    </td>
                </tr>
            </TABLE>
        </td>
    </tr>
<?php }

function printCaptainForm($playerObj) { ?>
	<tr BGCOLOR='#CCCCCC'>
        <td COLSPAN=4 align='center'>
            <B>2. Captain Information</B>
            <BR />
            <font face='verdana' SIZE=1>
                The captain is the first person we'll contact with team inquiries and is responsible for submitting scores.
            </FONT>
        </td>
    </tr>
    <tr>
        <td colspan=4>
            <TABLE class="playerTable" align=center cellspacing=10 cellpadding=1>
                <tr>
                    <td align='center'>
                        <B>First Name:</B>
                    </td><td align='center'>
                        <B>Last Name:</B>
                    </td><td align='center'>
                        <B>Gender:</B>
                    </td>   
                </tr>
                <tr BGCOLOR="white">
                    <td>
                        <INPUT TYPE="text" NAME="capFirst" VALUE="<?php print htmlentities($playerObj[0]->playerFirstName, ENT_QUOTES);?>" SIZE=40>
                    </td><td>
                        <INPUT TYPE="text" NAME="capLast" VALUE="<?php print htmlentities($playerObj[0]->playerLastName, ENT_QUOTES);?>" SIZE=40>
                    </td><td align=center>
                        <SELECT NAME="capSex">
                            <OPTION VALUE=''>Select One</OPTION>
                            <OPTION VALUE='M' <?php print $playerObj[0]->playerGender == 'M'?'selected':'';?>>Male</OPTION>
                            <OPTION VALUE='F' <?php print $playerObj[0]->playerGender == 'F'?'selected':'';?>>Female</OPTION>
                        </SELECT>
                    </td>
                </tr>
                <tr BGCOLOR="white">
                    <td align="center">
                        <B>Email:</B>
                    </td><td align="center">
                        <B>Phone Number:</B>
                    </td>
                </tr>
                <tr BGCOLOR="white">
                    <td>
                        <INPUT TYPE="text" NAME="capEmail" VALUE="<?php print htmlentities($playerObj[0]->playerEmail, ENT_QUOTES);?>" SIZE=40>
                    </td><td align="center">
                        <INPUT TYPE="text" NAME="capPhone" VALUE="<?php print htmlentities($playerObj[0]->playerPhone, ENT_QUOTES);?>" SIZE=15>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
<?php }

function printPlayerForm($playerObj, $people) { ?>
	<tr>
        <td colspan=4>
            <TABLE>
                <TR BGCOLOR='#CCCCCC'>
                    <TD COLSPAN=5 align='center'>
                        <font FACE='verdana' SIZE=2><B>3. Player Information</B></FONT>
                        <BR /><font face='verdana' SIZE=1><font face='verdana' COLOR='red'>*</FONT>
                            The second player will be listed as an alternate contact if the captain is unavailable.</FONT>
                    </TD>
                </TR>
                <tr BGCOLOR='white'>
                    <td align='center'>
                        <br />
                    </td><td align='center'>
                        <font face='verdana' SIZE='2'><B>First Name:</B></FONT>
                    </td><td align='center'>
                        <font face='verdana' SIZE='2'><B>Last Name:</B></FONT>
                    </td><td align='center'>
                        <font face='verdana' SIZE='2'><B>Email:</B></FONT>
                    </td><td align='center'>
                        <font face='verdana' SIZE='2'><B>Gender:</B></FONT>
                    </td>
                </tr>
                <?php for($v=1; $v<=$people; $v++){?>
                    <tr BGCOLOR="white">
                        <td>
                            <?php print $v.'.'.$v<3?'<font face=verdana COLOR="red" size=1>*</FONT>'.$v:$v ?>
                        </td><td>
                            <INPUT TYPE="text" NAME="playerFirst[]" VALUE="<?php print 
                                htmlentities($playerObj[$v]->playerFirstName, ENT_QUOTES);?>" SIZE=27>
                        </td><td>
                            <INPUT TYPE="text" NAME="playerLast[]" VALUE="<?php print 
                                htmlentities($playerObj[$v]->playerLastName, ENT_QUOTES);?>" SIZE=30>
                        </td><td>
                            <INPUT TYPE="text" NAME="playerEmail[]" VALUE="<?php 
                                print htmlentities($playerObj[$v]->playerEmail, ENT_QUOTES);?>" SIZE=30>
                        </td><td align=center>
                            <SELECT NAME="playerSex[]">
                            <OPTION VALUE='M' <?php print $playerObj[$v]->playerGender == 'M'?'selected':'';?>>Male</OPTION>
                            <OPTION VALUE='F' <?php print $playerObj[$v]->playerGender == 'F'?'selected':'';?>>Female</OPTION>
                            <OPTION VALUE='F' <?php print $playerObj[$v]->playerGender == 'O'?'selected':'';?>>Other</OPTION>
                        </SELECT>
                        </td>
                    </tr>
                <?php } ?>
            </TABLE>
        </td>
    </tr>     
<?php }

function printFormCommentsAndButtons($teamObj, $playerObj, $update, $payInfoDropDown, $aboutUsDropDown) { 
	global $seasonData; ?>
    <TR BGCOLOR='#CCCCCC'>
        <TD COLSPAN=4 align='center'>
            <B>4. Comments</B><BR>
            <font face='verdana' SIZE=1>Comments, notes, player needs, etc. (limit 1000 characters)</font>
        </TD>
    </TR>
    <TR>
        <TD colspan=4 align='center'>
            <TEXTAREA NAME='teamComments' COLS= 80 ROWS=6><?php print $teamObj->teamComments ?></TEXTAREA>
        </TD>
    </TR>
    <tr>
        <TD align=center>
            <font color='#FF0000'>*</font>How did you hear about us? 
            <select name='aboutUsMethod' onchange="showTextbox(this)">";
                <?php print $aboutUsDropDown ?>
            </select>
        </TD>
    </tr>
    <tr id='hearTextRow' style="display:none">
    	<td>
        	Other: <input type="text" style="width:250px" name="aboutUsTextBox" value="<?php print htmlentities($teamObj->aboutUsText, ENT_QUOTES) ?>" />
        </td>
    </tr>
    <?php if($update == 0) { ?>
    <TR BGCOLOR='#CCCCCC'>
        <TD align='center'>
            <B>5. Confirm Fees</B><BR>
            The registration process is not finalized until fees have been paid
        </TD>
    </TR>
    <tr>
        <TD align=center>
            <font color='#FF0000'>*</font>Payment Method:
            <select name='payMethod'>";
                <?php print $payInfoDropDown ?>
            </select>
        </TD>
    </tr>
    <tr>
        <td align=center>
            <br />
        </td>
    </tr>
    <tr>
        <td align=center><font face="Verdana" size=2>
            <B>Make Cheques Payable to Perpetual Motion<BR><BR>
            Send This Confirmation Form & Fees to:</b>
            <br>78 Kathleen St. Guelph, Ontario; N1H 4Y3
        </font></td>
    </tr>
    <?php } else if($update == 1) { ?>
		<tr>
        	<TR BGCOLOR='#CCCCCC'>
                <TD COLSPAN=4 align='center'>
                    <B>Confirm Fees</B><BR>
                    <font face='verdana' SIZE=1 COLOR='red'>**The registration process is not finalized until fees have been paid**</font>
                </TD>
            </TR>
        	<td colspan=4 align=center>
            	Would you like this team to be registered for the current season?
            	<SELECT NAME='isRegistered' onchange="updtShowPayMethod(this)">
				<?php for ($x = 1; $x>=0;$x--) { 
					if ($x == $teamObj->teamIsRegistered) { ?>
						<OPTION selected Value=<?php print $x?>><?php print $x>0?'Yes':'No' ?></OPTION>	
					<?php } else { ?>
						<OPTION Value=<?php print $x ?>><?php print $x>0?'Yes':'No' ?></OPTION>	
					<?php }
				} ?>
                </SELECT>
            </td>
        </tr>
        <tr id='updtPayMethod' <?php print $teamObj->teamIsRegistered == 1?'style="display:table-row"':'style="display:none"'?>>
        	<td>
            	<table class='payInfo' width="100%">
                    <tr>
                        <TD colspan=2 align=center>
                            Payment Method:
                            <select name='payMethod'>";
                                <?php print "hi" . $payInfoDropDown ?>
                            </select>
                        </TD>
                    </tr>
                    <tr>
                        <td colspan=2 align=center>
                            <br />
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2 align=center>
                            <B>Make Cheques Payable to Perpetual Motion<BR><BR>
                            Send This Confirmation Form & Fees to:</b>
                            <br>Perpetual Motion
                            <br>78 Kathleen St.
                            <br>Guelph, Ontario
                            <br>N1H 4Y3
                            <br>(519) 222-0095
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    <?php } ?>
    <tr id="regOrSave" style="display:table-row; background-color:#CCCCCC;">
    	<td COLSPAN=4 align='center'>
        	<B>6. Register Your Team or Save Details</B><BR>
			<font size=1>Submit your team registration to the convenor or save details to your profile for another time.</font>
        </td>
    </tr>
	<tr>
    	<td colspan=4 align=center>
        	<INPUT TYPE='Submit' <?php print $update==1?'':'style="display:none"' ?> NAME='update' onclick="return checkUpdate()" Value='Update Team'>
        	<INPUT TYPE='Submit' <?php print $update==0?'':'style="display:none"' ?> NAME='register' onclick="return showPayment()" Value='Register'>
			<INPUT TYPE='Submit' <?php print $update==0?'':'style="display:none"' ?> NAME='save' onclick="return errorCheck()" Value='Save Details'>
			<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>
		</td>
    </tr>
    <tr>
    	<td colspan=5 align=center>
        	<br /><font face='verdana' color=red size=2><b>Registration due date: 
			<?php foreach($seasonData as $season) {
				print '<br />'.$season['name'].' - '.$season['regDue'].'<br />';
            } ?>
            </b></font>
        </td>
    </tr>
<?php }

function dayString($dayNum) {
	if($dayNum ==1) {
		return 'Monday';
	} else if($dayNum ==2) {
		return 'Tuesday';
	} else if($dayNum ==3) {
		return 'Wednesday';
	} else if($dayNum ==4) {
		return 'Thursday';
	} else if($dayNum ==5) {
		return 'Friday';
	} else if($dayNum ==6) {
		return 'Saturday';
	} else if($dayNum ==7) {
		return 'Sunday';
	}
}