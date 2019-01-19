<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->group('/api/teams', function () use ($app) {
		$app->get('/{teamID}', function (Request $request, Response $response) {

			$teamID = (int)$request->getAttribute('teamID');

			$response->getBody()->write(json_encode(Models_Team::withID($this->db, $this->logger, $teamID)));

			return $response;
		});

		$app->get('/teams-for-league/{leagueID}', function (Request $request, Response $response) {

			$leagueID = (int)$request->getAttribute('leagueID');

			$teamsController = new Controllers_TeamsController($this->db, $this->logger);

			$response->getBody()->write(json_encode($teamsController->getTeams($leagueID)));

			return $response;
		});

		$app->get('/active-teams-for-user/{userID}', function (Request $request, Response $response) {

			$this->logger->debug("Hi derek");

			$userID = (int)$request->getAttribute('userID');

			$teamsController = new Controllers_TeamsController($this->db, $this->logger);
			$seasonsController = new Controllers_SeasonsController($this->db, $this->logger);

			$user = Models_User::withID($this->db, $this->logger, $userID);

			$allTeams = array();
			$allSeasons = array_merge(
				$seasonsController->getSeasonsAvailableForRegistration(),
				$seasonsController->getSeasonsAvailableForScoreReporter()
			);

			foreach($allSeasons as $curSeason) {
				$allTeams = array_merge($allTeams, $teamsController->getTeamsForUser($user, $curSeason, null));
			}

			$response->getBody()->write(json_encode($allTeams));

			return $response;
		});
	
	})->add($authenticate);

	$app->group('/api/teams', function () use ($app) {
		$app->delete('/{teamID}', function (Request $request, Response $response) {

			$teamID = (int)$request->getAttribute('teamID');

			$teamsController = new Controllers_TeamsController($this->db, $this->logger);

			$team = Models_Team::withID($this->db, $this->logger, $teamID);

			if($team != null) {
				$teamsController->removeTeam($team);
				$response = $response->withStatus(200);
				$response->getBody()->write("Team deleted");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Invalid team ID");
			}

			return $response;
		})->setName("team-delete");

		$app->post('/register/{teamID}', function (Request $request, Response $response) {

			$teamID = (int)$request->getAttribute('teamID');

			$teamsController = new Controllers_TeamsController($this->db, $this->logger);

			$team = Models_Team::withID($this->db, $this->logger, $teamID);

			if($team != null) {
				$league = $team->getLeague();
				$teams = $league->getTeams();

				$team->setNumInLeague(sizeof($teams) + 1);
				$team->setIsFinalized(true);
				$team->update();

				$response = $response->withStatus(200);
				$response->getBody()->write("Team registered");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Invalid team ID");
			}

			return $response;
		})->setName("team-register");

		$app->post('/deregister/{teamID}', function (Request $request, Response $response) {

			$teamID = (int)$request->getAttribute('teamID');
			$team = Models_Team::withID($this->db, $this->logger, $teamID);

			if($team != null) {
				$curTeamNum = $team->getNumInLeague();
				$team->setNumInLeague(0);
				$team->setIsFinalized(false);
				$team->update();

				$league = $team->getLeague();
				$teams = $league->getTeams();

				for($i = 0; $i < sizeof($teams); $i++) {
					$teams[$i]->setNumInLeague($i + 1);
					$teams[$i]->update();
				}

				$response = $response->withStatus(200);
				$response->getBody()->write("Team deregistered");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Invalid team ID");
			}

			return $response;
		})->setName("team-deregister");

		$app->post('/togglePaid/{teamID}', function (Request $request, Response $response) {

			$teamID = (int)$request->getAttribute('teamID');
			$team = Models_Team::withID($this->db, $this->logger, $teamID);

			if($team != null && $team->getId() > 0) {
				$team->setIsPaid(!$team->getIsPaid());
				$team->update();
				$response = $response->withStatus(200);
				$response->getBody()->write("Team marked " . ($team->getIsPaid() ? "paid" : "NOT paid"));
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Invalid team ID");
			}

			return $response;
		})->setName("team-toggle-paid");

	})->add($authenticateAdmin);
	
?>

