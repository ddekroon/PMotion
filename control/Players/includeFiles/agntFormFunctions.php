<?php //prints the top info of the score reporter, ie the game info and title
function printAgentTopInfo($sportsDropDown, $leaguesDropDown, $teamsDropDown, $isGroup) { 
	global $isIndividual, $numPlayers; ?>
	Sport
	<select id='userInput' name='sportID' onchange='reloadPageSport()'>
		<?php print $sportsDropDown; ?>
	</select><br /><br />
	League
	<select id='userInput' name='leagueID' onchange='reloadPage()'>
		<?php print $leaguesDropDown; ?>
	</select><br /><br />
	
	<?php if($isIndividual == 0) { ?>
	<font color="#FF0000">
			*  
		</font>
	<?php } ?>
	
	Team
	<select id='userInput' name='teamID' onchange='reloadPage()'>
		<option value=0>--Free Agency--</option>
		<?php print $teamsDropDown; ?>
	</select><br /><br />
    <?php if ($isGroup == 1) { ?>
		# People in Group
		<select id='userInput' name='groupSize' onchange='reloadPage()'>
			<?php for($i=2;$i<15; $i++) {
				if($i == $numPlayers) { ?>
					<option selected="selected" value=<?php print $i?>><?php print $i?></option>
				<?php } else { ?>
					<option value=<?php print $i?>><?php print $i?></option>
				<?php }
			} ?>
		</select>
    <?php } else { ?>
		<input id='userInput' type="hidden" name='groupSize' value=1 />
    <?php } 
}

function printAgentForm($numPlayers, $teamNum){
	global $playerFirstName, $playerLastName, $playerEmail, $playerGender, $isIndividual, $playerPhoneNum, $isFinalized; ?>
	
	<tr>
		<th colspan=2>
			Player Form
		</th>
	</tr>
	<?php if ($numPlayers == 1) { ?>
    <tr>
        <td>
            Individual: 
		</td><td>
        	<select id='userInput' name="isIndividual" onchange="reloadPage()">
            	<?php for($i=1; $i>=0; $i--) {
					if ($i == $isIndividual) {
						?><OPTION selected='selected' VALUE=<?php print $i ?>><?php print $i > 0 ? 'Yes' : 'No' ?></OPTION><?php
					} else {
						?><OPTION VALUE=<?php print $i ?>><?php print $i > 0 ? 'Yes' : 'No' ?></OPTION><?php
					}
				} ?>
            </select>
        </td>
    </tr>
    <?php } ?>
	<tr>
        <td>
            <font color="#FF0000">
                * 
            </font>
            First Name: 
		</td><td>
        	<input id='userInput' type="text" value='<?php print $playerFirstName[$teamNum]?>' name="firstName[]" />
        </td>
    </tr><tr>
        <td>
        	<font color="#FF0000">
                * 
            </font>
            Last Name: 
		</td><td>
        	<input id='userInput' type="text" value='<?php print $playerLastName[$teamNum]?>' name="lastName[]">
        </td>
    </tr>
    <tr>
        <td>
        	<font color="#FF0000">
                * 
            </font>
            Email: 
		</td><td>
        	<input id='userInput' type="text" value='<?php print $playerEmail[$teamNum]?>' name="email[]" />
        </td>
    </tr>
    <tr>
        <td>
            Gender: 
		</td><td>
         	<?php $letterArray = array('M', 'F', ); ?>
        	<select id='userInput' name="gender[]">
            <?php foreach($letterArray as $curLetter) {
				if ($playerGender[$teamNum] == $curLetter) { ?>
					<option selected="selected" value=<?php print $curLetter ?>><?php print $curLetter?></option>
				<?php } else { ?>
					<option value=<?php print $curLetter ?>><?php print $curLetter?></option>
				<?php } 
			} ?>
            </select>
        </td>
    </tr>
	<?php if ($isIndividual == 1 || $numPlayers > 1) { ?>
    <tr>
        <td>
            Phone Number: 
		</td><td>
        	<input id='userInput' type="text" value='<?php print $playerPhoneNum[$teamNum]?>' name="phoneNum[]" />
        </td>
    </tr><tr>
        <td>
            Finalized: 
		</td><td>
        	<select id='userInput' name="isFinalized[]">
				<OPTION selected='selected' VALUE=1>Yes</OPTION>
				<OPTION VALUE=0>No</OPTION>
            </select>
        </td>
    </tr>
    <?php }
}

function printAgentFooter($numPlayers) { ?>
    <tr>
    	<td colspan=2>
            <span style='color:#FF0000'>*</span> - needed
            <button name="addPlayer">Add Player<?php if($numPlayers > 1) print 's';?></button>
        </td>
    </tr>
<?php } ?>