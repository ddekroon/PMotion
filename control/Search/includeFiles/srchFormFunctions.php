<?php function printTeamsHeader() { ?>
	<tr>
        <th colspan=9>
            Teams Table
        </td>
    </tr><tr>
        <td>
            ID
        </td><td>
            Team Name
        </td><td>
            Edit
        </td><td>
            Spt
        </td><td>
            League
        </td><td>
            Captain
        </td><td>
            Email
        </td><td>
            Phone
        </td><td>
            User Name
        </td>
    </tr>

<?php }

function printTeamInfo($curTeam) { ?>
	<tr>
        <td>
            <?php print $curTeam->teamID ?>
        </td><td>
            <?php print "<a href=teamPage.php?teamID=$curTeam->teamID>".$curTeam->teamName.'</a>'; ?>
        </td><td>
            <?php print "<a href=../Teams/editTeam.php?sportID=$curTeam->teamSportID&leagueID=$curTeam->teamLeagueID&teamID=$curTeam->teamID>Edit</a>"; ?>
        </td><td>
            <?php print "<a href=../Teams/addSpirit.php?sportID=$curTeam->teamSportID&leagueID=$curTeam->teamLeagueID&teamID=$curTeam->teamID>Add</a>"; ?>
        </td><td>
            <?php print $curTeam->teamLeagueName ?>
        </td><td>
            <?php print $curTeam->teamCaptainName ?>
        </td><td>
            <span style="font-size:9px;"><?php print "<a target='_blank' href=mailto:$curTeam->teamCaptainEmail>".$curTeam->teamCaptainEmail.'</a>' ?></span>
        </td><td>
            <?php print $curTeam->teamCaptainPhoneNum ?>
        </td><td>
            <?php print $curTeam->teamUserName ?>
        </td>
    </tr>
<?php }

function printPlayersHeader() { ?>
	<tr>
        <th colspan=5>
            Players Table
        </th>
    </tr><tr>
        <td>
            ID
        </td><td>
            Player Name
        </td><td>
            Team Name
        </td><td>
            League
        </td><td>
            Email
        </td>
    </tr>
<?php }

function printPlayerInfo($curPlayer) { ?>
	<tr>
        <td>
            <?php print $curPlayer->playerID ?>
        </td><td>
        <?php print "<a href='../Players/updatePlayer.php?sportID=$curPlayer->playerSportID&leagueID=$curPlayer->playerLeagueID&teamID=$curPlayer->playerTeamID&playerID=$curPlayer->playerID'>"
			.$curPlayer->playerFirstName.' '.$curPlayer->playerLastName.'</a>' ?>
        </td><td>
            <?php print $curPlayer->playerTeamName ?>
        </td><td>
            <?php print $curPlayer->playerLeagueName ?>
        </td><td>
            <?php print "<a target='_blank' href='mailto:$curPlayer->playerEmail'>".$curPlayer->playerEmail.'</a>' ?>
        </td>
    </tr>
<?php }

function printUserHeader() { ?>
	<tr>
        <th colspan=4>
            Users With that Email
        </th>
    </tr><tr>
        <td>
            ID
        </td><td>
            Player Name
        </td><td>
            Username
        </td><td>
            Email
        </td>
    </tr>
<?php }

function printUserInfo($curPlayer) { ?>
	<tr>
        <td>
            <?php print $curPlayer->playerID ?>
        </td><td>
        	<?php print $curPlayer->playerFirstName.' '.$curPlayer->playerLastName?>
        </td><td>
            <?php print $curPlayer->playerUserName ?>
        </td><td>
            <?php print "<a target='_blank' href='mailto:$curPlayer->playerEmail'>".$curPlayer->playerEmail.'</a>' ?>
        </td>
    </tr>
<?php }