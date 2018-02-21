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
								<td class="light-gray-bar"></td>
							</tr>
							<tr>
								<td class='align-center'>
									<h2>Perpetual Motion Online Registration System</h2>
									<p>Registration Confirmation</p>
								</td>
							</tr>
							<tr>
								<td class="light-gray-bar"></td>
							</tr>
							<tr>
								<td>
									<table>
										<tbody>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter">League</td>
												<td class="padding-left three-quarters">
													<?php echo $team->getLeague()->getSport()->getName() . ' - ' . $team->getLeague()->getRegistrationFormattedName() ?>
												</td>
											</tr>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter">
													<?php $returningString ?> Team Name
												</td>
												<td class="padding-left three-quarters">
													<?php echo $team->getName() ?>
												</td>
											</tr>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter">Date</td>
												<td class="padding-left three-quarters">
													<?php echo $team->getDateCreated()->format('F j, Y g:ia'); ?>
												</td>
											</tr>
											<?php if($adminEmail) { ?>
												<tr>
													<td class="gray-bar-right align-right padding-right strong one-quarter">TeamID</td>
													<td class="padding-left three-quarters">
														<?php echo $team->getId(); ?>
													</td>
												</tr>
											<?php } ?>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter">Captain</td>
												<td class="padding-left three-quarters">
													<?php echo $captain->getFirstName() . ' ' . $captain->getLastName() . ' (' . $captain->getGender() . ')' ?><br />
													<?php echo $captain->getEmail() ?><br />
													<?php echo Includes_Helper::formatPhoneNumber($captain->getPhoneNumber()) ?>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td class="light-gray-bar"></td>
							</tr>
							<tr>
								<td>
									<table>
										<tbody>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter">Players</td>
												<td class="padding-left three-quarters">
													<?php if($team->getPlayers() != null && !empty($team->getPlayers())) { ?>
														<?php $curPlayerNum = 1; ?>
														<table class='align-center'>
															<tr><th></th><th>First Name</th><th>Last Name</th><th>Email</th><th>Gender</th></tr>
															<?php foreach($team->getPlayers() as $curPlayer) { ?>
																<?php if($curPlayer->getFirstName() != '' 
																		|| $curPlayer->getLastName() != ''
																		|| $curPlayer->getEmail() != '') { ?>
																	<tr>
																		<td><?php echo $curPlayerNum++ ?></td>
																		<td><?php echo $curPlayer->getFirstName() ?></td>
																		<td><?php echo $curPlayer->getLastName() ?></td>
																		<td><?php echo $curPlayer->getEmail() ?></td>
																		<td><?php echo $curPlayer->getGender() ?></td>
																	</tr>
																<?php } ?>
															<?php } ?>
														</table>
													<?php } ?>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td class="light-gray-bar"></td>
							</tr>
							<tr>
								<td>
									<table>
										<tbody>
											<?php if($adminEmail && $howHeardMethod != null) { ?>
												<tr>
													<td class="gray-bar-right align-right padding-right strong one-quarter">How They Heard About Us</td>
													<td class="padding-left three-quarters">
														<?php echo $howHeardMethod ?>
														<?php echo $howHeardOther != null ? ' - ' . $howHeardOther : '' ?>
													</td>
												</tr>
											<?php } ?>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter">Payment Method</td>
												<td class="padding-left three-quarters">
													<?php echo $paymentMethod ?>
												</td>
											</tr>
											<?php if($isComment) { ?>
												<tr>
													<td class="gray-bar-right align-right padding-right strong one-quarter">Comments</td>
													<td class="padding-left three-quarters">
														<?php echo $team->getRegistrationComment()->getComment() ?>
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td class="light-gray-bar"></td>
							</tr>
						</table>
					</center>
				</td>
			</tr>
		</table>
	</body>
</html>