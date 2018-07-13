<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->group('/control-panel/registration', function () use ($app) {

		$app->get('/configuration', function (Request $request, Response $response) {

			$propController = new Controllers_PropertiesController($this->db, $this->logger);

			return $this->view->render($response, "control-panel/registration/configuration.phtml", [
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

			return $this->view->render($response, "control-panel/registration/configEdit.phtml", [
					"request" => $request,
					"response" => $response,
					"prop" => $prop
				]
			);
		})->setName('configEdit');
		
	})->add($controlPanel)->add($authenticate);
	
?>
