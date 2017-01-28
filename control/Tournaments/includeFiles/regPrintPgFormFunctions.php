<?php //prints the top info of the score reporter, ie the game info and title
function printTopInfo($tourneysDropDown, $leaguesDropDown) { ?>
	Tournament
	<select name='tourneyID' onchange='reloadPageTourney()'>
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
        	Phone
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
			<?php print $curTeam->teamCaptainName ?>
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
        	<?php print $curTeam->teamCaptainPhone ?>
        </td>
    </tr> 
<?php }

function printTeamsHoldingTank($tourneyObj, $tourneyTeam) {
	$numHolding = 0; 
	$isWaiting = 0;
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
			}
		}
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
        	Pd
        </td><td>
        	Phone
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
        </td><td style="font-size:11px">
        	<?php print $curPlayer->playerFirstName.' '.$curPlayer->playerLastName ?>
        </td><td style="font-size:9px">
        	<?php print $curPlayer->playerEmail ?>
        </td><td>
			<?php print $curPlayer->playerPaid == 1?'Y':'N'?>
        </td><td>
        	<?php print $curPlayer->playerPhone ?>
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
	}
	if($isHolding == 1) { ?>
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
        	Email
        </td><td>
        	Paid
        </td><td>
        	Note
        </td><td>
        	Choose
        </td><td>
        	Phone
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
        </td><td style="font-size:9px">
        	<?php print $curPlayer->playerEmail ?>
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
        </td><td>
        	<?php print $curPlayer->playerPhone ?>
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