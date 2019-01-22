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

			if($team->getLeague()->getId() != $league->getId()) {
				return $response->withRedirect($this->router->pathFor('cp-edit-team', ['sportID' => $sport->getId(), 'leagueID' => $team->getLeague()->getId(), 'teamID' => $team->getId()]));
			}

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
		
	})->add($controlPanel)->add($authenticateAdmin);

	$app->group('/control-panel/registration', function () use ($app) {
		$app->get('/team-quick-add/{leagueID}', function (Request $request, Response $response) {

			$leagueID = (int)$request->getAttribute('leagueID');
			$league = Models_League::withID($this->db, $this->logger, $leagueID);

			return $this->view->render($response, "control-panel/registration/components/createTeam.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"curLeague" => $league
				]
			);

		})->setName("cp-team-quick-add");

		$app->get('/league-print-individual-teams/{leagueID}', function (Request $request, Response $response) {

			$leagueID = (int)$request->getAttribute('leagueID');
			$league = Models_League::withID($this->db, $this->logger, $leagueID);

			return $this->view->render($response, "control-panel/registration/components/leagueIndividualTeams.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"curLeague" => $league
				]
			);

		})->setName("cp-print-individuals-teams");

		$app->get('/league-excel-code/{leagueID}', function (Request $request, Response $response) {

			$leagueID = (int)$request->getAttribute('leagueID');
			$league = Models_League::withID($this->db, $this->logger, $leagueID);

			return $this->view->render($response, "control-panel/registration/components/leagueExcelCode.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"curLeague" => $league
				]
			);

		})->setName("cp-league-excel-code");

	})->add($authenticateAdmin);
	
?>
