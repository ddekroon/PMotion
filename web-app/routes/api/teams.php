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

		$app->post('/toggle-paid/{teamID}', function (Request $request, Response $response) {

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

		$app->post('/quick-update/{teamID}', function (Request $request, Response $response) {

			$teamID = (int)$request->getAttribute('teamID');
			$team = Models_Team::withID($this->db, $this->logger, $teamID);

			if($team != null && $team->getId() > 0) {

				$allPostVars = $request->getParsedBody();

				$team->setName($allPostVars['teamName']);
				$team->setLeagueId($allPostVars['leagueID']);
				$team->setMostRecentWeekSubmitted($allPostVars['teamWeekInScoreReporter']);
				$team->setIsDroppedOut($allPostVars['teamDroppedOut'] > 0);

				$team->update();
				$response = $response->withStatus(200);
				$response->getBody()->write("Team updated");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Invalid team given");
			}

			return $response;
		})->setName("team-quick-update");

		$app->post('/quick-add-submit/{leagueID}', function (Request $request, Response $response) {

			$leagueID = (int)$request->getAttribute('leagueID');
			$league = Models_League::withID($this->db, $this->logger, $leagueID);

			$allPostVars = $request->getParsedBody();
			$teamName = $allPostVars['teamName'];

			if($league != null && $league->getId() > 0 && $teamName != null && strlen($teamName) > 0) {

				$teamNumInLeague = sizeof($league->getTeams()) + 1;
				if($teamNumInLeague < 10) {
					$picName = $leagueID.'-0'.$teamNumInLeague;
				} else {
					$picName = $leagueID.'-'.$teamNumInLeague;
				}
				
				$team = Models_Team::withID($this->db, $this->logger, -1);
				$team->setLeagueId($league->getId());
				$team->setName($allPostVars['teamName']);
				$team->setNumInLeague($teamNumInLeague);
				$team->setDateCreated(new DateTime());
				$team->setIsFinalized(true);
				$team->getPaymentMethod(5);
				$team->setPicName($picName);

				$team->save();
				$response = $response->withStatus(200);
				$response->getBody()->write("Team created");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Invalid team name or league");
			}

			return $response;
		})->setName("team-quick-add");

		$app->post('/quick-add-player/{teamID}', function (Request $request, Response $response) {

			$teamID = (int)$request->getAttribute('teamID');
			$team = Models_Team::withID($this->db, $this->logger, $teamID);

			$teamsController = new Controllers_TeamsController($this->db, $this->logger);

			if($team != null && $team->getId() > 0 ) {
				$teamsController->addPlayerToTeam($team, $request);
				$response = $response->withStatus(200);
				$response->getBody()->write("Player added to team");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Invalid team");
			}

			return $response;
		})->setName("team-quick-add-player");

		$app->post('/change-position-in-league/{teamID}/{newPosition}', function (Request $request, Response $response) {

			$teamID = (int)$request->getAttribute('teamID');
			$newPosition = (int)$request->getAttribute('newPosition');
			$team = Models_Team::withID($this->db, $this->logger, $teamID);

			$teamsController = new Controllers_TeamsController($this->db, $this->logger);

			if($team != null && $team->getId() > 0) {
				$teamsController->changeTeamPositionInLeague($team, $newPosition);
				$response = $response->withStatus(200);
				$response->getBody()->write("Team position updated");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Invalid team");
			}

			return $response;
		})->setName("team-change-position-in-league");

	})->add($authenticateAdmin);
	
?>