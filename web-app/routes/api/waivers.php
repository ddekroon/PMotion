<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	
	$app->post('/api/waiver', function (Request $request, Response $response) {

		$waiversController = new Controllers_WaiversController($this->db, $this->logger);
		
		$returnObj = array();

		try {
			$waiversController->submitWaiver($request);
			$returnObj["success"] = 1;
			$returnObj["message"] = "Waiver Submitted";
			
			return $response->withStatus(200)
				->withHeader('Content-Type', 'application/json')
				->write(json_encode($returnObj));
		} catch(Exception $ex) {
			$returnObj["success"] = 0;
			$returnObj["message"] = "Couldn't submit waiver:\n" . $ex->getMessage();

			return $response->withStatus(400)
				->withHeader('Content-Type', 'application/json')
				->write(json_encode($returnObj));
		}
	});

?>