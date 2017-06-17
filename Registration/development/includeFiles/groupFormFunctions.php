<?php 
function printJavaScript() { ?>
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
<?php }

function printFormHeader($logo, $sportHeader) { ?>
	<tr>
        <td >
            <TABLE class="logo" align="center">
                <tr>
                    <td><img src=<?php print $logo?>></td>
                </tr>
            </TABLE>
        </td>
    </tr>
    <tr>
        <td ><font face='verdana' size=4>
            <B><?php print $sportHeader?></B></font>
        </td>
    </tr>
<?php }

function printDropDowns($sportsDropDown, $registration_due, $leagueDropDowns) { ?>
    <tr BGCOLOR="#CCCCCC">
        <td align="center">
            <b>1. Select Your Preferred Leagues</b>
            <BR /><font face='verdana' SIZE=1>In order of importance
        </td>
    </tr>
    <tr>
        <td align=center>
        	<table align="center" class="master">
            	<tr>
                	<td align="right">
            			<B><font color="#FF0000">*</font>Sport </B>
                    </td><td align=left>
                        <select id='userInput' name='sportID' onchange="reloadPageSport()">
                            <option value=0>Sports</option>
                            <?php print $sportsDropDown;?>
                        </select>
                    </td>
                </tr>
				<?php for($i=0;$i<3;$i++) { ?>
                <tr>
                    <td align=right>
                        <B><?php print $i==0?'<font color="#FF0000">*</font>':''?>Preferred League <?php print $i+1 ?> </B>
                    </td><td align=left>
                        <select id='userInput' name='leagueID[]'><option value=0>League</option>
                            <?php print $leagueDropDowns[$i];?>
                        </select>
                    </td>
                </tr>
                <?php } ?>
			</table>
        </td>
    </tr>
<?php }

function printPlayerForm($playerObj, $people) { ?>
	<tr>
        <td>
            <TABLE>
                <TR BGCOLOR='#CCCCCC'>
                    <TD COLSPAN=8 align='center'>
                        <font FACE='verdana' SIZE=2><B>2. Player(s) Information</B></FONT>
                        <BR /><font face='verdana' SIZE=1>1st additional player will be the alternate contact for the group
                    </TD>
                </TR>
                <tr BGCOLOR='white'>
                    <td align='center'>
                        <br />
                    </td><td align='center'>
                        <font face='verdana' SIZE='2'><B>First Name</B></FONT>
                    </td><td align='center'>
                        <font face='verdana' SIZE='2'><B>Last Name</B></FONT>
                    </td><td align='center'>
                        <font face='verdana' SIZE='2'><B>Email</B></FONT>
                    </td><td align='center'>
                        <font face='verdana' SIZE='2'><B>Phone Number</B></FONT>
                    </td><td align='center'>
                        <font face='verdana' SIZE='2'><B>Gender</B></FONT>
                    </td><td align='center'>
                        <font face='verdana' SIZE='2'><B>Skill</B></FONT>
                </tr>
                <?php for($v=0, $b=1; $v < 50; $v++, $b++){?>
                    <tr name='playerInfo[]' <?php print $v>4&&strlen($playerObj[$v]->playerFirstName) < 2?'style="display:none"':''?> BGCOLOR="white">
                        <td>
                            <?php print $b==1?"<font color='#FF0000'>*</font>You.":"$b." ?>
                        </td><td>
                            <INPUT TYPE="text" style="width:100px" NAME="playerFirst[]" VALUE="<?php print 
                                htmlentities($playerObj[$v]->playerFirstName, ENT_QUOTES);?>" SIZE=27>
                        </td><td>
                            <INPUT TYPE="text" NAME="playerLast[]" VALUE="<?php print 
                                htmlentities($playerObj[$v]->playerLastName, ENT_QUOTES);?>" SIZE=30>
                        </td><td>
                            <INPUT TYPE="text" NAME="playerEmail[]" VALUE="<?php 
                                print htmlentities($playerObj[$v]->playerEmail, ENT_QUOTES);?>" SIZE=30>
                        </td><td>
                            <INPUT TYPE="text" style="width:100px" NAME="playerPhone[]" VALUE="<?php 
                                print htmlentities(formatPhoneNumber($playerObj[$v]->playerPhone), ENT_QUOTES);?>" SIZE=30>
                        </td><td align=center>
                            <SELECT NAME="playerGender[]">
                                <OPTION VALUE='M' <?php print $playerObj[$v]->playerGender == 'M'?'selected':'';?>>Male</OPTION>
                                <OPTION VALUE='F' <?php print $playerObj[$v]->playerGender == 'F'?'selected':'';?>>Female</OPTION>
                            </SELECT>
                        </td><td align=center>
                            <SELECT NAME="playerSkill[]">
                                <OPTION VALUE=0>Choose</OPTION>
                                <OPTION VALUE=5>5(High)</OPTION>
                                <OPTION VALUE=4>4</OPTION>
                                <OPTION VALUE=3>3</OPTION>
                                <OPTION VALUE=2>2</OPTION>
                                <OPTION VALUE=1>1(Low)</OPTION>
                            </SELECT>
                        </td>
                    </tr>
                <?php } ?>
                	<tr>
                    	<td colspan=7>
                        	Add Rows:
                            <input type="text" id="numMoreRows" style="width:50px" />
                            <input type="button" onclick="return addRows()" value='Submit'/>
                        </td>
                    </tr>
            </TABLE>
        </td>
    </tr>     
<?php }

function printFormCommentsAndButtons($groupComments, $payInfoDropDown, $registration_due, $aboutUsDropDown, $aboutUsText) { ?>
    <TR BGCOLOR='#CCCCCC'>
        <td align='center'>
            <B>3. Comments</B><BR>
            <font face='verdana' SIZE=1>Comments, notes, player needs, etc. (limit 1000 characters)</font>
        </TD>
    </TR>
    <TR>
        <td align='center'>
            <TEXTAREA NAME='groupComments' COLS= 80 ROWS=6><?php print $groupComments ?></TEXTAREA>
        </TD>
    </TR>
    <TR BGCOLOR='#CCCCCC'>
        <TD align='center'>
            <B>4. Confirm Fees</B><BR>
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
    <TR BGCOLOR='#CCCCCC'>
        <TD align='center'>
            <B>5. How did you Hear About Us?</B><BR>
        </TD>
    </TR>
    <tr>
        <TD align=center>
            <font color='#FF0000'>*</font>Method:
            <select name='aboutUsMethod' onchange="showTextbox(this)">";
                <?php print $aboutUsDropDown ?>
            </select>
        </TD>
    </tr>
    <tr id='hearTextRow' style="display:none">
    	<td>
        	Other: <input type="text" style="width:250px" name="aboutUsTextBox" value="<?php print htmlentities($aboutUsText, ENT_QUOTES) ?>" />
        </td>
    </tr>
    <tr id="regOrSave" style="display:table-row; background-color:#CCCCCC;">
    	<td  align='center'>
        	<B>6. Register</B><BR>
			<font size=1>Submit your group/individual registration to the convenor</font>
        </td>
    </tr>
	<tr>
    	<td  align=center>
        	<INPUT TYPE='Submit' NAME='register' onclick="return checkError()" Value='Register'>
			<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>
		</td>
    </tr>
    <tr>
    	<td  align=center>
        	<br /><font face='verdana' color=red size=2><b>Registration due date: <?php print $registration_due?></b></font>
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