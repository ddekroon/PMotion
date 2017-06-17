<?php /*****************************************
File: leaguesControlPanel.php
Creator: Nick Froese
Created: May 2/2017
Program to update registration avalability table/page
******************************************/

date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once('includeFiles/groupMailFunctions.php');
require_once('includeFiles/hyperLinkFunctions.php');
require_once('../control/Leagues/includeFiles/leagControlPanelFormFunctions.php');
require_once('../control/Leagues/includeFiles/leagControlPanelSQLFunctions.php');
require_once('../control/Leagues/includeFiles/leagueClass.php');
require_once('../control/Leagues/includeFiles/seasonClass.php');
require_once('../control/Registration/includeFiles/indivVariableDeclarations.php');

//getting season variables from database
$seasonArray = mysql_fetch_array(mysql_query("SELECT season_id FROM $seasonsTable WHERE season_available_registration = 1"));
$seasonID = $seasonArray['season_id'];

$curSeason = mysql_fetch_assoc(mysql_query("SELECT season_name, season_year FROM $seasonsTable WHERE season_available_registration = 1"));
$season = $curSeason['season_name'];
$year = $curSeason['season_year'];

$leagueObjArray = getLeaguesData($seasonID);


?>

<html>
	<link rel="stylesheet" href="includeFiles/design.css" type="text/css" media="all" />
    	
				<h1 style="text-align:center;clear:both;font-family:Jockey;font-size:45px;"> <?php echo $season,' League ',$year ?></h1>

                
    <!-- Creates Tables -->
<?php    for($i=1; $i<=count($leagueObjArray);$i++){?>
    <h2 style="text-align:center;clear:both;font-family:'Trebuchet MS', Arial, Helvetica, sans-serif"> <?php if($leagueObjArray[$i][0]->leagueSportID == 1) {
					echo "<img src='../allSports/Logos/ultimate.png'>";
				} else if ($leagueObjArray[$i][0]->leagueSportID == 2){
					echo "<img src='../allSports/Logos/volleyball.png'>";
				} else if ($leagueObjArray[$i][0]->leagueSportID == 3){
					echo "<img src='../allSports/Logos/football.png'>";
				} else if ($leagueObjArray[$i][0]->leagueSportID == 4){
					echo "<img src='../allSports/Logos/soccer.png'>";
				}?> </h2>
	<table class='availability' align="center">
    	<tr>
        	<th>  </th>
            <th> Males Needed </th>
            <th> Females Needed </th>
            <th> Teams Needed </th>
        </tr>
<?php   for($j=0;$j<count($leagueObjArray[$i]);$j++){   ?>
			<tr>
            	<th> <?php printTableRows($leagueObjArray[$i][$j]->leagueSportID,$leagueObjArray[$i][$j]->leagueName); ?> </th>
				<td> <?php hyperlinkReg($leagueObjArray[$i][$j]->leagueSportID,$leagueObjArray[$i][$j]->leagueFullMales); ?></td>
                <td> <?php hyperlinkReg($leagueObjArray[$i][$j]->leagueSportID,$leagueObjArray[$i][$j]->leagueFullFemales);?> </td>
                <td> <?php hyperlinkTeam($leagueObjArray[$i][$j]->leagueSportID, $leagueObjArray[$i][$j]->leagueFullTeams);?> </td>
			</tr>


<?php } ?>       

    </table>
<?php    }  ?>
</html>

