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


global $email; 
if(isset($_GET['username'])){$user = $_GET['username'];}else{$user = 'unknown';}
if(isset($_GET['reason'])){$reason = $_GET['reason'];}else{$reason = 'unknown';}
if(isset($_GET['email'])){$email = $_GET['email'];}else{$email = 'unknown';}

$holderVariable="";
$holderVariable.="<FORM NAME='reset' action='".$_SERVER['PHP_SELF']."?username=".$user."&reason=".$reason."&email=".$email."' method='post'>";


global $error;        

function body($user, $reason, $password){

if($reason=="ResetPassword"){
$ttl="Password Reset";
$message="** ".$ttl." **
<BR><BR>Below is your new login information:
<BR><BR>Username: <B>".$user."</B>
<BR>Password: <B>".$password."</B>
<BR><br>
Thanks,
<BR>The Perpetual Motion Team.";
}
elseif($reason=="ResetUserName"){
$ttl="User Name Reset";
$message="** ".$ttl." **
<BR><BR>Below is your new user name information:
<BR><BR>Username: <B>".$user."</B>
<BR><br>
Thanks,
<BR>The Perpetual Motion Team.";
}


return $message;
}//end function body


if(isset($_POST['submit'])){
$user=$_POST['user'];
$val=$_POST['validationDatabase'];
$reason=$_POST['reason'];
$email=$_POST['email'];

if($reason=="ResetPassword"){
     $title="Password Change / Update Function";
     $subject="Password Changed";
     $reasonActual="Reset Password";
     $item="Password";
     $formBody="<tr><td><B>Username:</b></td><td>".$user."</td></tr>
     <tr><td><B>New Password:</B></td><td><input type='password' name='pass' maxlength='16'></td></tr>
     <tr><td><B>Confirm Password:</B></td><td><input type='password' name='pass2' maxlength='16'></td></tr>";

}elseif($reason=="ResetUserName"){
     $title="User Name Change / Update Function<BR>
     <font size=2>Note: Please use a memorable user name, the login is case sensitive.<font>";
     $subject="User Name Changed";
     $reasonActual="Reset User Name";
     $item="User Name";
     $formBody="<tr><td><B>Current Username:</b></td><td><input type='text' name='user' maxlength='16' value='".$user."' disabled></td></tr>
     <tr><td><B>New User Name:</B></td><td><input type='text' name='newUser' maxlength='16'></td></tr>";
}

$holderVariable.="<INPUT TYPE='hidden' name='user' value='".$user."'>";
$holderVariable.="<INPUT TYPE='hidden' name='validationDatabase' value='".$val."'>";
$holderVariable.="<INPUT TYPE='hidden' name='reason' value='".$reason."'>";
$holderVariable.="<INPUT TYPE='hidden' name='email' value='".$email."'>";


if($reason=="ResetPassword"){
     //checks that the password is between 6 and 16 characters
     $passLength=strlen($_POST['pass']);
     if(($passLength<=5)||($passLength>=17)){
     $error.="<BR>Password must be between 6 and 16 characters.";
     }

     // this makes sure both passwords entered match
     if ($_POST['pass'] != $_POST['pass2']) {
     $error.='<BR>Your passwords did not match.';
     }

     // this makes sure validation key entered matches the database
     if ($_POST['validation'] != $_POST['validationDatabase']) {
     $error.='<BR>Your validation key did not match the assigned validation key.';
     }
}elseif($reason=="ResetUserName"){
     // checks if the username is in use
     if (!get_magic_quotes_gpc()) {
     $_POST['newUser'] = addslashes($_POST['newUser']);
     }
     $usercheck = $_POST['newUser'];
     $check = mysql_query("SELECT username FROM users WHERE username = '$usercheck'")
     or die(mysql_error());
     $check2 = mysql_num_rows($check);

     //if the name exists it gives an error
     if ($check2 != 0) {
     $error.='<BR>The username "'.$_POST['newUser'].'" is already in use.';
     }
     //checks that the user name is between 6 and 16 characters
     $userLength=strlen($_POST['newUser']);
     if(($userLength<=5)||($userLength>=17)){
     $error.="<BR>Username must be between 6 and 16 characters.";
     }
     
     // this makes sure validation key entered matches the database
     if ($_POST['validation'] != $_POST['validationDatabase']) {
     $error.='<BR>Your validation key did not match the assigned validation key.';
     }
     
}


if(((strlen($error))<3)and($reason=="ResetPassword")){
     global $email;
     $email=stripslashes($email);
     $passwordEmail = $_POST['pass'];
     $_POST['pass'] = md5($_POST['pass']);
     $_POST['pass'] = addslashes($_POST['pass']);
     $_POST['validation'] = addslashes($_POST['validation']);
     $_POST['pass2'] = addslashes($_POST['pass2']);
     $update=mysql_query("UPDATE users SET password='".$_POST['pass']."' WHERE username='".$user."'");
     $subject=$subject." - ".$user;
     $body=body($user, $reason, $passwordEmail);
     $from_head  = 'MIME-Version: 1.0' . "\r\n";
     $from_head .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
     $from_head .= 'From: info@perpetualmotion.org';
     mail($email, $subject, $body, $from_head);
     header("Location: passwordNote2.htm");
}//end if
elseif(((strlen($error))<3)and($reason=="ResetUserName")){
     global $email;
     $email=stripslashes($email);
     $_POST['newUser'] = addslashes($_POST['newUser']);
     $_POST['validation'] = addslashes($_POST['validation']);
     $update=mysql_query("UPDATE users SET username='".$_POST['newUser']."' WHERE username='".$user."'");
     $update2=mysql_query("UPDATE registration SET username='".$_POST['newUser']."' WHERE username='".$user."'");
     $subject=$subject." - ".$user;
     $passwordEmail="IGNORE";
     $body=body($user, $reason, $passwordEmail);
     $from_head  = 'MIME-Version: 1.0' . "\r\n";
     $from_head .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
     $from_head .= 'From: info@perpetualmotion.org';
     mail($email, $subject, $body, $from_head);
     header("Location: userNote2.htm");
}//end elseif
else{

$headTitle= "Confirm ".$reasonActual." - Perpetual Motion's Online Registration System";
print $holderVariable;
print "<html><head><title>$headTitle</title></head><font size=3 face='arial'><TABLE align=center>
<tr><td colspan=2 align=center><img src='/logos/Perpetualmotionlogo2.jpg' width='300' height='130'></td></tr>
<tr><td colspan=2 align=center><font size=4><B>".$title."</B></td></tr>
<tr><td colspan=2 align=center><BR>Set New ".$item."</td></tr>
<tr><td colspan=1 align=center><font color=red>".$error."</font></td></tr>
<tr><td colspan=2 align=center>
<table align=center>";
print $formBody;
print"<tr><td><B>Validation Key:</b><br><font size=1>Refer to email, case sensitive</font></td>
<td><input type='text' name='validation' maxlength='6'></td></tr></table>
</tr></td>
<tr><td colspan=2 align=center><br><input type='submit' name='submit' value='Submit'></td></tr></table></html>";
}//end else
}//end if
else{ //if nothing was selected

if($reason=="ResetPassword"){
     $title="Password Change / Update Function";
     $subject="Password Changed";
     $reasonActual="Reset Password";
     $item="Password";
     $formBody="<tr><td><B>Username:</b></td><td>".$user."</td></tr>
     <tr><td><B>New Password:</B></td><td><input type='password' name='pass' maxlength='16'></td></tr>
     <tr><td><B>Confirm Password:</B></td><td><input type='password' name='pass2' maxlength='16'></td></tr>";

}elseif($reason=="ResetUserName"){
     $title="User Name Change / Update Function
     <BR><font size=2>Note: Please enter a memorable user name, the login is case sensitive.</font>";
     $subject="User Name Changed";
     $reasonActual="Reset User Name";
     $item="User Name";
     $formBody="<tr><td><B>Current Username:</b></td><td><input type='text' name='user' maxlength='16' value='".$user."' disabled></td></tr>
     <tr><td><B>New User Name:</B></td><td><input type='text' name='newUser' maxlength='16'></td></tr>";
}
$headTitle="Confirm ".$reasonActual." - Perpetual Motion's Online Registration System";
print $holderVariable;
print "<html><head><title>$headTitle</title></head>
<font size=3 face='arial'><TABLE align=center>
<tr><td colspan=2 align=center><img src='/Logos/Perpetualmotionlogo2.jpg' width='300' height='130'></td></tr>
<tr><td colspan=2 align=center><font size=4><B>".$title."</B></td></tr>
<tr><td colspan=2 align=center><BR>Set New ".$item."</td></tr>
<tr><td colspan=2 align=center>
<table align=center>";
print $formBody;
print"<tr><td><B>Validation Key:</b><br><font size=1>Refer to email, case sensitive</font></td>
<td><input type='text' name='validation' maxlength='6'></td></tr></table>
</tr></td>
<tr><td colspan=2 align=center><br><input type='submit' name='submit' value='Submit'></td></tr></table>";

print"<INPUT TYPE='hidden' name='user' value='".$user."'>";
$q=mysql_query("SELECT updateVerifyCode FROM users WHERE username= '".$user."'");
$a=mysql_fetch_array($q);
$val=$a['updateVerifyCode'];
print"<INPUT TYPE='hidden' name='validationDatabase' value='".$val."'>";
print"<INPUT TYPE='hidden' name='reason' value='".$reason."'>";
print"<INPUT TYPE='hidden' name='email' value='".$email."'>
</html>";
}
?>
