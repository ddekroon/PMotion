<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
		<title>Score Reporter Email</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
		<style> {{styles-css}} </style>
	</head>
	<body>

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
<table>
	<tbody>
		<tr>
			<td class="gray-bar-right align-right padding-right strong one-quarter">Opposition</td>
			<td class="padding-left three-quarters"><?php echo $submissions[$matchNum]->getOppTeam()->getName() ?></td>
		</tr>
		<?php for($i = 0; $i < $league->getNumGamesPerMatch(); $i++) { ?>
			<?php 
				if(sizeof($submissions) <= $gameNum + $i) {
					continue; //No submission for this game. Not sure how this is possible but hey, good to error check.
				}

				$curSubmission = $submissions[$gameNum + $i];
			?>
				
			<tr>
				<td class="gray-bar-right align-right padding-right strong one-quarter">Game <?php echo $i + 1 ?></td>
				<td class="padding-left three-quarters">

					We <?php echo $curSubmission->getResultsString() ?>
					<?php if($league->getIsAskForScores() || $league->getIsInPlayoffs()) { ?>
						<br />
						(Us: <?php echo $curSubmission->getScoreUs() ?> Them: <?php echo $curSubmission->getScoreThem() ?>)
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td class="gray-bar-right align-right padding-right strong one-quarter">Spirit Score</td>
			<td class="padding-left three-quarters"><?php echo $spiritScoreString ?></td>
		</tr>
	</tbody>
</table>
		
	</body>
</html>