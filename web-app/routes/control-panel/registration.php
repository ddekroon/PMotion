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
			
			$seasons = array_unique(array_merge($seasonsRegistration, $seasonsScoreReporter));

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
		$app->get('/player[/{playerID}]', function (Request $request, Response $response) {
			$player = Models_Player::withID($this->db, $this->logger, $request->getAttribute('playerID'));

			return $this->view->render($response, "control-panel/registration/player.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"curPlayer" => $player,
				]
			);
		})->setName("cp-edit-player");

		$app->get('/team-quick-add/{leagueID}', function (Request $request, Response $response) {

			$leagueID = (int)$request->getAttribute('leagueID');
			$league = Models_League::withID($this->db, $this->logger, $leagueID);

			return $this->view->render($response, "control-panel/registration/components/create-team.phtml", [
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

			return $this->view->render($response, "control-panel/registration/components/league-individual-teams.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"curLeague" => $league
				]
			);
		})->setName("cp-print-individuals-teams");

		$app->get('/league-print-league-teams/{leagueID}', function (Request $request, Response $response) {

			$leagueID = (int)$request->getAttribute('leagueID');
			$league = Models_League::withID($this->db, $this->logger, $leagueID);

			return $this->view->render($response, "control-panel/registration/components/league-teams.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"curLeague" => $league
				]
			);
		})->setName("cp-print-league-teams");

		$app->get('/league-excel-code/{leagueID}', function (Request $request, Response $response) {

			$leagueID = (int)$request->getAttribute('leagueID');
			$league = Models_League::withID($this->db, $this->logger, $leagueID);

			return $this->view->render($response, "control-panel/registration/components/league-excel-code.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"curLeague" => $league
				]
			);
		})->setName("cp-league-excel-code");

		$app->get('/team-add-player[/{teamID}]', function (Request $request, Response $response) {
			$team = Models_Team::withID($this->db, $this->logger, $request->getAttribute('teamID'));

			return $this->view->render($response, "control-panel/registration/components/add-player-form.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"curTeam" => $team,
					"postAction" => $this->router->pathFor("team-quick-add-player", ['teamID' => $team->getId()])
				]
			);
		})->setName("cp-add-player-to-team");

		$app->get('/league-add-free-agent[/{leagueID}]', function (Request $request, Response $response) {
			$league = Models_League::withID($this->db, $this->logger, $request->getAttribute('leagueID'));

			return $this->view->render($response, "control-panel/registration/components/add-player-form.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"curLeague" => $league,
					"postAction" => $this->router->pathFor("league-quick-add-free-agent", ['leagueID' => $league->getId()])
				]
			);
		})->setName("cp-add-free-agent-to-league");

	})->add($authenticateAdmin);
	
?>