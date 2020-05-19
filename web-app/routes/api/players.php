<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->group('/api/players', function () use ($app) {
		$app->get('/{playerID}', function (Request $request, Response $response) {
			$playerID = (int)$request->getAttribute('playerID');
			$response->getBody()->write(json_encode(Models_Player::withID($this->db, $this->logger, $playerID)));

			return $response;
		});
	})->add($authenticate);

	$app->group('/api/players', function () use ($app) {
		$app->delete('/{playerID}', function (Request $request, Response $response) {

			$playerID = (int)$request->getAttribute('playerID');

			$playersController = new Controllers_PlayersController($this->db, $this->logger);

			$player = Models_Player::withID($this->db, $this->logger, $playerID);

			if($player != null && $player->getId() > 0) {
				$playersController->deletePlayer($player);
				$response = $response->withStatus(200);
				$response->getBody()->write("Player deleted");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Invalid player ID");
			}

			return $response;
		})->setName("player-delete");

		$app->post('/update/{playerID}', function (Request $request, Response $response) {

			$playerID = (int)$request->getAttribute('playerID');
			$player = Models_Player::withID($this->db, $this->logger, $playerID);

			if($player != null && $player->getId() > 0) {
				$playersController = new Controllers_PlayersController($this->db, $this->logger);
				$playersController->updatePlayerFromRequest($player, $request);

				$response = $response->withStatus(200);
				$response->getBody()->write("Player updated");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Invalid player ID");
			}

			return $response;
		})->setName("player-update");


		$app->post('/addPlayerToTeam/{playerID}/{teamID}', function (Request $request, Response $response) {

			$playerID = (int)$request->getAttribute('playerID');
			$teamID = (int)$request->getAttribute('teamID');
			$player = Models_Player::withID($this->db, $this->logger, $playerID);
			$team = Models_Team::withID($this->db, $this->logger, $teamID);

			if($player != null && $player->getId() > 0) {
				$player->setTeamId($team != null && $team->getId() > 0 ? $teamID : null);
				$player->update();
				$response = $response->withStatus(200);
				$response->getBody()->write("Player added to team");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Couldn't add player to team, invalid player given.");
			}

			return $response;
		})->setName("player-add-to-team");

		$app->post('/addGroupToTeam/{groupID}/{teamID}', function (Request $request, Response $response) {

			$groupID = (int)$request->getAttribute('groupID');
			$teamID = (int)$request->getAttribute('teamID');

			$playersController = new Controllers_PlayersController($this->db, $this->logger);

			$players = $playersController->getPlayersInGroup($groupID);
			$team = Models_Team::withID($this->db, $this->logger, $teamID);

			if(isset($players) && sizeof($players) > 0) {
				foreach($players as $curPlayer) {
					$curPlayer->setTeamId($team != null && $team->getId() > 0 ? $teamID : null);
					$curPlayer->update();
				}
				
				$response = $response->withStatus(200);
				$response->getBody()->write("Player added to team");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Couldn't add group to team, invalid player given.");
			}

			return $response;
		})->setName("player-add-to-team");

		$app->post('/removePlayerFromGroup/{playerID}', function (Request $request, Response $response) {

			$playerID = (int)$request->getAttribute('playerID');
			$player = Models_Player::withID($this->db, $this->logger, $playerID);

			$this->logger->debug($player->getId());
			$this->logger->debug($player->getIsIndividual());
			$this->logger->debug($player->getIndividual()->getId());
			

			if($player != null && $player->getId() > 0 && $player->getIsIndividual() && $player->getIndividual() != null) {
				$ind = Models_Individual::withID($this->db, $this->logger, $player->getIndividual()->getId());
				$ind->setGroupId(0);
				$ind->update();

				$response = $response->withStatus(200);
				$response->getBody()->write("Player removed from group");
			} else {
				$response = $response->withStatus(400);
				$response->getBody()->write("Couldn't remove player from group, invalid player given.");
			}

			return $response;
		})->setName("player-add-to-team");

	})->add($authenticateAdmin);
	
?>