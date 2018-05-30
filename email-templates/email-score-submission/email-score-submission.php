<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
		<title>Score Reporter Email</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
		<style> {{styles-css}} </style>
	</head>
	<body>
		<!-- body -->
		<table class="body-wrap" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td class="center">
					<center>
						<table class="container">
							<tr>
								<td colspan="2" class="light-gray-bar"></td>
							</tr>
							<tr><td colspan='2'><br /></td></tr>
							<tr>
								<td class="gray-bar-right align-right padding-right strong one-quarter">Results Mailed</td>
								<td class="padding-left three-quarters"><?php echo $currDate ?></td>
							</tr>
							<tr>
								<td class="gray-bar-right align-right padding-right strong one-quarter">Team Name</td>
								<td class="padding-left three-quarters"><?php echo $team->getName() ?></td>
							</tr>
							<tr>
								<td class="gray-bar-right align-right padding-right strong one-quarter">Submitted by</td>
								<td class="padding-left three-quarters">
									<?php echo $firstSubmission->getSubmitterName() . ' (' . $firstSubmission->getSubmitterEmail() . ')'; ?>
								</td>
							</tr>
							<tr>
								<td class="gray-bar-right align-right padding-right strong one-quarter">League</td>
								<td class="padding-left three-quarters">
									<?php echo $league->getDayString() . ' ' .$league->getName(); ?>
								</td>
							</tr>
							<tr>
								<td class="gray-bar-right align-right padding-right strong one-quarter">Date Played</td>
								<td class="padding-left three-quarters">
									Week <?php echo $firstSubmission->getDate()->getWeekNumber() . " - " . $firstSubmission->getDate()->getDescription() ?>
								</td>
							</tr>
						</table>
						<?php 
							$gameNum = 0;
						
							for ($i = 0; $i < $league->getNumMatches(); $i++) {
								$curSubmission = $submissions[$gameNum];
								$gameNum += $league->getNumGamesPerMatch();
								
								$submissionErrors = [];

								$teamsNotEqual = false; //By default assume they submitted for the correct team.
								$oppSubmissions = [];

								//Not playoffs, not against practice team, there is a scheduled match opp team
								if(!$league->getIsInPlayoffs() && $curSubmission->getOppTeamId() > 1 
										&& sizeof($scheduledMatches) > $i && $scheduledMatches[$i]->getOppTeamId($team) > 0) {

									if ($curSubmission->getOppTeamId() != $scheduledMatches[$i]->getOppTeamId($team)) {
										$teamsNotEqual = true;
									} else {
										$oppSubmissions = $scoreReporterController->getScoreSubmissions($curSubmission->getOppTeam(), null, $curSubmission->getDate());
									}
								}

								$teamDBSubmissions = $scoreReporterController->getScoreSubmissions($team, null, $curSubmission->getDate());

								if (sizeof($teamDBSubmissions) > 0) {
									$submissionErrors[] = "Results have already been submitted, these will be ignored";
								}

								if(!$league->getIsInPlayoffs() && $curSubmission->getOppTeamId() > 0) {

									if ($teamsNotEqual) {
										$submissionErrors[] = "Opponent submitted against is different than in the database";
									} else {

										$oppSubmissionsCorrect = false;
										$curSubmissionAndOppSubmissionMatch = true; //1 signifies are the same

										for($k = 0; $k < sizeof($oppSubmissions); $k++) {
											if($oppSubmissions[$k]->getOppTeamId() == $team->getId()) {

												$oppSubmissionsCorrect = true;

												for($m = 0; $m < $league->getNumGamesPerMatch(); $m++) {
													if (!$scoreReporterController->checkSubmissionsMatch($submissions[($i * $league->getNumGamesPerMatch()) + $m]->getResult(), $oppSubmissions[$k + $m]->getResult())) {
														$curSubmissionAndOppSubmissionMatch = false;
													}
												}
												
												break; //If we get the opp submissions for the current team and check the game submissions now break because we have everything we need.
											}
										}
										if (!$curSubmissionAndOppSubmissionMatch) {
											$submissionErrors[] = "Submitted results don't match their opponenets";
										} 
										if(!$oppSubmissionsCorrect && sizeof($oppSubmissions) > 0) {
											$submissionErrors[] = "Opponents submitted against different teams";
										}
									}
								}
						?>
							<table class="container">
								<tr>
									<td colspan='2'>
										<?php foreach($submissionErrors as $curError) { ?>
											<p class='secondary strong center align-center'>
												<?php echo $curError ?>
											</p>
										<?php } ?>
									</td>
								</tr>
								<tr>
									<td colspan='2' class="light-gray-bar"></td>
								</tr>
								<tr>
									<td class="align-center"><h4>Submitted Results</h4></td>
									<td class="align-center"><h4>Opponent Results</h4></td>
								</tr>
								<tr>
									<td class="half">
										<?php 
											$this->insert('partials/email-score-submission-game', [
												"submissions" => $submissions,
												"matchNum" => $i
											]);
										?>
									</td>
									<td class="half">
										<?php if (sizeof($oppSubmissions) > 0) { ?>
											<?php for($k = 0; $k < $matches * $league->getNumGamesPerMatch(); $k = $k + $league->getNumGamesPerMatch()) { ?>
												<?php if($oppSubmissions[$k]->getOppTeamId() == $team->getId()) { ?>
													<?php 
														$this->insert('partials/email-score-submission-game', [
															"submissions" => $oppSubmissions,
															"matchNum" => $i
														]);
													?>
												<?php } ?>
											<?php } ?>			
										<?php } else { ?>
											<table>
												<tbody>
													<tr>
														<td class='align-center'>
															<?php echo !$league->getIsInPlayoffs() ? "Haven't submitted their scores yet" : "Playoffs" ?>
														</td>
													</tr>
												</tbody>
											</table>
										<?php }	?>
									</td>
								</tr>
								<tr><td colspan='2'><br /></td></tr>
								<tr>
									<td colspan="2" class="light-gray-bar"></td>
								</tr>
							
								<tr><td colspan='2'><br /></td></tr>
								<tr>
									<td colspan='2'>
										<table>
											<tbody>
												<tr>
													<td class="gray-bar-right align-right padding-right strong one-quarter">Comments</td>
													<td class="padding-left three-quarters">
														<?php echo ($curSubmission->getScoreSubmissionComment() != null ? $curSubmission->getScoreSubmissionComment()->getComment() : '') ?>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr><td colspan='2'><br /></td></tr>
								<tr>
									<td colspan="2" class="light-gray-bar"></td>
								</tr>
							</table>
						<?php } ?>
					</center>
				</td>
			</tr>
		</table>
	</body>
</html>