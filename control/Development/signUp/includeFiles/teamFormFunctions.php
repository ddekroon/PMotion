<?php 
function printJavaScript() { ?>
    <div class="Table" align="center" style="width:90%; max-width:750px; min-width:250px;">
    	<div class="Row" align="center">
            <noscript>
                <font face='verdana' size="2" color="red"><b>
                    For full functionality of this site it is necessary to enable JavaScript.
                </b></font><br />
                Here are the <a href='http://www.enable-javascript.com/' target='_blank'>
                instructions how to enable JavaScript in your web browser</a>.<br /><br />
          </noscript>
   		</div>
    </div>
<?php }

function printFormHeader($logo, $sportHeader) { ?>
	<div class="Table" align="center" style="width:90%; max-width:750px; min-width:250px;">
        <div class="noBorderCell" align="center" style="width:30%;">
             <img src="<?php print $logo?>" />
        </div>
        <div class="Row" align="center" style="width:30%;">        
        	<font face='verdana' size="4">
            <b><?php print $sportHeader?></b></font>
        </div>
    </div>
<?php }

function printOldTeamList($oldTeams, $seas_name) {
	if(count($oldTeams) > 0 && !isset($_POST['register'])){ ?>		
        <div class="Table" align="center" style="width:90%; max-width:750px; min-width:250px;">
            <div class="Cell" align="center">
            	<div class="Row" align="center">
                    <div class="noBorderCell">
                        <div class="Row" align="center">
                            <font face='verdana' size="3" color="red"><b>
                                Do you want to register one of these previous teams for <? print $seas_name?>?
                            </b></font>
                        </div>
                    </div>
                </div>
                <div class="Row" align="center">
                    <div class="noBorderCell">
                        <div class="Row" align="center">
                            <div class="Column" align="center">
                                <font style="font-weight:bold"> Team Name </font>
                            </div><div class="Column" align="center">
                               <font style="font-weight:bold"> League </font>
                            </div><div class="Column" align="center">
                                <font style="font-weight:bold"> Season </font>
                            </div>
                        </div>
                        <?php for($i=0;$i<count($oldTeams); $i++) { ?>
                        <div class="Row" align="center">
                            <div class="Column" align="center">
                                <?php print '<a href="signupTeam.php?sportID='.$oldTeams[$i]->teamSportID.'&teamID='.$oldTeams[$i]->teamID.'">'.$oldTeams[$i]->teamName.'</a>' ?>
                            </div><div class="Column" align="center">
                                <?php print $oldTeams[$i]->teamLeagueName ?>
                            </div><div class="Column" align="center">
                                <?php print $oldTeams[$i]->teamSeasonName ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
	<?php }
}

function printLeagueAndTeam($teamObj, $leaguesDropDown) { 
	global $seasonData; ?>
	<div class="Table" align="center" style="width:90%; max-width:750px; min-width:250px;">
    	<div class="Row" align="center" style="width:30%;">
            <font face='verdana' color="red"><b>Registration due date<br />
			<?php foreach($seasonData as $season) {
				print $season['name'].' - '.$season['regDue'].'<br />';
            } ?></b></font>
        </div>
        <div class="colourRow" align="center" style="width:30%;">
            <b>1. Select Your Division and Choose Team Name</b>
            <br />
            <font face='verdana' size="1">Please choose a division.</font>
        </div>
        <div class="Row" align="center" style="width:30%;">
            <div class="noBorderCell" style="width:30%;">
                <div class="Row" align="center" style="width:30%;">
                    <div class="Column" style="width:30%;">
                        Preferred League
                    </div>
                    <div class="Column" style="width:30%;" align="left">
                        <select id='changeLeague' class='userInput' name='leagueID' style="width:100%;">
                            <?php print $leaguesDropDown;?>
                        </select>
                    </div>
                </div>
                <div class="Row" style="width:30%;">
                    <div class="Column" style="width:30%;">
                        Team Name
                    </div>
                    <div class="Column" style="width:30%;" align="left">
                        <input class='userInput' type='text' style="width:100%;" name='teamName' value="<?php print htmlentities($teamObj->teamName, ENT_QUOTES);?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }

function printCaptainForm($playerObj) { ?>
	<div class="Table" style="width:90%; max-width:750px; min-width:250px;">
		<div class="colourRow" align="center" style="width:30%;">
            <b>2. Captain Information</b>
            <br />
            <font face='verdana' size="1">
                The captain is the first person we'll contact with team inquiries and is responsible for submitting scores.
            </font>
      </div>
   		<div class="Row" align="center" style="width:30%;">
        	<div class="noBorderCell" style="width:30%;">
                <div class="Row" style="width:30%;">
                	<div class="Column" style="width:30%;">
                    	<div class="Row" style="width:30%;">
                    		<b>First Name:</b>
                        </div>
                        <div class="Row" style="width:30%;">
                        	<input type="text" name="capFirst" style="width:100%;" value="<?php print htmlentities($playerObj[0]->playerFirstName, ENT_QUOTES);?>" size="40" />
                        </div>
                    </div>
                    <div class="Column" style="width:30%;">
                    	<div class="Row" style="width:30%;">
                    		<b>Last Name:</b>
                        </div>
                        <div class="Row" style="width:30%;">
                        	<input type="text" name="capLast" style="width:100%;" value="<?php print htmlentities($playerObj[0]->playerLastName, ENT_QUOTES);?>" size="40" />
                        </div>
                    </div>
                </div>
                <div class="Row" style="width:30%;">
                	<div class="Column" style="width:30%;">
                    	<div class="Row" style="width:30%;">
                        	<b>Email:</b>
                        </div>
                        <div class="Row" style="width:30%;">
                        	<input type="text" name="capEmail" style="width:100%;" value="<?php print htmlentities($playerObj[0]->playerEmail, ENT_QUOTES);?>" size="40" />
                        </div>
                    </div>
                	<div class="Column" style="width:30%;">
                    	<div class="Row" style="width:30%;">
                        	<b>Gender:</b>
                        </div>
                        <div class="Row" style="width:30%;">
                            <select name="capSex" style="width:100%;">
                                <option value=''>Select One</option>
                                <option value='M' <?php print $playerObj[0]->playerGender == 'M'?'selected':'';?>>Male</option>
                                <option value='F' <?php print $playerObj[0]->playerGender == 'F'?'selected':'';?>>Female</option>
                            </select>  
                      </div> 
                    </div>
                    <div class="Column" style="width:30%;">
                    	<div class="Row" style="width:30%;">
                        	<b>Phone #:</b>
                        </div>
                        <div class="Row" style="width:30%;">
                        	<input type="text" name="capPhone" style="width:100%;" value="<?php print htmlentities($playerObj[0]->playerPhone, ENT_QUOTES);?>" size="15" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }

function printPlayerForm($playerObj, $people) { ?>
	<div class="Table" align="center" style="width:90%; max-width:750px; min-width:250px;">
        <div class="colourRow" align="center" style="width:30%;">
            <font face='verdana' size="2"><b>3. Player Information</b></font>
            <br /><font face='verdana' size="1"><font face='verdana' color='red'>*</font>
                The second player will be listed as an alternate contact if the captain is unavailable.</font>
      </div>
            <div class="Row" align="center" style="width:30%;">
                <div class="noBorderCell" align="center" style="width:30%;">
            <?php for($v=1; $v<=$people; $v++){?>
                <div class="Row" align="center" style="width:30%;">
                    <div class="Column" style="width:5%;max-width:100px;">
                        <?php if ($v==1) { ?>
                        <br />
                        <?php } 
                        print $v.'.'.$v<3?'<font face=verdana COLOR="red" size=1>*</FONT>'.$v:$v ?>
                    </div>
                    <div class="Column" style="width:30%;">
                        <?php if ($v==1) { ?>
                            <font face='verdana' size='2'><b>First Name</b></font>
                        <?php } ?>
                        <input type="text" name="playerFirst[]" style="width:100%;min-width:55px;" value="<?php print 
                            htmlentities($playerObj[$v]->playerFirstName, ENT_QUOTES);?>" size="27" />
                  </div>
                    <div class="Column" style="width:30%;">
                        <?php if ($v==1) { ?>
                            <font face='verdana' size='2'><b>Last Name</b></font>
                        <?php } ?>
                        <input type="text" name="playerLast[]" style="width:100%;min-width:55px;" value="<?php print 
                            htmlentities($playerObj[$v]->playerLastName, ENT_QUOTES);?>" size="30" />
                  </div>
                    <div class="Column" style="width:30%;">
                        <?php if ($v==1) { ?>
                            <font face='verdana' size='2'><b>Email</b></font>
                        <?php } ?>
                        <input type="text" name="playerEmail[]" style="width:100%;min-width:55px;" value="<?php 
                            print htmlentities($playerObj[$v]->playerEmail, ENT_QUOTES);?>" size="30" />
                  </div>
                    <div class="Column" style="width:30%;">
                        <?php if ($v==1) { ?>
                            <font face='verdana' size='2'><b>Gender</b></font>
                        <?php } ?>
                        <select name="playerSex[]" style="width:100%;">
                        <option value='M' <?php print $playerObj[$v]->playerGender == 'M'?'selected':'';?>>Male</option>
                        <option value='F' <?php print $playerObj[$v]->playerGender == 'F'?'selected':'';?>>Female</option>
                        <option value='F' <?php print $playerObj[$v]->playerGender == 'O'?'selected':'';?>>Other</option>
                    </select>
                  </div>
                    <div class="Column" align="center" style="width:5%;">
                        <?php if ($v==1) { ?>
                            <font face='verdana' size='2'><b>Opt-In</b></font>
                        <?php } ?>
                        <input type='checkbox' name='checkBox[]' value='<?php print $v;?>' checked="checked" />
                  </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="Row" align="center">
            <font size="1">* By checking this, you agree to be added to our emailing list</font>
        </div>
    </div>
<?php }

function printFormCommentsAndButtons($teamObj, $playerObj, $update, $payInfoDropDown, $aboutUsDropDown) { 
	global $seasonData; ?>
    <div class="Table" align="center" style="width:90%; max-width:750px; min-width:250px;">
    	<div class="colourRow" align="center" style="width:30%;">
            <b>4. Comments</b><br />
            <font face='verdana' size="1">Comments, notes, player needs, etc. (limit 1000 characters)</font>
      </div>
        <div class="Row" align="center" style="width:30%;">
                <textarea name='teamComments' style="width:95%;" rows="6"><?php print $teamObj->teamComments ?></textarea>
      </div>
        <div class="Row" align="center" style="width:30%;">
                <font color='#FF0000'>*</font>How did you hear about us? 
                <select name='aboutUsMethod' style="width:30%; min-width:120px;" onchange="showTextbox(this)">";
                    <?php print $aboutUsDropDown ?>
                </select>
        </div>
        <div class="Row" id='hearTextRow' align="center" style="display:none;width:30%;">
        	Other: <input type="text" style="width:100%;" name="aboutUsTextBox" value="<?php print htmlentities($teamObj->aboutUsText, ENT_QUOTES) ?>" />
        </div>
    <?php if($update == 0) { ?>
        <div class="colourRow" align="center" style="width:30%;">
                <b>5. Confirm Fees</b><br />
                The registration process is not finalized until fees have been paid
      </div>
        <div class="Row" align="center" style="width:30%;">
                <font color='#FF0000'>*</font>Payment Method:
                <select name='payMethod' style="width:30%;min-width:100px;">
                    <?php print $payInfoDropDown ?>
                </select>
        </div>
        <div class="Row" align="center" style="width:30%;">
                <font face="Verdana" size="2">
                <b>Make Cheques Payable to Perpetual Motion<br /><br />
Send This Confirmation Form &amp; Fees to:</b>
                <br />78 Kathleen St. Guelph, Ontario; N1H 4Y3
            </font>
        </div>
    <?php } else if($update == 1) { ?>
		<div class="colourRow" align="center" style="width:30%;">
                    <b>Confirm Fees</b><br />
                    <font face='verdana' size="1" color='red'>**The registration process is not finalized until fees have been paid**</font>
      </div>
        <div class="Row" align="center" style="width:30%;">
            <div class="noBorderCell" style="width:30%;">
                <div class="Row" style="width:30%;">
                    <div class="Column" style="width:30%;">
                        Would you like this team to be registered for the current season?
                    </div>
                    <div class="Column" style="width:30%;">
                        <select name='isRegistered' style="width:30%;min-width:100px;" onchange="updtShowPayMethod(this)">
                        <?php for ($x = 1; $x>=0;$x--) { 
                            if ($x == $teamObj->teamIsRegistered) { ?>
                                <option selected="selected" value="<?php print $x?>"><?php print $x>0?'Yes':'No' ?></option>	
                            <?php } else { ?>
                                <option value="<?php print $x ?>"><?php print $x>0?'Yes':'No' ?></option>	
                            <?php }
                        } ?>
                        </select>
                  </div>
                </div>
            </div>
        </div>
        <div class="Row" align="center" style="width:30%;" id='updtPayMethod' <?php print $teamObj->teamIsRegistered == 1?'style="display:table-row"':'style="display:none"'?>>
        	<div class="noBorderCell" style="width:30%;">
            	<div class="Row" style="width:30%;">
                	<div class="Column" style="width:30%;">
                        Payment Method:
                    </div>
                    <div class="Column" style="width:30%;">
                        <select name='payMethod' style="width:30%;min-width:100px;">";
                            <?php print $payInfoDropDown ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="Row" align="center" style="width:30%;">
                <b>Make Cheques Payable to Perpetual Motion<br /><br />
Send This Confirmation Form &amp; Fees to:</b>
                <br />
                Perpetual Motion
                <br />
                78 Kathleen St.
                <br />
                Guelph, Ontario
                <br />
                N1H 4Y3
                <br />
                (519) 222-0095
      </div>
    <?php } ?>
    <div class="colourRow" id="regOrSave" align="center" style="width:30%;">
        	<b>6. Register Your Team or Save Details</b><br />
			<font size="1">Submit your team registration to the convenor or save details to your profile for another time.</font>
    </div>
    <div class="Row" align="center" style="width:30%;">
        	<input type='submit' <?php print $update==1?'':'style="display:none"' ?> name='update' onclick="return checkUpdate()" value='Update Team' />
        	<input type='submit' <?php print $update==0?'':'style="display:none"' ?> name='register' onclick="return showPayment()" value='Register' />
			<input type='submit' <?php print $update==0?'':'style="display:none"' ?> name='save' onclick="return errorCheck()" value='Save Details' />
			<input type='button' name='printit' value='Print Form' onclick='javascript:window.print();' />
	  </div>
   <div class="Row" align="center" style="width:30%;">
        	<br /><font face='verdana' color="red" size="2"><b>Registration due date: 
			<?php foreach($seasonData as $season) {
				print '<br />'.$season['name'].' - '.$season['regDue'].'<br />';
            } ?>
            </b></font>
      </div>
</div>
        
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