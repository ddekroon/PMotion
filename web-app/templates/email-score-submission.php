<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
		<title>Score Reporter Email</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<style> body{font-family:"Helvetica Neue",Helvetica,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;height:100%;-webkit-text-size-adjust:none;width:100%!important;color:#494e52;font-size:14px}table{border:none;vertical-align:top;padding:0;margin:0;border-collapse:collapse;width:100%;color:#494e52}td{padding:7px;margin:0;vertical-align:top}td.padding-right{padding-right:20px}td.padding-left{padding-left:20px}a{color:#ed1c24;text-decoration:none}a:hover{text-decoration:underline}.strong{font-weight:700}.center{text-align:center}.secondary{color:#ed1c24}.secondary-background{background:#ed1c24;color:#fff}.secondary-background a{color:#fff}.grey-background{background:#494e52}table.body-wrap{padding:0;width:100%}h1,h2,h3,h4{font-weight:600;margin:10px 0 20px;text-transform:uppercase}h1{font-size:33px;color:#ed1c24;border-bottom:9px solid #ed1c24;font-weight:600}h2{font-size:16px}h3{font-size:20px;background:#ed1c24;color:#fff;text-align:center;padding:3px}ol,p,ul{font-size:13px;font-weight:400;margin-bottom:10px;padding:0}ol,ul{list-style-position:inside}ol li,ul li{margin:0;list-style-position:inside;padding:0}.container{margin:0 auto;width:655px}.container-big{margin:0 auto}.container-small{width:615px;margin:0 auto}.content{display:block;margin:0 auto;padding:20px}.content table{width:100%;border:none;padding:0;margin:0}.half{width:50%}.one-quarter{width:25%}.three-quarters{width:75%}.gray-bar{padding:0;background:#494e52;height:3px}.light-gray-bar{padding:0;background:#d8d8d8;height:3px}.gray-bar-right{border-right:3px solid #494e52}.float-left{float:left}.float-right{float:right}.clear{clear:both}.align-right{text-align:right}.align-left{text-align:left}.align-center{text-align:center} </style>
	</head>
	<body style="-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; color: #494e52; font-family: 'Helvetica Neue',Helvetica,Helvetica,Arial,sans-serif; font-size: 14px; height: 100%; width: 100% !important;">
		<!-- body -->
		<table class="body-wrap" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; color: #494e52; margin: 0; padding: 0; vertical-align: top; width: 100%;">
			<tr>
				<td class="center" style="margin: 0; padding: 7px; text-align: center; vertical-align: top;">
					<center>
						<table class="container" style="border: none; border-collapse: collapse; color: #494e52; margin: 0 auto; padding: 0; vertical-align: top; width: 655px;">
							<tr>
								<td colspan="2" class="light-gray-bar" style="background: #d8d8d8; height: 3px; margin: 0; padding: 0; vertical-align: top;"></td>
							</tr>
							<tr><td colspan="2" style="margin: 0; padding: 7px; vertical-align: top;"><br></td></tr>
							<tr>
								<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Results Mailed</td>
								<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;"><?php echo $currDate ?></td>
							</tr>
							<tr>
								<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Team Name</td>
								<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;"><?php echo $team->getName() ?></td>
							</tr>
							<tr>
								<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Submitted by</td>
								<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
									<?php echo $firstSubmission->getSubmitterName() . ' (' . $firstSubmission->getSubmitterEmail() . ')'; ?>
								</td>
							</tr>
							<tr>
								<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">League</td>
								<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
									<?php echo $league->getDayString() . ' ' .$league->getName(); ?>
								</td>
							</tr>
							<tr>
								<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Date Played</td>
								<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
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
							<table class="container" style="border: none; border-collapse: collapse; color: #494e52; margin: 0 auto; padding: 0; vertical-align: top; width: 655px;">
								<tr>
									<td colspan="2" style="margin: 0; padding: 7px; vertical-align: top;">
										<?php foreach($submissionErrors as $curError) { ?>
											<p class="secondary strong center align-center" style="color: #ed1c24; font-size: 13px; font-weight: 700; margin-bottom: 10px; padding: 0; text-align: center;">
												<?php echo $curError ?>
											</p>
										<?php } ?>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="light-gray-bar" style="background: #d8d8d8; height: 3px; margin: 0; padding: 0; vertical-align: top;"></td>
								</tr>
								<tr>
									<td class="align-center" style="margin: 0; padding: 7px; text-align: center; vertical-align: top;"><h4 style="font-weight: 600; margin: 10px 0 20px; text-transform: uppercase;">Submitted Results</h4></td>
									<td class="align-center" style="margin: 0; padding: 7px; text-align: center; vertical-align: top;"><h4 style="font-weight: 600; margin: 10px 0 20px; text-transform: uppercase;">Opponent Results</h4></td>
								</tr>
								<tr>
									<td class="half" style="margin: 0; padding: 7px; vertical-align: top; width: 50%;">
										<?php 
											$this->insert('partials/email-score-submission-game', [
												"submissions" => $submissions,
												"matchNum" => $i
											]);
										?>
									</td>
									<td class="half" style="margin: 0; padding: 7px; vertical-align: top; width: 50%;">
										<?php if (sizeof($oppSubmissions) > 0) { ?>
											<?php for($k = 0, $oppMatchNum = 0; $k < $matches * $league->getNumGamesPerMatch(); $k = $k + $league->getNumGamesPerMatch(), $oppMatchNum++) { ?>
												<?php if(sizeof($oppSubmissions) >= $k && $oppSubmissions[$k]->getOppTeamId() == $team->getId()) { ?>
													<?php 
														$this->insert('partials/email-score-submission-game', [
															"submissions" => $oppSubmissions,
															"matchNum" => $oppMatchNum
														]);
													?>
												<?php } ?>
											<?php } ?>			
										<?php } else { ?>
											<table style="border: none; border-collapse: collapse; color: #494e52; margin: 0; padding: 0; vertical-align: top; width: 100%;">
												<tbody>
													<tr>
														<td class="align-center" style="margin: 0; padding: 7px; text-align: center; vertical-align: top;">
															<?php echo !$league->getIsInPlayoffs() ? "Haven't submitted their scores yet" : "Playoffs" ?>
														</td>
													</tr>
												</tbody>
											</table>
										<?php }	?>
									</td>
								</tr>
								<tr><td colspan="2" style="margin: 0; padding: 7px; vertical-align: top;"><br></td></tr>
								<tr>
									<td colspan="2" class="light-gray-bar" style="background: #d8d8d8; height: 3px; margin: 0; padding: 0; vertical-align: top;"></td>
								</tr>
							
								<tr><td colspan="2" style="margin: 0; padding: 7px; vertical-align: top;"><br></td></tr>
								<tr>
									<td colspan="2" style="margin: 0; padding: 7px; vertical-align: top;">
										<table style="border: none; border-collapse: collapse; color: #494e52; margin: 0; padding: 0; vertical-align: top; width: 100%;">
											<tbody>
												<tr>
													<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Comments</td>
													<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
														<?php echo ($curSubmission->getScoreSubmissionComment() != null ? $curSubmission->getScoreSubmissionComment()->getComment() : '') ?>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr><td colspan="2" style="margin: 0; padding: 7px; vertical-align: top;"><br></td></tr>
								<tr>
									<td colspan="2" class="light-gray-bar" style="background: #d8d8d8; height: 3px; margin: 0; padding: 0; vertical-align: top;"></td>
								</tr>
							</table>
						<?php } ?>
					</center>
				</td>
			</tr>
		</table>
	</body>
</html>