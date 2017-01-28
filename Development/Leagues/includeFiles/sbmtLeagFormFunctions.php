<?php

function printIDs($sportsDropDown, $seasonsDropDown) { ?>
	Sport
	<select id='userInput' name='sportID' onchange='reloadCreatePage()'>
		<?php print $sportsDropDown ?>
	</select><br /><br />
	Season
	<select id='userInput' name='seasonID' onchange='reloadCreatePage()'>
		<?php print $seasonsDropDown ?>
	</select><br /><br />
	<a href='leaguesControlPanel.php'>Leagues Control Panel</a>
<?php }

//prints the matches sections of the score reporter
function printForm() {
    global $leagueName, $dayNumber, $regFee, $numMatches, $numGames, $hasTies, $hasPractices, $maxPoints, $curWeek;
	global $allowIndividuals, $regAvailable, $teamsBeforeWaiting, $maxTeams, $askForScores, $leagueID, $sortByPercent; 
	global $update, $daysSpiritHidden, $hideSpiritHour, $showSpiritHour, $playoffWeek, $individualFee, $picLink;
	global $teamsFull, $malesFull, $femalesFull, $showStatic; ?>
    
	<tr>
		<th colspan=4>
			League Form
		</th>
	</tr><tr>
        <td>
            League Name
		</td><td>
            <input type='text' name='leagueName' style="width:150px" value="<?php print $leagueName?>">
        </td><td>
			Show Static Schedule
		</td><td>
			<input type='checkbox' <?php print $showStatic == 1?'checked':'' ?> name='showStatic' value=1 />
		</td>
    </tr><tr>
        <td>Day
        </td><td>    
            <select name='dayNumber'>
            <?php //Runs a for loop to load the max number of points based on the sport
            for ($z=1; $z < 8; $z++){ 
                if($z == $dayNumber) { ?>
                    <option selected='selected' value=<?php print $z?>><?php print dayString($z)?></option><?php
                } else { ?>
                    <option value=<?php print $z?>><?php print dayString($z)?></option><?php
                }
            } ?>
            </select>
        </td><td>
            Registration Fee
        </td><td>
             <input type='text' name='regFee' style="width:50px" value='<?php print $regFee?>'>
        </td>
    </tr><tr>
        <td>    
            Max Points
        </td><td>
            <select name='maxPoints'>
            <?php for ($x=0; $x <= $maxPoints+30; $x++){
                if ($x == $maxPoints) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x ?></option><?php
                }
            }?>
            </select>
        </td><td>
            Individual Fee
        </td><td>
             <input type='text' name='individualFee' style="width:50px" value='<?php print $individualFee?>'>
        </td>
    </tr><tr>
        <td>
            Number of matches
        </td><td>
            <select name='numMatches'>
            <?php for ($x=1; $x < 5; $x++){
                if ($x == $numMatches) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x?></option><?php
                }
            }?>
            </select>
        </td><td>
            Number of Games
        </td><td>
            <select name='numGames'>
            <?php for ($x=1; $x < 5; $x++){
                if ($x == $numGames) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x?></option><?php
                }
            }?>
            </select>
        </td>         
    </tr><tr>
        <td>    
            Has Ties
        </td><td>
            <select name='hasTies'>
            <?php for ($x=1; $x >= 0; $x--){
                if ($x == $hasTies) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                }
            }?>
            </select>
        </td><td>    
            Has Practise
        </td><td>
            <select name='hasPractices'>
            <?php for ($x=1; $x >= 0; $x--){
                if ($x == $hasPractices) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                }
            }?>
            </select>
        </td>
    </tr><tr>
        <td>    
            Week to start at
        </td><td>
            <select name='curWeek'>
            <?php for ($x=1; $x <12; $x++){ 
				if ($x == $curWeek || ($curWeek == 0 && $x == 1)) {?>
                	<option selected="selected" value=<?php print $x ?>><?php print $x ?></option>
                <?php } else { ?>
                	<option value=<?php print $x ?>><?php print $x ?></option>
                <?php }
            }?>
            </select>
		</td><td>    
            Playoffs Start Week
        </td><td>
            <select name='playoffWeek'>
            <?php for ($x=0; $x <12; $x++){ 
				if ($x == $playoffWeek || ($playoffWeek == 0 && $x == 8)) {?>
                	<option selected="selected" value=<?php print $x ?>><?php print $x ?></option>
                <?php } else { ?>
                	<option value=<?php print $x ?>><?php print $x ?></option>
                <?php }
            }?>
            </select>
        </td>
    </tr><tr>
    	<td>    
            Allow Individuals
        </td><td>
            <select name='allowIndividuals'>
            <?php for ($x=1; $x >= 0; $x--){
                if ($x == $allowIndividuals) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                }
            }?>
        </td><td>    
            Available for registration?
        </td><td>
            <select name='regAvailable'>
            <?php for ($x=1; $x >= 0; $x--){
                if ($x == $regAvailable) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                }
            }?>
        </td>
    </tr><tr>
        <td>    
            Teams before waiting list
        </td><td>
            <select name='teamsBeforeWaiting'>
            <option value=0>N/A</option>
            <?php for ($x=1; $x <= 50; $x++){
                if ($x == $teamsBeforeWaiting) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x ?></option><?php
                }
            }?>
            </select>
        </td><td>    
            Max teams
        </td><td>
            <select name='maxTeams'>
            <option value=0>N/A</option>
            <?php for ($x=1; $x <= 50; $x++){
                if ($x == $maxTeams) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x ?></option><?php
                }
            }?>
            </select>
        </td>
    </tr><tr>
    	<td>    
            Ask for scores
        </td><td>
            <select name='askForScores'>
            <?php for ($x=1; $x >= 0; $x--){
                if ($x == $askForScores) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                }
            }?>
        </td><td>    
            Sort By Percentage
        </td><td>
            <select name='sortByPercent'>
            <?php for ($x=1; $x >= 0; $x--){
                if ($x == $sortByPercent) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x > 0 ? 'Yes' : 'No' ?></option><?php
                }
            }?>
        </td>
    </tr><tr>
        <td>    
            Days spirit will be hidden
        </td><td>
            <select name='daysSpiritHidden'>
            <?php for ($x=0; $x < 8 ; $x++){
                if ($x == $daysSpiritHidden) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x ?></option><?php
                }
            }?>
            </select>
        </td><td colspan=2>    
            Spirit Hours: Hide
            <select name='hideSpiritHour'>
            <?php for ($x=0; $x < 25; $x++){
                if ($x == $hideSpiritHour) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x ?></option><?php
                }
            }?>
            </select>
        	Show
            <select name='showSpiritHour'>
            <?php for ($x=0; $x < 25; $x++){
                if ($x == $showSpiritHour) {
                    ?><option selected='selected' value=<?php print $x ?>><?php print $x ?></option><?php
                } else {
                    ?><option value=<?php print $x ?>><?php print $x ?></option><?php
                }
            }?>
            </select>
        </td>
    </tr><tr>
        <td colspan=2>
            Teams Full <input <?php $teamsFull > 0?print 'checked':''; ?> type='checkbox' name='teamsFull' value=1>
		</td><td>
			Males Full <input <?php $malesFull > 0?print 'checked':''; ?> type='checkbox' name='malesFull' value=1>
		</td><td>
			Females Full <input <?php $femalesFull > 0?print 'checked':''; ?> type='checkbox' name='femalesFull' value=1>
        </td>
    </tr><tr>
        <td colspan=4>
            Pic Link
            <input type='text' name='leaguePicLink' style="width:500px" value="<?php print $picLink?>">
        </td>
    </tr>
    <tr>
        <td colspan=4>
            <input type='Submit' style="font-size:18px" value='Submit League' name='SubmitLeague'>
            <?php if ($update == 1) { ?>
				<input type='Submit' style="font-size:18px" value='Delete League' name='deleteLeague'>
			<?php } ?>
        </td>
    </tr>
<?php } ?>