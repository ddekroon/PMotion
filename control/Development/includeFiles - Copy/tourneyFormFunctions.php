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
	<tr>
        <td colspan=2>
            <TABLE align="center">
                <tr>
                    <td><img src=<?php print $tourneyObj->logoLink ?>></td>
                </tr>
            </TABLE>
        </td>
    </tr>
    <tr>
        <td colspan=2><font face='verdana' size=4>
            <B><?php print 'Register for Perpetual Motion\'s '.$tourneyObj->tourneyName.' Tournament' ?></B></font>
        </td>
    </tr>
    <tr>
    	<td colspan=2>
        	<?php print 'Tournament Date: '.$tourneyObj->getFormattedDatePlayed(); ?>
		</td>
	</tr>
<?php }

function printDivisionDD($divisionDropDown, $tourneyObj) { ?>
    <tr BGCOLOR="#CCCCCC">
        <td colspan=2 align="center">
            <b>Select Your Division</b>
        </td>
    </tr>
    <tr BGCOLOR="white">
        <td align=left>
            <font color='#FF0000'>*</font><B>Preferred Division:</B>
        </td><td align=right>
            <select name='leagueID' style="width:300px" <?php print $tourneyObj->tourneyIsCards == 1?'onchange="reloadPageCard()"':'' ?>>
                <?php print $divisionDropDown;?>
            </select>
        </td>
    </tr>
<?php }

function printTeamForm($teamObj) { ?>
    <tr BGCOLOR="#CCCCCC">
        <td colspan=2 align="center">
            <b>Team Information</b>
        </td>
    </tr>
    <tr>
        <td align=left>
            <font color='#FF0000'>*</font><b>Team Name: </b>
        </td><td align=right>
            <input type='text' name='teamName' VALUE="<?php print htmlentities($teamObj->teamName, ENT_QUOTES);?>" style="width:300px">
        </td>
    </tr>
    <tr>
        <td align=left>
            <b>Team Rating: </b>
        </td><td align=right>
           	<select name='teamRating' style="width:300px">
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
        </td>
    </tr>
<?php }

function printCaptainForm($playerObj, $isCards, $cardsDropDown) { ?>
	<tr BGCOLOR='#CCCCCC'>
        <td colspan=2 align='center'>
            <B>Captain Information</B>
        </td>
    </tr>
    <tr BGCOLOR='white'>
        <td align=left>
            <font color='#FF0000'>*</font><B>First Name:</B>
        </td><td align=right>
            <INPUT TYPE="text" NAME="capFirst" VALUE="<?php print htmlentities($playerObj->playerFirstName, ENT_QUOTES);?>" style="width:300px">
        </td>
    </tr>
    <tr>
    	<td align=left>
            <font color='#FF0000'>*</font><B>Last Name:</B>
        </td><td align=right>
            <INPUT TYPE="text" NAME="capLast" VALUE="<?php print htmlentities($playerObj->playerLastName, ENT_QUOTES);?>" style="width:300px">
        </td>
    </tr>
  	<tr>
    	<td align=left>
            <font color='#FF0000'>*</font><B>Gender:</B>
        </td><td align=right>
            <SELECT NAME="capSex" style="width:300px;" onchange="showGenderCards(this)">
                <OPTION VALUE=''>Select One</OPTION>
                <OPTION VALUE='M' <?php print $playerObj->playerGender == 'M'?'selected':'';?>>Male</OPTION>
                <OPTION VALUE='F' <?php print $playerObj->playerGender == 'F'?'selected':'';?>>Female</OPTION>
            </SELECT>
        </td>   
    </tr>
    <tr BGCOLOR="white">
        <td align=left>
            <font color='#FF0000'>*</font><B>Email:</B>
        </td><td align=right>
            <INPUT TYPE="text" NAME="capEmail" VALUE="<?php print htmlentities($playerObj->playerEmail, ENT_QUOTES);?>" style="width:300px">
        </td>
    </tr>
  	<tr>
    	<td align=left>
            <font color='#FF0000'>*</font><B>Phone Number:</B>
        </td><td align=right>
            <INPUT TYPE="text" NAME="capPhone" VALUE="<?php print htmlentities($playerObj->playerPhone, ENT_QUOTES);?>" style="width:300px">
        </td>
    </tr>
    <?php /*<tr>
    	<td align=left>
            <B>Captain Skill:</B>
        </td><td align=right>
			<select name='capRating' style="width:300px">
                <option value=0>Choose</option>
            <?php for($k=10;$k>0;$k--) {
                if($k==10) {
                    $optionText = '10 (highest)';
                } elseif($k == 1) {
                    $optionText = '1 (lowest)';
                } else {
                    $optionText = $k;
                }
                $k == $playerObj->playerRating?$selectFilter = 'selected': $selectFilter = '';
                print "<option $selectFilter value=$k>$optionText</option>";
            } ?>
            </select>        
        </td>
    </tr>*/ ?>
    <tr>
    	<td align=left>
            <B>Street Address:</B>
        </td><td align=right>
            <INPUT TYPE="text" NAME="capAddress" VALUE="<?php print htmlentities($playerObj->playerAddress, ENT_QUOTES);?>" style="width:300px">
        </td>
    </tr>
    <tr>
    	<td align=left>
            <B>City:</B>
        </td><td align=right>
            <INPUT TYPE="text" NAME="capCity" VALUE="<?php print htmlentities($playerObj->playerCity, ENT_QUOTES);?>" style="width:300px">
        </td>
    </tr>
    <tr>
    	<td align=left>
            <B>Province/State:</B>
        </td><td align=right>
            <INPUT TYPE="text" NAME="capProvince" VALUE="<?php print htmlentities($playerObj->playerProvince, ENT_QUOTES);?>" style="width:300px">
        </td>
    </tr>
    <tr>
    	<td align=left>
            <B>Postal Code:</B>
        </td><td align=right>
            <INPUT TYPE="text" NAME="capPostalCode" VALUE="<?php print htmlentities($playerObj->playerPostalCode, ENT_QUOTES);?>" style="width:300px">
        </td>
    </tr>
    <?php if($isCards == 1) { ?>
		<tr>
        	<td align=left>
            	<font color='#FF0000'>*</font><B>Card:</B>
            </td><td align=right>
            	<SELECT NAME="cardsDropDown" style="width:300px">
                	<OPTION VALUE=0>Select A Card</OPTION>
					<?php print $cardsDropDown ?>
                </SELECT>
            </td>
        </tr>
    <?php }
}

function printExtraField($tourneyObj) { ?>
	<tr BGCOLOR='#CCCCCC'>
        <td colspan=2 align='center'>
            <B>Extra Field</B>
        </td>
    </tr>
	<tr BGCOLOR="white">
        <td align=left>
        	<font color='#FF0000'>*</font><b><?php print $tourneyObj->tourneyExtraFieldName ?>:</b>
        </td><td align=right>
        	<INPUT TYPE="text" NAME="extraData" VALUE="<?php print htmlentities($playerObj->playerExtraData, ENT_QUOTES);?>" style="width:300px">
        </td>
    </tr>
	
<?php }

function printPlayerFields($tourneyRegNumPlayers, $playerObj) { ?>
	<tr BGCOLOR='#CCCCCC'>
        <td colspan=2 align='center'>
            <B>Extra Player Information</B>
        </td>
    </tr>
    <tr>
    	<td colspan=2 style="padding: 0px 0px 0px 0px">
        	<table class='master' style="padding: 0px 0px 0px 0px; width:100%">
            	<tr>
                	<td align=center>
                    	<br />
                    </td><td align=center>
                    	First Name
                    </td><td align=center>
                    	Last Name
                    </td><td align=center>
                    	Skill
                    </td>
                </tr>
				<?php for($i=0, $j=2;$i<$tourneyRegNumPlayers;$i++, $j++) { ?>
                    <tr>
                        <td align=left>
                            <?php print $j == 2?'<font color="#FF0000">*</font>':'' ?><b><?php print 'Player '.$j ?>:</b>
                        </td><td align=center>
                            <INPUT TYPE="text" NAME="playerFirstName[]" VALUE="<?php print htmlentities($playerObj[$i]->playerFirstName, ENT_QUOTES);?>" style="width:150px">
                        </td><td align=center>
                            <INPUT TYPE="text" NAME="playerLastName[]" VALUE="<?php print htmlentities($playerObj[$i]->playerLastName, ENT_QUOTES);?>" style="width:150px">
                        </td><td align=right>
                        	<select name='playerRating[]' style="width:150px">
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
            			</td>
                    </tr>
            <?php } ?>
            </table>
        </td>
    </tr>
<?php }
	
function printFormCommentsAndButtons($teamObj, $playerObj, $payInfoDropDown, $tourneyObj, $aboutUsDropDown) { ?>
    <TR BGCOLOR='#CCCCCC'>
        <TD colspan=2 align='center'>
            <B>Comments</B><BR>
            <font face='verdana' SIZE=1>Comments, player needs, etc. (limit 1000 characters)</font>
        </TD>
    </TR>
    <TR>
        <TD colspan=2 align=center>
            <TEXTAREA NAME='teamComments' style=" height:50px;width:500px"><?php print $teamObj->teamComments ?></TEXTAREA>
        </TD>
    </TR>
    <tr>
        <TD align=left>
            <font color='#FF0000'>*</font>How did you hear about us? 
       	</TD><td align=right>
        	<select name='aboutUsMethod' style="width:300px" onchange="showTextbox(this)">";
                <?php print $aboutUsDropDown ?>
            </select>
        </TD>
    </tr>
    <tr id='hearTextRow'  style="display:none">
    	<td align="left">
        	Other: 
        </td><td align=right>
        	<input type="text" style="width:300px" name="aboutUsTextBox" value="<?php print htmlentities($teamObj->aboutUsText, ENT_QUOTES) ?>" />
        </td>
    </tr>
    <TR BGCOLOR='#CCCCCC'>
        <TD COLSPAN=2 align='center'>
            <font face='verdana' size=2><B>Confirm Fees</B></font><BR>
            <font face='verdana' SIZE=1 COLOR='red'>**The registration process is not finalized until fees have been paid**</font>
        </TD>
    </TR>
    <tr>
        <TD align=left>
            <font color='#FF0000'>*</font><font face='verdana' size=2>Payment Method: </font>
        </TD><td align=right>
            <select name='payMethod' style="width:300px">";
                <?php print $payInfoDropDown ?>
            </select>
        </TD>
    </tr>
    <tr>
        <td colspan=2 align=center>
            <font face='verdana' size=2><B>Make Cheques Payable to Perpetual Motion<BR><BR>
            Send This Confirmation Form & Fees to:</b>
            <br>Perpetual Motion - 223 Waterloo Ave. Guelph, Ontario
            <br />N1H 3J4 (519) 222-0095</font>
        </td>
    </tr>
    <tr id="regOrSave" style="display:table-row; background-color:#CCCCCC;">
    	<td colspan=2 align='center'>
        	<B>Register Your Team</B>
        </td>
    </tr>
	<tr>
    	<td colspan=2 align=center>
        	<?php if($tourneyObj->tourneyIsTeams == 1) {
				$functionName = 'errorCheckTeam()';
			} else if ($tourneyObj->tourneyIsCards == 1) {
				$functionName = 'errorCheckCard()';
			} else if ($tourneyObj->tourneyIsPlayers == 1) {
				$functionName = 'errorCheckPlayer()';
			} ?>
        	<INPUT TYPE='Submit' NAME='register' onclick="return <?php print $functionName ?>" Value='Register'>
			<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>
		</td>
    </tr>
    <tr>
    	<td colspan=5 align=center>
        	<br /><font face='verdana' color=red size=2><b>Registration due date: <?php print $tourneyObj->getFormattedDateClosed() ?></b></font>
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