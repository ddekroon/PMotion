<?php

class Controllers_TeamsController extends Controllers_Controller {
		
	function getTeams($leagues, $sports, $seasons, $isPractice) {
		
		$leaguesFilter = '';
		$sportsFilter = '';
		$seasonsFilter = '';
		$practiceFilter = 'OR 1 = 0';

		if(isset($leagues) && sizeof($leagues) > 0) {
			$leagueIDs = [];
			foreach($leagues as $curLeague) {
				if($curLeague->getId() > 0) {
					$leagueIDs[] = $curLeague->getId();
				}
			}

			if(sizeof($leagueIDs) > 0) {
				$leaguesFilter .= ' AND league.league_id in (' . implode(',', $leagueIDs) . ')';
			}
		}

		if(isset($sports) && sizeof($sports) > 0) {
			$sportsIDs = [];
			foreach($sports as $curSport) {
				if($curSport->getId() > 0) {
					$sportsIDs[] = $curSport->getId();
				}
			}

			if(sizeof($sportsIDs) > 0) {
				$sportsFilter .= ' AND league.league_sport_id in (' . implode(',', $sportsIDs) . ')';
			}
		}

		if(isset($seasons) && sizeof($seasons) > 0) {
			$seasonsIDs = [];
			foreach($seasons as $curSeason) {
				if($curSeason->getId() > 0) {
					$seasonsIDs[] = $curSeason->getId();
				}
			}

			if(sizeof($seasonsIDs) > 0) {
				$seasonsFilter .= ' AND league.league_season_id in (' . implode(',', $seasonsIDs) . ')';
			}
		}
			
		if($isPractice) { //league has practice, include the practice team
			$practiceFilter = ' OR team.team_id = 1';
		}

		$sql = "SELECT team.* FROM " . Includes_DBTableNames::teamsTable . " as team"
			. " INNER JOIN " . Includes_DBTableNames::leaguesTable . " league"
				. " ON league.league_id = team.team_league_id" . $leaguesFilter . $sportsFilter . $seasonsFilter
			. " WHERE ((team.team_num_in_league > 0 AND team.team_dropped_out = 0) $practiceFilter) "
			. " ORDER BY league.league_sport_id, league.league_season_id ASC, league.league_day_number ASC, team.team_num_in_league ASC";
			
		$stmt = $this->db->query($sql);
		
		$results = [];

		while($row = $stmt->fetch()) {
			$results[] = Models_Team::withRow($this->db, $this->logger, $row);
		}

		return $results;
	}
	
	static function compareHeadToHead($teamOne, $teamTwo) {
		return $teamOne->getHeadToHeadDifferential($teamTwo);
	}

	static function compareCommonPlusMinus($teamOne, $teamTwo) {
		return $teamOne->getCommonPlusMinusDifferential($teamTwo);
	}

	static function comparePoints($teamOne, $teamTwo) {
		
		if ($teamOne->getPoints() == $teamTwo->getPoints()) {
			
			if($teamOne->getSpiritAverage() == $teamTwo->getSpiritAverage()) {
				
				$headToHead = Controllers_TeamsController::compareHeadToHead($teamOne, $teamTwo);
				
				if($headToHead == 0) {
					return Controllers_TeamsController::compareCommonPlusMinus($teamOne, $teamTwo);
				} else {
					return $headToHead;
				}
				
			} else {
				return ($teamOne->getSpiritAverage() > $teamTwo->getSpiritAverage()) ? -1 : 1;
			}	
		}
		
		return ($teamOne->getPoints() > $teamTwo->getPoints()) ? -1 : 1;
	}

	static function comparePercent($teamOne, $teamTwo) {
		
		if ($teamOne->getWinPercent() == $teamTwo->getWinPercent()) {
			
			if($teamOne->getSpiritAverage() == $teamTwo->getSpiritAverage()) {
				
				$headToHead = Controllers_TeamsController::compareHeadToHead($teamOne, $teamTwo);
				
				if($headToHead == 0) {
					return Controllers_TeamsController::compareCommonPlusMinus($teamOne, $teamTwo);
				} else {
					return $headToHead;
				}
			} else {
				return ($teamOne->getSpiritAverage() > $teamTwo->getSpiritAverage()) ? -1 : 1;
			}	
		}
		
		return ($teamOne->getWinPercent() > $teamTwo->getWinPercent()) ? -1 : 1;
	}

	static function comparePosition($teamOne, $teamTwo) {
		
		if ($teamOne->getFinalPosition() == $teamTwo->getFinalPosition()) {
			return 0;
		}
		return ($teamOne->getFinalPosition() < $teamTwo->getFinalPosition()) ? -1 : 1;
	}

	static function compareSpirit($teamOne, $teamTwo) {
		
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
			$team->setLeagueId((int)$allPostVars['leagueID']);
		}
		$team->setName($allPostVars['teamName']);
		
		$team->getCaptain()->setFirstName($allPostVars['capFirstName']);
		$team->getCaptain()->setLastName($allPostVars['capLastName']);
		$team->getCaptain()->setEmail($allPostVars['capEmail']);
		$team->getCaptain()->setPhoneNumber($allPostVars['capPhoneNumber']);
		$team->getCaptain()->setGender($allPostVars['capGender']);
		$team->getCaptain()->setHowHeardMethod(isset($allPostVars['capHowHeardMethod']) ? $allPostVars['capHowHeardMethod'] : '0');
		$team->getCaptain()->setHowHeardOtherText(isset($allPostVars['capHowHeardMethodOther']) ? $allPostVars['capHowHeardMethodOther'] : '');
		
		$players = $team->getPlayers();
		
		for($i = 0; $i < $sport->getNumPlayerInputsForRegistration(); $i++) {
			$playerID = $allPostVars['playerID_' . $i];
			$curPlayer = Models_Player::withRow($this->db, $this->logger, []);
			
			if($isUpdate && isset($playerID) && !empty($playerID) && $playerID > 0) {
				$curPlayer = $team->getPlayerByID($playerID);
			}
			
			$curPlayer->setFirstName($allPostVars['playerFirstName_' . $i]);
			$curPlayer->setLastName($allPostVars['playerLastName_' . $i]);
			$curPlayer->setEmail($allPostVars['playerEmail_' . $i]);
			$curPlayer->setGender($allPostVars['playerGender_' . $i]);
			
			if($isUpdate) {
				$players[$i] = $curPlayer;
			} else {
				$players[] = $curPlayer;
			}
		}
				
		$team->setPlayers($players);
		
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
			$team->setPicName("");
		}
		
		//$team->varDump();
		
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
			$registrationController = new Controllers_RegistrationController($this->db, $this->logger);
			$registrationController->sendRegistrationEmail($team);
			$registrationController->sendWaiverEmails($team);
			
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
	
	/**
	 * 
	 * @param type $searchString
	 * @param type $granularity - Either AND or OR depending on which type of search you want (for filtering on keywords) - default AND
	 * @param type $league
	 * @param type $sport
	 * @param type $season
	 * @param type $dayOfWeek
	 * @return type
	 */
	function searchForTeams($searchString, $granularity, $league, $sport, $season, $dayOfWeek, $limit, $offset, $order) {
		
		if(!isset($searchString) || $searchString == null || strlen($searchString) <= 2) {
			return "";
		}
		
		$keywords = Includes_Helper::removeCommonWords($searchString); //break all search terms by space
		
		$sql = " SELECT distinct team.*,";
		
		$count = 0;
		$weightSql = "";
		foreach($keywords as $keyword) {
			if($count++ > 0) {
				$weightSql .= " + ";
			}
			$weightSql .= " IF("
						. " team.team_name LIKE " . $this->db->quote($keyword . "%") . ", 30,"
						. " IF(team.team_name LIKE " . $this->db->quote("%" . $keyword . "%") . ", 15, 0)"
					. ")"
					. " + IF(league.league_name LIKE " . $this->db->quote("%" . $keyword . "%") . ", 5,  0)"
					. " + IF(sport.sport_name   LIKE " . $this->db->quote("%" . $keyword . "%") . ", 5,  0)"
					. " + IF(season.season_name LIKE " . $this->db->quote("%" . $keyword . "%") . ", 5,  0)"
					. " + IF(season.season_year = " . $this->db->quote("%" . $keyword . "%") . ", 8,  0)";
		}
		
		$weightSql .= "+ IF(season.season_year >= " . (date('Y') - 1) . ", 10, season.season_year - " . date('Y') . ")"
				. " AS `weight`";
						
		$sql .= $weightSql . $this->getSearchForTeamsSql($searchString, $granularity, $league, $sport, $season, $dayOfWeek);
		
		$sql .= " ORDER BY " . (isset($order) && strlen($order) > 0 ? $order : "weight DESC, team.team_created DESC");
		
		if(isset($limit) && $limit > 0) {
			$sql .= " LIMIT ";
			
			if(isset($offset) && $offset > 0) {
				$sql .= $offset . ", ";
			}
			
			$sql .= $limit;
		}
		
		try {
		
			$this->db->setAttribute(PDO::ATTR_CURSOR, PDO::CURSOR_FWDONLY);
			$stmt = $this->db->prepare($sql);

			$stmt->execute();
		} catch(PDOException $e) {
			// error handling
		}
		
		$results = [];

		while(($row = $stmt->fetch()) != false) {
			$results[] = Models_Team::withRow($this->db, $this->logger, $row);
		}
		
		return $results;
	}
	
	public function searchForTeamsCount($searchString, $granularity, $league, $sport, $season, $dayOfWeek) {
		$sql = "SELECT count(distinct team.team_id) as count";
		
		$sql .= $this->getSearchForTeamsSql($searchString, $granularity, $league, $sport, $season, $dayOfWeek);
		
		try {
		
			$this->db->setAttribute(PDO::ATTR_CURSOR, PDO::CURSOR_FWDONLY);
			$stmt = $this->db->prepare($sql);

			$stmt->execute();
		} catch(PDOException $e) {
			// error handling
		}
		
		$row = $stmt->fetch();
		
		if($row != false) {
			return $row['count'];
		}
		
		return 0;
	}
	
	private function getSearchForTeamsSql($searchString, $granularity, $league, $sport, $season, $dayOfWeek) {
		
		if(!isset($searchString) || $searchString == null || strlen($searchString) <= 2) {
			return "";
		}
		
		$keywords = Includes_Helper::removeCommonWords($searchString); //break all search terms by space
		
		$sql = " FROM " . Includes_DBTableNames::teamsTable . " as team"  
				. " INNER JOIN " . Includes_DBTableNames::leaguesTable . " league ON team.team_league_id = league.league_id"
				. " INNER JOIN " . Includes_DBTableNames::seasonsTable . " season ON season.season_id = league.league_season_id"
				. " INNER JOIN " . Includes_DBTableNames::sportsTable . " sport ON sport.sport_id = league.league_sport_id"
				. " WHERE team.team_finalized = 1 AND team.team_num_in_league > 0 AND team.team_dropped_out = 0";
		
		//Have to put the where statment in with a default comparator so the rest of the dynamically generated sql is set up as valid sql.
		if(strcasecmp($granularity, "or") === 0) {
			$sql .= " AND (1 = 0";
		} else {
			$sql .= " AND (1 = 1";
		}


		foreach($keywords as $keyword) {
			if(strcasecmp($granularity, "or") === 0) {
				$sql .= " OR team.team_name LIKE " . $this->db->quote("%" . $keyword . "%")
						. " OR league.league_name LIKE " . $this->db->quote("%" . $keyword . "%")
						. " OR sport.sport_name LIKE " . $this->db->quote("%" . $keyword . "%")
						. " OR CONCAT(season.season_name, \" \", season.season_year) LIKE " . $this->db->quote("%" . $keyword . "%");
			} else {
				$sql .= " AND (team.team_name LIKE " . $this->db->quote("%" . $keyword . "%")
						. " OR league.league_name LIKE " . $this->db->quote("%" . $keyword . "%")
						. " OR sport.sport_name LIKE " . $this->db->quote("%" . $keyword . "%")
						. " OR CONCAT(season.season_name, \" \", season.season_year) LIKE " . $this->db->quote("%" . $keyword . "%")
						. ")";
			}
		}
		
		$sql .= ")";
		
		if(isset($league) && $league->getId() != null) {
			$sql .= " AND league.league_id = " . $league->getId();
		}
		
		if(isset($season) && $season->getId() != null) {
			$sql .= " AND league.league_season_id = " . $season->getId();
		}
		
		if(isset($sport) && $sport->getId() != null) {
			$sql .= " AND sport.sport_id = " . $sport->getId();
		}
				
		if(isset($dayOfWeek) && $dayOfWeek > -1) {
			$sql .= " AND league.league_day_number = $dayOfWeek";
		}
		
		return $sql;
	}
	
	function removeTeam($team) {
		$team->setIsDeleted(true);
		$team->update();
	}
	
	function getIsReturningTeam(Models_Team $team) {
		$sql = "SELECT count(team.team_id) as teamCount FROM " . Includes_DBTableNames::teamsTable . " as team "
				. " INNER JOIN " . Includes_DBTableNames::leaguesTable . " league ON league.league_id = team.team_league_id"
				. " WHERE league_season_id = " . ($team->getLeague()->getSeasonId() - 1) . " AND team_managed_by_user_id = " . $team->getManagedByUserId()
				. " AND team_finalized = 1";
				
		try {
			$this->db->setAttribute(PDO::ATTR_CURSOR, PDO::CURSOR_FWDONLY);
			$stmt = $this->db->prepare($sql);

			$stmt->execute();
		} catch(PDOException $e) {
			// error handling
			return false;
		}
		
		$row = $stmt->fetch();
		
		if($row != false) {
			return $row['teamCount'] > 0;
		}
		
		return false;
	}
	
	function getUnbalancedStandingsTeamData() {

		$sql = "SELECT team.*, "
		. "	IFNULL(team_submitted_wins, 0) as team_submitted_wins, "
		. "	IFNULL(team_submitted_losses, 0) as team_submitted_losses, "
		. "	IFNULL(team_submitted_ties, 0) as team_submitted_ties, "
		. "	IFNULL(team_submitted_practices, 0) as team_submitted_practices, "
		. "	IFNULL(team_submitted_cancels, 0) as team_submitted_cancels, "
		. "	IFNULL(team_opp_submitted_wins, 0) as team_opp_submitted_wins, "
		. "	IFNULL(team_opp_submitted_losses, 0) as team_opp_submitted_losses, "
		. "	IFNULL(team_opp_submitted_ties, 0) as team_opp_submitted_ties "
		. "FROM  teams_dbtable as team "
		. "	INNER JOIN leagues_dbtable league ON league.league_id = team.team_league_id "
		. "	INNER JOIN seasons_dbtable season ON season.season_id = league.league_season_id AND season_available_score_reporter = 1 "
		. "	LEFT OUTER JOIN ( "
		. "		SELECT "
		. "			score_submission_team_id, "
		. "			sum(case when score_submission_result = 1 then 1 else 0 end) as team_submitted_wins, "
		. "			sum(case when score_submission_result = 2 then 1 else 0 end) as team_submitted_losses, "
		. "			sum(case when score_submission_result = 3 then 1 else 0 end) as team_submitted_ties, "
		. "			sum(case when score_submission_result = 5 then 1 else 0 end) as team_submitted_practices, "
		. "			sum(case when score_submission_result = 4 then 1 else 0 end) as team_submitted_cancels "
		. "		FROM score_submissions_dbtable "
		. "		WHERE score_submission_ignored = 0 "
		. "		GROUP BY score_submission_team_id "
		. "	) teamScoreSubmission ON teamScoreSubmission.score_submission_team_id = team.team_id "
		. "	LEFT OUTER JOIN ( "
		. "		SELECT "
		. "			score_submission_opp_team_id, "
		. "			sum(case when score_submission_result = 1 then 1 else 0 end) as team_opp_submitted_wins, "
		. "			sum(case when score_submission_result = 2 then 1 else 0 end) as team_opp_submitted_losses, "
		. "			sum(case when score_submission_result = 3 then 1 else 0 end) as team_opp_submitted_ties "
		. "		FROM score_submissions_dbtable "
		. "		WHERE score_submission_ignored = 0 "
		. "		GROUP BY score_submission_opp_team_id "
		. "	) oppScoreSubmission ON oppScoreSubmission.score_submission_opp_team_id = team.team_id "
		. "WHERE (team_num_in_league > 0 || team_id = 1) "
		. "ORDER BY league_day_number ASC, league_name ASC, team_num_in_league ASC";
		
		$stmt = $this->db->query($sql);
		$allTeams = [];
		
        while(($row = $stmt->fetch()) != false) {		
            $allTeams[] = Models_Team::withRow($this->db, $this->logger, $row);
        }
		
		return $allTeams;
	}

	function addPlayerToTeam($team, $request) {
		$playersController = new Controllers_PlayersController($this->db, $this->logger);

		$player = Models_Player::withRow($this->db, $this->logger, []);
		$playersController->updatePlayerFromRequest($player, $request);
		$player->setTeamId($team->getId());
		$player->save();
	}

	function changeTeamPositionInLeague($team, $newPosition) {
		$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
		$leaguesController->updateLeagueTeamOrder($team->getLeague());

		$allTeams = $team->getLeague()->getTeams();

		if($newPosition > sizeof($allTeams) || $newPosition <= 0) {
			return;
		}

		$teamFromDb = Models_Team::withID($this->db, $this->logger, $team->getId());
		$curPosition = $teamFromDb->getNumInLeague();

		if($curPosition == $newPosition) return;

		for($i = 1; $i <= sizeof($allTeams); $i++) {
			$curTeam = $allTeams[$i - 1];

			if($curPosition < $newPosition) { //Moving a team back
				if($i > $curPosition && $i <= $newPosition) {
					$curTeam->setNumInLeague($i - 1);
					$curTeam->update();
				}
			} else { //Moving a team forward
				if($i < $curPosition && $i >= $newPosition) {
					$curTeam->setNumInLeague($i + 1);
					$curTeam->update();
				}
			}
		}

		$team->setNumInLeague($newPosition);
		$team->update();
	}

	static function compareTeamPosition($teamOne, $teamTwo) {
		return $teamOne->getHeadToHeadDifferential($teamTwo);
	}
}
