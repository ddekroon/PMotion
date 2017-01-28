<?php //prints the top info of the score reporter, ie the game info and title
function printTopInfo($tourneysDropDown, $leaguesDropDown) { ?>
	Tournament
	<select name='tourneyID' onchange='reloadPageTourney()'> <!-- reloads the page when something is selected -->
		<option value=0>--Tournaments--</option>
		<?php print $tourneysDropDown; ?>
	</select>
<?php }

function printLeagueHeader($leagueName) { ?>
	<tr>
		<th colspan=10 align="center">
			<?php print $leagueName ?>
        </th>
    </tr>
<?php }

function printTeamsHeader($tourneyObj){ ?>
	<tr>
   		<td>
        	<br />
        </td><td>
        	Name
        </td><td>
        	Edit
        </td><td>
        	Captain
        </td>
        <?php if($tourneyObj->tourneyIsExtraField == 1) { ?>
		<td>
        	Extra
        </td>
		<?php } ?>
        <td>
        	Rating
        </td><td>
        	Paid
        </td><td>
        	Email
        </td><td>
        	Choose
        </td><td>
        	Del
        </td>
    </tr>                     
<?php }


function printTeamNode($i, $curTeam, $tourneyObj){ ?>
	<tr>
   		<td>
        	<?php print $i ?>
            <input type='hidden' name="teamID[]" value="<?php print $curTeam->teamID ?>" />
        </td><td>
        	<input type='text' name="teamName[]" value="<?php print $curTeam->teamName ?>" />
        </td><td>
			<?php print "<a href='editTeam.php?tournamentID=$curTeam->teamTournamentID&leagueID=$curTeam->teamLeagueID&teamID=$curTeam->teamID'>Edit</a>" ?>
        </td><td>
			<?php print "<a href='mailto:".$curTeam->teamCaptainEmail."'>".$curTeam->teamCaptainName.'</a>' ?>
        </td>
        <?php if($tourneyObj->tourneyIsExtraField == 1) { ?>
		<td>
        	<input type='text' name="teamExtra[]" value="<?php print $curTeam->teamExtraField ?>" />
        </td>
		<?php } ?>
        <td>
        	<?php print $curTeam->teamRatingDropDown ?>
        </td><td>
        	<select name="teamPaid[]">
            	<?php for($i=1;$i>=0;$i--) { ?>
					<option <?php print $curTeam->teamPaid == $i?'selected':''?> value=<?php print $i?>><?php print $i == 1?'Yes':'No'?></option> 
				<?php } ?>
            </select>
        </td><td>
        	<?php print $curTeam->teamCaptainEmail ?>
        </td><td>
        	<?php print $curTeam->teamNumsDropDown ?>
        </td><td style="vertical-align:middle">
        	<input name="teamDelete[]" type="checkbox" value=<?php print $curTeam->teamID ?>  />
        </td>
    </tr> 
<?php }

function printTeamsEmail($teamsObj, $tourneyObj) { ?>
	<tr>
    	<td colspan='10'>
        	<?php 
			$emailCount = "1";
			foreach($teamsObj as $curTeam) {
				if(filter_var($curTeam->teamCaptainEmail, FILTER_VALIDATE_EMAIL)) {
					/*prints 49 emails then prints || to show the division*/
					if($emailCount != "49"){
						print $curTeam->teamCaptainEmail.', ';
						$emailCount++;
					}else{
						print $curTeam->teamCaptainEmail.'	|| ';
						$emailCount = "1";
					}
				}
			} ?>
        </td>
    </tr><tr>
		<td colspan="10">

        	<a target="_blank" href="mailto: ?subject=<?php print $tourneyObj->tourneyName.' - '.$tourneyObj->tourneySportName.' Tournament';
			$toEmail = controlPanelTournamentLeague();
			foreach($toEmail as $emailAdmin) {
				print '&CC='.$emailAdmin;
			}
        	foreach($teamsObj as $curTeam) {
				if(filter_var($curTeam->teamCaptainEmail, FILTER_VALIDATE_EMAIL)) {
					print '&BCC='.$curTeam->teamCaptainEmail;
				}
			} ?>">Email League</a>
        </td>
    </tr>
<?php }

function printTeamsHoldingTank($tourneyObj, $tourneyTeam) {
	$numHolding = 1; 
	$isWaiting = 0;
	$holdingEmails = array();
	foreach($tourneyTeam as $team) {
		if($team->teamIsWaiting == 1) {
			$isWaiting = 1;
		}
	}
	if($isWaiting == 1) {?>
		<tr>
			<th colspan=10>
				Holding Tank
			</th>
		</tr>
		<?php foreach($tourneyTeam as $team) {
			if($team->teamIsWaiting == 1) {
				printTeamNode($numHolding++, $team, $tourneyObj);
				$holdingEmails[] = $team->teamCaptainEmail;
			}
		}
		print '<tr><td colspan=1>';
		foreach($holdingEmails as $email) {
			print $email.', ';
		}
		print '</td></tr>';
	}
}

function printTeamsFooter($tourneyID) { ?>
	<button name="submitTeams" onclick="return checkYesNo()">Submit Teams</button>
	<button name="deleteTeams" onclick="return checkDelete()">Delete Checked Teams</button>
	<button name="printablePage" value=1 onclick="return showPrintPage(<?php print $tourneyID ?>)">Printable Page</button>
<?php }

function printCardsHeader(){ ?>
	<tr>
   		<td style="width:20px;">
        	#
        </td><td>
        	Card
        </td><td>
        	Name
        </td><td>
        	Email
        </td><td>
        	Paid
        </td><td>
        	Choose
        </td>
    </tr>                     
<?php }

function printCardNode($curPlayer, $curNum) { ?>
	<tr>
   		<td style="width:20px;">
        	<?php print $curNum ?>
        </td><td>
        	<select name="playerCardDD[]"><option value=0>N\A</option>
        		<?php print $curPlayer->playerCardsDropDown ?>
            </select>
            <input type="hidden" name="playerID[]" value="<?php print $curPlayer->playerID ?>" />
        </td><td>
        	<?php print "<a href='editPlayer.php?tournamentID=$curPlayer->playerTournamentID&leagueID=$curPlayer->playerLeagueID&playerID=$curPlayer->playerID'>$curPlayer->playerFirstName 
				$curPlayer->playerLastName</a>" ?>
        </td><td style="font-size:9px">
        	<?php print "<a target='_blank' href='mailto:$curPlayer->playerEmail'>$curPlayer->playerEmail</a>" ?>
        </td><td>
        	<select name="playerPaid[]">
            	<?php for($i=1;$i>=0;$i--) { ?>
					<option <?php print $curPlayer->playerPaid == $i?'selected':''?> value=<?php print $i ?>><?php print $i == 1?'Yes':'No'?></option> 
				<?php } ?>
            </select>
        </td><td style="vertical-align:middle">
        	<input name="playerDelete[]" type="checkbox" value=<?php print $curPlayer->playerID ?>  />
        </td>
    </tr> 
<?php }

function printCardsHoldingTank($tourneyPlayers, $tourneyObj) {
	$numHolding = 0; 
	$isHolding = 0;
	foreach($tourneyPlayers as $card) {
		if($card->playerIsWaiting == 1) {
			$isHolding == 1;
		}
	}?>
    <tr>
    	<td colspan='10'>
        	<?php foreach($tourneyPlayers as $curPlayer) {
				if(filter_var($curPlayer->playerEmail, FILTER_VALIDATE_EMAIL)) {
					print $curPlayer->playerEmail.', ';
				}
			} ?>
        </td>
    </tr><tr>
    	<td colspan=6>
        	<a target="_blank" href="mailto: ?subject=<?php print $tourneyObj->tourneyName.' - '.$tourneyObj->tourneySportName.' Tournament';
			$toEmail = controlPanelTournamentLeague();
			foreach($toEmail as $emailAdmin) {
				print '&CC='.$emailAdmin;
			}
        	foreach($tourneyPlayers as $curPlayer) {
				if(filter_var($curPlayer->playerEmail, FILTER_VALIDATE_EMAIL)) {
					print '&BCC='.$curPlayer->playerEmail;
				}
			} ?>">Email League</a>
        </td>
    </tr>
	<?php if($isHolding == 1) { ?>
		<tr>
			<th colspan=6>
				Holding Tank
			</th>
		</tr>
		<?php foreach($tourneyPlayers as $card) {
			if($card->playerIsWaiting == 1) {
				printCardNode($card);
			}
		}
	}
}

function printPlayersHeader(){ ?>
    <tr>
   		<td>
        	Num
        </td><td>
        	Name
        </td><td>
        	Edit
        </td><td>
        	Email
        </td><td>
        	Paid
        </td><td>
        	Note
        </td><td>
        	Choose
        </td><td>
        	Del
        </td>
    </tr>                     
<?php }

function printPlayerNode($curNum, $curPlayer, $isLeague) { ?>
	<tr>
   		<td>
        	<?php print $curNum ?>
            <input type="hidden" name="playerID[]" value="<?php print $curPlayer->playerID ?>" />
        </td><td>
        	<input type='text' style="width:80px" name="playerFirstName[]" value="<?php print $curPlayer->playerFirstName ?>" />
            <input type='text' style="width:100px"name="playerLastName[]" value="<?php print $curPlayer->playerLastName ?>" />
        </td><td>
        	<?php print "<a href='editPlayer.php?tournamentID=$curTeam->teamTournamentID&leagueID=$curTeam->teamLeagueID&playerID=$curPlayer->playerID'>Edit</a>" ?>
        </td><td style="font-size:9px">
        	<?php print "<a target='_blank' href='mailto:$curPlayer->playerEmail'>$curPlayer->playerEmail</a>" ?>
        </td><td>
        	<select name="playerPaid[]">
            	<?php for($i=1;$i>=0;$i--) { ?>
					<option <?php print $curPlayer->playerPaid == $i?'selected':''?> value=<?php print $i ?>><?php print $i == 1?'Yes':'No'?></option> 
				<?php } ?>
            </select>
        </td><td>
        	<input type='text' name="playerNote[]" value="<?php print $curPlayer->playerNote ?>" />
        </td><td>
        	<?php print $curPlayer->playerNumDropDown ?>
        </td><td style="vertical-align:middle">
        	<input name="playerDelete[]" type="checkbox" value=<?php print $curPlayer->playerID ?>  />
        </td>
    </tr> 
<?php } 

function printPlayersHoldingTank($tourneyObj, $tourneyPlayer) {
	$numHolding = 1; ?>
    <tr>
    	<th colspan=10>
        	Holding Tank
        </th>
    </tr>
	<?php foreach($tourneyPlayer as $player) {
		if($player->playerIsWaiting == 1) {
			printPlayerNode($numHolding++, $player, $tourneyObj->isLeagues);
		}
	}
}

function printCardsFooter($tourneyPlayers, $tourneyID, $tourneyObj, $leagueID) { 
	global $leaguesDropDown; 
	$numBlackCards = $tourneyObj->tourneyNumBlackCards[$leagueID];
	$numRedCards = $tourneyObj->tourneyNumRedCards[$leagueID]; ?>
	<table>
		<tr>
			<th colspan=2>
				Add a Player
			</th>
		</tr><tr>
			<td colspan=2>
				<select name="addPlayerCardDD"><option value=0>N\A</option>
					<?php print getPlayerCardDD(199, $numBlackCards);
					print getPlayerCardDD(399, $numRedCards); ?>
				</select>
				<select name='addPlayerLeagueID'>
					<?php print $leaguesDropDown ?>
				</select>
				First Name: <input type="text" style="width:70px" name="addPlayerFirstName"  /> 
				Last Name: <input type="text" style="width:100px" name="addPlayerLastName"  />
				Paid: <select name="addPlayerPaid">
					<?php for($i=1;$i>=0;$i--) { ?>
						<option <?php print $curPlayer->playerPaid == $i?'selected':''?> value=<?php print $i ?>><?php print $i == 1?'Yes':'No'?></option> 
					<?php } ?>
				</select>
				Note: <input type='text' name="addPlayerNote" />
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<button name="createCardPlayer" value="Add Player">Add New Player</button>
			</td>
		</tr>
	</table>
	</div><div class='tableData'>
		<a target="_blank" href="mailto: ?subject=<?php print $tourneyObj->tourneyName.' - '.$tourneyObj->tourneySportName.' Tournament';
		$toEmail = controlPanelTournamentLeague();
		foreach($toEmail as $emailAdmin) {
			print '&CC='.$emailAdmin;
		}
		foreach($tourneyPlayers as $playerLeague) {
			foreach($playerLeague as $playerSuit) {
				foreach($playerSuit as $playerNode) {
					if(filter_var($playerNode->playerEmail, FILTER_VALIDATE_EMAIL)) {
						print '&BCC='.$playerNode->playerEmail;
					}
				}
			}
		} ?>">Email Tournament</a>
		<button name="submitPlayerInfo" value=1 onclick="return checkYesNo()">Submit Data</button>
		<button name="deleteCheckedPlayers" value=1 onclick="return checkDelete()">Delete Checked Players</button>
		<button name="printablePage" value=1 onclick="return showPrintPage(<?php print $tourneyID ?>)">Printable Page</button>
	</div>
<?php }

function printPlayersFooter($tourneyID) { ?>
	<tr>
    	<td colspan=10>
        	<button name="submitPlayerInfo" value=1 onclick="return checkYesNo()">Submit Data</button>
        	<button name="deleteCheckedPlayers" value=1 onclick="return checkDelete()">Delete Checked Players</button>
            <button name="printablePage" value=1 onclick="return showPrintPage(<?php print $tourneyID ?>)">Printable Page</button>
        </td>
    </tr>
<?php }?>