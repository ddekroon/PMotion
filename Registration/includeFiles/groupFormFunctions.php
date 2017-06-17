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

function printDropDowns($sportsDropDown, $registration_due, $leagueDropDowns, $sportID) { ?>
    <tr BGCOLOR="#CCCCCC">
        <td align="center">
            <b>1. Select Your Preferred Leagues</b>
            <BR /><font face='verdana' SIZE=1>In order of importance
        </td>
    </tr>
 
                <?php /*Printing the league descriptions for frisbee and beach volleyball*/
					  switch($sportID){ 
						/*Frisbee*/
							case 1:?>
                            <tr bgcolor="#e6e6e6">
                                <td align="left" colspan=2>
                                <font face='verdana' SIZE=2 ><center><b><u>League Skill Level </u></b></center>
                                <font face='verdana' SIZE=2> <b>A:</b> This 7 vs 7 division is recommended for teams and players who would like to play very competitive Ultimate at a high-pace. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;Players generally have lots of tournament experience and a very strong knowledge of rules and strategies.  <br />
                                <font face='verdana' SIZE=2> <b>B7:</b> This 7 vs 7 division is recommended for teams and players who would like to try playing 7s Ultimate. Players generally have at  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;least a couple years of league experience and are fairly knowledgeable of rules and strategies. <br/>
                                <font face='verdana' SIZE=2> <b>B/B1:</b> This 5 vs 5 division is recommended for teams and players who are of high intermediate skill level. Players generally have a &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; few years of league experience, and a good knowledge of rules and strategies, such as the stack. <br />
                                <font face='verdana' SIZE=2> <b>B2:</b> This 5 vs 5 division is recommended for teams and players who are of intermediate skill level. Players generally have a couple &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; years of league experience and a decent knowledge of rules and strategies, such as the "stack". <br />
                                <font face='verdana' SIZE=2> <b>C/C1:</b> This 5 vs 5 division is recommended for teams and players who are of high beginner skill levels. Players generally have at &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; least a year of league experience and a basic knowledge of rules and strategies. <br />
                                <font face='verdana' SIZE=2> <b>C2:</b> This 5 vs 5 division is recommended for teams and players who are new to the sport of ultimate. Players have less than a year &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; of league experience and have little knowledge of rules and strategies. Players are more focused on learning the game and are &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; less concerned with the skill level. </td>
                                </tr>
                      <?php break;
						/*Beach Volleyball*/
							case 2:?>
                            		<tr bgcolor="#e6e6e6">
                                  
                                	<td align="left"> 
                                    	<font face='verdana' SIZE=2 ><center><b><u>League Skill Level </u></b></center><br/>
                                		<font face='verdana' SIZE=2> <b>&nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Open 2's:</b> Extremely competitive, high level of play. <br/> <br/>
                                        <b>&nbsp;Competitive 4's: </b> Extremely competitive, high level of play. Expect players to control the ball well with a hard spike and/or serve. <br/> <br/>
                                        <b style="text-indent:5em">Intermediate 4's: </b> Players with few years of experience looking for competitive play. Expect a moderately controlled game &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; with a spike. More court to cover by an individual. <br/><br/>
                                        <b>Intermediate 6's:</b> Similar to intermediate 4's but with less court to cover.<br/><br/>
                                        <b>&nbsp;Recreational 6's:</b> Out to have fun with less emphasis on the classic "bump-set-spike" play.
                                </td> </tr><!--
                                <tr><td align="left">
                                	<font face='verdana' SIZE=2> <b>Competetive 4's: </b> Extreamly competitive, high level of play. Expect players to control the ball well with a hard spike and/or serve.
                                </td></tr>
                                <tr><td align="left">
                                	<font face='verdana' SIZE=2> <b>Intermediate 4's: </b> Players with few years of experience looking for competetive play. Expect a moderatly controlled game (3 contacts) with a spike. More court to cover by an individual.
                                </td></tr>
                                <tr><td align="left">
                                	<font face='verdana' SIZE=2> <b>Intermediate 6's:</b> Similar to intermediate 4's but with less court to cover.
                                </td></tr>
                                <tr><td align="left">
                                	<font face='verdana' SIZE=2> <b>Recreational 6's:</b> Out to have fun with less emphasis on the classic "bump-set-spike" play.
                                    </td></tr>-->

                      <?php break;
						} ?>
    <tr>
        <td align=center>
        	<table align="center" class="master">
            	<tr>

                </tr>
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
<?php
 }

function printPlayerForm($playerObj, $people, $sportID) { ?>
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
                            	<!-->Drop menu with the skill levels<-->
                                <?php switch($sportID){ 
									/*Unknown*/
									case 0:?>
                                		<OPTION VALUE=0>Choose</OPTION>
                                		<OPTION VALUE=5>5(High)</OPTION>
                               	 		<OPTION VALUE=4>4</OPTION>
                                		<OPTION VALUE=3>3</OPTION>
                                		<OPTION VALUE=2>2</OPTION>
                                		<OPTION VALUE=1>1(Low)</OPTION>
									<?php break;
									/*Frisbee*/
									case 1:?> 
                                		<OPTION VALUE=0>Choose</OPTION>
                                		<OPTION VALUE=5 title="Very knowledgeable about the sport/Competed at a high level">5(High)</OPTION>
                               	 		<OPTION VALUE=4 title="Knowledgeable about the sport/Competed in the sport before">4</OPTION>
                                		<OPTION VALUE=3 title="Familiar with the sport/Some experience competing in the sport">3</OPTION>
                                		<OPTION VALUE=2 title="Little experience/Played Recreationally">2</OPTION>
                                		<OPTION VALUE=1 title="New to the sport">1(Low)</OPTION>
									<?php break;
									/*Beach Volleyball*/
									case 2:?>
                                		<OPTION VALUE=0>Choose</OPTION>
                                		<OPTION VALUE=5 title="Competed/competes at a high level ie. Varsity or equivalent">5(High)</OPTION>
                               	 		<OPTION VALUE=4 title="Competed/competes in competitive competition ie. Organized travel team or has played some sort of competitive league">4</OPTION>
                                		<OPTION VALUE=3 title="Familiar with the sport/Some experience competing in the sport ie. High school team or league play">3</OPTION>
                                		<OPTION VALUE=2 title="Little experience/Played Recreationally">2</OPTION>
                                		<OPTION VALUE=1 title="New to the sport">1(Low)</OPTION>
									<?php break;
									/*Football*/
									case 3:?>
                                		<OPTION VALUE=0>Choose</OPTION>
                                		<OPTION VALUE=5 title="Competed/competes at a high level ie. Varsity or equivalent">5(High)</OPTION>
                               	 		<OPTION VALUE=4 title="Competed/competes in competitive competition ie. Organized travel team or has played some sort of competitive league">4</OPTION>
                                		<OPTION VALUE=3 title="Familiar with the sport/Some experience competing in the sport ie. High school team or league play">3</OPTION>
                                		<OPTION VALUE=2 title="Little experience/Played Recreationally">2</OPTION>
                                		<OPTION VALUE=1 title="New to the sport">1(Low)</OPTION>
									<?php break;
									/*Soccer*/
									case 4:?>
                                		<OPTION VALUE=0>Choose</OPTION>
                                		<OPTION VALUE=5 title="Competed/competes at a high level ie. Varsity or equivalent">5(High)</OPTION>
                               	 		<OPTION VALUE=4 title="Competed/competes in competitive competition ie. Organized travel team or has played some sort of competitive league">4</OPTION>
                                		<OPTION VALUE=3 title="Familiar with the sport/Some experience competing in the sport ie. High school team or league play">3</OPTION>
                                		<OPTION VALUE=2 title="Little experience/Played Recreationally">2</OPTION>
                                		<OPTION VALUE=1 title="New to the sport">1(Low)</OPTION>
									<?php break;
									 } ?>
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