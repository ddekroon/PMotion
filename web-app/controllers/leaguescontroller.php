<?php

	class Controllers_LeaguesController extends Controllers_Controller {

		public function getLeaguesInScoreReporter($sportID) {

			$sql = "SELECT league.* FROM " . Includes_DBTableNames::leaguesTable . " as league "
					. "INNER JOIN " . Includes_DBTableNames::seasonsTable . " season ON season.season_id = league.league_season_id "
					. "WHERE season.season_available_score_reporter = 1 AND league.league_sport_id = $sportID "
					. "ORDER BY league.league_season_id ASC, league.league_day_number ASC, league.league_name ASC";

			$stmt = $this->db->query($sql);

			$results = [];

			while($row = $stmt->fetch()) {
				$results[] = Models_League::withRow($this->db, $this->logger, $row);
			}

			return $results;
		}
		
		public function getLeaguesForRegistration($sportID) {

			if($sportID == null || $sportID <= 0) {
				return [];
			}
			
			$sql = "SELECT league.* FROM " . Includes_DBTableNames::leaguesTable . " as league "
					. "INNER JOIN " . Includes_DBTableNames::seasonsTable . " season ON season.season_id = league.league_season_id "
					. "WHERE season.season_available_registration = 1 AND league.league_sport_id = $sportID "
					. "ORDER BY league.league_season_id ASC, league.league_day_number ASC, league.league_name ASC";
			
			$stmt = $this->db->query($sql);

			$results = [];

			while($row = $stmt->fetch()) {
				$results[] = Models_League::withRow($this->db, $this->logger, $row);
			}

			return $results;
		}
		
		public function getActiveDate(Models_League $league) {
			if($league == null) {
				return null;
			}
			
			$sql = "SELECT * FROM " . Includes_DBTableNames::datesTable 
					. " INNER JOIN " . Includes_DBTableNames::leaguesTable . " ON " . Includes_DBTableNames::datesTable . ".date_day_number = " . Includes_DBTableNames::leaguesTable . ".league_day_number"
					. " WHERE " . Includes_DBTableNames::leaguesTable . ".league_id = " . $league->getId()
					. " AND " . Includes_DBTableNames::datesTable . ".date_week_number = " . $league->getWeekInScoreReporter()
					. " AND date_sport_id = " . $league->getSportId() . " AND date_season_id = league_season_id LIMIT 1";
			
			$stmt = $this->db->query($sql);
			
			if(($row = $stmt->fetch()) != false) {
				return Models_Date::withRow($this->db, $this->logger, $row);
			}
			
			return null;
		}
		
		function setLeagueWeekInScoreReporter(Models_League $league) {
			
			$datesController = new Controllers_DatesController($this->db, $this->logger);
			$nextDate = $datesController->getNextLeagueDate($league);
			
			$curDayOfYear = date('z');
			$curTime = intval(date('G'));
			
			//if it is a correct time to switch the week in score reporter
			if (isset($nextDate) && 
					(
						$curDayOfYear > $nextDate->getDayOfYearNumber() 
						|| ($curDayOfYear == $nextDate->getDayOfYearNumber() && $curTime >= $league->getHideSpiritHour())
					)
			) { 
				
				$this->updateLeagueWeekNumberInScoreReporter($league, $nextDate->getWeekNumber());
				
				return true;
			}
			
			return false;
		}
		
		private function updateLeagueWeekNumberInScoreReporter(Models_League $league, $newWeekNumber) {
			$sql = "UPDATE " . Includes_DBTableNames::leaguesTable . " SET league_week_in_score_reporter = $newWeekNumber,
					league_show_cancel_default_option = 0 WHERE league_id = " . $league->getId(); 
			
			$this->db->query($sql);
			
			$league->setWeekInScoreReporter($newWeekNumber);
		}
		
		public function setLeagueWeekInStandings(Models_League $league) {
			if($league->getWeekInStandings() >= 50) {
				return; //This league is closed out
			}
			
			$maxWeek = 0;
			
			foreach($league->getTeams() as $team) {
				if($team->getMostRecentWeekSubmitted() > $maxWeek) {
					$maxWeek = $team->getMostRecentWeekSubmitted();
				}
			}
			
			if($maxWeek > $league->getWeekInStandings()) {
				$this->updateLeagueWeekNumberInStandings($league, $maxWeek);
				return true;
			}
			
			return false;
		}
		
		private function updateLeagueWeekNumberInStandings(Models_League $league, $newWeekNumber) {
			$sql = "UPDATE " . Includes_DBTableNames::leaguesTable . " SET league_week_in_standings = $newWeekNumber"
					. " WHERE league_id = " . $league->getId(); 
			
			$this->db->query($sql);
			
			$league->setWeekInStandings($newWeekNumber);
		}
		
		/* function getTeamsForStandings($league) {
			if ($league->getWeekInStandings() > 1) {
				$queryString = "SELECT
					team_id, team_name, team_wins, team_losses, team_ties, team_most_recent_week_submitted,
					team_wins * 2 + team_ties as team_points,
					SUM(spirit_score_edited_value) / COUNT(spirit_score_edited_value) as team_spirit_average
					FROM (SELECT * FROM $teamsTable where team_league_id = $leagueID AND team_num_in_league > 0 AND team_dropped_out = 0) as teamTable
					INNER JOIN $leaguesTable ON $leaguesTable.league_id = teamTable.team_league_id
					INNER JOIN $scoreSubmissionsTable ON ($scoreSubmissionsTable.score_submission_opp_team_id = teamTable.team_id 
					AND (score_submission_ignored = 0 OR score_submission_is_phantom = 1))
					INNER JOIN $spiritScoresTable ON ($spiritScoresTable.spirit_score_score_submission_id = $scoreSubmissionsTable.score_submission_id AND 
					(spirit_score_edited_value > 3.5 OR spirit_score_dont_show = 1 OR spirit_score_is_admin_addition = 1) AND spirit_score_edited_value > 0
					AND spirit_score_ignored = 0)
					INNER JOIN $datesTable ON (date_id = score_submission_date_id AND date_week_number < league_playoff_week)
					GROUP BY team_id";
				if(!($teamsQuery = $dbConnection->query($queryString))) { 
					print 'ERROR getting team objects - '.$dbConnection->error;
					exit(0);
				} else if($teamsQuery->num_rows == 0) {
					print 'Error, no teams';
					exit(0);
				}

				$teamMaxWeek = 0;
				while($teamObj = $teamsQuery->fetch_object()){ 
					$teams[] = new Team($teamObj);
					if ($teamObj->team_most_recent_week_submitted > $teamMaxWeek ) {
						$teamMaxWeek = $teamObj->team_most_recent_week_submitted;
					}
				}
				$teamsQuery->close();
				return $teams;	
			} else {
				$queryString = "SELECT
					team_id, team_name, team_wins, team_losses, team_ties, team_most_recent_week_submitted,
					team_wins * 2 + team_ties as team_points,
					SUM(spirit_score_edited_value) / COUNT(spirit_score_edited_value) as team_spirit_average
					FROM (SELECT * FROM $teamsTable where team_league_id = $leagueID AND team_num_in_league > 0 AND team_dropped_out = 0) as teamTable
					INNER JOIN $leaguesTable ON $leaguesTable.league_id = teamTable.team_league_id
					LEFT JOIN $scoreSubmissionsTable ON ($scoreSubmissionsTable.score_submission_opp_team_id = teamTable.team_id 
					AND (score_submission_ignored = 0 OR score_submission_is_phantom = 1))
					LEFT JOIN $spiritScoresTable ON ($spiritScoresTable.spirit_score_score_submission_id = $scoreSubmissionsTable.score_submission_id AND 
					(spirit_score_edited_value > 3.5 OR spirit_score_dont_show = 1 OR spirit_score_is_admin_addition = 1) AND spirit_score_edited_value > 0
					AND spirit_score_ignored = 0)
					LEFT JOIN $datesTable ON (date_id = score_submission_date_id AND date_week_number < league_playoff_week)
					GROUP BY team_id";
				if(!($teamsQuery = $dbConnection->query($queryString))) { 
					print 'ERROR getting team objects - '.$dbConnection->error;
					exit(0);
				} else if($teamsQuery->num_rows == 0) {
					print 'Error, no teams';
					exit(0);
				}

				$teamMaxWeek = 0;
				while($teamObj = $teamsQuery->fetch_object()){ 
					$teams[] = new Team($teamObj);
					if ($teamObj->team_most_recent_week_submitted > $teamMaxWeek ) {
						$teamMaxWeek = $teamObj->team_most_recent_week_submitted;
					}
				}
				$teamsQuery->close();
				return $teams;	
			}
		} */
		
		function checkHideSpirit($league){
	
			if(!$league->getSeason()->getIsAvailableScoreReporter()) {
				return false;
			}

			$dayOfWeekNum = date('N'); //Number representing day of week... Mon=1, Tues=2..Sun=7
			$timeOfDay = date('G');   //24 Hour representation of time: 0-23

			$dateHide = $league->getDayNumber();
			$dateShow = $dateHide + $league->getNumDaysSpiritHidden();
			
			if($dateShow > 7) {
				$dateShow = $dateShow % 7; //if games are sunday show date is gonna be greater than 7.
			}

			if($dayOfWeekNum == $dateHide) { //If it's the day of the game, hide spirit
				return $timeOfDay >= $league->getHideSpiritHour();
			}

			if(($dayOfWeekNum == $dateShow)) { //If it's 2 days after the game, show spirit
				return !($timeOfDay >= $league->getShowSpiritHour());
			}

			return ($dayOfWeekNum > $dateHide && $dayOfWeekNum < $dateShow) || ($dayOfWeekNum == 1 && $dateHide == 7);

		}

	}

?>