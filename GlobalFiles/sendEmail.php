<?php 
require_once("class.phpmailer.php");
require_once("class.smtp.php");

$host = 'ssl://smtp.gmail.com:465';
//$host = 'ssl://smtp.mandrillapp.com:465';
//$username = 'dave@perpetualmotion.org';
//$password = 'mUUYvLkPBEyL2w5Ubw1fOA';

function sendEmails($toAddresses, $fromAddress, $subject, $body) {
	global $host, $username, $password;

	$mailer = new PHPMailer();
	$mailer->IsSMTP();
	$mailer->Host = $host;
	//$mailer->SMTPSecure = 'ssl';  
	//$mailer->Port = 465;
	//$mailer->SMTPDebug = 1;
	$mailer->SMTPAuth = TRUE;
	$mailer->isHTML();
	//$mailer->Username = $username;
	//$mailer->Password = $password;
	
	if($fromAddress=='dave@perpetualmotion.org') {
		$mailer->Username = 'dave@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Dave Kelly';	
	} else if($fromAddress=='info@perpetualmotion.org') {
		$mailer->Username = 'info@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Info';
	} else if($fromAddress=='scores@perpetualmotion.org') {
		$mailer->Username = 'scores@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Score Reporter';
	} else if($fromAddress=='terry@perpetualmotion.org'){
		$mailer->Username = 'terry@perpetualmotion.org';
		$mailer->Password = 'tepha9321';
		$mailer->FromName = 'Terry Pham';
	} else if($fromAddress=='nick@perpetualmotion.org'){
		$mailer->Username = 'nick@perpetualmotion.org';
		$mailer->Password = 'Guelph2014';
		$mailer->FromName = 'Nick Froese';
	}else {
		print 'ERROR invalid fromAddress<br />';
		exit(0);
	}
	$mailer->From = $fromAddress;
	$mailer->FromName = $fromAddress;
	$mailer->Body = $body;
	$mailer->Subject = $subject;
	
	for($i = 0; $i < count($toAddresses); $i++) {
		if(filter_var($toAddresses[$i], FILTER_VALIDATE_EMAIL)) {
			$mailer->ClearAddresses();
			$mailer->AddAddress($toAddresses[$i]);
			if($mailer->Send()) {
				//print "Message sent to: ".$toAddresses[$i];
			} else {
				//print "<font style='color:red;'>Message <b>NOT</b> sent to:</font> ".$toAddresses[$i];
			}
			//print '<br />'.str_replace("\n", '', substr($body, 0, 200)).'<br /><br />';
		} else {
			print 'Invalid Email Address - '.$toAddresses[$i].'<br /><br />';
		}
	}
}

function sendEmailsNotSMTP($toAddresses, $fromAddress, $subject, $body) {
	global $host, $username, $password;
	
	$searchArray = array('@hotmail', '@yahoo', '@outlook', '@live', '@gmail');
	
	$mailer = new PHPMailer();
	$mailer->Host = $host;
	//$mailer->SMTPSecure = 'ssl';  
	//$mailer->Port = 465;
	$mailer->isHTML();
	//$mailer->Username = $username;
	//$mailer->Password = $password;
	
	if($fromAddress=='dave@perpetualmotion.org') {
		$mailer->Username = 'dave@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Dave Kelly';	
	} else if($fromAddress=='info@perpetualmotion.org') {
		$mailer->Username = 'info@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Info';
	} else if($fromAddress=='scores@perpetualmotion.org') {
		$mailer->Username = 'scores@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Score Reporter';
	} else if($fromAddress=='terry@perpetualmotion.org'){
		$mailer->Username = 'terry@perpetualmotion.org';
		$mailer->Password = 'tepha9321';
		$mailer->FromName = 'Terry Pham';
	} else if($fromAddress=='nick@perpetualmotion.org'){
		$mailer->Username = 'nick@perpetualmotion.org';
		$mailer->Password = 'Guelph2014';
		$mailer->FromName = 'Nick Froese';
	} else {
		print 'ERROR invalid fromAddress<br />';
		exit(0);
	}
	$mailer->From = $fromAddress;
	$mailer->FromName = $fromAddress;
	$mailer->Body = $body;
	$mailer->Subject = $subject;

	
	for($i = 0; $i < count($toAddresses); $i++) {
		if(filter_var($toAddresses[$i], FILTER_VALIDATE_EMAIL)) {
			
			$mailer->ClearAddresses();
			$mailer->AddAddress($toAddresses[$i]);
			if($mailer->Send()) {
				print "Message sent to: ".$toAddresses[$i];
			} else {
				print "<font style='color:red;'>Message <b>NOT</b> sent to:</font> ".$toAddresses[$i];
			}
			print '<br />'.str_replace("\n", '', substr($body, 0, 200)).'<br /><br />';
		} else {
			print 'Invalid Email Address - '.$toAddresses[$i].'<br /><br />';
		}
	}
}

function sendEmailsBcc($toAddresses, $fromAddress, $subject, $body) {
	global $host, $username, $password;
	$mailer = new PHPMailer();
	$mailer->IsSMTP();
	$mailer->Host = $host;
	//$mailer->SMTPDebug = 1;
	$mailer->SMTPAuth = TRUE;
	$mailer->isHTML();
	//$mailer->Username = $username;
	//$mailer->Password = $password;
	
	if($fromAddress=='dave@perpetualmotion.org') {
		$mailer->Username = 'dave@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Dave Kelly';	
	} else if($fromAddress=='info@perpetualmotion.org') {
		$mailer->Username = 'info@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Info';
	} else if($fromAddress=='scores@perpetualmotion.org') {
		$mailer->Username = 'scores@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Score Reporter';
	} else if($fromAddress=='terry@perpetualmotion.org'){
		$mailer->Username = 'terry@perpetualmotion.org';
		$mailer->Password = 'tepha9321';
		$mailer->FromName = 'Terry Pham';
	} else if($fromAddress=='nick@perpetualmotion.org'){
		$mailer->Username = 'nick@perpetualmotion.org';
		$mailer->Password = 'Guelph2014';
		$mailer->FromName = 'Nick Froese';
	} else {
		print 'ERROR invalid fromAddress<br />';
		exit(0);
	}
	$mailer->From = $fromAddress;
	$mailer->Body = $body;
	$mailer->Subject = $subject;
	$mailer->ClearAddresses();
	for($i = 0; $i < count($toAddresses); $i++) {
		if(filter_var($toAddresses[$i], FILTER_VALIDATE_EMAIL)) {
			$mailer->AddBCC($toAddresses[$i]);
		}
	}
	if($mailer->Send()) {
		$mailer->SmtpClose();
		//print "Message sent to: ";
		for($i = 0; $i < count($toAddresses); $i++) {
			if(filter_var($toAddresses[$i], FILTER_VALIDATE_EMAIL)) {
				//print $toAddresses[$i].', ';
			}
		}
	} else {
		//print "<font style='color:red;'>Message <b>NOT</b> sent to:</font> ".$toAddresses[$i];
	}
	//print '<br>'.str_replace("\n", '', substr($body, 0, 100)).'<br><br>';
}

function sendAdminEmails($toAddresses, $fromAddress, $subject, $body) {
	global $host, $username, $password;
	
	$mailer = new PHPMailer();
	//$mailer->IsSMTP();
	$mailer->Host = $host;
	//$mailer->SMTPDebug = 1;
	//$mailer->SMTPAuth = TRUE;
	$mailer->isHTML();
	//$mailer->Username = $username;
	//$mailer->Password = $password;
	
	if($fromAddress=='dave@perpetualmotion.org') {
		$mailer->Username = 'dave@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Dave Kelly';	
	} else if($fromAddress=='info@perpetualmotion.org') {
		$mailer->Username = 'info@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Info';
	} else if($fromAddress=='scores@perpetualmotion.org') {
		$mailer->Username = 'scores@perpetualmotion.org';
		$mailer->Password = 'djkdjkdjk';
		$mailer->FromName = 'Score Reporter';
	} else if($fromAddress=='terry@perpetualmotion.org'){
		$mailer->Username = 'terry@perpetualmotion.org';
		$mailer->Password = 'tepha9321';
		$mailer->FromName = 'Terry Pham';
	} else if($fromAddress=='nick@perpetualmotion.org'){
		$mailer->Username = 'nick@perpetualmotion.org';
		$mailer->Password = 'Guelph2014';
		$mailer->FromName = 'Nick Froese';
	} else {
		print 'ERROR invalid fromAddress<br />';
		exit(0);
	}
	
	$mailer->From = $fromAddress;
	$mailer->Body = $body;
	$mailer->Subject = $subject;
	$mailer->ClearAddresses();
	for($i = 0; $i < count($toAddresses); $i++) {
		$mailer->AddAddress($toAddresses[$i]);
	}
	$mailer->Send();
	$mailer->SmtpClose();
}