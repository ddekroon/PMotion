<?php

	class Controllers_LeaguesController extends Controllers_Controller {

		public function getLeaguesInScoreReporter($sportID) {

			$sql = "SELECT league.*,season.* FROM " . Includes_DBTableNames::leaguesTable . " as league "
					. "INNER JOIN " . Includes_DBTableNames::seasonsTable . " season ON season.season_id = league.league_season_id "
					. "WHERE season.season_available_score_reporter = 1 AND league.league_sport_id = $sportID "
					. "ORDER BY league.league_season_id ASC, league.league_day_number ASC, league.league_name ASC";

			$stmt = $this->db->query($sql);

			$results = [];

			while($row = $stmt->fetch()) {
				$results[] = Models_League::withRow($this->db, $row);
			}

			return $results;
		}
		
		public function getActiveDate(Models_League $league) {
			
			$sql = "SELECT * FROM " . Includes_DBTableNames::datesTable 
					. " Inner Join " . Includes_DBTableNames::leaguesTable . " ON " . Includes_DBTableNames::datesTable . ".date_day_number = " . Includes_DBTableNames::leaguesTable . ".league_day_number"
					. " WHERE " . Includes_DBTableNames::leaguesTable . ".league_id = " . $league->getId()
					. " AND " . Includes_DBTableNames::datesTable . ".date_week_number = " . Includes_DBTableNames::leaguesTable . ".league_week_in_score_reporter"
					. " AND date_sport_id = " . $league->getSportId() . " AND date_season_id = league_season_id LIMIT 1";
			
			$stmt = $this->db->query($sql);
			
			if(($row = $stmt->fetch()) != false) {
				return Models_Date::withRow($this->db, $row);
			}
			
			return null;
		}
		
		function setLeagueWeek($leagueID) {
			/*
			 * TODO:
			global $leaguesTable, $teamsTable, $datesTable, $scheduledMatchesTable, $seasonsTable;
			$leagueArray = query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID");
			$leagueWeek = $leagueArray['league_week_in_score_reporter'];
			$leagueSport = $leagueArray['league_sport_id'];
			$leagueSeason = $leagueArray['league_season_id'];
			$leagueDay = $leagueArray['league_day_number'];
			$dateChangeTime = $leagueArray['league_hide_spirit_hour'];

			$dateQuery = mysql_query("SELECT * FROM $datesTable 
				INNER JOIN $scheduledMatchesTable ON $scheduledMatchesTable.scheduled_match_date_id = $datesTable.date_id 
				WHERE (date_week_number = $leagueWeek + 1 OR date_week_number = $leagueWeek + 2) AND date_sport_id = $leagueSport
				AND date_season_id = $leagueSeason AND date_day_number = $leagueDay ORDER BY date_day_of_year_num ASC")
				or die('ERROR getting dates '.mysql_error());
			if(mysql_num_rows($dateQuery) == 0) {
				$dateQuery = mysql_query("SELECT * FROM $datesTable WHERE date_week_number = $leagueWeek + 1 AND date_sport_id = 
					$leagueSport AND date_season_id = $leagueSeason AND date_day_number = $leagueDay") 
					or die('ERROR getting date 2 - '.mysql_error());
			}
			$dateArray = mysql_fetch_array($dateQuery);
			$dateDayOfYear = $dateArray['date_day_of_year_num'];
			$nextWeek = $dateArray['date_week_number'];
			$curDayOfYear = date('z');
			$curTime = intval(date('G'));

			
			//if it is a correct time to switch the week in score reporter
			if ($dateDayOfYear != '' && $curDayOfYear > $dateDayOfYear || ($curDayOfYear == $dateDayOfYear && $curTime >= $dateChangeTime)) { 
				mysql_query("UPDATE $leaguesTable SET league_week_in_score_reporter = $nextWeek,
					league_show_cancel_default_option = 0 WHERE league_id = $leagueID") 
					or die('ERROR setting new week '.mysql_error());
				$leagueWeek = $nextWeek;
			}
			return $leagueWeek;
			 * 
			 */
		}

	}

?>