<?php function printIDs($tourneysDropDown) { ?>
	<tr>
        <td>
            Tournament:
            <select name='tourneyID' onchange='reloadCreatePage()'>
            	<option value=0>Choose a Tournament</option>
                <?php print $tourneysDropDown ?>
            </select>
        </td>
    </tr>
<?php }

function printTeamsAndLeagues($tourneyObj) { ?>
	<input type='hidden' name='tourneyAvailableID' value=<?php print $tourneyObj->ID?>/>
    <input type='hidden' name='tourneyName' value=<?php print $tourneyObj->tourneyName?>/>
    <tr>
        <td>Is Leagues:   
            <SELECT NAME='tourneyIsLeagues' onchange="changeNumLeagues(this)">
            <?php for ($z=1; $z >= 0; $z--){ ?>
                <OPTION <?php print $z == $tourneyObj->tourneyIsLeagues?'selected':''?> VALUE=<?php print $z?>>
					<?php print $z > 0 ? 'Yes':'No'?>
                </OPTION>
            <?php } ?>
            </SELECT>
        </td><td>Num Leagues:   
            <SELECT <?php print $tourneyObj->tourneyIsLeagues ==1?'':'disabled'?> NAME='tourneyNumLeagues' onchange="showLeagueNames(this)">
            <?php for ($z = 8; $z > 0; $z--){ ?>
                <OPTION <?php print $z == $tourneyObj->tourneyNumLeagues?'selected':''?> VALUE=<?php print $z?>>
					<?php print $z?>
                </OPTION>
            <?php } ?>
            </SELECT>
        </td><td colspan = 2>Is Teams:   
            <SELECT NAME='tourneyIsTeams' onchange="changeNumTeams(this)">
            <?php for ($z=1; $z >= 0; $z--){ ?>
                <OPTION <?php print $z == $tourneyObj->tourneyIsTeams?'selected':''?> VALUE=<?php print $z?>>
					<?php print $z > 0 ? 'Yes':'No'?>
                </OPTION>
            <?php } ?>
            </SELECT>
        </td><td colspan = 2>Is Players:   
            <SELECT NAME='tourneyIsPlayers' onchange="changeNumPlayers(this)">
            <?php for ($z=1; $z >= 0; $z--){ ?>
                <OPTION <?php print $z == $tourneyObj->tourneyIsPlayers?'selected':''?> VALUE=<?php print $z?>>
					<?php print $z > 0 ? 'Yes':'No'?>
                </OPTION>
            <?php } ?>
            </SELECT>
        </td><td colspan = 2>Is Cards:   
            <SELECT NAME='tourneyIsCards' onchange="changeNumCards(this)">
            <?php for ($z=1; $z >= 0; $z--){ ?>
                <OPTION <?php print $z == $tourneyObj->tourneyIsCards?'selected':''?> VALUE=<?php print $z?>>
					<?php print $z > 0 ? 'Yes':'No'?>
                </OPTION>
            <?php } ?>
            </SELECT>
        </td><td>
        	Is Full
        </td><td>
        	Price(s)  
        </td>
    </tr><tr>
    	<td colspan = 2>
        	<label style="font-size:16px;" id='leaguesTitle'><?php print $tourneyObj->tourneyIsLeagues?'League Names':''?></label>
        </td><td colspan = 2>
        	<label style="font-size:16px;" id='teamsTitle'>
            	<?php print $tourneyObj->tourneyIsTeams?$tourneyObj->tourneyIsLeagues == 1?'Num teams Per League:':'Num Teams:':''?>
            </label>
        </td><td colspan = 2>
       		<label style="font-size:16px;" id='playersTitle'>
            	<?php print $tourneyObj->tourneyIsPlayers?$tourneyObj->tourneyIsLeagues == 1?'Num players Per League:':'Num Players:':''?>
            </label>
        </td><td colspan=2>
       		<label style="font-size:16px;" id='cardsTitle'>
            	<?php print $tourneyObj->tourneyIsCards?$tourneyObj->tourneyIsLeagues == 1?'Num Cards Per League (B-R)':'Num Cards (B-R)':''?>
            </label>
        </td><td>
       		<label style="font-size:16px;" id='cardsTitle'>
            	<?php print $tourneyObj->tourneyIsCards?$tourneyObj->tourneyIsLeagues == 1?'(M-F)':'(M-F)':''?>
            </label>
        </td><td>
        	<br />
        </td>
    </tr>
	<?php for($i=0;$i<8;$i++) { ?>
    <tr>
        <td colspan=2>
        <input type="text" <?php print $tourneyObj->tourneyIsLeagues ==  1?$i>=$tourneyObj->tourneyNumLeagues?'style="display:none;"':'':'style="display:none;"'?>
            name="tourneyLeagueName[]" value="<?php print $tourneyObj->tourneyLeagueNames[$i] ?>" />
        </td><td colspan=2>
        <SELECT <?php print $tourneyObj->tourneyIsTeams ==1? $i >= $tourneyObj->tourneyNumLeagues?'style="display:none;"':'':'style="display:none;"'?> NAME='tourneyNumTeams[]'>
            <option value=0>N/A</option>
        <?php for ($z = 25; $z > 0; $z--){ ?>
            <OPTION <?php print $z == $tourneyObj->tourneyNumTeams[$i]?'selected':''?> VALUE=<?php print $z?>>
				<?php print $z?>
            </OPTION>
        <?php } ?>
        </SELECT>
        </td><td colspan=2>
        <SELECT <?php print $tourneyObj->tourneyIsPlayers ==1? $i >= $tourneyObj->tourneyNumLeagues?'style="display:none;"':'':'style="display:none;"'?> NAME='tourneyNumPlayers[]'>
            <option value=0>N/A</option>
        <?php for ($z = 50; $z > 0; $z--){ ?>
            <OPTION <?php print $z == $tourneyObj->tourneyNumPlayers[$i]?'selected':''?> VALUE=<?php print $z?>>
				<?php print $z?>
            </OPTION>
        <?php } ?>
        </SELECT>
        </td><td>
        <SELECT <?php print $tourneyObj->tourneyIsCards ==1? $i >= $tourneyObj->tourneyNumLeagues?'style="display:none;"':'':'style="display:none;"'?> NAME='tourneyNumBlackCards[]'>
            <option value=0>N/A</option>
        <?php for ($z = 20; $z > 0; $z--){ ?>
            <OPTION <?php print $z == $tourneyObj->tourneyNumBlackCards[$i]?'selected':''?> VALUE=<?php print $z?>>
				<?php print $z?>
            </OPTION>
        <?php } ?>
        </SELECT>
        </td><td>
        <SELECT <?php print $tourneyObj->tourneyIsCards ==1? $i >= $tourneyObj->tourneyNumLeagues?'style="display:none;"':'':'style="display:none;"'?> NAME='tourneyNumRedCards[]'>
            <option value=0>N/A</option>
        <?php for ($z = 20; $z > 0; $z--){ ?>
            <OPTION <?php print $z == $tourneyObj->tourneyNumRedCards[$i]?'selected':''?> VALUE=<?php print $z?>>
				<?php print $z?>
            </OPTION>
        <?php } ?>
        </SELECT>
        </td><td>
        	<?php if($tourneyObj->tourneyIsCards ==1 || $tourneyObj->tourneyIsCards ==1) {
				$playerFilter = 1;
			} else {
				$playerFilter = 0;
			} ?>
            <SELECT <?php print $playerFilter ==1?$i >= $tourneyObj->tourneyNumLeagues?'style="display:none;"':'':'style="display:none;"'?> NAME='tourneyIsFullMale[]'>
                <OPTION <?php print $tourneyObj->tourneyIsFullMale[$i] == 1?'selected':''?> VALUE=1>Yes</OPTION>
                <OPTION <?php print $tourneyObj->tourneyIsFullMale[$i] == 0?'selected':''?> VALUE=0>No</OPTION>
            </SELECT>
            <SELECT <?php print $playerFilter ==1?$i >= $tourneyObj->tourneyNumLeagues?'style="display:none;"':'':'style="display:none;"'?> NAME='tourneyIsFullFemale[]'>
                <OPTION <?php print $tourneyObj->tourneyIsFullFemale[$i] == 1?'selected':''?> VALUE=1>Yes</OPTION>
                <OPTION <?php print $tourneyObj->tourneyIsFullFemale[$i] == 0?'selected':''?> VALUE=0>No</OPTION>
            </SELECT>
			<SELECT <?php print $playerFilter ==0?$i >= $tourneyObj->tourneyNumLeagues?'style="display:none;"':'':'style="display:none;"'?> NAME='tourneyIsFull[]'>
                <OPTION <?php print $tourneyObj->tourneyIsFull[$i] == 1?'selected':''?> VALUE=1>Yes</OPTION>
                <OPTION <?php print $tourneyObj->tourneyIsFull[$i] == 0?'selected':''?> VALUE=0>No</OPTION>
            </SELECT>
        </td><td>
        	<input type="text" style="width:30px;<?php print $i >= $tourneyObj->tourneyNumLeagues?'display:none;':''?>" 
            	NAME='tourneyLeaguePrices[]' value="<?php print $tourneyObj->tourneyLeaguePrices[$i] ?>" />
        </td>
    </tr>
    <?php }	
}

//prints the matches sections of the score reporter
function printForm($tourneyObj) { ?>
	
    <tr>
        <td align=center>
            Overall Tournament Number:
            <input type="text" name="tourneyNumRunning" value="<?php print $tourneyObj->tourneyNumRunning?>">	
        </td>
        <td align=center>
            Is Extra Field:
            <SELECT NAME='tourneyIsExtraField' onchange="changeExtraField(this)">
            <?php for ($z=1; $z >= 0; $z--){ ?>
                <OPTION <?php print $z == $tourneyObj->tourneyIsExtraField?'selected':''?> VALUE=<?php print $z?>><?php print $z > 0 ? 'Yes':'No'?></OPTION>
            <?php } ?>
            </SELECT>
            Extra Field:
            <input type="text" <?php print $tourneyObj->tourneyIsExtraField == 1?'':'disabled'?> name="tourneyExtraFieldName" value="<?php print $tourneyObj->tourneyExtraFieldName?>">	
        </td>      
    </tr>
    <tr>
        <td>    
            Game Day:
            <SELECT NAME='datePlayedMonth'>
            <?php for ($x=1; $x <= 12; $x++){
                if ($x == date('n', strtotime($tourneyObj->tourneyDatePlayed))) {
                    ?><OPTION selected='selected' VALUE=<?php print $x ?>><?php print date( 'F', mktime(0, 0, 0, $x)) ?></OPTION><?php
                } else {
                    ?><OPTION VALUE=<?php print $x ?>><?php print date( 'F', mktime(0, 0, 0, $x)) ?></OPTION><?php
                }
            }?>
            </SELECT>
            <SELECT NAME='datePlayedDay'>
            <?php for ($x=1; $x <= 31; $x++){
                if ($x == date('j', strtotime($tourneyObj->tourneyDatePlayed))) {
                    ?><OPTION selected='selected' VALUE=<?php print $x ?>><?php print $x ?></OPTION><?php
                } else {
                    ?><OPTION VALUE=<?php print $x ?>><?php print $x ?></OPTION><?php
                }
            }?>
            </SELECT>
            <SELECT NAME='datePlayedYear'>
            <?php for ($x=date('Y')+1; $x >= date('Y'); $x--){
                if ($x == date('Y', strtotime($tourneyObj->tourneyDatePlayed))) {
                    ?><OPTION selected='selected' VALUE=<?php print $x ?>><?php print $x ?></OPTION><?php
                } else {
                    ?><OPTION VALUE=<?php print $x ?>><?php print $x ?></OPTION><?php
                }
            }?>
            </SELECT>
        </td> 
        <td>    
            Registration Close Date:
            <SELECT NAME='dateClosedMonth'>
            <?php for ($x=1; $x <= 12; $x++){
                if ($x == date('n', strtotime($tourneyObj->tourneyDateClosed))) {
                    ?><OPTION selected='selected' VALUE=<?php print $x ?>><?php print date( 'F', mktime(0, 0, 0, $x)) ?></OPTION><?php
                } else {
                    ?><OPTION VALUE=<?php print $x ?>><?php print date( 'F', mktime(0, 0, 0, $x)) ?></OPTION><?php
                }
            }?>
            </SELECT>
            <SELECT NAME='dateClosedDay'>
            <?php for ($x=1; $x <= 31; $x++){
                if ($x == date('j', strtotime($tourneyObj->tourneyDateClosed))) {
                    ?><OPTION selected='selected' VALUE=<?php print $x ?>><?php print $x ?></OPTION><?php
                } else {
                    ?><OPTION VALUE=<?php print $x ?>><?php print $x ?></OPTION><?php
                }
            }?>
            </SELECT>
            <SELECT NAME='dateClosedYear'>
            <?php for ($x=date('Y')+1; $x >= date('Y'); $x--){
                if ($x == date('Y', strtotime($tourneyObj->tourneyDateClosed))) {
                    ?><OPTION selected='selected' VALUE=<?php print $x ?>><?php print $x ?></OPTION><?php
                } else {
                    ?><OPTION VALUE=<?php print $x ?>><?php print $x ?></OPTION><?php
                }
            }?>
            </SELECT>
        </td>
    </tr>
    <tr>
        <td colspan="2">
        	Num of Game Days:
            <SELECT NAME='tourneyNumDays'>
            <?php for ($z=7; $z >= 0; $z--){ ?>
                <option <?php print $z == $tourneyObj->tourneyNumDays?'selected':''?> value=<?php print $z?>><?php print $z?></option>
            <?php } ?>
            </SELECT>
        </td>
    </tr>
<?php }

function printFooter() { ?>
	<tr>
        <td>
            <input TYPE='Submit' Value='Submit Tourney' name='SubmitTourney' />
        </td>
    </tr>
<?php }