<?php /***************************
* Derek Dekroon
* derek@perpetualmotion.org
* June 13, 2012
* sptFunctions.php
*
* This file holds all the functions for badSpiritSource.php.
**********************************/ 

function printTeamsHeader() { ?>
	<tr>
    	<td style="width:20px;">
        	ID
        </td><td>
        	Team Name
        </td><td>
        	Captain Email
        </td><td>
        	League Name
        </td><td>
        	Wins
        </td><td>
        	Losses
        </td><td>
        	Ties
        </td><td>
        	OppW
        </td><td>
        	OppL
        </td><td>
        	OppT
        </td>
    </tr>
<?php }

function printTeamNode($curTeam) { ?>
	<tr>
    	<td style="width:20px;">
            <?php print $curTeam->teamID; ?>
		</td><td>
            <?php print "<a target='_blank' href='../Search/teamPage.php?teamID=$curTeam->teamID'>$curTeam->teamName</a>"; ?>
		</td><td>
        	<?php $body = "Hey, I was looking through our standings and noticed an issue with your score submissions on DATE. You submitted SCORE, while your opponent TEAM submitted SCORE. I cant be sure exactly what happened but any ideas you might have as to why this error has came up would be appreciated. Thanks,";
            print "<a target='_blank' href='mailto:$curTeam->teamCptnEmail?subject=Score Submissions Issue&body=$body'>".$curTeam->teamCptnEmail.'</a>'; ?>
        </td><td>
            <?php print "<a target='_blank' href='../Standings/editSubmissions.php?sportID=$curTeam->teamSportID&leagueID=$curTeam->teamLeagueID'>$curTeam->teamLeagueName</a>" ?>
        </td><td>
            <?php print $curTeam->teamWins ?>
        </td><td>
            <?php print $curTeam->teamLosses ?>
        </td><td>
            <?php print $curTeam->teamTies ?>
        </td><td>
            <?php print $curTeam->teamOppWins ?>
        </td><td>
            <?php print $curTeam->teamOppLosses ?>
        </td><td>
            <?php print $curTeam->teamOppTies ?>
        </td>
    </tr>
<?php }

function printStandingsHeader() { ?>
	<tr>
    	<td style="width:20px;">
        	ID
        </td><td>
        	Team Name
        </td><td>
        	Captain Email
        </td><td>
        	League Name
        </td><td>
        	Wins
        </td><td>
        	Losses
        </td><td>
        	Ties
        </td><td>
        	StndW
        </td><td>
        	StndL
        </td><td>
        	StndT
        </td>
    </tr>
<?php }

function printStandingsNode($curTeam) { ?>
	<tr>
    	<td style="width:20px;">
            <?php print $curTeam->teamID; ?>
		</td><td>
            <?php print "<a target='_blank' href='../Search/teamPage.php?teamID=$curTeam->teamID'>$curTeam->teamName</a>"; ?>
		</td><td>
        	<?php print "<a target='_blank' href='mailto:$curTeam->teamCptnEmail?subject=Incorrect Standings'>".$curTeam->teamCptnEmail.'</a>'; ?>
        </td><td>
            <?php print "<a target='_blank' href='../Standings/editSubmissions.php?sportID=$curTeam->teamSportID&leagueID=$curTeam->teamLeagueID'>$curTeam->teamLeagueName</a>" ?>
        </td><td>
            <?php print $curTeam->teamWins ?>
        </td><td>
            <?php print $curTeam->teamLosses ?>
        </td><td>
            <?php print $curTeam->teamTies ?>
        </td><td>
            <?php print $curTeam->teamStndWins ?>
        </td><td>
            <?php print $curTeam->teamStndLosses ?>
        </td><td>
            <?php print $curTeam->teamStndTies ?>
        </td>
    </tr>
<?php }

function printSubmissionsHeader() { ?>
	<tr>
    	<td style="width:20px;">
        	ID
        </td><td>
        	Team Name
        </td><td>
        	Captain Email
        </td><td>
        	League Name
        </td><td>
        	Wins
        </td><td>
        	Losses
        </td><td>
        	Ties
        </td><td>
        	Practices
        </td><td>
        	Cancels
        </td><td>
        	League Submissions
        </td>
    </tr>
<?php }

function printSubmissionNode($curTeam, $numSubmissions) { ?>
	<tr>
    	<td style="width:20px;">
            <?php print $curTeam->teamID; ?>
		</td><td>
            <?php print "<a target='_blank' href='../Search/teamPage.php?teamID=$curTeam->teamID'>$curTeam->teamName</a>"; ?>
		</td><td>
        	<?php $body = "Hey, I was looking through our standings and noticed an issue with your score submissions on DATE. You submitted SCORE, while your opponent TEAM submitted SCORE. I cant be sure exactly what happened but any ideas you might have as to why this error has came up would be appreciated. Thanks,";
            print "<a target='_blank' href='mailto:$curTeam->teamCptnEmail?subject=Score Submissions Issue&body=$body'>".$curTeam->teamCptnEmail.'</a>'; ?>
        </td><td>
            <?php print "<a target='_blank' href='../Standings/editSubmissions.php?sportID=$curTeam->teamSportID&leagueID=$curTeam->teamLeagueID'>$curTeam->teamLeagueName</a>" ?>
        </td><td>
            <?php print $curTeam->teamWins ?>
        </td><td>
            <?php print $curTeam->teamLosses ?>
        </td><td>
            <?php print $curTeam->teamTies ?>
        </td><td>
            <?php print $curTeam->teamPractices ?>
        </td><td>
            <?php print $curTeam->teamCancels ?>
        </td><td>
            <?php print $numSubmissions ?>
        </td>
    </tr>
<?php }

function printButtons() { ?>
    <tr>
        <td>
            <input type='submit' name='removeSubmissions' value='Remove'>
        </td><td>
            <input type='submit' name='CheckAll' value='Check All' onClick="return checkAll()" />
        </td><td>
            <input type='submit' name='UncheckAll' value='Uncheck All' onClick="return uncheckAll()" />
        </td>
    </tr>
<?php }