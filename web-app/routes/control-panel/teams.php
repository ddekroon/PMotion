<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;
	
	$app->group('/control-panel/teams', function () use ($app) {
		$app->get('/prize-winners[/{timeFrame}[/{sportID}[/{leagueID}]]]', function (Request $request, Response $response) {

			$timeFrame = (int)$request->getAttribute('timeFrame');
			if(!isset($timeFrame) || $timeFrame <= 0) {
				//TODO make timeFrame a db table
				$timeFrame = 9;
			}

			$sport = Models_Sport::withID($this->db, $this->logger, $request->getAttribute('sportID'));
			$league = Models_League::withID($this->db, $this->logger, $request->getAttribute('leagueID'));

			$seasonsController = new Controllers_SeasonsController($this->db, $this->logger);
			$sportsController = new Controllers_SportsController($this->db, $this->logger);
			$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
			$teamsController = new Controllers_TeamsController($this->db, $this->logger);
			$prizesController = new Controllers_PrizeWinnersController($this->db, $this->logger);

			$sports = [];
			$leagues = [];
			$teams = [];
			$winners = [];

			$leagues[] = $league;
			$sports[] = $sport;
			$seasons = $seasonsController->getSeasonsAvailableForScoreReporter();
			$winners = $prizesController->getPrizeWinners($timeFrame, $sports, $leagues);

			if(sizeof($sports) > 0) {
				$teams = $teamsController->getTeams($leagues, $sports, $seasons, false);
			}
			
			return $this->view->render($response, "control-panel/teams/prize-winners.phtml", [
				"request" => $request,
				"router" => $this->router,
				"allSports" => $sportsController->getSports(),
				"curSport" => $sport,
				"curLeague" => $league,
				"seasons" => $seasons,
				"teams" => $teams,
				"timeFrame" => $timeFrame
			]);
		})->setName('cp-prize-winners');
		
	})->add($controlPanel)->add($authenticate);
	
?>