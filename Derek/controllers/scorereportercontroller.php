<?php

class Controllers_ScoreReporterController extends Controllers_Controller {
	
	public function getLeagueById($leagueID) {
		$sql = "SELECT league.*,season.* FROM $this->leaguesTable as league "
				. "INNER JOIN $this->seasonsTable as season ON season.season_id = league.league_season_id "
				. "WHERE league.league_id = $leagueID";
						
		$stmt = $this->db->query($sql);
		
        if(($row = $stmt->fetch()) != false) {		
            return new Models_League($row);
        }
	}
	
	public function getTeamById($teamID) {
		$sql = "SELECT team.* FROM $this->teamsTable as team "
				. "WHERE team.team_id = $teamID";
						
		$stmt = $this->db->query($sql);
		
        if(($row = $stmt->fetch()) != false) {		
            return new Models_Team($row);
        }
	}
	
    public function getLeagues($sportID) {

        $sql = "SELECT league.*,season.* FROM $this->leaguesTable as league "
				. "INNER JOIN $this->seasonsTable season ON season.season_id = league.league_season_id "
				. "WHERE season.season_available_score_reporter = 1 AND league.league_sport_id = $sportID "
				. "ORDER BY league.league_season_id ASC, league.league_day_number ASC, league.league_name ASC";
		
        $stmt = $this->db->query($sql);

        $results = [];
		
        while($row = $stmt->fetch()) {
			
            $results[] = new Models_League($row);
        }
		
        return $results;
    }
	
	function getTeams($leagueID) {
		$league = $this->getLeagueById($leagueID);
				
		if(isset($league) && $league->getId() != null) {
			
			if($league->getHasPracticeGames()) { //league has practice, include the practice team
				$sql = "SELECT team.* FROM $this->teamsTable as team "
						. "WHERE ((team.team_league_id = $leagueID AND team.team_num_in_league > 0) OR team.team_id = 1) AND team.team_dropped_out = 0 "
						. "ORDER BY team.team_num_in_league";
			} else {
				$sql = "SELECT team.* FROM $this->teamsTable as team "
						. "WHERE team.team_league_id = $leagueID AND team.team_num_in_league > 0 AND team.team_dropped_out = 0 "
						. "ORDER BY team.team_num_in_league";
			}
			
			$stmt = $this->db->query($sql);

			$results = [];

			while($row = $stmt->fetch()) {
				$results[] = new Models_Team($row);
			}

			return $results;
			
		}
		
		return [];
	}
	
	public function getMatches($team, $league) {
		
		if(isset($team) && $team->getId() != null && isset($league) && $league->getId() != null) {
			$sql = "SELECT scheduled_match_id,scheduled_match_date_id,scheduled_match_team_id_1,scheduled_match_team_id_2 FROM $this->scheduledMatchesTable "
					. "INNER JOIN $this->datesTable ON $this->scheduledMatchesTable.scheduled_match_date_id = $this->datesTable.date_id "
					. "INNER JOIN $this->leaguesTable ON $this->scheduledMatchesTable.scheduled_match_league_id = $this->leaguesTable.league_id "
					. "WHERE ($this->scheduledMatchesTable.scheduled_match_team_id_2 = " . $team->getId() . " OR $this->scheduledMatchesTable.scheduled_match_team_id_1 = " . $team->getId() . ") "
					. "AND $this->datesTable.date_week_number = $this->leaguesTable.league_week_in_score_reporter "
					. "AND $this->datesTable.date_season_id = $this->leaguesTable.league_season_id AND $this->leaguesTable.league_id = " . $team->getLeagueId() . " "
					. "ORDER BY scheduled_match_time";

			$stmt = $this->db->query($sql);

			$matches = [];

			//goes through the results and figures out which opponents team teamID had
			for($i = 0; $i < $league->getNumMatches(); $i++) {

				$matchNode = $stmt->fetch();
				//This if statements checks if both scheduled matches are against the same team, this can happen if you double submit a leagues matches for a week.
				if($i != 0) {
					if(($matchNode['scheduled_match_team_id_1'] == $matches[$i-1]->getOppTeamId() || $matchNode['scheduled_match_team_id_2'] == $matches[$i-1]->getOppTeamId()) 
							&& $matchNode['scheduled_match_team_id_1'] != '') {
						$i--;
						continue;
					}
				}

				$match = new Models_Match($matchNode);

				if ($matchNode['scheduled_match_team_id_1'] == $team->getId()) {
					$match->setOppTeamId($matchNode['scheduled_match_team_id_2']);
				} else {
					$match->setOppTeamId($matchNode['scheduled_match_team_id_1']);
				}

				$matches[] = $match;
			}

			return $matches;
		}
		
		return [];
	}

    /**
     * Get one ticket by its ID
     *
     * @param int $ticket_id The ID of the ticket
     * @return TicketEntity  The ticket
     */
    public function getTicketById($ticket_id) {
        $sql = "SELECT t.id, t.title, t.description, c.component
            from tickets t
            join components c on (c.id = t.component_id)
            where t.id = :ticket_id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["ticket_id" => $ticket_id]);

        if($result) {
            return new TicketEntity($stmt->fetch());
        }

    }

    public function save(TicketEntity $ticket) {
        $sql = "insert into tickets
            (title, description, component_id) values
            (:title, :description, 
            (select id from components where component = :component))";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "title" => $ticket->getTitle(),
            "description" => $ticket->getDescription(),
            "component" => $ticket->getComponent(),
        ]);

        if(!$result) {
            throw new Exception("could not save record");
        }
    }
}
