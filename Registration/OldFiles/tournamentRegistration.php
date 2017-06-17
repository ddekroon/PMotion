<?php
	require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'connect.php');
	date_default_timezone_set('America/New_York');
	
    $teamName="";
	$cityRepresenting="";
	$tournament="";
	$division="";
	$rating="";
	$teamCaptain="";
	$captainEmail="";
	$captainPhone="";
	$captainAddress="";
	$captainCity="";
	$captainProvince="";
	$captainPostalCode="";
    
	$tournament=$_GET['tournament'];
	//VARIABLE DECLARATIONS (BASED ON WHAT SPORT WAS CHOSEN) OCCUR IN THIS FILE
	//NOTE: This is where the logo is defined, as well
	require_once('tournamentDeclarations.PHP');

	$query=mysql_query("SELECT * FROM tournamentRegistration ".$filter);
	$arr=mysql_fetch_array($query);
	$tournamentDate=$arr['tournamentDate'];
	$registrationDue=$arr['registrationDeadline'];

	if(isset($_POST['submit'])){ 
		global $err, $teamName, $tournament, $division, $rating, $teamCaptain, $captainEmail, $captainPhone, $captainAddress, $captainCity, $cityRepresenting, $captainProvince, $captainPostalCode;
		$teamName=$_POST['teamName'];
		$tournament=$_POST['tournament'];
		$division=$_POST['division'];
		$rating=$_POST['rating'];
		$teamCaptain=$_POST['capName'];
		$captainEmail=$_POST['capEmail'];
		$captainPhone=$_POST['capPhone'];
		$captainAddress=$_POST['capAddress'];
		$captainCity=$_POST['capCity'];
		$cityRepresenting=$_POST['cityRepresenting'];
		$captainProvince=$_POST['province'];
		$captainPostalCode=$_POST['postalCode'];
		$teamName=addslashes($teamName);
		$tournament=addslashes($tournament);
		$division=addslashes($division);
		$teamCaptain=addslashes($teamCaptain);
		$captainEmail=addslashes($captainEmail);
		$captainAddress=addslashes($captainAddress);
		$captainCity=addslashes($captainCity);
		$cityRepresenting=addslashes($cityRepresenting);
		$captainProvince=addslashes($captainProvince);
		$captainPostalCode=addslashes($captainPostalCode);
		submit();
	}
	
	function submit(){
		global $err, $teamName, $tournament, $division, $rating, $teamCaptain, $captainEmail, $captainPhone, $captainAddress, $captainCity, $cityRepresenting, $captainProvince, $captainPostalCode;
		errorCheck();
		if((strlen($err))<=2){
			enterIntoDB();
			sendEmail($tournament);
			header ("location: thankyoureg.htm");
		}
	}
	
	function enterIntoDB(){
		global $err, $teamName, $tournament, $division, $rating, $teamCaptain, $captainEmail, $captainPhone, $captainAddress, $captainCity, $cityRepresenting, $captainProvince, $captainPostalCode;
		$divisionNameQuery=mysql_query("SELECT * FROM tournamentRegistration WHERE divisionID=$division");
		$divisionNameArray=mysql_fetch_array($divisionNameQuery);
		$divisionName=$divisionNameArray['divisionName'];
		$tournamentDate=$divisionNameArray['tournamentDate'];
		
			
		if($tournament=="wheresthebeach"){
			if($division==1){$league="Comp 4s";}
			if($division==2){$league="Int 4s";}
			if($division==3){$league="Int 6s";}
			if($division==4){$league="Rec 6s";}
			$preSelect=mysql_query("SELECT * FROM wheresthebeach WHERE league='$league' AND teamName=''");
			$number=mysql_num_rows($preSelect);
			if(($number==0) OR ($number==FALSE)){
				$numQ=mysql_query("SELECT * FROM wheresthebeach WHERE league='$league'");
				$num=0;
				while($numA=mysql_fetch_array($numQ)){
					$num+=1;
				}
				$num=$num+1;
				$update=mysql_query("INSERT INTO wheresthebeach (number, league, teamName, captain, rating, paid, captainEmail, captainAddress, captainPhone, captainCity, province,
					postalCode) VALUES ($num, '$league', '$teamName', '$teamCaptain', '$rating', 'No', '$captainEmail', '$captainAddress', '$captainPhone', '$captainCity', '$captainProvince',
					'$captainPostalCode')");
				$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode,
					cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
					'$captainPostalCode', '$cityRepresenting')") or die(" ERROR ! : ".mysql_error());
			}else{
				while($array=mysql_fetch_array($preSelect)){
					$update=mysql_query("UPDATE wheresthebeach SET teamName='$teamName', captain='$teamCaptain', rating='$rating',  paid='No', captainEmail='$captainEmail',
						captainAddress='$captainAddress', captainPhone='$captainPhone', captainCity='$captainCity', province='$captainProvince', postalCode='$captainPostalCode' 
						WHERE number=".$array['number']);
					$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode,
						cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
						'$captainPostalCode', '$cityRepresenting')") or die(" ERROR ! : ".mysql_error());
					break;
				}
			}
		}
		elseif($tournament=="stallfall"){
			if($division==101){$league="A";}
			if($division==102){$league="B";}
			$preSelect=mysql_query("SELECT * FROM stallfall WHERE division='$league' AND teamName=''");
			$number=mysql_num_rows($preSelect);
			$num=0;
			if(($number==0) OR ($number==FALSE)){
				$numQ=mysql_query("SELECT * FROM stallfall WHERE division='$league'");
				while($numA=mysql_fetch_array($numQ)){
					$num+=1;
				}
				$num=$num+1;
				
				$update=mysql_query("INSERT INTO stallfall (division, teamName, captain, rating, paid, captainEmail, captainAddress, captainPhone, captainCity, province, postalCode,
					cityRepresenting) VALUES ('$league', '$teamName', '$teamCaptain', '$rating', 'No', '$captainEmail', '$captainAddress', '$captainPhone', '$captainCity', '$captainProvince',
					'$captainPostalCode', '$cityRepresenting')") or die(" ERROR TEST ! : ".mysql_error());
				
				$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode,
					cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
					'$captainPostalCode', '$cityRepresenting')");
			}else{
				while($array=mysql_fetch_array($preSelect)){
					$update=mysql_query("UPDATE stallfall SET teamName='$teamName', captain='$teamCaptain', rating='$rating', paid='No', captainEmail='$captainEmail',
						captainAddress='$captainAddress', captainPhone='$captainPhone', captainCity='$captainCity', province='$captainProvince', postalCode='$captainPostalCode',
						cityRepresenting='$cityRepresenting' WHERE number=".$array['number']." AND division='".$league."'");
					$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode,
						cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
						'$captainPostalCode', '$cityRepresenting')") or die(" ERROR ! : ".mysql_error());
					break;
				}
			}
		}elseif($tournament=="beaverbowl"){
			$preSelect=mysql_query("SELECT * FROM beaverbowl WHERE teamName=''");
			$number=mysql_num_rows($preSelect);
			$num=0;
			if(($number==0) OR ($number==FALSE)){
				$numQ=mysql_query("SELECT * FROM beaverbowl");
				while($numA=mysql_fetch_array($numQ)){
					$num+=1;
				}
				$num=$num+1;
				$update=mysql_query("INSERT INTO beaverbowl (number, teamName, captain, rating, paid, captainEmail, captainAddress, captainPhone, captainCity, province, postalCode) 
					VALUES ($num, '$teamName', '$teamCaptain', '$rating', 'No', '$captainEmail', '$captainAddress', '$captainPhone', '$captainCity', '$captainProvince', 
					'$captainPostalCode')") or die(" ERROR ! : ".mysql_error());
				$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode,
					cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
					'$captainPostalCode', '$cityRepresenting')") or die(" ERROR ! : ".mysql_error());
			}else{
				while($array=mysql_fetch_array($preSelect)){
					$update=mysql_query("UPDATE beaverbowl SET teamName='$teamName', captain='$teamCaptain', rating='$rating', paid='No', captainEmail='$captainEmail',
						captainAddress='$captainAddress', captainPhone='$captainPhone', captainCity='$captainCity', province='$captainProvince', postalCode='$captainPostalCode' 
						WHERE number=".$array['number']);
					$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode,
						cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
						'$captainPostalCode', '$cityRepresenting')") or die(" ERROR ! : ".mysql_error());
					break;
				}
			}
		}elseif($tournament=="worldcup"){
			$preSelect=mysql_query("SELECT * FROM worldcup WHERE teamName=''");
			$number=mysql_num_rows($preSelect);
			if(($number==0) OR ($number==FALSE)){
				$numQ=mysql_query("SELECT * FROM worldcup");
				$num=0;
				while($numA=mysql_fetch_array($numQ)){
					$num+=1;
				}
				$num=$num+1;
				$update=mysql_query("INSERT INTO worldcup (number, teamName, captain, rating, paid, captainEmail, captainAddress, captainPhone, captainCity, province, postalCode) VALUES 
					($num, '$teamName', '$teamCaptain', '$rating', 'No', '$captainEmail', '$captainAddress', '$captainPhone', '$captainCity', '$captainProvince', '$captainPostalCode')")
					or die(" ERROR TESTING! : ".mysql_error());
				$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode,
					cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
					'$captainPostalCode', '$cityRepresenting')") or die(" ERROR TEST! : ".mysql_error());
			}else{
				while($array=mysql_fetch_array($preSelect)){
					$update=mysql_query("UPDATE worldcup SET teamName='$teamName', captain='$teamCaptain', rating='$rating', paid='No', captainEmail='$captainEmail',
						captainAddress='$captainAddress', captainPhone='$captainPhone', captainCity='$captainCity', province='$captainProvince', postalCode='$captainPostalCode' 
						WHERE number=".$array['number']) or die(" ERROR NOTESTING! : ".mysql_error());
					$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode,
						cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
						'$captainPostalCode', '$cityRepresenting')") or die(" ERROR TEST! : ".mysql_error());
					break;
				}
			}
		}elseif($tournament=="dodgeball"){
			$preSelect=mysql_query("SELECT * FROM dodgeball WHERE teamName=''");
			$number=mysql_num_rows($preSelect);
			if(($number==0) OR ($number==FALSE)){ //this SHOULD mean there are no rows in tournaments, I guess it just means more rows are to be added... too much manual stuff for comfort
				$numQ=mysql_query("SELECT * FROM dodgeball");
				$num = mysql_num_rows(numQ) + 1;
				$update=mysql_query("INSERT INTO dodgeball (number, teamName, captain, rating, paid, captainEmail, captainAddress, captainPhone, captainCity, province, postalCode) VALUES
					($num, '$teamName', '$teamCaptain', '$rating', 'No', '$captainEmail', '$captainAddress', '$captainPhone', '$captainCity', '$captainProvince', '$captainPostalCode')");
				$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode,
					cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
					'$captainPostalCode', '$cityRepresenting')") or die(" ERROR ! : ".mysql_error());
			}else{ //there are rows available (open rows) so the team should now be inserted?
				$array=mysql_fetch_array($preSelect);
				$update=mysql_query("UPDATE dodgeball SET teamName='$teamName', captain='$teamCaptain', rating='$rating', paid='No', captainEmail='$captainEmail', 
					captainAddress= '$captainAddress', captainPhone='$captainPhone', captainCity='$captainCity', province='$captainProvince', postalCode='$captainPostalCode' 
					WHERE number=".$array['number']);
				$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode,
					cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
					'$captainPostalCode', '$cityRepresenting')") or die(" ERROR ! : ".mysql_error());
			}
		}elseif($tournament=="discgolf"){
			$preSelect=mysql_query("SELECT * FROM discgolf WHERE teamName=''");
			$number=mysql_num_rows($preSelect);
			if(($number==0) OR ($number==FALSE)){
				$numQ=mysql_query("SELECT * FROM discgolf");
				while($numA=mysql_fetch_array($numQ)){
					$num=$num+1;
				}
				$num=$num+1;
				$update=mysql_query("INSERT INTO discgolf (number, teamName, captain, rating, paid, captainEmail, captainAddress, captainPhone, captainCity, province, postalCode, male1, male2)
					VALUES ($num, '$teamName', '$teamCaptain', '$rating', 'No', '$captainEmail', '$captainAddress', '$captainPhone', '$captainCity', '$captainProvince', '$captainPostalCode',
					'$male1', '$male2')");
				$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode, 
					cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
					'$captainPostalCode', '$cityRepresenting')") or die(" ERROR ! : ".mysql_error());
			}else{
				while($array=mysql_fetch_array($preSelect)){
					$update=mysql_query("UPDATE discgolf SET teamName='$teamName', captain='$teamCaptain', rating='$rating', paid='No', captainEmail='$captainEmail',
						captainAddress='$captainAddress', captainPhone='$captainPhone', captainCity='$captainCity', province='$captainProvince', postalCode='$captainPostalCode',
						male1='$name1', male2='$name2' WHERE number=".$array['number']);
					$insert=mysql_query("INSERT INTO tournamentSignups (teamName, division, captain, captainEmail, captainPhone, captainAddress, captainCity, province, postalCode,
						cityRepresenting) VALUES ('$teamName', $division, '$teamCaptain', '$captainEmail', '$captainPhone', '$captainAddress', '$captainCity', '$captainProvince',
						'$captainPostalCode', '$cityRepresenting')") or die(" ERROR ! : ".mysql_error());
					break;
				}
			}
		}
	}
		
	
	function body(){
        global $err, $teamName, $tournament, $division, $rating, $teamCaptain, $captainEmail, $captainPhone; 
		global $captainAddress, $captainCity, $cityRepresenting, $captainProvince, $captainPostalCode;

        $teamName=stripslashes($teamName);
        $tournament=stripslashes($tournament);
        $division=stripslashes($division);
        $rating=stripslashes($rating);
        $teamCaptain=stripslashes($teamCaptain);
        $captainEmail=stripslashes($captainEmail);
        $captainPhone=stripslashes($captainPhone);
        $captainAddress=stripslashes($captainAddress);
        $date=date('r');
		$captainProvince=stripslashes($captainProvince);
		$cityRepresenting=stripslashes($cityRepresenting);
		$captainCity=stripslashes($captainCity);
		$captainPostalCode=stripslashes($captainPostalCode);

        $body="";

        $body.="<TR><TD colspan=2 align=center>-----------------Captain's Information-------------------";
        $body.="<TR><TD><B>Captain's Name:</B><td>".$teamCaptain;
		$body.="<TR><TD><B>Email:</b><td>".$captainEmail;
		$body.="<TR><TD><B>Phone Number:</b><td>".$captainPhone;
		$body.="<tr><TD><B>Mailing Address:</B>";
        $body.="<tr><td>&nbsp;<TD>".$captainAddress."<BR>".$captainCity.", ".$captainProvince."<BR>".$captainPostalCode;
        $body.="<tr><td><BR>";
		$body.="<tr><td>Received:<td>".$date."</TABLE>";
        return $body;
		
	}//end function

	function sendEmail($tournament){
		global $err, $teamName, $division, $rating, $teamCaptain, $captainEmail, $captainPhone, $captainAddress, $captainCity, $cityRepresenting, $captainProvince, $captainPostalCode;
		
		$title="<font size=3><TABLE align=center cellspacing=2 cellpadding=2 width='500'>";
        $title.="<tr><td colspan=2 align=center><B>Perpetual Motion's Online Registration System<BR>-- Registration Confirmation --</B><BR>";
		
		if($tournament=="wheresthebeach"){
			$tournamentName="Where's the Beach Volleyball Tournament";
			if($division==1){$league="- Competitive 4's Division";}
			if($division==2){$league="- Intermediate 4's Division";}
			if($division==3){$league="- Intermediate 6's Division";}
			if($division==4){$league="- Recreational 6's Division";}
		}elseif($tournament=="stallfall"){
			$tournamentName="Stall Fall Ultimate Tournament";
			if($division==101){$league="- A Division";}
			if($division==102){$league="- B Division";}
		}elseif($tournament=="beaverbowl"){
			$tournamentName="Beaver Bowl Flag Football Tournament";
			if($division==101){$league="";}
		}elseif($tournament=="worldcup"){
			$tournamentName="Guelph 6 vs 6 Soccer World Cup Tournament";
			if($division==101){$league="";}
		}elseif($tournament=="dodgeball"){
			$tournamentName="Perpetual Motion's Guelph Dodgeball Tournament";
			if($division==101){$league="";}
		}elseif($tournament=="discgolf"){
			$tournamentName="Perpetual Motion's Guelph Disc Golf Tournament";
			if($division==101){$league="";}
		}
		$secondary="<BR>New Team Registered for <br><B>".$tournamentName." ".$league."</B> ";
		$body=body();
        $teamLine="<TR><td align=center colspan=2><b>Team Name:</b> ".stripslashes($teamName)."<BR><b>Rating: </b>".stripslashes($rating);
        if($tournament=="stallfall"){
			$teamLine.="<BR><b>City Representing: </b>".stripslashes($cityRepresenting)."<BR><BR>";
		}
		$message=$title.$secondary.$teamLine.$body;
        $to=$captainEmail;
        $subject="Registration Confirmation - ".stripslashes(html_entity_decode($teamName, ENT_QUOTES));
        $altSubject="TournamentReg - ".stripslashes(html_entity_decode($teamName, ENT_QUOTES))." - ".stripslashes(html_entity_decode($tournamentName, ENT_QUOTES));


        $from_head  = 'MIME-Version: 1.0' . "\r\n";
        $from_head .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $from_head .= 'Content-Transfer-Encoding: base64' . "\r\n";
        $from_head .= 'From: info@perpetualmotion.org';

        mail($to, $subject,  rtrim(chunk_split(base64_encode($message))), $from_head);
        mail('dave@perpetualmotion.org',  $altSubject, rtrim(chunk_split(base64_encode($message))), $from_head);
        mail('zach@perpetualmotion.org', $altSubject, rtrim(chunk_split(base64_encode($message))), $from_head);
		mail('derek@perpetualmotion.org', $altSubject, rtrim(chunk_split(base64_encode($message))), $from_head);

	}//end function

	function errorCheck(){
        global $err, $teamName, $tournament, $division, $rating, $teamCaptain, $captainEmail, $captainPhone, $captainAddress, $captainCity, $cityRepresenting;
		global $captainProvince, $captainPostalCode;
		
        $err=""; //Set err = nothing
		if($teamName==""){  $err.="Please enter a team name.<BR>";}    
		if($tournament=="stallfall"){if($cityRepresenting==""){   $err.="Please enter the city your team will be representing.<BR>";}}
		if($division=="nullVal"){  $err.="Please select a division.<BR>";}     
		if($teamCaptain==""){      $err.="Please enter the captain's name.<BR>";}           
		if($captainEmail==""){    $err.="Please enter an email address.<BR>";}   
		//if($captainPhone==""){  $err.="Please enter a phone number.<BR>";}
		//if($captainAddress==""){  $err.="Please enter a street address.<BR>";}
		//if($captainCity==""){  $err.="Please enter a city.<BR>";}
		//if($captainProvince==""){  $err.="Please enter a province.<BR>";}
		//if($captainPostalCode==""){  $err.="Please enter a postal code.<BR>";}
		return $err;
	}//end function ?>
	
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Register for Perpetual Motion Tournaments</title>
	
		<style type="text/css">
		<!--
		.default {
			font-family: Verdana, Geneva, sans-serif;
			font-size: 12px;
			font-weight:bold;
		}
		.default1 {
			font-family: Verdana, Geneva, sans-serif;
			font-size: 12px;
		}
		body p {
			text-align: center;
		}
		.h3 {
			text-align: center;
			font-family: Verdana, Geneva, sans-serif;
			font-size: 14px;
		}
		.strong1 {
			color: #F00;
		}
		-->
		</style>
		 
	</head>
    <body>
		<form name="tournamentRegister" action="<?php echo $_SERVER['PHP_SELF'];?>?tournament=<?php print $tournament;?>" method="post">
        	<table align="center" width="60%">
				<tr>
            		<td align="center">
                		<img src="<?php print $logo;?>" width="<?php print $logoWidth;?>" height="<?php print $logoHeight;?>" alt="Logo" />
                    </td>
                </tr>
            </table>
            <h3 class="h3"><?php print $sportHeader;?></h3>  
            <p class="default" align="center">Tournament date: <?php print $tournamentDate;?></p>
            <?php 
            	if($err!=""){ print "<p align=center><font color=red>".$err."</font></p>";}
            ?>
            <p class="default1" align="center">
            <table border="0" align="center">
                <tr>
                    <td align="left" class="default" scope="row">Team Name:<?php if($tournament=='worldcup'){print"<BR>(country to represent)";}?></td>
                    <td><input type='text' name='teamName' size=40 value="<?php print $teamName?>"></td>
                </tr>
                <?php if($tournament=="stallfall"){?>
                    <tr>
                        <td align="left" class="default" scope="row">City Representing:</td>
                        <td><input type='text' name='cityRepresenting' size=40 value="<?php print $cityRepresenting?>"></td>
                    </tr>
                <?php }?>
                <tr>
                    <td align="left" class="default" scope="row">Division:</td>
                    <td align="center">
                        <?php 
                            $divisionDD="<select name='division'>";
                            $divisionDD.="<option value='nullVal'>Choose Division</option>";
                                $q=mysql_query("SELECT * FROM tournamentRegistration WHERE tournament='".$tournament."'");
                                while($array= mysql_fetch_array($q)){
                                    if($array['divisionID']==$division){
										$divisionDD.= "<option SELECTED value='$array[divisionID]'>$array[divisionName]</option>";
									} else {
										$divisionDD.= "<option value='$array[divisionID]'>$array[divisionName]</option>";
									}
                                }//end while
                                $divisionDD.= "</select>";
                                print $divisionDD."<BR>";?>
                    </td>
                </tr>
                
                <tr>
                    <td align="left" class="default" scope="row">How would you rate your team?</td>
                    <td align="center"><select name="rating">
                        <option value="nullVal">Rating</option>
                        <?php if($rating==1){?><option SELECTED value="1"><?php }else{?><option value="1"><?php }?>1 (lowest)</option>
                        <?php if($rating==2){?><option SELECTED value="2"><?php }else{?><option value="2"><?php }?>2</option>
                        <?php if($rating==3){?><option SELECTED value="3"><?php }else{?><option value="3"><?php }?>3</option>
                        <?php if($rating==4){?><option SELECTED value="4"><?php }else{?><option value="4"><?php }?>4</option>
                        <?php if($rating==5){?><option SELECTED value="5"><?php }else{?><option value="5"><?php }?>5</option>
                        <?php if($rating==6){?><option SELECTED value="6"><?php }else{?><option value="6"><?php }?>6</option>
                        <?php if($rating==7){?><option SELECTED value="7"><?php }else{?><option value="7"><?php }?>7</option>
                        <?php if($rating==8){?><option SELECTED value="8"><?php }else{?><option value="8"><?php }?>8</option>
                        <?php if($rating==9){?><option SELECTED value="9"><?php }else{?><option value="9"><?php }?>9</option>
                        <?php if($rating==10){?><option SELECTED value="10"><?php }else{?><option value="10"><?php }?>10 (highest)</option>
                    </select></td>
                </tr>
                
                <tr>
                    <td	 align="left" class="default" scope="row">Team Captain:</td>
                    <td><input type='text' name='capName' size=40 value="<?php print $teamCaptain?>"></td>
                </tr>
                <tr>
                    <td align="left" class="default" scope="row">Email Address:</td>
                    <td><input type='text' name='capEmail' size=40 value="<?php print $captainEmail?>"></td>
                </tr>
                <tr>
                    <td align="left" class="default" scope="row">Phone Number:</td>
                    <td><input type='text' name='capPhone' size=40 value="<?php print $captainPhone?>"></td>
                </tr>
                <tr>
                    <td align="left" class="default" scope="row">Street Address:</td>
                    <td><input type='text' name='capAddress' size=40 value="<?php print $captainAddress?>"></td>
                </tr>
                <tr>
                    <td align="left" class="default" scope="row">City:</td>
                    <td><input type='text' name='capCity' size=40 value="<?php print $captainCity?>"></td>
                </tr>
                <tr>
                    <td align="left" class="default" scope="row">Province:</td>
                    <td><input type='text' name='province' size=40 value="<?php print $captainProvince?>"></td>
                </tr>
                <tr>
                    <td align="left" class="default" scope="row">Postal Code:</td>
                    <td><input type='text' name='postalCode' size=40 value="<?php print $captainPostalCode?>"></td>
                </tr>
            
            	<?php 
					if($tournament=="discgolf"){?>
                		<tr>
                        	<td colspan=2 align=center><strong>Player Information</strong></td>
                        </tr>
                    	<tr>
                        	<td scope="col">&nbsp;</td>
                        	<td><table width="100%"><tr><td width="60%">Name</td>
                        	<td>Skill Level</td></tr></table></td>
                    	</tr>
                    	<tr>
                        	<td scope="row" class="default">Golfer 1:</td>
                        	<td>
                            	<table>
                                	<tr>
                                    	<td><input type='text' name='male1Name' size=20/></td>
                                        <td>
                                            <select name="male1rating">
                                                <option value="nullVal">Rating</option>
                                                <option value="1">1 (lowest)</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10 (highest)</option>
                                            </select>
                                        </td>
 			                       </tr>
                    			</table>
                            </td>
                    	</tr>
                    	<tr>
                        	<td scope="row" class="default">Golfer 2:</td>
                        	<td>
                            	<table>
                                	<tr>
                                    	<td><input type='text' name='male2Name' size=20/></td>
                                        <td>
                                            <select name="male2rating">
                                                <option value="nullVal">Rating</option>
                                                <option value="1">1 (lowest)</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10 (highest)</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <?php 
                        }//end if
                    ?>
                    <tr>
                        <td colspan=2 align=center><input type="submit" name="submit" value="Submit" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2><p class="default">Please make cheques payable to <strong>Perpetual Motion or send an email transfer to dave@perpetualmotion.org</strong></p>
                            <p class="default">Registration deadline: <strong><span class="strong1"><?php print $registrationDue;?></span></strong></p>
                            <p class="default1"><strong>Mail or drop off registration form and entry fees to:</strong><br />
                            Perpetual Motion<br />
                            223 Waterloo Ave<br />
                            Guelph, Ontario<br />
                            N1H 3J4<br />
                            <a href="http://www.perpetualmotion.org/Maps/office.html">(map)</a>
                            <br />
                            <br />
                            (519) 222 - 0095
                            </p>
                        </td>
                    </tr>
                </table>
            <INPUT TYPE='hidden' name='tournament' value='<?php print $tournament; ?>'>
        </form>							
    </body>
</html>