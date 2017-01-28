<?php

function printPageHeader($teamObjs, $teamID) { 
	global $picExists, $picLink ?>
	<h1>
		<?php for($i=0;$i<count($teamObjs);$i++) {
			if($teamID == $teamObjs[$i]->teamID) {
				print $teamObjs[$i]->teamName.'<br/>';
			}
		}?>
	</h1>
	<h5>
		<?php if($picExists == 1) {
			print "<a href='$picLink' target='_blank'>(picture)</a>";
		} else {
			print '(No Picture)';
		} ?>
	</h5>
<?php }

function printSubmissionsHeader() { ?>
	<tr>
		<th colspan=7>
			Score Submissions
		</th>
	</tr><tr>
        <td>
            Del
        </td><td>
            Date Played
        </td><td>
            Opponent
        </td><td>
            Results
        </td><td>
            Spirit Score
        </td><td>
            Submitted By
        </td><td>
            Submitted
        </td>
	</tr>
<?php }

function printSubmissionNode($submissions, $i, $games, $team, $numTeams, $sportID) { ?>
	<tr>
        <td>
        	<INPUT TYPE='checkbox' NAME='deleteSubmission[]' VALUE=<?php print $i ?>>
        </td><td>
                <?php print 'Week '.$submissions[$i]->weekNum.' - '.$submissions[$i]->gameDate; ?>
        </td><td>
        <?php for($j=0;$j<$numTeams;$j++) {
            if($submissions[$i]->oppTeamID == $team[$j]->teamID) {
                print '<a href="teamPage.php?teamID='.$submissions[$i]->oppTeamID.'">'.$team[$j]->teamName.'</a>';
            }
        }?>
        </td><td>
            <?php for($j=0;$j<$games;$j++) { ?>
            	<INPUT TYPE='hidden' NAME='deleteSubmissionID[]' VALUE=<?php print $submissions[$i+$j]->submissionID ?>>
				<?php print $submissions[$i+$j]->getresultString();
					
				if($sportID != 2) {
					print ' '.$submissions[$i+$j]->getScoreString();
				} 
				if($j == 0 && $games == 2) {
					print ', ';
				}			
            } ?>
        </td><td>
        	<?php if ($submissions[$i]->isSpirit == 1) {
				print $submissions[$i]->spiritValue; 
			} else {
				print 'N/A';
			}?>
        </td><td>
        	<?php print $submissions[$i]->submitterName."<font size=1>(<a target='_blank' href='mailto:".$submissions[$i]->submitterEmail."'>".$submissions[$i]->submitterEmail.'</a>)</font>'; ?>
        </td><td>
        	<?php print date('F j, Y', strtotime($submissions[$i]->submittedDate)); ?>
        </td>
	</tr>
    <?php if ($submissions[$i]->isComment == 1) { ?>
	<tr>
        <td></td>
		<td colspan=6>
				<?php print $submissions[$i]->commentValue; ?>
        </td>
    </tr>
    <?php }
}

function printOppSubmissionsHeader() { ?>
	<tr>
		<th colspan=6>
			Opponent Score Submissions
		</th>
	</tr><tr>
		<td>
			Date Played
		</td><td>
			Team Name
		</td><td>
			Results
		</td><td>
			Spirit Score
		</td><td>
			Submitted By
		</td><td>
			Submitted
		</td>
	</tr>
<?php }

function printOppSubmissionNode($submissions, $i, $games, $team, $numTeams, $sportID) { ?>
	<tr>
    	<td>
			<?php print 'Week '.$submissions[$i]->weekNum.' - '.$submissions[$i]->gameDate; ?>
		</td>
		<td>
        	<?php for($j=0;$j<$numTeams;$j++) {
				if($submissions[$i]->teamID == $team[$j]->teamID) {
					print '<a href="teamPage.php?teamID='.$submissions[$i]->teamID.'">'.$team[$j]->teamName.'</a>';
				}
			}?>
		</td>
		<td>
			<?php if($sportID != 2) {
				for($j=0;$j<$games;$j++) {
					print $submissions[$i+$j]->getresultString().$submissions[$i+$j]->getScoreString();
					if($j == 0 && $games == 2) {
						print ', ';
					}
				}
			} else {
				for($j=0;$j<$games;$j++) {
					print $submissions[$i+$j]->getresultString();
					if($j == 0 && $games == 2) {
						print ', ';
					}
				}			
			} ?>
		</td>
		<td>
			<?php if ($submissions[$i]->isSpirit == 1) {
				print $submissions[$i]->spiritValue; 
			} else {
				print 'N/A';
			}?>
		</td>
		<td>
			<?php print $submissions[$i]->submitterName."<font size=1>(<a target='_blank' href='mailto:".$submissions[$i]->submitterEmail."'>".$submissions[$i]->submitterEmail.'</a>)</font>'; ?>
		</td>
		<td>
			<?php print date('F j, Y', strtotime($submissions[$i]->submittedDate)); ?>
		</td>
	</tr>
    <?php if ($submissions[$i]->isComment == 1) { ?>
	<tr>
		<td colspan=6>
				<?php print $submissions[$i]->commentValue; ?>
        </td>
    </tr>
    <?php }
}

function printPlayerHeader($teamObjs) { ?>
    <tr align="center">
		<th colspan=8>
			<?php print($teamObjs[$i]->teamName. " Players"); ?>
		</th>
	</tr><tr>
    	<td></td><td>
        	Team Name
        </td><td>
        	Player
        </td><td>
        	Email
        </td>
    </tr>
<?php }


function printPlayerNode($playerNum) {
	global $playerArray, $player, $bccArray;
	$sportID = $playerArray[$playerNum]->playerSportID;
	$leagueID = $playerArray[$playerNum]->playerLeagueID;
	$playerID = $playerArray[$playerNum]->playerID; ?>
	<tr align="center">
		<td>
			<?php print $player + 1?>
            <INPUT TYPE='hidden' NAME='playerID[]' VALUE='<?php print $playerArray[$playerNum]->playerID;?>' />
		</td>
		<td>
			<?php print $playerArray[$playerNum]->playerTeamName?>
			<INPUT TYPE='hidden' NAME="teamName[]" VALUE="<?php print $playerArray[$playerNum]->playerTeamName;?>" />
		</td>
		<td>
			<a href='<?php print "../Players/updatePlayer.php?sportID=$sportID&leagueID=$leagueID&playerID=$playerID"?>'>
				<?php print $playerArray[$playerNum]->playerFirstName.' '.$playerArray[$playerNum]->playerLastName;?>
			</a>
			<INPUT TYPE='hidden' NAME='playerName[]' VALUE='<?php print $playerArray[$playerNum]->playerFirstName.' '.$playerArray[$playerNum]->playerLastName;?>' />
		</td>
		<td>
			<font size=1>
			<a href="mailto:<?php print $playerArray[$playerNum]->playerEmail;?>">
				<?php print $playerArray[$playerNum]->playerEmail;?>
			</a>
			</font>
			<INPUT TYPE='hidden' NAME='playerEmail[]' VALUE='<?php print $playerArray[$playerNum]->playerEmail;?>' />
		</td>
	</tr>
	<?php $player++; 
} ?>