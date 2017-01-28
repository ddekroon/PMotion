<?php 
function printHiddenValues($tourneyObj) {
	foreach($tourneyObj->tourneyNumBlackCards as $numBlack) {
		print "<input type='hidden' name='numBlack[]' value=$numBlack />";
	}
	foreach($tourneyObj->tourneyNumRedCards as $numRed) {
		print "<input type='hidden' name='numRed[]' value=$numRed />";
	}
	print "<input type='hidden' name='tourneyID' value='".$tourneyObj->tourneyID."' />";
}

function printFormHeader($tourneyObj) { ?>
	<div class="Table" style="width:90%; max-width:550px; min-width:250px;">
        <div class="Row" align="center">
        	<img src=<?php print $tourneyObj->logoLink ?>>
        </div>
        <div class="Row" align="center">
        	<font face='verdana' size=4>
            <B><?php print 'Register for Perpetual Motion\'s '.$tourneyObj->tourneyName.' Tournament' ?></B></font>
        </div>
        <div class="Row" align="center">
            <?php print 'Tournament Date: '.$tourneyObj->getFormattedDatePlayed(); ?>
        </div>
    </div>
<?php }

function printDivisionDD($divisionDropDown, $tourneyObj) { ?>
    <div class="Table" style="width:90%; max-width:550px; min-width:250px;">
    	<div class="colourRow" align="center" style="width:35%;">
            <b>Select Your Division</b>
        </div>
		<div class="Row" align="center" style="width:30%;">
        	<div class="noBorderCell" style="width:30%;">
            	<div class="Row style="width:30%;"">
                	<div class="Column" style="width:30%; min-width:100px;">
           				<font color='#FF0000'>*</font><B>Preferred Division:</B>
                    </div>
                    <div class="Column" style="width:30%;">
                        <select name='leagueID' style="width:100%; min-width:100px;" <?php print $tourneyObj->tourneyIsCards == 1?'onchange="reloadPageCard()"':'' ?>>
                            <?php print $divisionDropDown;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }

function printTeamForm($teamObj) { ?>
    <div class="Table" style="width:90%; max-width:550px; min-width:250px;">
    	<div class="colourRow" align="center" style="width:30%;">
            <b>Team Information</b>
        </div>
    	<div class="Row" align="center" style="width:30%;">
        	<div class="noBorderCell" style="width:30%;">
            	<div class="Row" style="width:30%;">
                	<div class="Column" style="width:30%;">
            			<font color='#FF0000'>*</font><b>Team Name: </b>
                    </div>
        			<div class="Column" style="width:30%;">
            			<input type='text' name='teamName' VALUE="<?php print htmlentities($teamObj->teamName, ENT_QUOTES);?>" style="width:30%; min-width:100px;">
                    </div>
                </div>
        		<div class="Row" style="width:30%;">
                	<div class="Column" style="width:30%;">
            			<b>Team Rating: </b>
        			</div>
                    <div class="Column" style="width:30%;">
                        <select name='teamRating' style="width:30%; min-width:100px;">
                            <option value=0>Choose</option>
							<?php for($i=10;$i>0;$i--) {
                                if($i==10) {
                                    $optionText = '10 (highest)';
                                } elseif($i == 1) {
                                    $optionText = '1 (lowest)';
                                } else {
                                    $optionText = $i;
                                }
                                $i == $teamObj->teamRating?$selectFilter = 'selected': $selectFilter = '';
                                print "<option $selectFilter value=$i>$optionText</option>";
                            } ?>
                      	</select>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }

function printCaptainForm($playerObj, $isCards, $cardsDropDown) { ?>
	<div class="Table" style="width:90%; max-width:550px; min-width:250px;">
    	<div class="colourRow" align="center" style="width:30%;">
            <B>Captain Information</B>
        </div>
    	<div class="Row" align="center" style="width:30%;">
        	<div class="noBorderCell" style="width:30%;">
            	<div class="Row" style="width:30%;">
                	<div class="Column" align="left" style="width:30%;">
            			<font color='#FF0000'>*</font><B>First Name:</B>
                    </div>
       				<div class="Column" style="width:30%;">
            			<INPUT TYPE="text" NAME="capFirst" VALUE="<?php print htmlentities($playerObj->playerFirstName, ENT_QUOTES);?>" style="width:100%">
                    </div>
                </div>
        		<div class="Row" style="width:30%;">
                	<div class="Column" align="left" style="width:30%;">
            			<font color='#FF0000'>*</font><B>Last Name:</B>
                    </div>
                    <div class="Column" style="width:30%;">
           				<INPUT TYPE="text" NAME="capLast" VALUE="<?php print htmlentities($playerObj->playerLastName, ENT_QUOTES);?>" style="width:100%">
                    </div>
                </div>
       			<div class="Row" style="width:30%;">
                	<div class="Column" align="left" style="width:30%;">
            			<font color='#FF0000'>*</font><B>Gender:</B>
       				</div>
                    <div class="Column" style="width:30%;">
                        <SELECT NAME="capSex" style="width:100%;" onchange="showGenderCards(this)">
                            <OPTION VALUE=''>Select One</OPTION>
                            <OPTION VALUE='M' <?php print $playerObj->playerGender == 'M'?'selected':'';?>>Male</OPTION>
                            <OPTION VALUE='F' <?php print $playerObj->playerGender == 'F'?'selected':'';?>>Female</OPTION>
                        </SELECT>
                    </div>
                </div>
        		<div class="Row" style="width:30%;">
                	<div class="Column" align="left" style="width:30%;">
            			<font color='#FF0000'>*</font><B>Email:</B>
                    </div>
                    <div class="Column" style="width:30%;">
            			<INPUT TYPE="text" NAME="capEmail" VALUE="<?php print htmlentities($playerObj->playerEmail, ENT_QUOTES);?>" style="width:100%">
					</div>
                </div>
                <div class="Row" style="width:30%;">
                	<div class="Column" align="left" style="width:30%;">
	            		<font color='#FF0000'>*</font><B>Phone Number:</B>
                    </div>
                    <div class="Column" style="width:30%;">
            			<INPUT TYPE="text" NAME="capPhone" VALUE="<?php print htmlentities($playerObj->playerPhone, ENT_QUOTES);?>" style="width:100%">
        			</div>
                </div>
                <div class="Row" style="width:30%;">
                    <div class="Column" align="left" style="width:30%;">
                        <B>Street Address:</B>
                    </div>
                    <div class="Column" style="width:30%;">
                        <INPUT TYPE="text" NAME="capAddress" VALUE="<?php print htmlentities($playerObj->playerAddress, ENT_QUOTES);?>" style="width:100%">
                    </div>
                </div>
                <div class="Row" style="width:30%;">
                    <div class="Column" align="left" style="width:30%;">
                        <B>City:</B>
                    </div>
                    <div class="Column" style="width:30%;">
                        <INPUT TYPE="text" NAME="capCity" VALUE="<?php print htmlentities($playerObj->playerCity, ENT_QUOTES);?>" style="width:100%">
                    </div>
                </div>
                <div class="Row" style="width:30%;">
                    <div class="Column" align="left" style="width:30%;">
                        <B>Province/State:</B>
                    </div>
                    <div class="Column" style="width:30%;">
                        <INPUT TYPE="text" NAME="capProvince" VALUE="<?php print htmlentities($playerObj->playerProvince, ENT_QUOTES);?>" style="width:100%">
                    </div>
                </div>
                <div class="Row" style="width:30%;">
                    <div class="Column" align="left" style="width:30%;">
                        <B>Postal Code:</B>
                    </div>
                    <div class="Column" style="width:30%;">
                        <INPUT TYPE="text" NAME="capPostalCode" VALUE="<?php print htmlentities($playerObj->playerPostalCode, ENT_QUOTES);?>" style="width:100%">
                    </div>
                </div>
        <?php if($isCards == 1) { ?>
                <div class="Row" style="width:30%;">
                    <div class="Column" align="left" style="width:30%;">
                        <font color='#FF0000'>*</font><B>Card:</B>
                    </div>
                    <div class="Column" style="width:30%;">
                        <SELECT NAME="cardsDropDown" style="width:100%">
                            <OPTION VALUE=0>Select A Card</OPTION>
                            <?php print $cardsDropDown ?>
                        </SELECT>
                    </div>
                </div>
        <?php } ?>
                <div class="Row" style="width:30%;">
                    <div class="Column" align="left" style="width:30%;">
                        <B>Add Email*:</B>
                    </div>
                    <div class="Column" style="width:30%;">
                        <INPUT TYPE='checkbox' NAME='checkBox[]' VALUE='<?php print 1;?>' />
                    </div>
                </div>
            </div>
    	</div>
        <div class="Row" align="center">
            <font size=1>* By checking this, you agree to be added to our emailing list</font>
        </div>
    </div>
    
<?php }

function printExtraField($tourneyObj) { ?>
	<div class="Table" style="width:90%; max-width:550px; min-width:250px;">
    	<div class="colourRow" align="center" style="width:30%;">
            <B>Extra Field</B>
       	</div>
    	<div class="Row" style="width:30%;">
        	<div class="noBorderCell" style="width:30%;">
            	<div class="Row" style="width:30%;">
                	<div class="Column" style="width:30%;">
        				<font color='#FF0000'>*</font><b><?php print $tourneyObj->tourneyExtraFieldName ?>:</b>
        			</div>
                    <div class="Column" style="width:30%;">
        				<INPUT TYPE="text" NAME="extraData" VALUE="<?php print htmlentities($playerObj->playerExtraData, ENT_QUOTES);?>" style="width:100%">
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }

function printPlayerFields($tourneyRegNumPlayers, $playerObj) { ?>
	<div class="Table" style="width:90%; max-width:550px; min-width:250px;">
    	<div class="colourRow" align="center" style="width:30%;">
            <B>Extra Player Information</B>
        </div>
        
        <div class="Row" style="width:30%;">
        	<div class="noBorderCell" style="width:30%;">
            	<?php for($i=0, $j=2;$i<$tourneyRegNumPlayers;$i++, $j++) { ?>
                    <div class="Row" style="width:30%;">
                        <div class="Column" style="width:20%;">
                            <br />
                            <?php print $j == 2?'<font color="#FF0000">*</font>':'' ?><b><?php print 'Player '.$j ?>:</b>
                        </div>
                        <div class="Column" style="width:30%;">
                        	<?php if ($i==0) { ?>
                            <div class="Row" align="center" style="width:30%;">
                                First Name
                            </div>
							<?php } ?>
                            <div class="Row" style="width:30%;">
                                <INPUT TYPE="text" NAME="playerFirstName[]" VALUE="<?php print htmlentities($playerObj[$i]->playerFirstName, ENT_QUOTES);?>" style="width:100%">
                            </div>
                        </div>
                        <div class="Column" style="width:30%;">
                        	<?php if ($i==0) { ?>
                            <div class="Row" align="center" style="width:30%;">
                                Last Name
                            </div>
                            <?php } ?>
                            <div class="Row" style="width:30%;">
                                <INPUT TYPE="text" NAME="playerLastName[]" VALUE="<?php print htmlentities($playerObj[$i]->playerLastName, ENT_QUOTES);?>" style="width:100%">
                            </div>
                        </div>
                        <div class="Column" style="width:30%;">
                        	<?php if ($i==0) { ?>
                            <div class="Row" align="center" style="width:100%;">
                                Skill
                            </div>
                            <?php } ?>
                            <div class="Row" style="width:30%;">
                                <select name='playerRating[]' style="width:100%;min-width:55px;">
                                    <option value=0>Choose</option>
                                    <?php for($k=10;$k>0;$k--) {
                                        if($k==10) {
                                            $optionText = '10 (highest)';
                                        } elseif($k == 1) {
                                            $optionText = '1 (lowest)';
                                        } else {
                                            $optionText = $k;
                                        }
                                        $k == $playerObj[$i]->playerRating?$selectFilter = 'selected': $selectFilter = '';
                                        print "<option $selectFilter value=$k>$optionText</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>    
	</div>
<?php }
	
function printFormCommentsAndButtons($teamObj, $playerObj, $payInfoDropDown, $tourneyObj, $aboutUsDropDown) { ?>
    <div class="Table" style="width:90%; max-width:550px; min-width:250px;">
    	<div class="colourRow" align="center" style="width:30%;">
            <B>Comments</B><BR>
            <font face='verdana' SIZE=1>Comments, player needs, etc. (limit 1000 characters)</font>
        </div>
    	<div class="Row" align="center" style="width:30%;">
            <TEXTAREA NAME='teamComments' style=" height:50px;width:100%;"><?php print $teamObj->teamComments ?></TEXTAREA>
        </div>
        <div class="Row" align="center" style="width:30%;">
        	<div class="noBorderCell" align="center" style="width:30%;">
            	<div class="Row" align="center" style="width:30%;">
                	<div class="Column" style="width:30%;">
                    	<font color='#FF0000'>*</font>How did you hear about us? 
       				</div>
                    <div class="Column" style="width:30%;">
                        <select name='aboutUsMethod' style="width:100%" onchange="showTextbox(this)">";
                            <?php print $aboutUsDropDown ?>
                        </select>
                    </div>
                </div>
                <div class="Row" id='hearTextRow'  style="display:none;width:30%;">
                    <div class="Column" style="width:30%;">
                    	Other: 
                    </div>
                    <div class="Column" style="width:30%;">
                    	<input type="text" style="width:100%" name="aboutUsTextBox" value="<?php print htmlentities($teamObj->aboutUsText, ENT_QUOTES) ?>" />
                    </div>
                </div>
            </div>
       	</div>
        
        
    <div class="colourRow" align="center" style="width:30%;">
            <font face='verdana' size=2><B>Confirm Fees</B></font><BR>
            <font face='verdana' SIZE=1 COLOR='red'>**The registration process is not finalized until fees have been paid**</font>
        </div>
    <div class="Row" align="center" style="width:30%;">
    	<div class="noBorderCell" style="width:30%;">
        	<div class="Row" style="width:30%;">
            	<div class="Column" style="width:30%;">
            		<font color='#FF0000'>*</font><font face='verdana' size=2>Payment Method: </font>
                </div>
                <div class="Column" style="width:30%;">
                    <select name='payMethod' style="width:100%">";
                        <?php print $payInfoDropDown ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="Row" align="center" style="width:30%;">
            <font face='verdana' size=2><B>Make Cheques Payable to Perpetual Motion<BR><BR>
            Send This Confirmation Form & Fees to:</b>
            <br>Perpetual Motion - 78 Kathleen St. Guelph, Ontario
            <br />N1H 4Y3 (519) 222-0095</font>
    </div>
    <div class="colourRow" id="regOrSave" align="center" style="width:30%;">
        	<B>Register Your Team</B>
        </div>
        <div class="Row" align="center" style="width:30%;">
        	<?php if($tourneyObj->tourneyIsTeams == 1) {
				$functionName = 'errorCheckTeam()';
			} else if ($tourneyObj->tourneyIsCards == 1) {
				$functionName = 'errorCheckCard()';
			} else if ($tourneyObj->tourneyIsPlayers == 1) {
				$functionName = 'errorCheckPlayer()';
			} ?>
        	<INPUT TYPE='Submit' NAME='register' onclick="return <?php print $functionName ?>" Value='Register'>
			<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>
		</div>
    <div class="Row" align="center" style="width:30%;">
        	<br /><font face='verdana' color=red size=2><b>Registration due date: <?php print $tourneyObj->getFormattedDateClosed() ?></b></font>
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