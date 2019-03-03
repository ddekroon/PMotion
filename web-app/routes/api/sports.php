<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->get('/api/sport/{id}', function (Request $request, Response $response) {

		$sportID = (int)$request->getAttribute('id');

		$returnObj = array();
		
		if($sportID > 0) {
			$sportController = new Controllers_SportsController($this->db, $this->logger);
			$returnObj["status"] = 1;
			$returnObj["data"] = json_encode($sportController->getSportById($sportID));
		} else {
			$returnObj["status"] = 0;
			$returnObj["data"] = new StdClass();
		}

		$response->getBody()->write(json_encode($returnObj));

		return $response;
	});
	
	$app->get('/api/sports', function (Request $request, Response $response) {

		
		$sportsController = new Controllers_SportsController($this->db, $this->logger);
		
		$returnObj = array();
		$returnObj["status"] = 1;
		//$returnObj["data"] = $sportController->getSports();
		$returnObj["data"] = $sportsController->getSports();

		$response->getBody()->write(json_encode($returnObj));

		return $response;
	});
	
?>