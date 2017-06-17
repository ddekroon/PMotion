<?php
session_start ();
$dbservertype='mysql';
$servername='mysql.giantgoat.dreamhosters.com';
// username and password to log onto db server
$dbusername='pmotiondata';
$dbpassword='rememberHeavysalmon4';
// name of database
$dbname='data_perpetualmotion';

////////////////////////////////////////
////// DONOT EDIT BELOW  /////////
///////////////////////////////////////
connecttodb($servername,$dbname,$dbusername,$dbpassword);
function connecttodb($servername,$dbname,$dbuser,$dbpassword)
{
global $link;
$link=mysql_connect ("$servername","$dbuser","$dbpassword");
if(!$link){die("Could not connect to MySQL");}
mysql_select_db("$dbname",$link) or die ("could not open db".mysql_error());
}
//////// End of connecting to database ////////
$title="Delete Teams - Perpetual Motion's Registration System";
if(isset($_SESSION["username"])){$user = $_SESSION["username"];}else{$user = 'unknown';}
if (!isset ($_SESSION["username"])) //if your variable isn't there, then the session must not be
{
session_unset (); //so destroy whatever session there was and bring them to login page
session_destroy ();
$url = "Location: login.PHP";
header ($url);
}
else //otherwise, they can see the page
{

   if(isset($_POST['cancel'])){ //if cancel selected, back to members page
   $url = "Location: members.PHP";
   header ($url);
   }elseif(isset($_POST['deleteTeam'])){ //if delete, delete from database
   $rID=$_POST['identification'];
   $h=count($rID);
   $j=0;
   while($j<$h){
   $remove=mysql_query("DELETE FROM captains WHERE registrationID=".$rID[$j]);
   $remove=mysql_query("DELETE FROM registration WHERE registrationID=".$rID[$j]);
   $remove=mysql_query("DELETE FROM players WHERE registrationID=".$rID[$j]);
   $j++;
   }//end while
   header("location: members.PHP");
   }else{ //if neither selected, show page
?>
   


   <form name='confirmDelete' action="<?php echo $_SERVER['PHP_SELF']?>" method='POST'>


   <?php $delete = $_POST['team'];
   $n        = count($delete);
   if ($n<=0){header("location: members.PHP");} //if none were selected, loop back to members page
   $i        = 0;
   ?>
   <INPUT TYPE='hidden' NAME='user' VALUE='$user'>
   <TABLE align=center><tr><td colspan=3 align=center><FONT size=4 face='verdana'><b>Confirm Team Removal<b><br></td></tr>
   <tr><td colspan=3 align=center><font size=2 face=verdana>Are you sure you wish to delete the following <?php print $n?> teams?<br>
   <font size=1>By selecting yes, the teams will be permanently removed from the database.</td></tr>
   <tr><td colspan=3 align=center><font size=2 face=verdana><br>
   <TABLE width=600 align=center cellspacing=3 cellpadding=3 bgcolor='#ccccff'>
   <tr><td align=center><font size=2 face=verdana><b>Team Name</td>
   <td align=center><font size=2 face=verdana><b>Sport</td>
   <td align=center><font size=2 face=verdana><b> League</td></tr>

   <?php while ($i < $n)
   {
      $query= mysql_query("SELECT * from registration WHERE registrationID=$delete[$i]");
      $array= mysql_fetch_array($query);
      
      $tName= $array['teamName'];
      $sport=$array['sport'];
        if($sport=="ultimate"){$sportName="Ultimate";}
        elseif($sport=="beach"){$sportName="Beach Volleyball";}
        elseif($sport=="football"){$sportName="Flag Football";}
        elseif($sport=="soccer"){$sportName="6 vs 6 Soccer";}
      $league= $array['league'];
      ?>
      <tr><td><font size=2 face=verdana><?php print $tName?></td>
      <td><font size=2 face=verdana><?php print $sportName?></td>
      <td><font size=2 face=verdana><?php print $league?></td></tr>
      <input type='HIDDEN' name='identification[]' value=<?php print $delete[$i]?>>
      <?php $i++;
   }?>
   </table></tr>
   <tr><td align='center'><INPUT TYPE='SUBMIT' name='cancel' value='Cancel'></td>
   <td align='left'><INPUT TYPE='submit' name='deleteTeam' value='Delete'></td></tr>
   </TABLE>
   </form>
   <?php
   }// end else, no buttons were selected
}// End else they can see the page
print "</html>";
?>
