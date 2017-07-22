<?php

class Controllers_TeamsController extends Controllers_Controller {
		
	function getTeams($leagueID) {
		$league = Models_League::withID($this->db, $this->logger, $leagueID);
		
		if(isset($league) && $league->getId() != null) {
			
			if($league->getIsPracticeGames()) { //league has practice, include the practice team
				$sql = "SELECT team.* FROM " . Includes_DBTableNames::teamsTable . " as team "
						. "WHERE ((team.team_league_id = $leagueID AND team.team_num_in_league > 0) OR team.team_id = 1) AND team.team_dropped_out = 0 "
						. "ORDER BY team.team_num_in_league";
			} else {
				$sql = "SELECT team.* FROM " . Includes_DBTableNames::teamsTable . " as team "
						. "WHERE team.team_league_id = $leagueID AND team.team_num_in_league > 0 AND team.team_dropped_out = 0 "
						. "ORDER BY team.team_num_in_league";
			}
			
			$stmt = $this->db->query($sql);
			
			$results = [];

			while($row = $stmt->fetch()) {
				$results[] = Models_Team::withRow($this->db, $this->logger, $row);
			}

			return $results;
			
		}
		
		return [];
	}
	
	function compareHeadToHead($teamOne, $teamTwo) {
		$sql = "SELECT SUM(score_submission_score_us) - SUM(score_submission_score_them) as score_differential "
				. "FROM " . Includes_DBTableNames::scoreSubmissionsTable . " "
				. "WHERE score_submission_ignored = 0 AND score_submission_team_id = " . $teamOne->getId() . " AND score_submission_opp_team_id = " . $teamTwo->getId() . " "
				. "GROUP BY score_submission_team_id";
		
		$stmt = $this->db->query($sql);
		$row = $stmt->fetch();
		
		return $row["score_differential"];
	}

	function compareCommonPlusMinus($teamOne, $teamTwo) {
		
		$diffOne = 0;
		$diffTwo = 0;
		
		$sql = "SELECT score_submission_team_id, SUM(score_submission_score_us - score_submission_score_them) as common_score_differential "
				. "FROM ("
						. "SELECT score_submission_team_id, score_submission_score_us, score_submission_score_them "
						. "FROM " . Includes_DBTableNames::scoreSubmissionsTable . " "
						. "WHERE score_submission_ignored = 0 AND (score_submission_team_id = " . $teamOne->getId() . " OR score_submission_team_id = " . $teamTwo->getId() . ")"
				. ") as sub_table "
				. "GROUP BY score_submission_team_id";
		
		$stmt = $this->db->query($sql);
		
		while($row = $stmt->fetch()) {
			if($row['score_submission_team_id'] == $teamOne->getId()) {
				$diffOne = $submission['common_score_differential'];
			} else {
				$diffTwo = $submission['common_score_differential'];
			}
		}
		
		return $diffOne - $diffTwo;
	}

	function comparePoints($teamOne, $teamTwo) {
		
		if ($teamOne->getPoints() == $teamTwo->getPoints()) {
			
			if($teamOne->getSpiritAverage() == $teamTwo->getSpiritAverage()) {
				
				$headToHead = compareHeadToHead($teamOne, $teamTwo);
				
				if($headToHead == 0) {
					return compareCommonPlusMinus($teamOne, $teamTwo);
				} else {
					return $headToHead;
				}
				
			} else {
				return ($teamOne->getSpiritAverage() > $teamTwo->getSpiritAverage()) ? -1 : 1;
			}	
		}
		
		return ($teamOne->getPoints() > $teamTwo->getPoints()) ? -1 : 1;
	}

	function comparePercent($teamOne, $teamTwo) {
		
		if ($teamOne->getWinPercent() == $teamTwo->getWinPercent()) {
			
			if($teamOne->getSpiritAverage() == $teamTwo->getSpiritAverage()) {
				
				$headToHead = compareHeadToHead($teamOne, $teamTwo);
				
				if($headToHead == 0) {
					return compareCommonPlusMinus($teamOne, $teamTwo);
				} else {
					return $headToHead;
				}
			} else {
				return ($teamOne->getSpiritAverage() > $teamTwo->getSpiritAverage()) ? -1 : 1;
			}	
		}
		
		return ($teamOne->getWinPercent() > $teamTwo->getWinPercent()) ? -1 : 1;
	}

	function comparePosition($teamOne, $teamTwo) {
		
		if ($teamOne->getFinalPosition() == $teamTwo->getFinalPosition()) {
			return 0;
		}
		return ($teamOne->getFinalPosition() < $teamTwo->getFinalPosition()) ? -1 : 1;
	}

	function compareSpirit($teamOne, $teamTwo) {
		
		if ($teamOne->getFinalSpiritPosition() == $teamTwo->getFinalSpiritPosition()) {
			return 0;
		}
		return ($teamOne->getFinalSpiritPosition() < $teamTwo->getFinalSpiritPosition()) ? -1 : 1;
	}
	
	//Sorts whether the teams tied
	function checkTied($teams, $curNum, $numTeams, $index) {
		if($curNum == 0 && $curNum == $numTeams - 1) { //if there is only one team for some reason
			return false;
		} else if($curNum == 0 && $curNum != $numTeams - 1) { //first team in multi team league
			switch($index) {
				case 'finalPosition':
					return $teams[$curNum]->getFinalPosition() == $teams[$curNum + 1]->getFinalPosition();
				case 'finalSpiritPosition':
					return $teams[$curNum]->getFinalSpiritPosition() == $teams[$curNum + 1]->getFinalSpiritPosition();
			}
		} else if($curNum > 0 && $curNum != $numTeams - 1) { //some middle team in multi team league
			switch($index) {
				case 'finalPosition':
					return $teams[$curNum - 1]->getFinalPosition() == $teams[$curNum]->getFinalPosition() || $teams[$curNum]->getFinalPosition() == $teams[$curNum + 1]->getFinalPosition();
				case 'finalSpiritPosition':
					return $teams[$curNum - 1]->getFinalSpiritPosition() == $teams[$curNum]->getFinalSpiritPosition() || $teams[$curNum]->getFinalSpiritPosition() == $teams[$curNum + 1]->getFinalSpiritPosition();
			}
		} else { //by default last team in multi team league
			switch($index) {
				case 'finalPosition':
					return $teams[$curNum - 1]->getFinalPosition() == $teams[$curNum]->getFinalPosition();
				case 'finalSpiritPosition':
					return $teams[$curNum - 1]->getFinalSpiritPosition() == $teams[$curNum]->getFinalSpiritPosition();
			}
		}
	}
	
	public function getTeamsForUser($user, $season, $dayNum) {
		
		if(!isset($user) || $user == null) {
			return [];
		}
		
		$sql = "SELECT team.* FROM " . Includes_DBTableNames::teamsTable . " as team "
				. "INNER JOIN " . Includes_DBTableNames::leaguesTable . " league ON league.league_id = team.team_league_id "
				. "WHERE team.team_managed_by_user_id = " . $user->getId();
		
		if(isset($season)) {
			$sql .= " AND league.league_season_id = " . $season->getId();
		}
				
		if(isset($dayNum)) {
			$sql .= " AND league.league_day_number = $dayNum";
		}
								
		$stmt = $this->db->query($sql);
		$results = [];
		
        while(($row = $stmt->fetch()) != false) {		
            $results[] = Models_Team::withRow($this->db, $this->logger, $row);
        }
		
		return $results;
	}
	
	public function insertOrUpdateTeam($team, $user, $request) {
		
		$allPostVars = $request->getParsedBody();
		$isUpdate = true;
				
		if(!isset($team) || $team == null || $team->getId() == null) {
						
			$team = Models_Team::withID($this->db, $this->logger, -1);
			$isUpdate = false;
			$team->setManagedByUserId($user->getId());
		}
		
		$action = $allPostVars['action'];
		
		$sport = Models_Sport::withID($this->db, $this->logger, $allPostVars['sportID']);
		
		if(isset($allPostVars['leagueID'])) {
			$team->setLeagueId($allPostVars['leagueID']);
		}
		$team->setName($allPostVars['teamName']);
		
		$team->getCaptain()->setFirstName($allPostVars['capFirstName']);
		$team->getCaptain()->setLastName($allPostVars['capLastName']);
		$team->getCaptain()->setEmail($allPostVars['capEmail']);
		$team->getCaptain()->setPhoneNumber($allPostVars['capPhoneNumber']);
		$team->getCaptain()->setGender($allPostVars['capGender']);
		
		if($action == 'register') {
			$team->getCaptain()->setHowHeardMethod($allPostVars['capHowHeardMethod']);
			$team->getCaptain()->setHowHeardOtherText($allPostVars['capHowHeardMethodOther']);
		}
		
		for($i = 0; $i < $sport->getNumPlayerInputsForRegistration(); $i++) {
			$playerID = $allPostVars['playerID_' . $i];
			$curPlayer = $team->getPlayerByID($playerID);
			
			if($curPlayer == null || $curPlayer->getId() == null) {
				$curPlayer = Models_Player::withID($this->db, $this->logger, -1);
			}
			
			$curPlayer->setFirstName($allPostVars['playerFirstName_' . $i]);
			$curPlayer->setLastName($allPostVars['playerLastName_' . $i]);
			$curPlayer->setEmail($allPostVars['playerEmail_' . $i]);
			$curPlayer->setGender($allPostVars['playerGender_' . $i]);
			
			$team->getPlayers()[$i] = $curPlayer;
		}
		
		$team->getRegistrationComment()->setComment($allPostVars['teamComments']);
		//$team->getPlayers();
		
		
		if($action == 'register') {
			$stmt = $this->db->query("SELECT MAX(team_num_in_league) as highestTeamNum FROM " . Includes_DBTableNames::teamsTable . " "
					. "WHERE team_league_id = " . $team->getLeagueId()
			);

			if(($row = $stmt->fetch()) != false) {
				$team->setNumInLeague($row['highestTeamNum'] + 1);
			}
			
			$team->setIsFinalized(true);
			$team->setManagedByUserId($user->getId());
			$team->setPaymentMethod($allPostVars['teamPaymentMethod']);
			$team->setPicName($team->getLeagueId() . ($team->getNumInLeague() < 10 ?  '-0' : '-') . $team->getNumInLeague());
			
		} else if(!$isUpdate) {
			$team->setNumInLeague(0);
			$team->setIsFinalized(false);
			$team->setManagedByUserId($user->getId());
			$team->setPicName($team->getLeagueId() . ($team->getNumInLeague() < 10 ?  '-0' : '-') . $team->getNumInLeague());
		}
		
		$team->saveOrUpdate();
		
		$team->getCaptain()->setTeamId($team->getId());
		$team->getCaptain()->saveOrUpdate();
				
		foreach($team->getPlayers() as $curPlayer) {
			$curPlayer->setTeamId($team->getId());
			$curPlayer->saveOrUpdate();
			
			if($action == 'register') {
				$this->insertPlayerAddressDB($curPlayer);
			}
		}
				
		$team->getRegistrationComment()->saveOrUpdate();
		
		
		$userHistory = Models_UserHistory::withID($this->db, $this->logger, -1);
		$userHistory->setUserId($user->getId());
		$userHistory->setUsername($user->getUsername());
		$userHistory->setType($action == 'register' ? 'Registered Team' : 'Saved Team');
		$userHistory->setDescription($team->getId());
		$userHistory->save();
		
		
		$oldTeam = Models_Team::withID($this->db, $this->logger, $allPostVars['oldTeamID']);
		if(isset($oldTeam) && $oldTeam != null && $oldTeam->getId() != null && $team->getLeague()->getSeason()->getId() != $oldTeam->getLeague()->getSeason()->getId()) {
			$oldTeam->setIsDeleted(true); //if you're using an old team for the new one this will make it so the old one doesn't show up on the members page
			$oldTeam->saveOrUpdate();
		}
		
		if($action == 'register') {
			return "Your team has been registered.";
		} else if(!$isUpdate) {
			return "Your team has been saved.";
		} else {
			return "Your team has been updated.";
		}
	}

	function insertPlayerAddressDB($player) {

		if(filter_var($player->getEmail(), FILTER_VALIDATE_EMAIL)) {
			
			$stmt = $this->db->query("SELECT count(*) as numEmails FROM " . Includes_DBTableNames::addressesTable . " WHERE EmailAddress = '" . $player->getEmail() . "'");

			if(($row = $stmt->fetch()) != false) {
				if($row['numEmails'] == 0) {
					$newEmail = Models_EmailAddress::withID($this->db, $this->logger, -1);
					$newEmail->setFirstName($player->getFirstName());
					$newEmail->setLastName($player->getLastName());
					$newEmail->setEmail($player->getEmail());
					
					$newEmail->save();
				}
			}
		}
	}

	//returns 2 if the league is full, 1 if the user will be put on waiting, 0 if they're fine to register
	function checkLeaguesFull($leagueID) {
		global $leaguesTable, $teamsTable;

		//gets new team number in league
		$leagueQuery = mysql_query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID");
		$leagueArray = mysql_fetch_array($leagueQuery);
		$numUntilWaiting = $leagueArray['league_num_teams_before_waiting'];
		$leagueFull = $leagueArray['league_maximum_teams'];

		//gets new team number in league
		$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_finalized = 1");
		$numTeamsRegistered = mysql_num_rows($teamsQuery);

		if ($numTeamsRegistered >= $leagueFull && $leagueFull != 0) {
			return 2;
		} else if ($numTeamsRegistered >= $numUntilWaiting && $leagueFull != 0) {
			return 1;
		} else {
			return 0;
		}
	}

	//Checks if a team exists in a certain league with a certain teamname
	function checkTeamExists($leagueID, $teamName) {
		global $teamsTable;
		$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_name = '$teamName'");
		$numTeams = mysql_num_rows($teamsQuery);
		if ($numTeams > 0) 
			return true;
		else
			return false;	
	}

	//This function formats a phone number into the proper display. Makes "5198234502" or anything similar appear as "(519)-823-4502"
	//How it works:
	//- Chops phone number input into 3 sections: area code, prefix (first 3 digits), and number (last 4 digits)
	function formatPhoneNumber($strPhone){
		$strPhone = preg_replace('/[^0-9]/','', $strPhone);
		if (strlen($strPhone)!= 10){
				return $strPhone;
		}
		$strArea = substr($strPhone, 0, 3);
		$strPrefix = substr($strPhone, 3, 3);
		$strNumber = substr($strPhone, 6, 4);
		$strPhone = "(".$strArea.") ".$strPrefix."-".$strNumber;
		return ($strPhone);
	}
}
