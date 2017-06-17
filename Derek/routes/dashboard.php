<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->get('/dashboard-forbidden', function (Request $request, Response $response) {
		return $this->view->render($response, "forbidden.phtml", [
				"request" => $request,
				"router" => $this->router
			]
		);
	})->setName('dashboard-forbidden')->add($dashboard)->add($authenticate);
	
	$app->get('/dashboard-bad-request', function (Request $request, Response $response) {
		return $this->view->render($response, "bad-request.phtml", [
				"request" => $request,
				"router" => $this->router
			]
		);
	})->setName('dashboard-bad-request')->add($dashboard)->add($authenticate);
	
	//Edit Team
	$app->get('/edit-team/{teamID}', function (Request $request, Response $response) {
		
		$user = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]); //Load user from db, that way we refresh all user info.
		$team = Models_Team::withID($this->db, $this->logger, (int)$request->getAttribute('teamID'));
		
		if(is_null($team->getId())) {
			return $response->withRedirect($this->router->pathFor('dashboard-bad-request'), 400);
		}
		
		if($team->getManagedByUserId() != $user->getId() && $user->getAccess() != Includes_Accesslevel::ADMIN) {
			return $response->withRedirect($this->router->pathFor('dashboard-forbidden'), 403);
		}
		
		$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
		$seasonsController = new Controllers_SeasonsController($this->db, $this->logger);
		
		return $this->view->render($response, "dashboard/edit-team.phtml", [
			"user" => $user,
			"team" => $team,
			"router" => $this->router,
			"leaguesAvailableForRegistration" => $leaguesController->getLeaguesForRegistration($team->getLeague()->getSportId()),
			"seasonsAvailableForRegistration" => $seasonsController->getSeasonsAvailableForRegistration(),
			"sport" => $team->getLeague()->getSport()
		]);
				
	})->setName('edit-team')->add($dashboard)->add($authenticate);