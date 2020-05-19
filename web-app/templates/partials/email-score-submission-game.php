<?php 
	$league = $submissions[0]->getTeam()->getLeague();
	
	$spiritScoreVal = 0;
	
	$gameNum = $league->getNumGamesPerMatch() * $matchNum;
	
	$spiritScore = $submissions[$gameNum]->getSpiritScore();

	if($spiritScore != null) {
		$spiritScoreVal = $spiritScore->getValue();
	}

	if($spiritScoreVal == 0) {
		$spiritScoreString = 'N/A';
	} else {
		$spiritScoreString = number_format((float)$spiritScoreVal, 1, '.', '');
	}
?>
<table style="border: none; border-collapse: collapse; color: #494e52; margin: 0; padding: 0; vertical-align: top; width: 100%;">
	<tbody>
		<tr>
			<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Opposition</td>
			<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;"><?php echo $submissions[$gameNum]->getOppTeam()->getName() ?></td>
		</tr>
		<?php for($i = 0; $i < $league->getNumGamesPerMatch(); $i++) { ?>
			<?php 
				if(sizeof($submissions) <= $gameNum + $i) {
					continue; //No submission for this game. Not sure how this is possible but hey, good to error check.
				}

				$curSubmission = $submissions[$gameNum + $i];
			?>
				
			<tr>
				<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Game <?php echo $i + 1 ?></td>
				<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">

					We <?php echo $curSubmission->getResultsString() ?>
					<?php if($league->getIsAskForScores() || $league->getIsInPlayoffs()) { ?>
						<br>
						(Us: <?php echo $curSubmission->getScoreUs() ?> Them: <?php echo $curSubmission->getScoreThem() ?>)
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Spirit Score</td>
			<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;"><?php echo $spiritScoreString ?></td>
		</tr>
	</tbody>
</table>