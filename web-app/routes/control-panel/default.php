<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	//Home - redirect to login if not logged in, otherwise direct to dashboard
	$app->get('/control-panel', function (Request $request, Response $response) {
		
		$user = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]); //Load user from db, that way we refresh all user info.
		
		return $this->view->render($response, "control-panel/index.phtml", [
			"request" => $request,
			"user" => $user,
			"router" => $this->router
		]);
	})->setName('control-panel')->add($controlPanel)->add($authenticate);
	
	$app->group('/control-panel', function () use ($app) {
		$app->get('/forbidden', function (Request $request, Response $response) {
			return $this->view->render($response, "forbidden.phtml", [
					"request" => $request,
					"router" => $this->router
				]
			);
		})->setName('control-panel-forbidden');
		
	})->add($controlPanel)->add($authenticate);
	
?>
