<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->group('/api/leagues', function () use ($app) {
		$app->get('/{leagueID}', function (Request $request, Response $response) {

			$leagueID = (int)$request->getAttribute('leagueID');

			$response->getBody()->write(json_encode(Models_League::withID($this->db, $this->logger, $leagueID)));

			return $response;
		});

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

