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

		$app->get('/configuration', function (Request $request, Response $response) {

			$propController = new Controllers_PropertiesController($this->db, $this->logger);

			return $this->view->render($response, "control-panel/configuration.phtml", [
					"request" => $request,
					"router" => $this->router,
					"db" => $this->db,
					"logger" => $this->logger,
					"propController" => $propController
				]
			);
		})->setName('config');

		$app->get('/configEdit/{propID}', function (Request $request, Response $response) {

			$prop = Models_Property::withID($this->db, $this->logger, (int)$request->getAttribute('propID'));

			return $this->view->render($response, "control-panel/configEdit.phtml", [
					"request" => $request,
					"response" => $response,
					"prop" => $prop
				]
			);
		})->setName('configEdit');
		
	})->add($controlPanel)->add($authenticate);
	
?>