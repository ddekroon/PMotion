<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;
	
	//Dashboard
	$app->get('/dashboard', function (Request $request, Response $response) {
		
		$user = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]); //Load user from db, that way we refresh all user info.
		$seasonsController = new Controllers_SeasonsController($this->db, $this->logger);
		
		return $this->view->render($response, "dashboard.phtml", [
			"user" => $user,
			"router" => $this->router,
			"seasonsInRegistration" => $seasonsController->getSeasonsAvailableForRegistration(),
			"seasonsInScoreReporter" => $seasonsController->getSeasonsAvailableForScoreReporter(),
			"teamsController" => new Controllers_TeamsController($this->db, $this->logger)
		]);
		
	})->setName('dashboard')->add($dashboard)->add($authenticate);

	$app->group('/dashboard', function () use ($app) {
		$app->get('/forbidden', function (Request $request, Response $response) {
			return $this->view->render($response, "forbidden.phtml", [
					"request" => $request,
					"router" => $this->router
				]
			);
		})->setName('dashboard-forbidden');

		$app->get('/bad-request', function (Request $request, Response $response) {
			return $this->view->render($response, "bad-request.phtml", [
					"request" => $request,
					"router" => $this->router
				]
			);
		})->setName('dashboard-bad-request');

		//Edit Profile
		$app->get('/edit-profile', function (Request $request, Response $response) {
			$user = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]);

			if(is_null($user) || $user->getId() == null) {
				return $response->withRedirect($this->router->pathFor('dashboard-forbidden'), 403);
			}

			return $this->view->render($response, "dashboard/edit-profile.phtml", [
				"request" => $request,
				"router" => $this->router,
				"user" => $user
			]);
		})->setName('edit-profile');

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
			$sportsController = new Controllers_SportsController($this->db, $this->logger);

			return $this->view->render($response, "dashboard/edit-team.phtml", [
				"user" => $user,
				"team" => $team,
				"router" => $this->router,
				"leaguesAvailableForRegistration" => $leaguesController->getLeaguesForRegistration($team->getLeague()->getSportId()),
				"seasonsAvailableForRegistration" => $seasonsController->getSeasonsAvailableForRegistration(),
				"sport" => $team->getLeague()->getSport(),
				"sports" => $sportsController->getSports()
			]);

		})->setName('edit-team');

		$app->get('/score-reporter[/{sportID}[/{leagueID}[/{teamID}]]]', function(Request $request, Response $response) {

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
		})->setName('dashboard-score-reporter');

		$app->get('/standings/{leagueID}', function(Request $request, Response $response) {

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
		})->setName('dashboard-standings');

		//Edit Team
		$app->get('/team/{teamID}', function (Request $request, Response $response) {
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
		})->setName('dashboard-team-page');
		
		//Edit Team
		$app->get('/search', function (Request $request, Response $response) {
			
			$user = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]);

			if(is_null($user) || $user->getId() == null) {
				return $response->withRedirect($this->router->pathFor('dashboard-forbidden'), 403);
			}
			
			$teamsController = new Controllers_TeamsController($this->db, $this->logger);
			$allGetVars = $request->getQueryParams();
			
			$searchString = isset($allGetVars['search']) ? $allGetVars['search'] : "";
			$league = Models_League::withID($this->db, $this->logger, isset($allGetVars['leagueID']) ? $allGetVars['leagueID'] : -1);
			$sport = Models_League::withID($this->db, $this->logger, isset($allGetVars['sportID']) ? $allGetVars['sportID'] : -1);
			$season = Models_League::withID($this->db, $this->logger, isset($allGetVars['seasonID']) ? $allGetVars['seasonID']  : -1);
			$dayOfWeek = isset($allGetVars['dayOfWeek']) ? $allGetVars['dayOfWeek'] : -1;
			
			/* $teams = $teamsController->searchForTeams($searchString, "AND", $league, $sport, $season, $dayOfWeek);
			
			echo sizeof($teams);
			
			if($teams == null || sizeof($teams) == 0) {
				$teams = $teamsController->searchForTeams($searchString, "OR", $league, $sport, $season, $dayOfWeek);
			} */

			return $this->view->render($response, "dashboard/search-page.phtml", [
					"request" => $request,
					"user" => $user,
					"searchString" => $searchString,
					"league" => $league,
					"sport" => $sport,
					"season" => $season,
					"dayOfWeek" => $dayOfWeek,
					"router" => $this->router,
					"isDashboard" => true
			]); 
		})->setName('dashboard-search-page');
		
	})->add($dashboard)->add($authenticate);