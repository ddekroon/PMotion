<?php

date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR."connect.PHP");
session_start ();

//Start actual code
if(isset($_SESSION["username"])){
	$user = $_SESSION["username"];
}else{
	$user = 'unknown';
}

$holderVar="<INPUT TYPE='hidden' NAME='user' VALUE='$user'>";

$months=array('January','February','March','April','May','June','July','August','September','October','November','December');
$query=mysql_query("SELECT * FROM registrationDates");
while($regResults =mysql_fetch_array($query)){
	$openDate=$regResults['registrationOpens'];
	$closeDate=$regResults['registrationCloses'];
	$open=explode(" ", $openDate);
	$close=explode(" ", $closeDate);
	$n=0;
	
	foreach($months AS $month){
		$n+=1;
		if($open[0]==$month){
			$regOpen=mktime(0,0,0,$n,$open[1],$open[2]);
			//$regOpen=date("z",mktime(0,0,0,$n,$open[1],$open[2]));
		}
		if($close[0]==$month){
			$regClose=mktime(0,0,0,$n,$close[1],$close[2]);
			//$regClose=date("z",mktime(0,0,0,$n,$close[1],$close[2]));
		}
	}
	
	$d=date('z');
	$dateLongVal=mktime();
	if(($dateLongVal>=$regOpen) AND ($dateLongVal<=$regClose)){
		$q=mysql_query("SELECT * FROM regcategory WHERE season='".$regResults['Season']."'");
		while($a=mysql_fetch_array($q)){
			if($a['available']!=1){
				$quer=mysql_query("UPDATE regcategory SET available=1 WHERE semi_identifier='".$a['semi_identifier']."'");
			}
		}
	}elseif(($dateLongVal<$regOpen) OR ($dateLongVal>$regClose)){
		$q=mysql_query("SELECT * FROM regcategory WHERE season='".$regResults['Season']."'");
		while($a=mysql_fetch_array($q)){
			if($a['available']==1){
				$quer=mysql_query("UPDATE regcategory SET available=0 WHERE semi_identifier='".$a['semi_identifier']."'");
			}
		}
	}
}

if (!isset ($_SESSION["username"])) { //if your variable isn't there, then the session must not be
	session_unset (); //so destroy whatever session there was and bring them to login page
	session_destroy ();
	$url = "Location: login.PHP";
	header ( $url );
} else { //otherwise, they can see the page
	$seas_array=array("","Spring","Summer","Fall", "Winter 1", "Winter 2"); //sets what each number means
	
	$quer=mysql_query("SELECT confirmationDue, registrationDue, semi_identifier FROM regcategory WHERE available=1");
	$arra=mysql_fetch_array($quer);
	$registration_due=$arra['registrationDue'];
	$confirm_date=$arra['confirmationDue'];
	$seas_array=array("", "Spring", "Summer", "Fall", "Winter 1", "Winter 2");
	$seas_number=substr($arra['semi_identifier'], 4, 1);
	$current_number=$seas_number-1;
	$seas_name=$seas_array[$current_number];
	$upcoming_seas=$seas_array[$seas_number];
		
	print $holderVar;                           
	print "<FORM NAME='members' METHOD='POST' action='delete.PHP'>";
	
	print "<font face='arial' size=2>Welcome, <B>".$user."</B>
	<font size=1>(<a href=logout.PHP style='text-decoration: none;'>Logout</a>)</font>";
	
	print "<TABLE ALIGN='center'>
	<tr><td colspan=2 align=center><font size=4 face=verdana><b>Welcome to Perpetual Motion's Online Registration System<br></b></td></tr>";
	
	$query=mysql_query("SELECT * FROM registration WHERE userName='$user'");
	if(mysql_num_rows($query) > 0){
		print "
		<tr><td colspan=3 align=center><font face=verdana size=2>
		<TABLE width = 700 align=center cellspacing=3 cellpadding=3 bgcolor='#ccccff'>
		<tr><td colspan=5 align=center><font face=verdana size=2><b><u>Previous Teams Registered<br><BR></u>";
		if($seas_name==("Spring" or "Summer" or "Winter 1")){
			print "<font size=2 color=red>$seas_name teams' confirmation date for $upcoming_seas leagues:<br>$confirm_date</B><BR>
			($seas_name teams have first priority for $upcoming_seas but must confirm registration by above date)<BR><BR>";
		}
		print "<font color=black><b>Click on your team's name to re-register a previous team for the $upcoming_seas league<hr>
		<tr><font size=1><td align='center'><input type='submit' name='delete' value='Delete'>
		<td align=center><font size=2><b>Team Name</b></td><td align=center><font size=2><b>Sport</b>
		<td align=center><font size=2><b>League</b>
		<td align=center><font size=2><b>Confirmed?(Y/N)";		
		
		$count=0;
		while ($array=mysql_fetch_array($query)){
			$count=$count+1; 
			$registeredYNBool=$array['registered'];
			if($registeredYNBool=="0"){
				$registeredYN="<b>No</b>";
			}elseif($registeredYNBool=="1"){
				$registeredYN="Yes";
			}else{
				$registeredYN="";
			}
			$sport=$array['sport'];
			if($sport=="ultimate"){$sportName="Ultimate";}
			elseif($sport=="beach"){$sportName="Beach Volleyball";}
			elseif($sport=="football"){$sportName="Flag Football";}
			elseif($sport=="soccer"){$sportName="6 vs 6 Soccer";}
			$teamName=stripslashes($array['teamName']);
			$league=stripslashes($array['league']);
			$regID=$array['registrationID'];
			
			print"<tr><td align=center><INPUT TYPE='CHECKBOX' NAME='team[]' VALUE=$regID></INPUT></td>
			<td><font size=2><a href='update.PHP?sport=$sport&registrationID=$regID'>$teamName</a></td>
			<td><font size=2>".$sportName."</td>
			<td><font size=2>".$league."</td>
			<td align=center><font size=2>$registeredYN</font></td></tr>";
			
		
		}
		print"</table>";
}// end if strlen



$title="Register for Perpetual Motion";
print"<HTML><head><title>$title</title> 
</head>
<tr><td colspan=5 align=center><font size=3 color=red><br><B>Registration due date: $registration_due<BR>
<tr><td colspan=5 align=center><br>
<font size=2>Select a league logo below to start a new registration<br>
<TR><TD align=center><a href=signup.PHP?sport=ultimate><img border=0 src='/Logos/GuelphUltimate.jpg'></a><BR>
<td align=center><a href=signup.PHP?sport=beach><img border=0 src='/Logos/WheresTheBeach.jpg'></a><BR>

<tr>
<td colspan=2 align='center'><BR><BR>
<img border=0 src='/Logos/Perpetualmotionlogo2.jpg' width='300' height='130'>

<tr>
<TD align=center><BR><a href=signup.PHP?sport=football><img border=0 src='/Logos/GuelphFlagFootball.jpg'></a>
<td align=center><BR><a href=signup.PHP?sport=soccer><img border=0 src='/Soccer/Logos/6vs6 SoccerFinal1.jpg' width=170 height=88></a>
</TABLE></HTML>";

}
?>

