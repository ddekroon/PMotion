<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->get('/dashboard/forbidden', function (Request $request, Response $response) {
		return $this->view->render($response, "forbidden.phtml", [
				"request" => $request,
				"router" => $this->router
			]
		);
	})->setName('dashboard-forbidden')->add($dashboard)->add($authenticate);
	
	$app->get('/dashboard/bad-request', function (Request $request, Response $response) {
		return $this->view->render($response, "bad-request.phtml", [
				"request" => $request,
				"router" => $this->router
			]
		);
	})->setName('dashboard-bad-request')->add($dashboard)->add($authenticate);
	
	//Edit Profile
	$app->get('/dashboard/edit-profile', function (Request $request, Response $response) {
		$user = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]);
		
		if(is_null($user) || $user->getId() == null) {
			return $response->withRedirect($this->router->pathFor('dashboard-forbidden'), 403);
		}
		
		return $this->view->render($response, "dashboard/edit-profile.phtml", [
			"user" => $user
		]);
	})->setName('edit-profile')->add($dashboard)->add($authenticate);
	
	//Edit Team
	$app->get('/dashboard/edit-team/{teamID}', function (Request $request, Response $response) {
		
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
	
	$app->get('/dashboard/score-reporter[/{sportID}[/{leagueID}[/{teamID}]]]', function(Request $request, Response $response) {

		$user = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]);
		$sport = Models_Sport::withID($this->db, $this->logger, $request->getAttribute('sportID'));
		$league = Models_League::withID($this->db, $this->logger, $request->getAttribute('leagueID'));
		$team = Models_Team::withID($this->db, $this->logger, $request->getAttribute('teamID'));

		$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
		
		if($league != null) {
			$leaguesController->setLeagueWeek($league);
		}

		return $this->view->render($response, "score-reporter/score-reporter.phtml", [
				"request" => $request,
				"user" => $user,
				"sport" => $sport,
				"league" => $league,
				"team" => $team,
				"router" => $this->router,
				"leagues" => $leaguesController->getLeaguesInScoreReporter($sport->getId())
			]
		);
	})->setName('dashboard-score-reporter')->add($dashboard)->add($authenticate);
	
	$app->get('/dashboard/standings/{leagueID}', function(Request $request, Response $response) {
		
		$user = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]);
		$league = Models_League::withID($this->db, $this->logger, $request->getAttribute('leagueID'));
		
		$league->checkUpdateWeekInStandings();
		
		return $this->view->render($response, "standings.phtml", [
				"request" => $request,
				"user" => $user,
				"league" => $league,
				"router" => $this->router,
				"leagueController" => new Controllers_LeaguesController($this->db, $this->logger),
				"teamController" => new Controllers_TeamsController($this->db, $this->logger)
			]
		);
	})->setName('dashboard-standings')->add($dashboard)->add($authenticate);
	
	//Edit Team
	$app->get('/dashboard/team/{teamID}', function (Request $request, Response $response) {
		$user = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]);
		
		if(is_null($user) || $user->getId() == null) {
			return $response->withRedirect($this->router->pathFor('dashboard-forbidden'), 403);
		}
		
		$team = Models_Team::withID($this->db, $this->logger, $request->getAttribute('teamID'));
		
		return $this->view->render($response, "team-page.phtml", [
				"request" => $request,
				"user" => $user,
				"team" => $team,
				"router" => $this->router,
				"isDashboard" => true,
				"leagueController" => new Controllers_LeaguesController($this->db, $this->logger),
				"teamController" => new Controllers_TeamsController($this->db, $this->logger)
		]); 
	})->setName('dashboard-team-page')->add($dashboard)->add($authenticate);