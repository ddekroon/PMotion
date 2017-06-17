<?php


/*
Variables and Declarations for connecting to the database server
*/

$dbservertype='mysql';
$servername='mysql.giantgoat.dreamhosters.com';
$dbusername='pmotiondata';
$dbpassword='rememberHeavysalmon4';
$dbname='data_perpetualmotion';


print"<FORM NAME='reset' action='".$_SERVER['PHP_SELF']."' method='post'>";

connecttodb($servername,$dbname,$dbusername,$dbpassword);
function connecttodb($servername,$dbname,$dbuser,$dbpassword){
global $link;
$link=mysql_connect ("$servername","$dbuser","$dbpassword");
if(!$link){die("Could not connect to MySQL");}
mysql_select_db("$dbname",$link) or die ("could not open db".mysql_error());
}
//////// End of connecting to database ////////

if(isset($_GET['username'])){$user = $_GET['username'];}else{$user = 'unknown';}
	

if(isset($_POST['submit'])){
$user=$_POST['user'];
$val=$_POST['validationDatabase'];
print"<INPUT TYPE='hidden' name='user' value='".$user."'>";
print"<INPUT TYPE='hidden' name='validationDatabase' value='".$val."'>";

//checks that the password is between 6 and 16 characters
$passLength=strlen($_POST['pass']);
if(($passLength<=5)||($passLength>=17)){
$error.="<BR>Password must be between 6 and 16 characters.";
}

// this makes sure both passwords entered match
if ($_POST['pass'] != $_POST['pass2']) {
$error.='<BR>Your passwords did not match.';
}

// this makes sure both passwords entered match
if ($_POST['validation'] != $_POST['validationDatabase']) {
$error.='<BR>Your validation key did not match the assigned validation key.';
}

if((strlen($error))<3){
     $_POST['pass'] = md5($_POST['pass']);
     $_POST['pass'] = addslashes($_POST['pass']);
     $_POST['validation'] = addslashes($_POST['validation']);
     $_POST['pass2'] = addslashes($_POST['pass2']);
     $update=mysql_query("UPDATE users SET password='".$_POST['pass']."' WHERE username='".$user."'");
     header("Location: passwordNote2.htm");
}//end if
else{
print "<font size=3 face='arial'><TABLE align=center>
<tr><td colspan=2 align=center><img src='../Logos/Perpetualmotionlogo2.jpg' width='300' height='130'></td></tr>
<tr><td colspan=2 align=center><font size=4><B>Password Change / Update Function</B></td></tr>
<tr><td colspan=2 align=center><BR>Set New Password</td></tr>
<tr><td colspan=2 align=center>".$error."</td></tr>
<tr><td colspan=2 align=center>
<table align=center>
<tr><td><B>Username:</b></td><td>".$user."</td></tr>
<tr><td><B>New Password:</B></td><td><input type='password' name='pass' maxlength='16'></td></tr>
<tr><td><B>Confirm Password:</B></td><td><input type='password' name='pass2' maxlength='16'></td></tr>
<tr><td><B>Validation Key:</b><br><font size=1>Refer to email</font></td>
<td><input type='text' name='validation' maxlength='6'></td></tr></table>
</tr></td>
<tr><td colspan=2 align=center><br><input type='submit' name='submit' value='Submit'></td></tr></table>";
}//end else
}//end if
else{ //if nothing was selected

print "<font size=3 face='arial'><TABLE align=center>
<tr><td colspan=2 align=center><img src='../Logos/Perpetualmotionlogo2.jpg' width='300' height='130'></td></tr>
<tr><td colspan=2 align=center><font size=4><B>Password Change / Update Function</B></td></tr>
<tr><td colspan=2 align=center><BR>Set New Password</td></tr>
<tr><td colspan=2 align=center>
<table align=center>
<tr><td><B>Username:</b></td><td>".$user."</td></tr>
<tr><td><B>New Password:</B></td><td><input type='password' name='pass' maxlength='16'></td></tr>
<tr><td><B>Confirm Password:</B></td><td><input type='password' name='pass2' maxlength='16'></td></tr>
<tr><td><B>Validation Key:</b><br><font size=1>Refer to email</font></td>
<td><input type='text' name='validation' maxlength='6'></td></tr></table>
</tr></td>
<tr><td colspan=2 align=center><br><input type='submit' name='submit' value='Submit'></td></tr></table>";
print"<INPUT TYPE='hidden' name='user' value='".$user."'>";
$q=mysql_query("SELECT updateVerifyCode FROM users WHERE username= '".$user."'");
$a=mysql_fetch_array($q);
$val=$a['updateVerifyCode'];
print"<INPUT TYPE='hidden' name='validationDatabase' value='".$val."'>";
}
?>
