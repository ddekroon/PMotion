<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->group('/control-panel/registration', function () use ($app) {

		$app->get('/home[/{sportID}[/{leagueID}]]', function (Request $request, Response $response) {

			$sport = Models_Sport::withID($this->db, $this->logger, $request->getAttribute('sportID'));
			$league = Models_League::withID($this->db, $this->logger, $request->getAttribute('leagueID'));

			$seasonsController = new Controllers_SeasonsController($this->db, $this->logger);
			$sportsController = new Controllers_SportsController($this->db, $this->logger);
			$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);

			$seasonsRegistration = $seasonsController->getSeasonsAvailableForRegistration();
			$seasonsScoreReporter = $seasonsController->getSeasonsAvailableForScoreReporter();
			
			$seasons = array_merge($seasonsRegistration, $seasonsScoreReporter);

			//print_r($seasons);
			$this->logger->debug($seasons[0]->getName());


			return $this->view->render($response, "control-panel/registration/index.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"curSport" => $sport,
					"curLeague" => $league,
					"allSports" => $sportsController->getSports(),
					"seasons" => $seasons
				]
			);
		})->setName('cp-registration');

		$app->get('/team[/{sportID}[/{leagueID}[/{teamID}]]]', function (Request $request, Response $response) {

			$sport = Models_Sport::withID($this->db, $this->logger, $request->getAttribute('sportID'));
			$league = Models_League::withID($this->db, $this->logger, $request->getAttribute('leagueID'));
			$team = Models_Team::withID($this->db, $this->logger, $request->getAttribute('teamID'));

			$seasonsController = new Controllers_SeasonsController($this->db, $this->logger);
			$sportsController = new Controllers_SportsController($this->db, $this->logger);
			//$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
			//$teamsController = new Controllers_TeamsController($this->db, $this->logger);

			$seasonsRegistration = $seasonsController->getSeasonsAvailableForRegistration();
			$seasonsScoreReporter = $seasonsController->getSeasonsAvailableForScoreReporter();
			
			$seasons = array_merge($seasonsRegistration, $seasonsScoreReporter);

			return $this->view->render($response, "control-panel/registration/team.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"curSport" => $sport,
					"curLeague" => $league,
					"allSports" => $sportsController->getSports(),
					"seasons" => $seasons,
					"curTeam" => $team
				]
			);
		})->setName('cp-edit-team');
		
	})->add($controlPanel)->add($authenticate);
	
?>
