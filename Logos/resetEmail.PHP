<?php

/*
Variables and Declarations for connecting to the database server
*/

$dbservertype='mysql';
$servername='mysql.giantgoat.dreamhosters.com';
$dbusername='pmotiondata';
$dbpassword='rememberHeavysalmon4';
$dbname='data_perpetualmotion';

connecttodb($servername,$dbname,$dbusername,$dbpassword);
function connecttodb($servername,$dbname,$dbuser,$dbpassword){
global $link;
$link=mysql_connect ("$servername","$dbuser","$dbpassword");
if(!$link){die("Could not connect to MySQL");}
mysql_select_db("$dbname",$link) or die ("could not open db".mysql_error());
}
//////// End of connecting to database ////////



$error="";


function gen ($start, $end){
srand ((double) microtime( )*1000000);
$random_number = rand($start,$end);
}//end function gen


function body($user, $reason, $emailAddress){
$updateCode="";
$updateCode.="".chr(rand(65,90)); //Capital letter
$updateCode.="".chr(rand(65,90)); //Capital letter
$updateCode.="".chr(rand(97,122)); //Small letter
$updateCode.="".chr(rand(48,57));  //number
$updateCode.="".chr(rand(48,57));  //number
$updateCode.="".chr(rand(97,122)); //small letter
$pQuery=mysql_query("UPDATE users SET updateVerifyCode= '".$updateCode."' where username='".$user."'");

if($reason=="ResetPassword"){$ttl="PASSWORD CHANGE / RESET INSTRUCTIONS";}
elseif($reason=="ResetUserName"){$ttl="USER NAME CHANGE / RESET INSTRUCTIONS";}
$message="** - ".$ttl." - **
<BR><BR>To reset your user information, please click the link below and fill in the required information.
<BR><BR>User Name: <B>".$user."</B>
<BR>Validation Code: <B>".$updateCode."</B>
<BR><br>
<a href=http://www.perpetualmotion.org/Registration/resetInfo.PHP?username=".$user."&reason=".$reason."&email=".$emailAddress.">
http://www.perpetualmotion.org/Registration/resetInfo.PHP?username=".$user."&reason=".$reason."&email=".$emailAddress."</a>
<BR><BR>
Thanks,
<BR>The Perpetual Motion Team.";

return $message;
}//end function body

if(isset($_GET['reason'])){$reason=$_GET['reason'];}else{$reason='UNKNOWN';}

$holderVariable="<FORM NAME='resetEmail' METHOD='POST' action=".$_SERVER['PHP_SELF']."?reason=".$reason.">";

if($reason=="ResetPassword"){
     $title="Password Change / Update Function";
     $subject="Password Change Instructions";
     $reasonActual="Reset Password";
}elseif($reason=="ResetUserName"){
     $title="User Name Change / Update Function";
     $subject="User Name Change Instructions";
     $reasonActual="Reset User Name";
}

if(!isset($_POST['submit'])){
     print $holderVariable;
     $headTitle=$reasonActual." - Perpetual Motion's Online Registration System";
     print "<html><head><title>$headTitle</title></head>
     <font size=3 face='arial'><TABLE align=center>
     <tr><td colspan=2 align=center><img src='/Logos/Perpetualmotionlogo2.jpg' width='300' height='130'></td></tr>
     <tr><td colspan=2 align=center><font size=4><B>".$title."</B></td></tr>
     <tr><td colspan=2 align=center><BR>Please enter the email address you used when registering to receive further instructions</td></tr>
     <tr><td colspan=2 align=center>
     <table align=center>";
     print "<tr><td><b>Reason:</b></td><td><input type='text' name='reasonActual' maxlength='50' value='".$reasonActual."' disabled></td></tr>";
     print"<tr><td><B>Email Address:</b></td><td><input type='text' name='email' maxlength='50'></td></tr></table>
     </td></tr>";
     print'<tr><td colspan=2 align=center><br><input type="submit" name="submit" value="Submit"></td></tr></table>';
     print "<input type='hidden' name='reason' maxlength='50' value='".$reason."'>";
     print"</html>";
}

else{
     $email=$_POST['email'];
     $eQuery=mysql_query('SELECT * FROM users where email="'.$email.'"');
     $eArray=mysql_fetch_array($eQuery);
     $emailDatabase=$eArray['email'];
     $to=stripslashes($emailDatabase);
     if(isset($emailDatabase)){
          $usernameDatabase=$eArray['username'];
          $subject=$subject." - ".$usernameDatabase;
          $body=body($usernameDatabase, $reason, $emailDatabase);
          $from_head  = 'MIME-Version: 1.0' . "\r\n";
          $from_head .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
          $from_head .= 'From: info@perpetualmotion.org';
          mail($to, $subject, $body, $from_head);
          header("Location: passwordNote1.htm");
     }else{$error="Error: Email not registered in our database";
          print $holderVariable;
          $headTitle=$reasonActual." - Perpetual Motion's Online Registration System";
          print "<html><head><title>$headTitle</title></head>
          <font size=3 face='arial'><TABLE align=center>
          <tr><td colspan=2 align=center><img src='/Logos/Perpetualmotionlogo2.jpg' width='300' height='130'></td></tr>
          <tr><td colspan=2 align=center><font size=4><B>".$title."</B></td></tr>
          <tr><td colspan=2 align=center><font size=2 color=red>$error</font></td></tr>
          <tr><td colspan=2 align=center>Please enter the email address you used when registering to receive further instructions</td></tr>
          <tr><td colspan=2 align=center>
          <table align=center>
          <tr><td><b>Reason:</b></td><td><input type='text' name='reasonActual' maxlength='50' value='".$reasonActual."' disabled></td></tr>";
          print "<tr><td><B>Email Address:</b></td><td><input type='text' name='email' maxlength='50' value='".$_POST['email']."'></td></tr></table>
          </td></tr>";
          print'<tr><td colspan=2 align=center><br><input type="submit" name="submit" value="Submit"></td></tr></table>';
          print "<input type='hidden' name='reason' maxlength='50' value='".$reason."'></html>";
     }
}//end if isset

?>
