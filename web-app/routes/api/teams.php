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
	
?>

