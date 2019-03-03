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
		
	})->add($controlPanel)->add($authenticate);
	
?>