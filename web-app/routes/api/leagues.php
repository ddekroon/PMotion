<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->group('/api/leagues', function () use ($app) {
		$app->get('/{leagueID}', function (Request $request, Response $response) {

			$scheduledMatchesController = new Controllers_ScheduledMatchesController($this->db, $this->logger);
			$leagueController = new Controllers_LeaguesController($this->db, $this->logger);
			$datesController = new Controllers_DatesController($this->db, $this->logger);

			$leagueID = (int)$request->getAttribute('leagueID');
			$league = Models_League::withID($this->db, $this->logger, $leagueID);

			if($league->getId() > 0) {
				$league->getTeams();
			}

			$teamsForStandings = $league->getTeams();

			if($league->getWeekInStandings() < 50) { // active league
				if (!$league->getIsSortByWinPct()) {
					usort($teamsForStandings, array("Controllers_TeamsController", "comparePoints"));
				} else {
					usort($teamsForStandings, array("Controllers_TeamsController", "comparePercent"));
				}
			} else {
				usort($teamsForStandings, array("Controllers_TeamsController", "comparePosition"));
			}

			if(!$leagueController->checkHideSpirit($league)) {
				foreach($teamsForStandings as $curTeam) {
					$curTeam->getSpiritAverage();
				}
			}

			$league->setScheduledMatches($scheduledMatchesController->getLeagueScheduledMatches($league, false));
			$league->setStandings($teamsForStandings);
			$league->setDates($datesController->getFilteredDates($league->getSportId(), $league->getSeasonId(), $league->getDayNumber()));

			return $response->withStatus(200)
				->withHeader('Content-Type', 'application/json')
				->write(json_encode($league));
		});
	});

	$app->group('/api/leagues', function () use ($app) {
		$app->post('/quick-add-agent/{leagueID}', function (Request $request, Response $response) {

			$leagueID = (int)$request->getAttribute('leagueID');
			$league = Models_League::withID($this->db, $this->logger, $leagueID);

			$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);

			if($league != null && $league->getId() > 0 ) {
				$leaguesController->addAgentToLeague($league, $request);
				$response = $response->withStatus(200);
				$response->getBody()->write("Agent added to league");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Invalid league");
			}

			return $response;
		})->setName("league-quick-add-free-agent");

	})->add($authenticateAdmin);
	
?>