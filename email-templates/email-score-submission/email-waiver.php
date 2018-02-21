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
						<table class="container align-center">
							<tr>
								<td class="light-gray-bar"></td>
							</tr>
							<tr>
								<td>
									<h2>Perpetual Motion's Online Waivers</h2>
								</td>
							</tr>
							<tr>
								<td>
									You have been registered for <span class="strong"><?php echo $team->getLeague()->getFormattedName() ?></span>
								</td>
							</tr>
							<tr>
								<td>
									Please go to our 
									<a href="http://data.perpetualmotion.org/waiver.php?sportID=<?php echo $team->getLeague()->getSport()->getId() ?>">Online Waiver</a>
									page to sign your waiver.
								</td>
							</tr>
							<tr>
								<td>
									** Please note you only need to do this once per year
								</td>
							</tr>
							<tr>
								<td>
									Thank you for signing your waiver online, have a great season!
								</td>
							</tr>
							<tr><td><br /></td></tr>
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