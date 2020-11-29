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
						<table class="container align-center" style="border: none; border-collapse: collapse; color: #494e52; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 655px;">
							<tr>
								<td class="light-gray-bar" style="background: #d8d8d8; height: 3px; margin: 0; padding: 0; vertical-align: top;"></td>
							</tr>
							<tr>
								<td style="margin: 0; padding: 7px; vertical-align: top;">
									<h2 style="font-size: 16px; font-weight: 600; margin: 10px 0 20px; text-transform: uppercase;">Perpetual Motion's Online Waivers</h2>
								</td>
							</tr>
							<tr>
								<td style="margin: 0; padding: 7px; vertical-align: top;">
									You have been registered for <span class="strong" style="font-weight: 700;"><?php echo $team->getLeague()->getFormattedName() ?></span>
								</td>
							</tr>
							<tr>
								<td style="margin: 0; padding: 7px; vertical-align: top;">
									Please go to our 
									<a href="https://data.perpetualmotion.org/waiver.php?sportID=<?php echo $team->getLeague()->getSport()->getId() ?>" style="color: #ed1c24; text-decoration: none;">Online Waiver</a>
									page to sign your waiver.
								</td>
							</tr>
							<tr>
								<td style="margin: 0; padding: 7px; vertical-align: top;">
									** Please note you only need to do this once per year
								</td>
							</tr>
							<tr>
								<td style="margin: 0; padding: 7px; vertical-align: top;">
									Thank you for signing your waiver online, have a great season!
								</td>
							</tr>
							<tr><td style="margin: 0; padding: 7px; vertical-align: top;"><br></td></tr>
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