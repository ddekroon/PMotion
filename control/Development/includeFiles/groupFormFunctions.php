<?php 

function printJavaScript() { ?>
    <div class="Table" align="center" style="width:90%; max-width:750px; min-width:250px;">
    	<div class="Row" align="center">
            <noscript>
                <font face='verdana' size=2 color=red><b>
                    For full functionality of this site it is necessary to enable JavaScript.
                </b></font><br />
                Here are the <a href='http://www.enable-javascript.com/' target='_blank'>
                instructions how to enable JavaScript in your web browser</a>.<br /><br />
            </noscript>
    	</div>
    </div>
<?php }

function printFormHeader($logo, $sportHeader) { ?>
	<div class="Table" style="width:90%; max-width:750px; min-width:250px;">
        <div class="noBorderCell" align="center">
        	<img src=<?php print $logo?>></td>
        </div>
        <div class="Row" align="center">
        	<font face='verdana' size=4><B><?php print $sportHeader?></B></font>
        </div>
    </div>
    <br />
<?php }

function printDropDowns($sportsDropDown, $registration_due, $leagueDropDowns) { ?>
    <div class="Table" style="width:90%; max-width:750px; min-width:250px;">
    	<div class="colourRow" align="center">
            <b>1. Select Your Preferred Leagues</b>
            <BR /><font face='verdana' SIZE=1>In order of importance
        </div>
    	<p><div class="Row" align="center">
            <B><font size=2 color="#FF0000">*</font><font size=2>Sport </font></B>
            <select id='userInput' name='sportID' onchange="reloadPageSport()">
            	<option value=0>Sports</option>
            	<?php print $sportsDropDown;?>        
            </select>
        </div>
        <div class="Row" align="center">
        	<div class="noBorderCell">        
				<?php for($i=0;$i<3;$i++) { ?>
                    <div class="Row" align="center">
                        <div class="Column">
                        <B><?php print $i==0?'<font size=2 color="#FF0000">*</font>':''?><font size=2>Preferred League <?php print $i+1 ?> </font></B>
                        </div>
                        <div class="Column">
                        <select id='userInput' style="width:100%" name='leagueID[]'><option value=0>League</option>
                            <?php print $leagueDropDowns[$i];?>    
                        </select> 
                        </div>
                    </div>
                <?php } ?>
        	</div>
        </div>
	</div>  
<?php }

function printPlayerForm($playerObj, $people) { ?>
	<div class="Table" align="center" style="width:90%; max-width:750px; min-width:250px;">
    	<div class="colourRow" align="center">
        	<font FACE='verdana' SIZE=2><B>2. Player(s) Information</B></FONT>
            	<BR /><font face='verdana' SIZE=1>1st additional player will be the alternate contact for the group
            </font>
        </div>
        <br  />
        <?php for($v=0, $b=1; $v < 50; $v++, $b++){?>
        	<div class="repeatingRow" align="center" id='playerInfo[]' <?php print $v>4&&strlen($playerObj[$v]->playerFirstName) < 2?'style="display:none"':''?>>
            	 <div class="Column" align="right" style="width:5%;">
				 <?php print $b==1?"<br /> <font color='#FF0000'>*</font>You.":"$b." ?>
                 </div>
                <div class="Column" align="center" style="width:18%;">
                	<?php if ($b==1) { ?>
						<font face='verdana' SIZE='2'><B>First Name</B></FONT>
                    <?php } ?>
                	<INPUT TYPE="text" style="width:100%" NAME="playerFirst[]" VALUE="<?php print htmlentities($playerObj[$v]->playerFirstName, ENT_QUOTES);?>">
                </div>
           		<div class="Column" align="center" style="width:25%;">
                <?php if ($b==1) { ?>
						<font face='verdana' SIZE='2'><B>Last Name</B></FONT>
                    <?php } ?>
                	<INPUT TYPE="text" style="width:100%" NAME="playerLast[]" VALUE="<?php print htmlentities($playerObj[$v]->playerLastName, ENT_QUOTES);?>">
                </div>
            	<div class="Column" align="center" style="width:32%;">
                <?php if ($b==1) { ?>
						<font face='verdana' SIZE='2'><B>Email</B></FONT>
                    <?php } ?>
                	<INPUT TYPE="text" style="width:100%" NAME="playerEmail[]" VALUE="<?php print htmlentities($playerObj[$v]->playerEmail, ENT_QUOTES);?>">
                </div>
            	<div class="Column" align="center" style="width:25%;">
                <?php if ($b==1) { ?>
						<font face='verdana' SIZE='2'><B>Phone #</B></FONT>
                    <?php } ?>
                	<INPUT TYPE="text" style="width:100%" style="width:100px" NAME="playerPhone[]" VALUE="<?php  print htmlentities(formatPhoneNumber($playerObj[$v]->playerPhone), ENT_QUOTES);?>">
                </div>
            	<div class="Column" align="center" style="width:5%;">
                <?php if ($b==1) { ?>
						<font face='verdana' SIZE='2'><B>Gender</B></FONT>
                    <?php } ?>
                    <SELECT NAME="playerGender[]">
                        <OPTION VALUE='M' <?php print $playerObj[$v]->playerGender == 'M'?'selected':'';?>>Male</OPTION>
                        <OPTION VALUE='F' <?php print $playerObj[$v]->playerGender == 'F'?'selected':'';?>>Female</OPTION>
                    </SELECT>
                </div>
                <div class="Column" align="center" style="width:5%;">
                <?php if ($b==1) { ?>
						<font face='verdana' SIZE='2'><B>Skill</B></FONT>
                    <?php } ?>
                    <SELECT NAME="playerSkill[]">
                        <OPTION VALUE=0>Choose</OPTION>
                        <OPTION VALUE=5>5(High)</OPTION>
                        <OPTION VALUE=4>4</OPTION>
                        <OPTION VALUE=3>3</OPTION>
                        <OPTION VALUE=2>2</OPTION>
                        <OPTION VALUE=1>1(Low)</OPTION>
                    </SELECT>
                </div>
                <div class="Column" align="center" style="width:5%;">
                	<?php if ($b==1) { ?>
                    	<font face='verdana' SIZE='2'><B>Add*</B></FONT>
                    <?php } ?>
                    <INPUT TYPE='checkbox' NAME='checkBox[]' VALUE='<?php print $v;?>' />
                </div>
            </div>    
        <?php } ?>
    </div>
    <div class="Table" align="center" style="width:90%; max-width:750px; min-width:250px;">
        <div class="Row" align="center">
            Add Rows:
            <input type="text" id="numMoreRows" style="width:50px" />
            <script type="text/javascript" src="includeFiles/groupJavaFunctions.js"></script>
            <input type="button" onclick="return addRows()" value='Submit'/>
        </div>
        <div class="Row" align="center">
        	<font size=1>* By checking this, you agree to receive reminders about score submissions and registration due dates via email.</font>
        </div>
    </div>  
    <br  />
<?php }

function printFormCommentsAndButtons($groupComments, $payInfoDropDown, $registration_due, $aboutUsDropDown, $aboutUsText) { ?>
    <div class="Table" align="center" style="width:90%; max-width:750px; min-width:250px;">
    	<div class="colourRow" align="center">
            <B>3. Comments</B><BR>
            <font face='verdana' SIZE=1>Comments, notes, player needs, etc. (limit 1000 characters)</font>
        </div>
    	<p><div class="Row" align="center">
            <TEXTAREA NAME='groupComments' style="width:95%" ROWS=6><?php print $groupComments ?></TEXTAREA>
        </div></p>
    	<div class="colourRow" align="center">
            <B>4. Confirm Fees</B><BR>
            The registration process is not finalized until fees have been paid
       	</div>
   		<p><div class="Row" align="center">
            <font color='#FF0000'>*</font>Payment Method:
            <select name='payMethod'>";
                <?php print $payInfoDropDown ?>
            </select>
        </div>
    	<div class="Row">
            <br />
        </div>
    	<div class="Row" align="center">
        	<font face="Verdana" size=2>
            	<B>Make Cheques Payable to Perpetual Motion<BR><BR>
            	Send This Confirmation Form & Fees to:</b>
            	<br>78 Kathleen St. Guelph, Ontario; N1H 4Y3
        	</font>
        </div></p>
    	<div class="colourRow" align="center">
            <B>5. How did you Hear About Us?</B><BR>
        </div>
   		<p><div class="Row" align="center">
            <font color='#FF0000'>*</font>Method:
            <select name='aboutUsMethod' onchange="showTextbox(this)">";
                <?php print $aboutUsDropDown ?>
            </select>
        </div>
    	<div class="Row" align="center" id='hearTextRow' style="display:none">
        	Other: <input type="text" style="width:100%" name="aboutUsTextBox" value="<?php print htmlentities($aboutUsText, ENT_QUOTES) ?>" />
    	</div></p>
    	<div class="colourRow" id="regOrSave" align="center">
        	<B>6. Register</B><BR>
			<font size=1>Submit your group/individual registration to the convenor</font>
		</div>
    	<p><div class="Row" align="center">
        	<INPUT TYPE='Submit' NAME='register' onclick="return checkError()" Value='Register'>
			<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>
		</div>
    	<div class="Row" align="center">
        	<br /><font face='verdana' color=red size=2><b>Registration due date: <?php print $registration_due?></b></font>
        </div>
    </div></p>
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
} ?>