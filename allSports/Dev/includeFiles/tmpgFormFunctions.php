<?php

function printMatchHeader() { 
	global $sportID; ?>
	<tr>
    	<th>#</th>
		<th>Date</th>
		<th>Opponent</th>
		<th>Result</th>
		<th>Field</th>
		<th>Game Time</th>
		<?php if($sportID != 2) { ?>
			<th>Colour</th>
		<?php } ?>
	</tr>
<?php }

function printMatchNode($matchObj, $rowNum, $leagueID, $teamObjs) { 
	global $sportID; ?>
	<tr <?php print $rowNum == 1?'style="background-color:#EEE"':'' ?>>
    	<td id='matchNum'><?php print $matchObj->matchNum; ?></td>
		<td id='datePlayed'><?php print $matchObj->getFormattedDatePlayed(); ?></td>
		<td id='opponent'>
        	<?php if ($matchObj->oppTeamID != 1) { 
				print "<a href='teamPage.php?teamID=".$matchObj->oppTeamID."'>".$matchObj->oppTeamName.'</a>';
			} else {
				print $matchObj->oppTeamName;	
			}
			print ' '.$matchObj->standingsString;?>
		</td>
		<td id='result'><?php print $matchObj->getResultString(); ?></td>
		<td id='fieldName'><?php print "<a target='_blank' href='http://www.perpetualmotion.org".$matchObj->matchFieldLink."'>$matchObj->matchField</a>" ?></td>
		<td id='gameTime'><?php print $matchObj->getFormattedGameTime() ?></td>
		<?php print $sportID != 2?"<td id='shirtColour'>".$matchObj->matchShirtColour.'</td>':''; ?>
	</tr>
<?php }