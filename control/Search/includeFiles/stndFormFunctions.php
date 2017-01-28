<?php function printLeagueHeader($leagueHasTies, $sortByPercent) { 
	global $sportName, $leagueName, $leagueDayString, $leagueWeekNum, $dateDescription; ?>
    <tr>
		<th colspan=9>
			<?php print $sportName.' - '.$leagueName.' - '.$leagueDayString.' (Week '.$leagueWeekNum.' - '.$dateDescription.')';?>
		</th>
	</tr><tr>
        <td colspan=2>
            Team
        </td><td>
            Win
        </td><td>
            Loss
        </td><td>
			Tie
		</td><td>
            Points
        </td><td>
			Win Pct
		</td><td>
            Spirit Avg.
        </td><td>
            Given
        </td>
    </tr>
<?php }

function printLeagueNode($curTeam, $i) { 
	global $hideSpirit, $leagueWeekNum, $teamMaxWeek, $leagueWeekNum, $sortByPercent, $leagueHasTies ?>
	<tr>
        <td>
            <?php print $i+1?>
            <input type='hidden' name='teamID[]' value='<?php print $curTeam->teamID; ?>' />
        </td><td style="text-align:left; <?php print $curTeam->teamTitleWidth ?>">
            <font style='font-size:<?php print $curTeam->teamTitleSize ?>'>
            <?php 
			if($curTeam->teamDroppedOut == 0)
			{
				print '<a href="http://data.perpetualmotion.org/control/Search/teamPage.php?teamID='.$curTeam->teamID.'">'.$curTeam->teamName.'</a>';
			}
			
			if ($curTeam->teamSubmittedWeek < $leagueWeekNum && $teamMaxWeek >= $leagueWeekNum) 
			{
				print ' **';
			}?>
            </font>
        </td><td>
            <input type='text' name='teamWins[]' id='standings' value='<?php print $curTeam->teamWins; ?>' />
        </td><td>
            <input type='text' name='teamLosses[]' id='standings' value='<?php print $curTeam->teamLosses; ?>' />
        </td><td>
			<input type='text' name='teamTies[]' id='standings' value='<?php print $curTeam->teamTies; ?>' />
		</td><td>
            <?php print $curTeam->teamPoints?>
        </td><td>
			<?php print number_format($curTeam->teamWinPercent, 3, '.', '')?>
		</td><td>
            <?php if($curTeam->teamSpiritAverage > 0) {
				print number_format($curTeam->teamSpiritAverage, 2, '.', '');
			} else {
				print 'N/A';
			}?>
        </td><td>
            <?php print $curTeam->teamSpiritAvgGiven(); ?>
        </td>
    </tr>
<?php }

function printTotals($totalWins, $totalLosses, $totalTies, $teamsObj) {
	global $leagueHasTies, $sortByPercent; ?>
    <tr>
        <th colspan=2>TOTALS </th><th>
            <?php print $totalWins; ?>
        </th><th>
            <?php print $totalLosses; ?>
        </th><th>
			<?php print $totalTies;?>
		</th><th><!-- Points --></th><th><!-- Win Pct --></th><th>
        	<?php $totalSpirit = 0;
			for($i=0 ;$i<count($teamsObj); $i++) {
				$totalSpirit = $totalSpirit + $teamsObj[$i]->teamSpiritAverage;
			}
			print count($teamsObj) != 0?number_format($totalSpirit/count($teamsObj), 2, '.', ''):'N/A' ?>
        </th><th>
        <?php $totalSpiritGiven = 0;
			for($i=0 ;$i<count($teamsObj); $i++) {
				$totalSpiritGiven = $totalSpiritGiven + $teamsObj[$i]->teamSpiritAvgGiven();
			}
			print count($teamsObj) != 0?number_format($totalSpiritGiven/count($teamsObj), 2, '.', ''):'N/A' ?>
		</th>
    </tr>
<?php }

function printBottomButton() { ?>
	<tr>
        <td colspan=9>
			<input type="submit" name="submitStandings" value="Submit Standings" onclick="return confirm('Are you sure you want to change these standings?')" />
		</td>
	</tr>
<?php }