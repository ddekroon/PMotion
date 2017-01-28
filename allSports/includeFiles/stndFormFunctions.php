<?php

function printLeagueTopInfo($leagueObj, $dateDescription) { ?>
	<table class="titleBox">
		<tr>
        	<tr>
                <th align="right">
                    <button type='Submit' class='no-print' name='printit' value='Print Form' onclick='javascript:window.print();'><img src='printButton.png' style="width:30px;height:25px;"></button>
                </th>
            </tr><tr>
                <th>
                    <?php print $leagueObj->sport_name.' - '.$leagueObj->league_name.' - '.getWeekday($leagueObj->league_day_number);?>
                    
                </th>
            </tr>
		</tr><tr>
			<td align="center">
				<?php print 'Week '.$leagueObj->league_week_in_standings.' - '.$dateDescription?>
			</td>
		</tr>
	</table>
<?php }

function printStandingsTable($leagueObj, $teams, $leagueID) { ?>
	<table class='activeStandingsTable' style="width:90%;max-width:450px;">
		<?php printLeagueHeader($leagueObj);
		if(count($teams) > 0) 
		{
			$teamCount = 0;
			foreach($teams as $team) 
			{
				if ($team->team_league_id == $leagueID && $team->team_dropped_out == 0) 
				{						
					printLeagueNode($leagueObj, $team, $teamCount++);
				}
			} 
		} 
		else 
		{
			print 'No teams to display';
		}?>
	</table>
<?php }

function printLeagueHeader($leagueObj) {
	global $spiritNotice;
	$leagueDayString = getWeekday($leagueObj->league_day_number % 7);
	$leagueShowDayString = getWeekday(($leagueObj->league_day_number + 2) % 7); ?>
	
    <tr>
        <th colspan=2>Team</th>
		<th>Win</th>
		<th>Loss</th>
        <?php if($leagueObj->league_has_ties == 1){ //if ties are used ?>
            <th>Tie</th>
        <?php } ?>
        <th>Points</th>   
        <?php if($leagueObj->league_sort_by_win_pct == 1){ //if win pct is used ?>
            <th>Win Pct</th>
        <?php } 
        if(hideSpirit($leagueObj) == false){ ?>
            <th>Spirit Avg.</th>
            <?php $spiritNotice = '';
        } else {
            $spiritNotice = 'Spirit averages will be hidden on '.$leagueDayString.' at '.
			date('g A', mktime($leagueObj->league_hide_spirit_hour)).' and reposted on '.$leagueShowDayString.' at '.
			date('g A', mktime($leagueObj->league_show_spirit_hour)).'.';
        } ?>
    </tr>
<?php }

function printLeagueNode($leagueObj, $curTeam, $i) { ?>
	<tr>
		<th><?php print $i+1; ?></th>
		<td class='leftAlign' style="<?php print 'font-size:'.$curTeam->getTitleSize(); ?>;width:150px">
			<?php print '<a href="teamPage.php?teamID='.$curTeam->team_id.'">'.$curTeam->team_name.'</a>';
			if ($curTeam->team_most_recent_week_submitted < $leagueObj->league_week_in_standings &&
				$leagueObj->league_week_in_standings < 50) {
				print ' **';
			} ?>
		</td>
		<td><?php print $curTeam->team_wins; ?></td>
		<td><?php print $curTeam->team_losses; ?></td>
		<?php print $leagueObj->league_has_ties==1?'<td>'.$curTeam->team_ties.'</td>':''; ?>
		<td><?php print $curTeam->getPoints()?></td>
		<?php print $leagueObj->league_sort_by_win_pct == 1?'<td>'.number_format($curTeam->getWinPercent(), 3, '.', '').'</td>':'';
		if(hideSpirit($leagueObj) == false){ //If spirit is allowed to be shown (past the two day mark) ?>
			<td><?php print $curTeam->team_spirit_average > 0?number_format($curTeam->team_spirit_average, 2, '.', ''):'N/A'; ?></td>
		<?php } ?>
    </tr>
<?php }

function printActiveFooter() {
	global $spiritNotice ?>
	<p class='bottomLine'>** - Waiting on Results</p>
	<p class='bottomLine'><?php print $spiritNotice; ?></p>
<?php }

function printFinalTopInfo($leagueObj) { ?>
	<p class='leagueWinners'>
		<?php print getWeekday($leagueObj->league_day_number).' '.$leagueObj->sport_name.' - '.$leagueObj->league_name.' - '.$leagueObj->season_name;  ?>
	</p>
	<p class='leagueWinners'>Final Standings</p>

<?php }

function addOrdinalNumberSuffix($num) {
    if (!in_array(($num % 100),array(11,12,13))){
      switch ($num % 10) {
        // Handle 1st, 2nd, 3rd
        case 1:  return $num.'st';
			break;
        case 2:  return $num.'nd';
			break;
        case 3:  return $num.'rd';
			break;
      }
    }
    return $num.'th';
  }

function printFinalStandings($curTeam, $isTied, $index) { ?>
	<tr>
    	<td>
        	<?php if($isTied == 1) {
					print 'T'.addOrdinalNumberSuffix($curTeam->$index); 
				} else {
					print addOrdinalNumberSuffix($curTeam->$index); 
				}?>
				<?php print " - <a href='/allSports/TeamPictures/archivePicturePage.php?teamID=".$curTeam->team_id."'>".$curTeam->team_name.'</a>'; ?>
        </td>
    </tr>
<?php }

function printSpiritTopInfo() { ?>
	<p class='spiritWinners'>Spirit Winners</p>
<?php }