<?php

function printMatchHeader() { 
	global $sportID; ?>
	<div class="Row" style="font-weight:bold;">
    	<div class="Column">#</div>
		<div class="Column">Date</div>
		<div class="Column">Opponent</div>
		<div class="Column">Result</div>
		<div class="Column">Field</div>
		<div class="Column">Game Time</div>
		<?php if($sportID != 2) { ?>
			<div class="Column">Colour</div>
		<?php } ?>
	</div>
<?php }

function printMatchNode($matchObj, $rowNum, $leagueID, $teamObjs) 
{ 
	global $sportID; ?>
	<div class="Row" <?php print $rowNum == 1?'style="background-color:#EEE"':'' ?>>
    	<div class="Column" id='matchNum'><?php print $matchObj->matchNum; ?></div>
		<div class="Column" id='datePlayed'><?php print $matchObj->getFormattedDatePlayed(); ?></div>
		<div class="Column" id='opponent'>
        
        	<?php if ($matchObj->oppTeamID != 1) 
			{ 
				print "<a href='teamPage.php?teamID=".$matchObj->oppTeamID."'>".$matchObj->oppTeamName.'</a>';
			}
			else 
			{
				print $matchObj->oppTeamName;	
			}
			
			print ' '.$matchObj->standingsString;?>
		</div>
		<div class="Column" id='result'><?php print $matchObj->getResultString(); ?></div>
		<div class="Column" id='fieldName'><?php print "<a target='_blank' href='http://www.perpetualmotion.org".$matchObj->matchFieldLink."'>$matchObj->matchField</a>" ?></div>
		<div class="Column" id='gameTime'><?php print $matchObj->getFormattedGameTime() ?></div>
		<?php print $sportID != 2?"<div class='Column' id='shirtColour'>".$matchObj->matchShirtColour.'</div>':''; ?>
	</div>
<?php }