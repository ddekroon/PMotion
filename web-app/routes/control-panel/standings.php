<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;
	
	$app->group('/control-panel/standings', function () use ($app) {
		
		$app->get('/unbalanced-standings', function (Request $request, Response $response) {
			$user = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]);

			if(is_null($user) || $user->getId() == null) {
				return $response->withRedirect($this->router->pathFor('control-panel-forbidden'), 403);
			}
			
			$teamsController = new Controllers_TeamsController($this->db, $this->logger);

			return $this->view->render($response, "control-panel/standings/unbalanced-standings.phtml", [
				"request" => $request,
				"router" => $this->router,
				"user" => $user,
				"teams" => $teamsController->getUnbalancedStandingsTeamData()
			]);
		})->setName('cp-unbalanced-standings');

		$app->get('/score-reporter-cancel-option', function (Request $request, Response $response) {
			
			$sportsController = new Controllers_SportsController($this->db, $this->logger);
			$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
			$sports = $sportsController->getSports();

			foreach($sports as $curSport) {
				$curSport->setLeagues($leaguesController->getLeaguesInScoreReporter($curSport->getId()));
			}

			return $this->view->render($response, "control-panel/standings/score-reporter-cancel-option.phtml", [
				"request" => $request,
				"router" => $this->router,
				"sports" => $sports
			]);
		})->setName('cp-score-reporter-cancel-option');
		
	})->add($controlPanel)->add($authenticateAdmin);

	$app->group('/control-panel/standings', function () use ($app) {
		$app->post('/score-reporter-cancel-option', function (Request $request, Response $response) {
			
			$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
			$returnObj = array();

			try {
				$leaguesController->updateCancelOptions($request);
				$returnObj["status"] = 1;
				$returnObj["successMessage"] = "League Cancel Options Updated";
			} catch(Exception $e) {
				$returnObj["status"] = 0;
				$returnObj["errorMessage"] = $e->getMessage();
			}
	
			$response->getBody()->write(json_encode($returnObj));
	
			return $response;

		})->setName('cp-submit-score-reporter-cancel-option');
	})->add($authenticateAdmin);
	
?>