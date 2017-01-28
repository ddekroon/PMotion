<?php function printIDs($seasonsDropDown) { ?>
	Season
	<select id='userInput' NAME='seasonID' onchange='reloadSeason(this)'>
		<?php print $seasonsDropDown ?>
	</select>
	<input type='button' id="toggleSeasonButton" value='Toggle Season Data' />
<?php }

function printSeasonEditor($seasonObj, $sports) { ?>
	<tr>
		<th colspan=4>
			Season Editor
		</th>
	</tr><tr>
		<td>
			Season Name
		</td><td>
			<input type='text' name='seasonName' style='width:250px;' value='<?php print $seasonObj->seasonName ?>' />
		</td><td>
			Num Weeks
		</td><td>
			<select name='numWeeks'>
				<?php for ($i = 15; $i >= 5; $i--) {
					print "<option value=$i ";
					print $seasonObj->seasonNumWeeks == $i?'selected':'';
					print ">$i</option>";
				} ?>
			</select>
		</td>
	</tr><tr>
		<td>
			Season Available Registration
		</td><td>
			<select name='availableRegistration'>
				<option value=1 <?php print $seasonObj->seasonAvailableRegistration == 1?'selected':'' ?>>Yes</option>
				<option value=0 <?php print $seasonObj->seasonAvailableRegistration == 0?'selected':'' ?>>No</option>
			</select>
		</td><td>
			Season Available Score Reporter
		</td><td>
			<select name='availableScoreReporter'>
				<option value=1 <?php print $seasonObj->seasonAvailableScoreReporter == 1?'selected':'' ?>>Yes</option>
				<option value=0 <?php print $seasonObj->seasonAvailableScoreReporter == 0?'selected':'' ?>>No</option>
			</select>
		</td>
	</tr>
	<?php if($seasonObj->seasonName != 'Summer' && $seasonObj->seasonName != 'Winter 2') { 
		$colspan = 2;
	} else {
		$colspan = 1;
	}?>
	<tr>
		<td colspan='<?php print $colspan ?>'>
			Registration Opens
		</td><td colspan='<?php print $colspan ?>'>
			<?php print date_picker(strtotime($seasonObj->seasonRegistrationOpensDate), 'RegOpen'); ?>
		</td>
	<?php if($seasonObj->seasonName == 'Summer' || $seasonObj->seasonName == 'Winter 2') { ?>
		<td>
			Confirmation Due By
		</td><td>
			<?php print date_picker(strtotime($seasonObj->seasonConfirmationDueBy), 'ConfDue'); ?>
		</td>
	<?php } ?>
	</tr><tr>
		<td colspan=2>
			Registration Up Until
		</td><td colspan=2>
			<?php print date_picker(strtotime($seasonObj->seasonRegistrationUpUntil), 'RegUpUntil'); ?>
		</td>
	</tr> <!-- <tr> -->
    
    <!-- Dave no longer wants This option but it's commented out incase he changes his mind-->
		
        <!--<td>
			Registration Due By
		</td><td> --> 
			<?php //print date_picker(strtotime($seasonObj->seasonRegistrationDueBy), 'RegDue'); ?>
 
		<!--</td>
        
        <td>
			Registration Due By Sport
		</td> <td>-->
        	<!-- This is now hidden so that it cant be changed, the option is set to "yes"-->
			<select hidden id="regBySport" name='regBySportOn'>
				<option <?php //print $seasonObj->regBySport == 1?'selected':'' ?> value=1>Yes</option>
				<option <?php //print $seasonObj->regBySport == 0?'selected':'' ?> value=0>No</option>
			</select>
		<!--</td>
	</tr> -->
    

		<?php
			/*Setting registration by sport to always be on*/
			$seasonObj->regBysport = 1;
			for($i = 1; $i <= count($sports); $i++) {
				if(($i + 1) % 2 == 0) {
					print "<tr class='sportRow' ";
					print $seasonObj->regBySport == 1?'':"style='display:none'";
					print '>';
				}
			
			print '<td>'.$sports[$i]['name'].'</td>';
			print '<td>'.date_picker(strtotime($sports[$i]['regDueBy']), "RegDueSport[$i]").'</td>';
			
			if($i % 2 == 0) {
				print '</tr>';
			}
			
		} ?>
	</tr>
<?php }

function printSportHeader($sportNum, $sportName) { ?>
				
		<table>
			<tr>
				<th colspan=5>
					<?php print $sportName ?>
				</th>
			</tr><tr>
				<td rowspan="2">
					League
				</td><td rowspan="2">
					ID
				</td><td colspan=3>
					Full
				</td>
			</tr><tr>
				<td>
					T
				</td><td>
					M
				</td><td>
					F
				</td>
			</tr>
<?php }

function printPastSportHeader($sportNum, $sportName) { ?>
		<table>
			<tr>
				<th colspan=2>
					<?php print $sportName ?>
				</th>
			</tr><tr>
				<td>
					League
				</td><td>
					Create
				</td>
			</tr>
<?php }

function printLeagueNode($leagueObj) { 
	global $seasonID ?>
	<tr>
		<td>
			<input type='hidden' name='leagueID[]' value='<?php print $leagueObj->leagueID ?>' />
			<?php print "<a href='submitLeague.php?sportID=".$leagueObj->leagueSportID.'&seasonID='.$seasonID.'&leagueID='.$leagueObj->leagueID."&update=1'>".$leagueObj->leagueName.'($'.$leagueObj->leagueCost.')</a>' ?>
		</td><td>
			<?php print $leagueObj->leagueID ?>
		</td><td>
			<input type="checkbox" <?php print $leagueObj->leagueFullTeams==1?'checked':'' ?> name="fullTeams[]" value='<?php print $leagueObj->leagueID ?>'>
		</td><td>
			<input type="checkbox" <?php print $leagueObj->leagueFullMales==1?'checked':'' ?> name="fullMales[]" value='<?php print $leagueObj->leagueID ?>'>
		</td><td>
			<input type="checkbox" <?php print $leagueObj->leagueFullFemales==1?'checked':'' ?> name="fullFemales[]" value='<?php print $leagueObj->leagueID ?>'>
		</td>
	</tr>
<?php }

function printPastLeagueNode($leagueObj) { 
	global $seasonID ?>
	<tr>
		<td>
			<?php print "<a href='updateLeague.php?sportID=".$leagueObj->leagueSportID.'&seasonID='.$seasonID."'>".$leagueObj->leagueName.'</a>' ?>
		</td><td>
			<input type="checkbox" name="pastLeagueID[]" value='<?php print $leagueObj->leagueID ?>'>
		</td>
	</tr>
<?php }

function printSportFooter($sportNum, $sportName) { 
	global $seasonID; ?>
		<tr>
			<td colspan=5>
				<?php print "<a href='submitLeague.php?sportID=$sportNum&seasonID=$seasonID'>Create New $sportName League</a>"; ?>
			</td>
		</tr>
	</table>
<?php }

function printLeagues($leagueObjArray, $yearFilter) {
	for($sportNum = 1; $sportNum <= 4; $sportNum++){ 
		if(count($leagueObjArray[$sportNum]) > 0) {
			if($yearFilter == 0) {
				printSportHeader($sportNum, $leagueObjArray[$sportNum][0]->leagueSportName);
			} else {
				printPastSportHeader($sportNum, $leagueObjArray[$sportNum][0]->leagueSportName);
			}
			for($leagueNum = 0; $leagueNum < count($leagueObjArray[$sportNum]); $leagueNum++) {
				if($yearFilter == 0) { //curYear
					printLeagueNode($leagueObjArray[$sportNum][$leagueNum]);
				} else {
					printPastLeagueNode($leagueObjArray[$sportNum][$leagueNum]);
				}
			}
			printSportFooter($sportNum, $leagueObjArray[$sportNum][0]->leagueSportName);
		}
	}
}

function printCheckAllButtons() { ?>
	<br /><input type='button' onClick="return checkAll()" name="checkAllButton" value='Check All' />
	<input type='button' onClick="return uncheckAll()" name="uncheckAllButton" value='Uncheck All' />
<?php }

function printBottomButton() { ?>
	<tr>
		<td colspan=4>
			<input type='submit' name='updateLeagues' value='Update Everything!' />
		</td>
	</tr>
<?php }

function date_picker($date, $type) {
	$day = date('j', $date);
	$month = date('n', $date);
	$year = date('Y', $date);

	$months=array('','January','February','March','April','May',
	'June','July','August', 'September','October','November','December');

	// Month dropdown
	$html="<select name='month".$type."'>";

	for($i=1;$i<=12;$i++)
	{
	   $html .= "<option ";
	   $html .= $i==$month?'selected':'';
	   $html .= " value='$i'>$months[$i]</option>";
	}
	$html.="</select> ";
   
	// Day dropdown
	$html.="<select name='day".$type."'>";
	for($i=1;$i<=31;$i++)
	{
	   $html .= "<option ";
	   $html .= $i==$day?'selected':'';
	   $html .= " value='$i'>$i</option>";
	}
	$html.="</select> ";

	// Year dropdown
	$html.="<select name='year".$type."'>";

	for($i=$year - 1;$i<=$year + 1;$i++)
	{      
		$html .= "<option ";
		$html .= $i==$year?'selected':'';
		$html .= " value='$i'>$i</option>";
	}
	$html.="</select> ";

	return $html;
}