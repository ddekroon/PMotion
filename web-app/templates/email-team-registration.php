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
								<td class="light-gray-bar" style="background: #d8d8d8; height: 3px; margin: 0; padding: 0; vertical-align: top;"></td>
							</tr>
							<tr>
								<td class="align-center" style="margin: 0; padding: 7px; text-align: center; vertical-align: top;">
									<h2 style="font-size: 16px; font-weight: 600; margin: 10px 0 20px; text-transform: uppercase;">Perpetual Motion Online Registration System</h2>
									<p style="font-size: 13px; font-weight: 400; margin-bottom: 10px; padding: 0;">Registration Confirmation</p>
								</td>
							</tr>
							<tr>
								<td class="light-gray-bar" style="background: #d8d8d8; height: 3px; margin: 0; padding: 0; vertical-align: top;"></td>
							</tr>
							<tr>
								<td style="margin: 0; padding: 7px; vertical-align: top;">
									<table style="border: none; border-collapse: collapse; color: #494e52; margin: 0; padding: 0; vertical-align: top; width: 100%;">
										<tbody>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">League</td>
												<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
													<?php echo $team->getLeague()->getSport()->getName() . ' - ' . $team->getLeague()->getRegistrationFormattedName() ?>
												</td>
											</tr>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">
													<?php $returningString ?> Team Name
												</td>
												<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
													<?php echo $team->getName() ?>
												</td>
											</tr>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Date</td>
												<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
													<?php echo $team->getDateCreated()->format('F j, Y g:ia'); ?>
												</td>
											</tr>
											<?php if($adminEmail) { ?>
												<tr>
													<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">TeamID</td>
													<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
														<?php echo $team->getId(); ?>
													</td>
												</tr>
											<?php } ?>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Captain</td>
												<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
													<?php echo $captain->getFirstName() . ' ' . $captain->getLastName() . ' (' . $captain->getGender() . ')' ?><br>
													<?php echo $captain->getEmail() ?><br>
													<?php echo Includes_Helper::formatPhoneNumber($captain->getPhoneNumber()) ?>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td class="light-gray-bar" style="background: #d8d8d8; height: 3px; margin: 0; padding: 0; vertical-align: top;"></td>
							</tr>
							<tr>
								<td style="margin: 0; padding: 7px; vertical-align: top;">
									<table style="border: none; border-collapse: collapse; color: #494e52; margin: 0; padding: 0; vertical-align: top; width: 100%;">
										<tbody>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Players</td>
												<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
													<?php if($team->getPlayers() != null && !empty($team->getPlayers())) { ?>
														<?php $curPlayerNum = 1; ?>
														<table class="align-center" style="border: none; border-collapse: collapse; color: #494e52; margin: 0; padding: 0; text-align: center; vertical-align: top; width: 100%;">
															<tr><th></th><th>First Name</th><th>Last Name</th><th>Email</th><th>Phone</th><th>Gender</th></tr>
															<?php foreach($team->getPlayers() as $curPlayer) { ?>
																<?php if($curPlayer->getFirstName() != '' 
																		|| $curPlayer->getLastName() != ''
																		|| $curPlayer->getEmail() != '') { ?>
																	<tr>
																		<td style="margin: 0; padding: 7px; vertical-align: top;"><?php echo $curPlayerNum++ ?></td>
																		<td style="margin: 0; padding: 7px; vertical-align: top;"><?php echo $curPlayer->getFirstName() ?></td>
																		<td style="margin: 0; padding: 7px; vertical-align: top;"><?php echo $curPlayer->getLastName() ?></td>
																		<td style="margin: 0; padding: 7px; vertical-align: top;"><?php echo $curPlayer->getEmail() ?></td>
																		<td style="margin: 0; padding: 7px; vertical-align: top;"><?php echo Includes_Helper::formatPhoneNumber($curPlayer->getPhoneNumber()) ?></td>
																		<td style="margin: 0; padding: 7px; vertical-align: top;"><?php echo $curPlayer->getGender() ?></td>
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
								<td class="light-gray-bar" style="background: #d8d8d8; height: 3px; margin: 0; padding: 0; vertical-align: top;"></td>
							</tr>
							<tr>
								<td style="margin: 0; padding: 7px; vertical-align: top;">
									<table style="border: none; border-collapse: collapse; color: #494e52; margin: 0; padding: 0; vertical-align: top; width: 100%;">
										<tbody>
											<?php if($adminEmail && $howHeardMethod != null) { ?>
												<tr>
													<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">How They Heard About Us</td>
													<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
														<?php echo $howHeardMethod ?>
														<?php echo $howHeardOther != null ? ' - ' . $howHeardOther : '' ?>
													</td>
												</tr>
											<?php } ?>
											<tr>
												<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Payment Method</td>
												<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
													<?php echo $paymentMethod ?>
												</td>
											</tr>
											<?php if($isComment) { ?>
												<tr>
													<td class="gray-bar-right align-right padding-right strong one-quarter" style="border-right: 3px solid #494e52; font-weight: 700; margin: 0; padding: 7px; padding-right: 20px; text-align: right; vertical-align: top; width: 25%;">Comments</td>
													<td class="padding-left three-quarters" style="margin: 0; padding: 7px; padding-left: 20px; vertical-align: top; width: 75%;">
														<?php echo $team->getRegistrationComment()->getComment() ?>
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td class="light-gray-bar" style="background: #d8d8d8; height: 3px; margin: 0; padding: 0; vertical-align: top;"></td>
							</tr>
						</table>
					</center>
				</td>
			</tr>
		</table>
	</body>
</html>